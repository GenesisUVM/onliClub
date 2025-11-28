<?php
session_start();
require_once 'db.php';

// Verificar autenticación y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: ../frontend/profesor_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_curso = isset($_POST['id_curso']) ? (int) $_POST['id_curso'] : 0;
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $precio = (float) $_POST['precio'];
    $estado = $_POST['estado'];
    $id_profesor = $_SESSION['id_usuario'];

    // Validaciones básicas
    if (empty($titulo) || empty($descripcion)) {
        // TODO: Manejar error de validación
        header("Location: ../frontend/editar_curso.php?id=$id_curso&error=campos_vacios");
        exit;
    }

    // Verificar que el curso pertenece al profesor
    $check_stmt = $conn->prepare("SELECT id_curso FROM Cursos WHERE id_curso = ? AND id_profesor = ?");
    $check_stmt->bind_param("ii", $id_curso, $id_profesor);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../frontend/mis_cursos_profesor.php?error=no_autorizado");
        exit;
    }
    $check_stmt->close();

    // Actualizar en la base de datos
    $stmt = $conn->prepare("UPDATE Cursos SET titulo = ?, descripcion = ?, precio = ?, estado = ? WHERE id_curso = ?");

    if ($stmt) {
        $stmt->bind_param("ssdsi", $titulo, $descripcion, $precio, $estado, $id_curso);

        if ($stmt->execute()) {
            // Éxito
            header("Location: ../frontend/mis_cursos_profesor.php?mensaje=curso_actualizado");
        } else {
            // Error
            header("Location: ../frontend/editar_curso.php?id=$id_curso&error=db_error");
        }
        $stmt->close();
    } else {
        header("Location: ../frontend/editar_curso.php?id=$id_curso&error=stmt_error");
    }
} else {
    header('Location: ../frontend/mis_cursos_profesor.php');
}

$conn->close();
?>