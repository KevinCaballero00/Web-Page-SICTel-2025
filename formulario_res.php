<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit;
}

// Obtener y limpiar datos del formulario
$email     = trim($_POST['email_form'] ?? '');
$modalidad = trim($_POST['modalidad'] ?? '');
$estado    = trim($_POST['estado'] ?? '');
$titulo    = trim($_POST['name_form'] ?? '');
$ponente   = trim($_POST['ponente'] ?? '');
$documento = trim($_POST['documento'] ?? '');
$num_doc   = trim($_POST['num_doc'] ?? '');
$telefono  = trim($_POST['telefono'] ?? '');
$niv_estu  = trim($_POST['niv_estu'] ?? '');
$programa  = trim($_POST['programa'] ?? '');
$grupo_inv = trim($_POST['grupo_inv'] ?? '');
$institu   = trim($_POST['institu'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');

// Si hay sesión de usuario, usar nombre/email de la sesión (asegura que la ponencia quede vinculada al usuario autenticado)
if (!empty($_SESSION['name'])) {
    $ponente = $_SESSION['name'];
}
if (!empty($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

// Estado de evaluación por defecto (requiere haber ejecutado el ALTER TABLE)
$eval_status = 'Pendiente';
$revisado = 0;

// Validación mínima
$errors = [];
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido';
}
if (empty($titulo)) {
    $errors[] = 'El título es requerido';
}

if (!empty($errors)) {
    session_start();
    $_SESSION['form_errors'] = $errors;
    header('Location: formulario.php');
    exit;
}

// Insert usando prepared statement (tabla 'formularios')
$sql = "INSERT INTO formularios 
    (email_form, modalidad, estado, name_form, ponente, documento, num_doc, telefono, niv_estu, programa, grupo_inv, institu, ubicacion, eval_status, revisado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    die('Error del servidor.');
}

$stmt->bind_param(
    'ssssssssssssssi',
    $email,
    $modalidad,
    $estado,
    $titulo,
    $ponente,
    $documento,
    $num_doc,
    $telefono,
    $niv_estu,
    $programa,
    $grupo_inv,
    $institu,
    $ubicacion,
    $eval_status,
    $revisado
);


if ($stmt->execute()) {
    header('Location: formulario.php?success=1');
} else {
    error_log('Error ejecutando SQL: ' . $stmt->error);
    header('Location: formulario.php?error=1');
}

$stmt->close();
$conn->close();
exit;
?>

