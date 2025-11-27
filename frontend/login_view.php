<?php
$role = $_GET['role'] ?? 'student';
$roleTitle = 'Alumno';
$icon = '🎓'; // Default icon

switch ($role) {
    case 'teacher':
        $roleTitle = 'Profesor';
        $icon = '👨‍🏫';
        break;
    case 'admin':
        $roleTitle = 'Administrador';
        $icon = '⚙️';
        break;
    default:
        $roleTitle = 'Alumno';
        $icon = '🎓';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login <?php echo $roleTitle; ?> - OnliClub</title>
    <link rel="stylesheet" href="css/app.css?v=<?php echo time(); ?>">
    <style>
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-icon {
            font-size: 4em;
            margin-bottom: 15px;
            display: inline-block;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .login-title {
            color: #1A4D63;
            margin-bottom: 5px;
            margin-top: 0;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        .login-subtitle {
            color: #666;
            margin: 0;
            font-size: 0.95em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1A4D63;
            font-weight: 600;
            font-size: 0.9em;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00A3BF;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 163, 191, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00A3BF 0%, #1A4D63 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-family: 'Poppins', sans-serif;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 163, 191, 0.3);
        }

        .login-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .other-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            font-size: 0.9em;
        }

        .other-links a {
            color: #666;
            text-decoration: none;
            transition: color 0.2s;
        }

        .other-links a:hover {
            color: #00A3BF;
        }

        .register-link {
            display: inline-block;
            padding: 12px;
            background: #fff0f0;
            color: #dc3545;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ffcdd2;
            transition: all 0.2s;
        }

        .register-link:hover {
            background: #ffe5e5;
            transform: scale(1.02);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 0.95em;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>

<body class="login">
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon"><?php echo $icon; ?></div>
            <h1 class="login-title"><?php echo $roleTitle; ?></h1>
            <p class="login-subtitle">Bienvenido a OnliClub</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="../backend/login.php">
            <input type="hidden" name="expected_role" value="<?php echo $roleTitle; ?>">

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" placeholder="ejemplo@correo.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="login-btn">Iniciar Sesión</button>
        </form>

        <div class="login-links">
            <a href="<?php echo ($role == 'teacher') ? 'profesor_register.php' : 'alumno_register.php'; ?>"
                class="register-link">
                Crear cuenta nueva de <?php echo $roleTitle; ?>
            </a>

            <div class="other-links">
                <a href="<?php echo ($role == 'teacher') ? 'alumno_login.php' : 'profesor_login.php'; ?>">
                    Cambiar a <?php echo ($role == 'teacher') ? 'Alumno' : 'Profesor'; ?>
                </a>
                <span>•</span>
                <a href="index.php">Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>

</html>