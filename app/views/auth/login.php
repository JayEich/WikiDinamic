<?php
use App\Helpers\Security;

$csrftoken = $_SESSION['csrf_token'] ?? Security::generateCSRFToken();
Security::logError("sesionToken:".$_SESSION['csrf_token']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf_token" content="<?= $csrftoken ?>">
  <link rel="stylesheet" href="<?= asset('OpenSans-Italic.woff') ?>">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>Wikiconcept - Login</title>

    <?= vite('src/style.css') ?>
    <?= vite('src/main.js') ?>
    
</head>
<body class="login-body">
    <div class="container-form sign-up">
    <form class="formulario" method="POST" action="<?= route('login') ?>" id="loginForm">
        
        <!-- Logo -->
        <h2 class="create-account">
        <img src="<?= asset('wikiloguito.png') ?>" class="wikilogo" alt="Logo principal">
        </h2>

        <!-- Título -->
        <h4>Iniciar Sesión</h4>

        <!-- Mensajes de error/login -->
        <div id="message" class="mb-3 text-center text-danger fw-bold"></div><br>

        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= $csrftoken ?>">

        <!-- Usuario -->
        <div class="input-group" id="loginicon">
        <span class="input-group-text">
            <i class="fa-solid fa-user" style="color: #030339;"></i>
        </span>
        <input type="text" name="usuario" id="usuario" placeholder="Usuario" autocomplete="off" required>
        </div>

        <!-- Contraseña -->
        <div class="input-group">
            <span class="input-group-text">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña" autocomplete="off" required>
        </div>

        <div class="toggle-password-container">
            <button type="button" class="toggle-password" id="togglePassword">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <small id="capsLockIndicator">Mayúscula está activado</small>

        <!-- Botón de envío -->
        <input type="submit" value="Iniciar Sesión" class="btn">
    </form>
    </div>

   
    <script>
    window.APP = {
        baseURL: "<?= rtrim($_ENV['APP_URL'] ?? '/wikiconceptMVC/public', '/') ?>"
    };
    </script>
</body>
</html>
