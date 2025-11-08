<?php
session_start();
// Si ya hay sesión de otro usuario, se puede mostrar un aviso o forzar logout. Aquí dejamos el formulario.
$emailPrefill = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Alumno - OnliClub</title>
</head>
<body>
    <h2>Iniciar sesión - Alumno</h2>
    <?php if (!empty($emailPrefill)): ?>
        <p>Se ha precargado el email: <strong><?php echo $emailPrefill; ?></strong></p>
    <?php endif; ?>

    <form method="post" action="../backend/login.php">
        <input type="hidden" name="expected_role" value="Alumno">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo $emailPrefill; ?>"><br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Ingresar como Alumno</button>
    </form>

    <p><a href="profesor_login.php">Ir al login de Profesor</a></p>
</body>
</html>
