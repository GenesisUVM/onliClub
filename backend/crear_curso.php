<?php
session_start();
require_once 'db.php';

// Verificar si el usuario es profesor
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: ../frontend/profesor_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $imagen_url = trim($_POST['imagen_url']);
    $id_profesor = $_SESSION['id_usuario'];

    // Validación básica
    if (empty($titulo) || empty($descripcion)) {
        // Podríamos redirigir con un error, por ahora solo volvemos
        header('Location: ../frontend/crear_curso.php?error=Campos obligatorios faltantes');
        exit;
    }

    // Insertar en la base de datos
    // Nota: Asumimos que la tabla cursos tiene las columnas: titulo, descripcion, imagen, id_profesor
    $stmt = $conn->prepare("INSERT INTO Cursos (titulo, descripcion, imagen, id_profesor, fecha_creacion) VALUES (?, ?, ?, ?, NOW())");

    if ($stmt) {
        $stmt->bind_param("sssi", $titulo, $descripcion, $imagen_url, $id_profesor);

        if ($stmt->execute()) {
            // Éxito
            header('Location: ../frontend/mis_cursos_profesor.php?success=Curso creado exitosamente');
        } else {
            // Error SQL
            header('Location: ../frontend/crear_curso.php?error=Error al crear el curso: ' . $conn->error);
        }
        $stmt->close();
    } else {
        header('Location: ../frontend/crear_curso.php?error=Error de base de datos');
    }
} else {
    header('Location: ../frontend/crear_curso.php');
}
exit;
