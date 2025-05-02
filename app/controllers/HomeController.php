<?php
namespace App\Controllers;

use App\Helpers\Security;

class HomeController {
    public function index() {
        Security::startSecureSession();
        if (empty($_SESSION['usuario'])) {
            header('Location: ' . route('login'));
            exit;
        } else {
            // Si el usuario ya está logueado, el middleware 'guest' en /login lo redirigirá
            // a su dashboard. Así que no necesitamos hacer la redirección aquí.
            // Podrías simplemente mostrar una página de bienvenida simple si quieres,
            // o redirigir a algún lugar neutral si es necesario.
            // Por ahora, para probar la redirección al dashboard, no haremos nada aquí
            // y dejaremos que el middleware 'guest' en /login se encargue.
            // Opcionalmente, puedes redirigir a una página de inicio general
            // para usuarios logueados que no sea específica de un rol.
            // header('Location: ' . route('some.general.logged.in.page'));
            // exit;
        }
    }
}