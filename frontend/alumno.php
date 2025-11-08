<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Alumno - OnliClub</title>
</head>
<body>
    <h1>Bienvenido al panel del Alumno</h1>
    <p>Hola, <?php echo $_SESSION['nombre']; ?>. Aquí verás tus cursos y actividades.</p>
</body>
</html>
