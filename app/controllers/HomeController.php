<?php
namespace App\Controllers;

class HomeController {
    public function index() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
             $role = $_SESSION['role'];
             $dashboardRoute = match ($role) {
                 'admin' => route('admin.dashboard'),
                 'user' => route('user.dashboard'),
                 'superadmin' => route('superadmin.dashboard'),
                 default => route('login'),
             };
             header("Location: $dashboardRoute");
        } else {
             header('Location: ' . route('login'));
        }
        exit;
    }
}