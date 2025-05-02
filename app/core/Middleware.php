<?php
namespace Core;
class Middleware
{
    public static function handle(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            MiddlewareKernel::run($middleware);
        }
    }
}
