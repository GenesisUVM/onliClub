<?php
// Procesa el login de usuario (Alumno o Profesor)
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id_usuario, nombre, apellido, password_hash, rol FROM Usuarios WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Detectar si la petición viene desde AJAX (fetch/XHR) para devolver JSON
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
           || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            // Si el formulario esperaba un rol específico, validar coincidencia
            if (isset($_POST['expected_role']) && !empty($_POST['expected_role'])) {
                $expected = trim($_POST['expected_role']);
                if ($user['rol'] !== $expected) {
                    $stmt->close();
                    $conn->close();
                    $loginPage = '../frontend/' . strtolower($expected) . '_login.php';
                    $msg = 'Tipo de usuario no coincide con el login solicitado';
                    if ($isAjax) {
                        echo json_encode(['success' => false, 'error' => $msg]);
                    } else {
                        $redirect = $loginPage . '?error=' . urlencode($msg) . '&email=' . urlencode($email);
                        header('Location: ' . $redirect);
                    }
                    exit;
                }
            }

            // Login exitoso
            session_start();
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            // Determinar destino según rol
            $role = $user['rol'];
            $dest = '../frontend/index.php';
            if ($role === 'Alumno') $dest = '../frontend/alumno.php';
            elseif ($role === 'Profesor') $dest = '../frontend/profesor.php';
            elseif ($role === 'Administrador') $dest = '../frontend/admin.php';

            // Si la petición es AJAX, devolver JSON, sino redirigir
            if ($isAjax) {
                echo json_encode(['success' => true, 'rol' => $user['rol'], 'nombre' => $user['nombre'], 'redirect' => $dest]);
            } else {
                header('Location: ' . $dest);
            }
            $stmt->close();
            $conn->close();
            exit;
        } else {
            $stmt->close();
            $conn->close();
            $msg = 'Contraseña incorrecta';
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => $msg]);
            } else {
                $loginPage = isset($_POST['expected_role']) ? ('../frontend/' . strtolower(trim($_POST['expected_role'])) . '_login.php') : '../frontend/index.php';
                header('Location: ' . $loginPage . '?error=' . urlencode($msg) . '&email=' . urlencode($email));
            }
            exit;
        }
    } else {
        $stmt->close();
        $conn->close();
        $msg = 'Usuario no encontrado';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]);
        } else {
            $loginPage = isset($_POST['expected_role']) ? ('../frontend/' . strtolower(trim($_POST['expected_role'])) . '_login.php') : '../frontend/index.php';
            header('Location: ' . $loginPage . '?error=' . urlencode($msg) . '&email=' . urlencode($email));
        }
        exit;
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
