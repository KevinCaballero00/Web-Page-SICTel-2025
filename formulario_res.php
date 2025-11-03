<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit;
}

// Sanitize / map inputs (nombres corregidos para coincidir con formulario.php)
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
$sql = "INSERT INTO formularios (email_form, modalidad, estado, name_form, ponente, documento, num_doc, telefono, niv_estu, programa, grupo_inv, institu, ubicacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    die('Error del servidor.');
}

$bind = $stmt->bind_param(
    'sssssssssssss',
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
    $ubicacion
);

if (!$bind) {
    error_log('Bind failed: ' . $stmt->error);
    die('Error del servidor.');
}

$exec = $stmt->execute();
if ($exec) {
    header('Location: formulario.php?success=1');
} else {
    error_log('Execute failed: ' . $stmt->error);
    header('Location: formulario.php?error=1');
}

$stmt->close();
$conn->close();
exit;
?>

