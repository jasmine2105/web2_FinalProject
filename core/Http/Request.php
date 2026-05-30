<?php

declare(strict_types=1);

namespace Core\Http;

readonly class Request
{
    public function __construct(
        public array $get,
        public array $post,
        public array $server,
        public array $files,
        public array $cookies
    ) {}

    public static function createFromGlobals(): self
    {
        return new self(
            get:     $_GET,
            post:    $_POST,
            server:  $_SERVER,
            files:   $_FILES,
            cookies: $_COOKIE
        );
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            return substr($uri, 0, $position);
        }
        return $uri;
    }
}
