<?php
// Destruye la sesión y redirige al login (opcionalmente con un role)
session_start();

// Opcional: parámetro next para redirigir a una URL específica (relativa al frontend)
$role = isset($_GET['role']) ? $_GET['role'] : null;
$next = isset($_GET['next']) ? $_GET['next'] : null;
$email = isset($_GET['email']) ? $_GET['email'] : null;

// Limpiar sesión
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Construir la URL de redirección
if ($next) {
    $redirect = $next;
} else {
    // Redirigir a páginas de login separadas según role
    if ($role === 'Alumno') {
        $redirect = dirname($_SERVER['PHP_SELF']) . '/../frontend/alumno_login.php';
    } elseif ($role === 'Profesor') {
        $redirect = dirname($_SERVER['PHP_SELF']) . '/../frontend/profesor_login.php';
    } else {
        $redirect = dirname($_SERVER['PHP_SELF']) . '/../frontend/login.php';
    }
}

// Si se proporcionó email, añadirlo al querystring
if ($email) {
    $sep = (parse_url($redirect, PHP_URL_QUERY) ? '&' : '?');
    $redirect .= $sep . 'email=' . urlencode($email);
}

// Redirigir
header('Location: ' . $redirect);
exit;
