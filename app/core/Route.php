<?php
namespace Core;
class Route
{
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static array $groupStack = [];

    public static function get(string $uri, string $controllerAction): self
    {
        return self::addRoute('GET', $uri, $controllerAction);
    }

    public static function post(string $uri, string $controllerAction): self
    {
        return self::addRoute('POST', $uri, $controllerAction);
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    private static function addRoute(string $method, string $uri, string $controllerAction): self
    {
        [$controller, $action] = explode('@', $controllerAction);

        $prefix = '';
        $groupMiddleware = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= rtrim($group['prefix'], '/') . '/';
            }
            if (isset($group['middleware'])) {
                $groupMiddleware = array_merge(
                    $groupMiddleware,
                    is_array($group['middleware']) ? $group['middleware'] : explode(',', $group['middleware'])
                );
            }
        }

        $fullUri = rtrim($prefix . $uri, '/');
        $pattern = self::convertToRegex($fullUri);

        self::$routes[$method][$pattern] = [
            'uri' => $fullUri,
            'controller' => $controller,
            'action' => $action,
            'name' => null,
            'middleware' => array_map('trim', $groupMiddleware),
            'params' => self::extractParams($fullUri)
        ];

        return new self($method, $pattern);
    }

    private static function convertToRegex(string $uri): string
    {
        return '#^' . preg_replace('/\\{[^\\/]+\\}/', '([^/]+)', $uri) . '$#';
    }

    private static function extractParams(string $uri): array
    {
        preg_match_all('/\\{([^}]+)\\}/', $uri, $matches);
        return $matches[1];
    }

    private string $method;
    private string $uri;

    private function __construct(string $method, string $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }


    public function name(string $alias): self
    {
        $currentRouteData = self::$routes[$this->method][$this->uri];
        self::$routes[$this->method][$this->uri]['name'] = $alias;
        self::$namedRoutes[$alias] = $currentRouteData['uri'];
        return $this;
    }
    public function middleware(string|array $middlewares): self
    {
        $middlewares = is_array($middlewares) ? $middlewares : explode(',', $middlewares);
        $current = self::$routes[$this->method][$this->uri]['middleware'];
        self::$routes[$this->method][$this->uri]['middleware'] = array_unique(array_merge($current, array_map('trim', $middlewares)));
        return $this;
    }

    public static function resolve(string $uri, string $method): ?array
    {
        foreach (self::$routes[$method] ?? [] as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $params = [];
                foreach ($route['params'] as $i => $name) {
                    $params[$name] = $matches[$i] ?? null;
                }
                $route['params'] = $params;
                return $route;
            }
        }
        return null;
    }

    public static function all(): array
    {
        return self::$routes;
    }

    public static function named(string $name): ?string 
    {
        return self::$namedRoutes[$name] ?? null;
    }

    public static function setRoutes(array $routes, array $namedRoutes): void
    {
        self::$routes = $routes;
        self::$namedRoutes = $namedRoutes; 
    }
}
