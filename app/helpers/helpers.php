<?php

if (!function_exists('route')) {
    function route(string $name): string {
        $uriLimpia = \Core\Route::named($name);

        if (!$uriLimpia) {
            \App\Helpers\Security::logError("Intento de generar URL para ruta nombrada inexistente: {$name}");
            throw new InvalidArgumentException("La ruta '{$name}' no está registrada por nombre.");
        }

        $baseUrl = rtrim($_ENV['APP_URL'] ?? '/wikiconceptMVC', '/'); 
        return rtrim($baseUrl, '/') . '/' . ltrim($uriLimpia, '/');
    }
}

if (!function_exists('routeParam')) {
    function routeParam(string $name, array $params = []): string {
        $baseUrl = '/wikiconceptMVC/public/';
        $target = \Core\Route::named($name);
        if (!$target) throw new InvalidArgumentException("Ruta no encontrada: $name");

        [, $uri] = $target;

        foreach ($params as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        $base = '/wikiconceptMVC/public/assets/';
        $path = ltrim($path, '/');
        return $base . $path;
    }
}



//PARA DESARROLLO Y PUREBAS (INNECESARIO PAR FUNCIONAMIENTO)
if (!function_exists('vite')) {
    function vite(string $entry): string {
        $env = $_ENV['APP_ENV'] ?? 'production';

        if ($env === 'local') {
            $url = "http://localhost:5173/$entry";

            // Agrega un script para el cliente HMR de Vite (solo para JS)
            if (str_ends_with($entry, '.js')) {
                return <<<HTML
<script type="module" src="$url"></script>
HTML;
            }

            // Para CSS
            if (str_ends_with($entry, '.css')) {
                return <<<HTML
<link rel="stylesheet" href="$url">
HTML;
            }

            return '';
        }

        // En producción (usa asset)
        if (str_ends_with($entry, '.js')) {
            return '<script type="module" src="' . asset($entry) . '"></script>';
        }

        if (str_ends_with($entry, '.css')) {
            return '<link rel="stylesheet" href="' . asset($entry) . '">';
        }

        return '';
    }
}
