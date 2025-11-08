<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OnliClub</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <form id="loginForm" method="post" action="../backend/login.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Ingresar</button>
    </form>
    <div id="loginResult">
        <?php
        if (isset($_SESSION['login_error'])) {
            echo $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        // Mostrar aviso si se solicita un role específico al llegar al login
        if (isset($_GET['role'])) {
            $requested = htmlspecialchars($_GET['role']);
            echo "<p><strong>Has solicitado iniciar sesión como: $requested.</strong> Introduce tus credenciales.</p>";
        }
        ?>
    </div>
</body>
</html>
