<?php

declare(strict_types=1);

namespace Core;

use Core\Container\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Exception;
use ReflectionMethod;
use ReflectionNamedType;

class Application
{
    public Container $container;
    public Router $router;
    private array $middleware = [];

    public function __construct()
    {
        $this->container = new Container();
        $this->router    = new Router();

        $this->container->singleton(Container::class,   fn() => $this->container);
        $this->container->singleton(Router::class,      fn() => $this->router);
        $this->container->singleton(Application::class, fn() => $this);
    }

    public function use(string $middlewareClass): void
    {
        $this->middleware[] = $middlewareClass;
    }

    public function handle(Request $request): Response
    {
        $this->container->singleton(Request::class, fn() => $request);

        $routeInfo = $this->router->resolve($request);

        if (empty($routeInfo)) {
            try {
                $engine = $this->container->resolve(\Core\View\Engine::class);
                $html   = $engine->render('errors/404', ['message' => 'The page you requested was not found.']);
            } catch (\Throwable) {
                $html = '<h1>404 Not Found</h1><p>The page you requested was not found.</p>';
            }
            return new Response($html, 404);
        }

        $action = $routeInfo['action'];
        $params = $routeInfo['params'];

        $dispatch = function (Request $req) use ($action, $params): Response {
            if (is_array($action)) {
                [$class, $method] = $action;
                $controller = $this->container->resolve($class);
                $response   = $this->dispatch($controller, $method, $req, $params);
            } elseif (is_callable($action)) {
                $response = call_user_func_array($action, $params);
            } else {
                throw new Exception("Invalid route action.");
            }
            return $response instanceof Response ? $response : new Response((string) $response);
        };

        $pipeline = $dispatch;
        foreach (array_reverse($this->middleware) as $middlewareClass) {
            $next       = $pipeline;
            $middleware = $this->container->resolve($middlewareClass);
            $pipeline   = fn(Request $req) => $middleware->handle($req, $next);
        }

        return $pipeline($request);
    }

    private function dispatch(object $controller, string $method, Request $request, array $routeParams): Response
    {
        $reflector = new ReflectionMethod($controller, $method);
        $args      = [];

        foreach ($reflector->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && $type->getName() === Request::class) {
                $args[] = $request;
            } else {
                $args[] = $routeParams[$param->getName()]
                    ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
            }
        }

        $response = $controller->$method(...$args);

        return $response instanceof Response ? $response : new Response((string) $response);
    }
}
