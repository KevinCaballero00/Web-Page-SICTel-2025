<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro SICTel 2025 - UFPS</title>
    <link rel="stylesheet" href="assets/css/styleLogin.css">
</head>
<body>
    <div class="container">
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="post">
                <h2>Bienvenido a SICTel 2025</h2>
                <?= showError($errors['login']); ?>
                <input type="email" name="email" placeholder="Correo electronico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="login">Iniciar Sesión</button>
                <p>¿No tienes una cuenta? <a href="#" onclick="showForm('register-form')">Registrate</a></p>
                <p><a href="index.html" onclick="">Continuar sin iniciar sesión</a></p>
            </form>
        </div>

        
        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login_register.php" method="post">
                <h2>Registro SICTel 2025</h2>
                <?= showError($errors['register']); ?>
                <input type="text" name="name" placeholder="Nombre" required>
                <input type="text" name="lastName" placeholder="Apellido" required>
                <input type="email" name="email" placeholder="Correo Electronico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <select name="role" required>
                    <option value="">--Selecciona un Rol--</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Ponente">Ponente</option>
                    <option value="Evaluador">Evaluador</option>
                </select>
                <button type="submit" name="register">Registrarse</button>
                <p>¿Ya tienes una cuenta? <a href="#" onclick="showForm('login-form')">Iniciar Sesión</a></p>
            </form>
        </div>
    </div>

    <script src="scriptLogin.js"></script>
    
</body>
</html>