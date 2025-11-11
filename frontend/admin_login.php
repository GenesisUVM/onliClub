<?php
session_start();
$emailPrefill = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - OnliClub</title>
    <link rel="stylesheet" href="css/app.css">
</head>
<body class="login">
    <h2>Iniciar sesión - Administrador</h2>
    <?php if (!empty($emailPrefill)): ?>
        <p>Se ha precargado el email: <strong><?php echo $emailPrefill; ?></strong></p>
    <?php endif; ?>

    <form method="post" action="../backend/login.php">
        <input type="hidden" name="expected_role" value="Administrador">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo $emailPrefill; ?>"><br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Ingresar como Administrador</button>
    </form>

    <p><a href="alumno_login.php">Ir al login de Alumno</a> | <a href="profesor_login.php">Ir al login de Profesor</a></p>
</body>
</html>
