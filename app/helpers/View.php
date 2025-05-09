<?php
namespace App\Helpers;

class View {
    public static function render($viewPath, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $viewPath) . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Vista no encontrada: $viewFile";
        }
    }
}
