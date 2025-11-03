<?php

session_start();
require_once 'config.php';

// Registro de usuario
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = "¡Este Email ya se encuentra registrado!";
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (name, lastName, email, password, role) VALUES ('$name', '$lastName', '$email', '$password', '$role')");
    }

    header("Location: login.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'Administrador') {
                header("Location: index.html");
            } else {
                header("Location: index.html");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = '¡Correo electronico o contraseña no validos!';
    $_SESSION['active_form'] = 'login';
    header("Location: login.php");
    exit();
}

?>