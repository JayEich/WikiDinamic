<?php
namespace App\Controllers;
use App\Models\Wiki;

class WikiController {
    public function index() {
        // Si moviste Wiki a un namespace:
        // $wikis = \App\Models\Wiki::getAll();
        // Si no (aún con require_once):
        require_once '../app/models/Wiki.php'; // Considera mover modelos a namespaces también
        $wikis = Wiki::getAll(); // Usa el namespace global si no está en uno propio

        require_once '../app/views/wiki/index.php';
    }
}
?>