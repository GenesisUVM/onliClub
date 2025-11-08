<?php
session_start();
require_once __DIR__ . '/db.php';

// Verificar que el usuario está autenticado y es un alumno
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: ../frontend/alumno_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso_id = isset($_POST['curso_id']) ? (int)$_POST['curso_id'] : 0;
    
    if ($curso_id > 0) {
        // Verificar si el curso existe y está disponible
        $stmt = $conn->prepare("SELECT disponible, precio FROM Cursos WHERE id_curso = ?");
        $stmt->bind_param('i', $curso_id);
        $stmt->execute();
        $curso = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($curso && $curso['disponible']) {
            // Verificar si el usuario ya está inscrito
            $stmt = $conn->prepare("SELECT 1 FROM InscripcionesCursos WHERE id_usuario = ? AND id_curso = ?");
            $stmt->bind_param('ii', $_SESSION['id_usuario'], $curso_id);
            $stmt->execute();
            $ya_inscrito = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            
            if (!$ya_inscrito) {
                // Si el curso tiene precio, aquí iría la lógica de pago
                if ($curso['precio'] > 0) {
                    // TODO: Implementar lógica de pago
                    $_SESSION['inscription_error'] = "La inscripción a cursos de pago aún no está implementada.";
                    header('Location: ../frontend/ver_curso.php?id=' . $curso_id);
                    exit;
                }
                
                // Inscribir al usuario
                $stmt = $conn->prepare("
                    INSERT INTO InscripcionesCursos (id_usuario, id_curso, fecha_inscripcion)
                    VALUES (?, ?, NOW())
                ");
                $stmt->bind_param('ii', $_SESSION['id_usuario'], $curso_id);
                
                if ($stmt->execute()) {
                    $_SESSION['inscription_success'] = "¡Te has inscrito correctamente al curso!";
                    header('Location: ../frontend/curso.php?curso_id=' . $curso_id);
                    exit;
                } else {
                    $_SESSION['inscription_error'] = "Error al procesar la inscripción. Intenta de nuevo.";
                }
                $stmt->close();
            } else {
                $_SESSION['inscription_error'] = "Ya estás inscrito en este curso.";
            }
        } else {
            $_SESSION['inscription_error'] = "El curso no está disponible.";
        }
    } else {
        $_SESSION['inscription_error'] = "Curso no válido.";
    }
}

// Si algo falló, redirigir de vuelta
header('Location: ../frontend/ver_curso.php?id=' . $curso_id);
exit;