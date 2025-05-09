<?php
namespace App\Helpers;

class Redirect {
    public static function to($url) {
        header("Location: $url");
        exit;
    }

    public static function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }

    public static function with($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }
}
