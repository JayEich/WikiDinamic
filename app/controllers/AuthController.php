<?php
namespace App\Controllers;

use App\Helpers\Security;
use App\Models\Usuario;

class AuthController {
    public function login() {
        Security::startSecureSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
                Security::logError("token_invalido");
                Security::logError("token: ".$_POST['csrf_token']." Sessiontoken: ".$_SESSION['csrf_token']);
                header('Location: ' . route('login'));
                exit;
            }

            $usuario = Security::sanitizeInput($_POST['usuario'] ?? '');
            $clave   = Security::sanitizeInput($_POST['contraseña'] ?? '');
            $datosUsuario = Usuario::autenticar($usuario, $clave);

            if ($datosUsuario) {
                session_regenerate_id(true);

                $_SESSION['usuario']    = $datosUsuario['username'];
                $_SESSION['role']       = $datosUsuario['role'];
                $_SESSION['user_id']    = $datosUsuario['uuid'];

                Security::logError("Usuario autenticado: {$datosUsuario['username']} con rol {$datosUsuario['role']}");

                if ($datosUsuario['role'] === 'admin') {
                    header('Location: ' . route('admin.dashboard'));
                    exit;
                } elseif ($datosUsuario['role'] === 'superadmin') {
                    header('Location: ' . route('superadmin.dashboard'));
                    exit;
                } elseif ($datosUsuario['role'] === 'user') {
                    header('Location: ' . route('user.dashboard'));
                    exit;
                } else {
                    Security::logError("Rol desconocido o inválido encontrado para usuario {$datosUsuario['username']}: {$datosUsuario['role']}");
                    header('Location: ' . route('login'));
                    exit;
                }

            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Usuario o contraseña incorrectos.'];
                Security::logError("Error de autenticación para usuario: {$usuario}");
                header('Location: ' . route('login'));
                exit;
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        Security::startSecureSession();

        // Validar token CSRF antes de cerrar sesión
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
            http_response_code(403);
            Security::logError("CSRF token inválido o método no permitido");
            exit;
        }

        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');

        header('Location: ' . route('login'));
        exit;
    }
}