<?php
namespace App\Controllers\Superadmin;

use App\Helpers\Security;
class DashboardController {

    public function index() {

        $username = $_SESSION['usuario'] ?? 'Superadmin';
        Security::logError("Accediendo a Superadmin Dashboard.");
        $username = $_SESSION['usuario'] ?? 'Superadmin';
        require __DIR__ . '/../../../views/superadmin/dashboard.php';
    }
}