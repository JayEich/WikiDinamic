<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Route;
use Core\RouteManager;

// 1. Cargar rutas (se reconstruye automáticamente si es necesario)
RouteManager::load();

// 2. Obtener todas las rutas
$routes = Route::all();

// 3. Mostrar encabezado
echo "\n";
echo str_pad("MÉTODO", 10) . str_pad("URI", 20) . str_pad("NOMBRE", 25) . str_pad("CONTROLADOR", 20) . "MIDDLEWARE\n";
echo str_repeat('─', 90) . "\n";

// 4. Imprimir cada ruta
foreach ($routes as $method => $methodRoutes) {
    foreach ($methodRoutes as $uri => $info) {
        $name = $info['name'] ?? '-';
        $middleware = implode(', ', $info['middleware'] ?? []);
        $controllerAction = $info['controller'] . '@' . $info['action'];

        echo str_pad($method, 10) .
             str_pad($uri, 20) .
             str_pad($name, 25) .
             str_pad($controllerAction, 20) .
             $middleware . "\n";
    }
}

echo str_repeat('─', 90) . "\n\n";
