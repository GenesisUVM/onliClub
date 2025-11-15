<?php
session_start();
$emailPrefill = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Alumno - OnliClub</title>
    <link rel="stylesheet" href="css/app.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
            border-color: #00A3BF;
        }
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00A3BF, #1A4D63);
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
            color: #00A3BF;
            text-decoration: none;
            margin: 0 10px;
        }
        .login-links a:hover {
            text-decoration: underline;
        }
        .demo-credentials {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body class="login">
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">🎓</div>
            <h1 class="login-title">Bienvenido Alumno</h1>
            <p>Ingresa a tu cuenta para continuar aprendiendo</p>
        </div>

        <?php if (!empty($emailPrefill)): ?>
            <div style="background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                Email precargado: <strong><?php echo $emailPrefill; ?></strong>
            </div>
        <?php endif; ?>

        <form method="post" action="../backend/login.php">
            <input type="hidden" name="expected_role" value="Alumno">
            
            <div class="form-group">
                <label for="email">📧 Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo $emailPrefill; ?>" placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Tu contraseña">
            </div>
            
            <button type="submit" class="login-btn">🎯 Ingresar como Alumno</button>
        </form>

        <div class="demo-credentials">
            <strong>Credenciales de prueba:</strong><br>
            Email: alumno@test.com<br>
            Contraseña: password
        </div>

        <div class="login-links">
            <a href="profesor_login.php">👨‍🏫 Soy Profesor</a>
            <a href="index.php">🏠 Volver al Inicio</a>
        </div>
    </div>
</body>
</html>