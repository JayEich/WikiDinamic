<?php

use Core\Route;
use Core\RouteManager;
use Core\Middleware;
use Core\Request;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/config/middleware.php';

//Unicamente para desarrollo y pruebas
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
//-------------------------------------------------------------------------------------------------------------------------------------------------------

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Cargar rutas desde cache o generar si no existe
RouteManager::load();

$url = $_GET['url'] ?? 'login';
$url = rtrim($url, '/');
$method = $_SERVER['REQUEST_METHOD'];

$route = Route::resolve($url, $method);

if ($route) {
    [$controllerName, $action] = [$route['controller'], $route['action']];

    // Ejecutar middlewares
    if (!empty($route['middleware'])) {
        Middleware::handle($route['middleware']);
    }

    // Incluir el controlador
    $fullControllerName = "App\\Controllers\\" . $controllerName; 
    if (class_exists($fullControllerName)) {
        $controller = new $fullControllerName(); 
    } else {
        http_response_code(500);
        echo "Error: Controlador '{$controllerName}' no encontrado.";
        exit;
    }

    // Inyección de parámetros (Request y {param})
    $params = $route['params'] ?? [];
    $reflection = new ReflectionMethod($controller, $action);
    $args = [];

    foreach ($reflection->getParameters() as $param) {
        $type = $param->getType();

        if ($type && is_a($type->getName(), Request::class, true)) {
            $className = $type->getName();
            $args[] = new $className();
        } elseif (!empty($params)) {
            $args[] = array_shift($params); // inyectar parámetros dinámicos {id}, etc.
        }
    }

    // Ejecutar la acción del controlador con argumentos inyectados
    $controller->$action(...$args);
} else {
    http_response_code(404);
    echo "Página no encontrada";
}
