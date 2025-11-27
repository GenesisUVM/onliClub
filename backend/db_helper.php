<?php
require_once 'db.php';

class DBHelper {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPopularCourses($limit = 6) {
        $courses = [];
        $stmt = $this->conn->prepare("SELECT id_curso, titulo, descripcion, '' AS imagen FROM cursos ORDER BY id_curso DESC LIMIT ?");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        $stmt->close();
        return $courses;
    }

    public function getCategories($limit = 20) {
        $categories = [];
        $stmt = $this->conn->prepare("SELECT id_categoria, nombre FROM Categorias ORDER BY nombre LIMIT ?");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        $stmt->close();
        return $categories;
    }

    public function getStudentStats($userId) {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(DISTINCT ic.id_curso) as total_cursos,
                COUNT(DISTINCT cl.id_leccion) as lecciones_completadas,
                COUNT(DISTINCT l.id_leccion) as total_lecciones
            FROM Usuarios u
            LEFT JOIN InscripcionesCursos ic ON u.id_usuario = ic.id_usuario
            LEFT JOIN Lecciones l ON ic.id_curso = l.id_curso
            LEFT JOIN CompletadoLecciones cl ON l.id_leccion = cl.id_leccion AND cl.id_usuario = u.id_usuario
            WHERE u.id_usuario = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $stats;
    }

    public function getRecentCourses($userId, $limit = 3) {
        $stmt = $this->conn->prepare("
            SELECT c.id_curso, c.titulo, c.descripcion, 
                   COUNT(DISTINCT l.id_leccion) as total_lecciones,
                   COUNT(DISTINCT cl.id_leccion) as lecciones_completadas,
                   ic.fecha_inscripcion
            FROM Cursos c
            JOIN InscripcionesCursos ic ON c.id_curso = ic.id_curso
            LEFT JOIN Lecciones l ON c.id_curso = l.id_curso
            LEFT JOIN CompletadoLecciones cl ON l.id_leccion = cl.id_leccion AND cl.id_usuario = ?
            WHERE ic.id_usuario = ?
            GROUP BY c.id_curso
            ORDER BY ic.fecha_inscripcion DESC
            LIMIT ?
        ");
        $stmt->bind_param('iii', $userId, $userId, $limit);
        $stmt->execute();
        $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $courses;
    }
}
?>
