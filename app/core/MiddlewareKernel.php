<?php
namespace Core;
class MiddlewareKernel
{
    protected static array $middlewares = [];

    public static function register(string $name, callable $callback): void
    {
        self::$middlewares[$name] = $callback;
    }

    public static function run(string $name): void
    {
        if (!isset(self::$middlewares[$name])) {
            throw new \Exception("Middleware '{$name}' no está registrado.");
        }

        call_user_func(self::$middlewares[$name]);
    }

    public static function has(string $name): bool
    {
        return isset(self::$middlewares[$name]);
    }

    public static function all(): array
    {
        return self::$middlewares;
    }
}
