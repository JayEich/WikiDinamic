<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superadmin - WikiConcept</title>
    <?= vite('src/style.css') ?>
</head>
<body>
    <?php // include __DIR__ . '/../layouts/superadmin_nav.php'; ?>
     <nav class="navbar"> <div class="logo"></div>
        <div class="title">Panel Superadmin</div>
        <div class="user-info">
             <span><?= htmlspecialchars($username) ?></span>
             <form action="<?= route('logout') ?>" method="POST" style="display: inline;">
                 <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                 <button type="submit" id="btnCerrarSesion">Logout</button>
             </form>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Bienvenido al Dashboard, <?= htmlspecialchars($username) ?>!</h1>
        <p>Este es el panel principal del Superadministrador.</p>
        <hr>
        <p>Próximas acciones:</p>
        <ul>
            <li><a href="<?= route('superadmin.client.edit') ?>">Editar Información del Cliente</a></li>
            </ul>
    </div>

    <?= vite('src/main.js') ?>
</body>
</html>