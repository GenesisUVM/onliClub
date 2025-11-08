<?php
// Endpoint para marcar una lección como completada
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: ../frontend/alumno_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso_id = isset($_POST['curso_id']) ? (int)$_POST['curso_id'] : 0;
    $leccion_id = isset($_POST['leccion_id']) ? (int)$_POST['leccion_id'] : 0;
    
    if ($curso_id && $leccion_id) {
        // Verificar que la lección pertenece al curso y el usuario está inscrito
        $stmt = $conn->prepare("
            SELECT 1 
            FROM Lecciones l
            JOIN InscripcionesCursos ic ON l.id_curso = ic.id_curso
            WHERE l.id_curso = ? AND l.id_leccion = ? AND ic.id_usuario = ?
        ");
        $stmt->bind_param('iii', $curso_id, $leccion_id, $_SESSION['id_usuario']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Marcar como completada (ignorar si ya existe)
            $stmt = $conn->prepare("
                INSERT IGNORE INTO CompletadoLecciones 
                (id_usuario, id_leccion, fecha_completado)
                VALUES (?, ?, NOW())
            ");
            $stmt->bind_param('ii', $_SESSION['id_usuario'], $leccion_id);
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // Redirigir de vuelta a la lección
    header("Location: ../frontend/curso.php?curso_id=$curso_id&leccion_id=$leccion_id");
    exit;
}

// Si no es POST, redirigir a la página de cursos
header('Location: ../frontend/mis_cursos.php');
exit;