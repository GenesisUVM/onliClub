<?php
$role = $_GET['role'] ?? 'student';
$roleTitle = 'Alumno';
$icon = '🎓'; // Default icon
$dbRole = 'Alumno';

switch ($role) {
    case 'teacher':
        $roleTitle = 'Profesor';
        $icon = '👨‍🏫';
        $dbRole = 'Profesor';
        break;
    case 'admin':
        $roleTitle = 'Administrador';
        $icon = '⚙️';
        $dbRole = 'Administrador';
        break;
    default:
        $roleTitle = 'Alumno';
        $icon = '🎓';
        $dbRole = 'Alumno';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro <?php echo $roleTitle; ?> - OnliClub</title>
    <link rel="stylesheet" href="css/app.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .login-title {
            color: #1A4D63;
            margin-bottom: 10px;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1A4D63;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #dc3545;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
        }

        .login-links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .login-links a {
            color: #dc3545;
            text-decoration: none;
            margin: 0 5px;
        }

        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="login">
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon"><?php echo $icon; ?></div>
            <h1 class="login-title">Registro <?php echo $roleTitle; ?></h1>
            <p>Crea tu cuenta</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div
                style="background: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 20px; color: #721c24; text-align: center;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="../backend/register.php">
            <input type="hidden" name="role" value="<?php echo $dbRole; ?>">

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre">
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required placeholder="Tu apellido">
            </div>

            <div class="form-group">
                <label for="email">📧 Email:</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>

            <div class="form-group">
                <label for="password">🔒 Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Crea una contraseña">
            </div>

            <button type="submit" class="login-btn">Registrarse como <?php echo $roleTitle; ?></button>
        </form>

        <div class="login-links">
            <p>¿Ya tienes cuenta?</p>
            <a href="<?php echo ($role == 'teacher') ? 'profesor_login.php' : 'alumno_login.php'; ?>">Iniciar Sesión</a>
        </div>
    </div>
</body>

</html>