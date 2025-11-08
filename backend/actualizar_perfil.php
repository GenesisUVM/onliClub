<?php
// Endpoint para actualizar el perfil del usuario
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../frontend/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Verificar si el email ya existe (excluyendo el usuario actual)
    $stmt = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE email = ? AND id_usuario != ?");
    $stmt->bind_param('si', $email, $_SESSION['id_usuario']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['profile_error'] = "El email ya está en uso por otro usuario.";
        header('Location: ../frontend/perfil.php');
        exit;
    }
    
    // Preparar la actualización
    if (!empty($password)) {
        // Actualizar incluyendo la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            UPDATE Usuarios 
            SET nombre = ?, apellido = ?, email = ?, password_hash = ?
            WHERE id_usuario = ?
        ");
        $stmt->bind_param('ssssi', $nombre, $apellido, $email, $password_hash, $_SESSION['id_usuario']);
    } else {
        // Actualizar sin la contraseña
        $stmt = $conn->prepare("
            UPDATE Usuarios 
            SET nombre = ?, apellido = ?, email = ?
            WHERE id_usuario = ?
        ");
        $stmt->bind_param('sssi', $nombre, $apellido, $email, $_SESSION['id_usuario']);
    }
    
    if ($stmt->execute()) {
        // Actualizar la sesión con los nuevos datos
        $_SESSION['nombre'] = $nombre;
        $_SESSION['email'] = $email;
        $_SESSION['profile_success'] = "Perfil actualizado correctamente.";
    } else {
        $_SESSION['profile_error'] = "Error al actualizar el perfil.";
    }
    
    header('Location: ../frontend/perfil.php');
    exit;
}

// Si no es POST, redirigir al perfil
header('Location: ../frontend/perfil.php');
exit;