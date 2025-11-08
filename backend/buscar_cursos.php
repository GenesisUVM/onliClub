<?php
// Endpoint para buscar cursos
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    // Si no hay query, devolver cursos populares
    $sql = "
        SELECT c.id_curso, c.titulo, c.descripcion, COUNT(ic.id_usuario) as total_inscritos
        FROM Cursos c
        LEFT JOIN InscripcionesCursos ic ON c.id_curso = ic.id_curso
        GROUP BY c.id_curso
        ORDER BY total_inscritos DESC
        LIMIT 5
    ";
    $stmt = $conn->prepare($sql);
} else {
    // Buscar por título o descripción
    $sql = "
        SELECT c.id_curso, c.titulo, c.descripcion
        FROM Cursos c
        WHERE c.titulo LIKE ? OR c.descripcion LIKE ?
        LIMIT 10
    ";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($cursos);