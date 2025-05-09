<?php
namespace App\Helpers;

class Auth
{
    // Obtener usuario actual desde sesión
    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    // Obtener UUID del usuario autenticado
    public static function id()
    {
        return $_SESSION['user']['uuid'] ?? null;
    }

    // Verificar si hay sesión activa
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    // Verificar si el usuario es admin
    public static function isAdmin()
    {
        return self::check() && $_SESSION['user']['role'] === 'admin';
    }

    // Verificar si el usuario es superadmin
    public static function isSuperAdmin()
    {
        return self::check() && $_SESSION['user']['role'] === 'superadmin';
    }

    // Verificar si es un usuario básico
    public static function isUser()
    {
        return self::check() && $_SESSION['user']['role'] === 'user';
    }

    // Cerrar sesión
    public static function logout()
    {
        session_destroy();
    }
}
