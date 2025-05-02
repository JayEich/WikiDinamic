<?php
namespace App\Helpers;

class Security {
    private const METHOD = 'AES-256-CBC';
    private const SECRET_KEY = 'conceptoBPO-l4ssm3j0r3sspr4kt1k4ss.1056*2025++'; 
    private const SECRET_IV = "clave_unica_iv";

    //Session
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name("wiki_session");

            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_secure', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.use_only_cookies', 1);
            ini_set('session.gc_maxlifetime', 600);

            session_start();
            session_regenerate_id(true);
        }
    
        $expiracion = 10 * 60; 
    
        if (isset($_SESSION['last_activity'])) {
            $inactivo = time() - $_SESSION['last_activity'];
            if ($inactivo > $expiracion) {
                session_unset();
                session_destroy();
                setcookie(session_name(), '', time() - 3600, '/');
                header('Location: ' . route('login')); 
                exit;
            }
        }
    
        $_SESSION['last_activity'] = time();
    }
    

    // CSRF
    public static function generateCSRFToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Input
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function validateInputType($value, $type) {
        $value = self::sanitizeInput($value);
        switch ($type) {
            case 'email': return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : false;
            case 'numerico': return preg_match('/^[0-9]+$/', $value) ? $value : false;
            case 'nombre': return preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/', $value) ? $value : false;
            case 'dataname': return preg_match('/^[A-Za-z0-9._%+-]+$/', $value) ? $value : false;
            case 'alfanumerico': return preg_match('/^[A-Za-z0-9]+$/', $value) ? $value : false;
            default: return $value;
        }
    }

    // Encriptar ID
    public static function encryptID($id) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::METHOD));
        $encrypted = openssl_encrypt($id, self::METHOD, self::SECRET_KEY, 0, $iv);
        return base64_encode($iv . "::" . $encrypted);
    }

    public static function decryptID($encrypted_id) {
        $data = base64_decode($encrypted_id);
        $parts = explode("::", $data, 2);
        if (count($parts) !== 2) return false;
        [$iv, $encrypted] = $parts;
        return openssl_decrypt($encrypted, self::METHOD, self::SECRET_KEY, 0, $iv);
    }

    // Imagenes
    public static function guardarImagenHasheada($file, $adminUsername, $imagenAnterior = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return $imagenAnterior;
        }

        if ($imagenAnterior) {
            $rutaAnterior = __DIR__ . "/../../" . $imagenAnterior;
            if (file_exists($rutaAnterior)) unlink($rutaAnterior);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $hashedName = bin2hex(random_bytes(10)) . '.' . $extension;
        $dir = __DIR__ . "/../../public/localimages/$adminUsername";

        if (!file_exists($dir)) mkdir($dir, 0775, true);

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($extension), $permitidas)) throw new \Exception("Formato de imagen no permitido.");
        if ($file['size'] > 2 * 1024 * 1024) throw new \Exception("La imagen supera el límite de 2MB.");

        $rutaRelativa = "localimages/$adminUsername/$hashedName";
        $rutaAbsoluta = __DIR__ . "/../../public/" . $rutaRelativa;

        move_uploaded_file($file['tmp_name'], $rutaAbsoluta);
        return $rutaRelativa;
    }

    // Roles y sesión
    public static function validateRole($requiredRole) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // Log
    public static function logError($mensaje) {
        $fecha = date('Y-m-d H:i:s');
        file_put_contents(__DIR__ . "/../../storage/logs/error_log.txt", "[$fecha] $mensaje\n", FILE_APPEND);
    }
}
