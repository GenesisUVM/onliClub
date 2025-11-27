<?php
// backend/register.php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'Alumno'; // Default to Alumno

    // Basic validation
    if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        header('Location: ../frontend/register_view.php?role=' . strtolower($role == 'Alumno' ? 'student' : 'teacher') . '&error=' . urlencode('Todos los campos son obligatorios'));
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare('SELECT id_usuario FROM Usuarios WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: ../frontend/register_view.php?role=' . strtolower($role == 'Alumno' ? 'student' : 'teacher') . '&error=' . urlencode('El correo electrónico ya está registrado'));
        exit;
    }
    $stmt->close();

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare('INSERT INTO Usuarios (nombre, apellido, email, password_hash, rol) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $nombre, $apellido, $email, $password_hash, $role);

    if ($stmt->execute()) {
        // Registration successful
        $stmt->close();
        $conn->close();

        // Redirect to login page with success message
        $loginPage = '../frontend/' . ($role == 'Alumno' ? 'alumno' : 'profesor') . '_login.php';
        header('Location: ' . $loginPage . '?success=' . urlencode('Registro exitoso. Por favor inicia sesión.'));
        exit;
    } else {
        $stmt->close();
        $conn->close();
        header('Location: ../frontend/register_view.php?role=' . strtolower($role == 'Alumno' ? 'student' : 'teacher') . '&error=' . urlencode('Error al registrar el usuario: ' . $conn->error));
        exit;
    }
} else {
    header('Location: ../frontend/index.php');
    exit;
}
