<?php

declare(strict_types=1);

namespace Core\Http;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->register(method: 'GET', uri: $uri, action: $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->register(method: 'POST', uri: $uri, action: $action);
    }

    private function register(string $method, string $uri, array $action): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $uri);
        $pattern = "#^{$pattern}$#";
        $this->routes[$method][$pattern] = $action;
    }

    public function resolve(Request $request): array
    {
        $method = $request->getMethod();
        $uri    = $request->getUri();
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => $action) {
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);
                return ['action' => $action, 'params' => $params];
            }
        }

        return [];
    }
}
