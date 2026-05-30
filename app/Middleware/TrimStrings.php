<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;

class TrimStrings
{
    public function handle(Request $request, callable $next): Response
    {
        $trimmed = array_map(
            fn(mixed $value): mixed => is_string($value) ? trim($value) : $value,
            $request->post
        );

        $clean = new Request(
            get:     $request->get,
            post:    $trimmed,
            server:  $request->server,
            files:   $request->files,
            cookies: $request->cookies,
        );

        return $next($clean);
    }
}
