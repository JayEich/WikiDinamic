<?php
namespace App\Controllers;

use App\Helpers\Security;

class AdminController {

    public function dashboard() {
        Security::logError("Accediendo a Admin Dashboard.");
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

}
?>