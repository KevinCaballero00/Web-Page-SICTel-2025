<?php
include 'config.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Obtener el email del usuario de la sesión
$email = $_SESSION['email'];

// Consulta correcta usando prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil - SICTel 2025</title>
    <link rel="stylesheet" href="assets/css/styleProfile.css">
</head>

<body>
    <div class="container">
        <div class="form-box active profile-box">
            <?php if ($user): ?>
                <h2>Mi Perfil</h2>
                <!-- Información del usuario -->
                <div class="profile-info">
                    <div class="info-group">
                        <label>Nombre:</label>
                        <p><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Apellido:</label>
                        <p><?php echo htmlspecialchars($user['lastName']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Email:</label>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Rol:</label>
                        <p><?php echo htmlspecialchars($user['role']); ?></p>
                    </div>
                </div>
                <!-- Botones de acción -->
                <?php if ($user['role'] === 'Ponente'): ?>
                    <button onclick="window.location.href='formulario.php'">Inscribir Ponencia</button>
                <?php endif; ?>

                <?php if ($user['role'] === 'Evaluador'): ?>
                    <button onclick="window.location.href='gestion_formularios.php'">Ver Ponencias</button>
                <?php endif; ?>

                <?php if ($user['role'] === 'Administrador'): ?>
                    <button onclick="window.location.href='gestion_admin.php'">Administrar Usuarios</button>
                <?php endif; ?>

                <button onclick="window.location.href='login.php'">Cerrar Sesión</button>

                <button onclick="window.location.href='index.html'">Volver al Inicio</button>
            <?php else: ?>
                <p class="error-message">No se encontraron datos del usuario.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>