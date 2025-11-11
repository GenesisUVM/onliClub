<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - OnliClub</title>
</head>
<body>
    <h1>Bienvenido al panel del Administrador</h1>
    <p>Hola, <?php echo $_SESSION['nombre']; ?>. Gestión avanzada de la plataforma.</p>
</body>
</html>
