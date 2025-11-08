<?php
session_start();
// Si el archivo no existía antes, lo creamos. Protegemos la página por rol.
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Profesor - OnliClub</title>
</head>
<body>
    <h1>Bienvenido al panel del Profesor</h1>
    <p>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>. Aquí podrás gestionar tus cursos y alumnos.</p>

    <p>
        <!-- Enlace para cambiar al flujo de Alumno: destruye sesión y redirige al login con role=Alumno, pasando el email actual para precarga -->
        <a href="../backend/logout.php?role=Alumno&email=<?php echo urlencode($_SESSION['email'] ?? ''); ?>">Cambiar a Alumno (ir al login como Alumno)</a>
    </p>

    <p>
        <!-- Enlace de logout simple -->
        <a href="../backend/logout.php">Cerrar sesión</a>
    </p>
</body>
</html>
