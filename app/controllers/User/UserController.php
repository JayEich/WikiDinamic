<?php
namespace App\Controllers\User;

use App\Helpers\Security;

class UserController {
    public function index() {
        Security::logError("Accediendo a User Dashboard.");
        $username = $_SESSION['usuario'] ?? 'Usuario';
        require __DIR__ . '/../../../views/user/dashboard.php';
    }
}