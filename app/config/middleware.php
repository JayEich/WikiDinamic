<?php

use Core\MiddlewareKernel;
use App\Helpers\Security; 

MiddlewareKernel::register('auth', function () {
    Security::startSecureSession(); 
    if (empty($_SESSION['usuario'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
             http_response_code(401); // Unauthorized
             echo json_encode(['error' => 'No autenticado']);
             exit;
        } else {
            header('Location: ' . route('login')); 
            exit;
        }
    }
});

MiddlewareKernel::register('guest', function () {
    Security::startSecureSession();
    if (!empty($_SESSION['usuario'])) {
        // Determina el dashboard basado en rol
         $role = $_SESSION['role'] ?? null;
         $dashboardRoute = match ($role) {
             'admin' => route('admin.dashboard'),
             'user' => route('user.dashboard'),
             'superadmin' => route('superadmin.dashboard'),
             default => route('login'),
         };
        header("Location: $dashboardRoute");
        exit;
    }
});


MiddlewareKernel::register('admin', function () {
    if (empty($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') ) { // Permitir superadmin también
        http_response_code(403); // Forbidden
        exit("Acceso restringido: solo administradores.");
    }
});


MiddlewareKernel::register('superadmin', function () {
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
        http_response_code(403); // Forbidden
        exit("Acceso restringido: solo superadministradores.");
    }
    if (empty($_SESSION['client_uuid'])) {
         http_response_code(403);
         // Loggear este caso raro: 
         \App\Helpers\Security::logError("Superadmin {$_SESSION['user_id']} sin client_uuid asociado.");
         exit("Error de configuración: Superadministrador no asociado a un cliente.");
    }
});

?>