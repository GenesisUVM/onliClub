<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: alumno_login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/student_layout.php';

// Obtener los cursos del estudiante
$query = "SELECT c.*, 
                 COUNT(DISTINCT l.id_leccion) as total_lecciones,
                 COUNT(DISTINCT cl.id_leccion) as lecciones_completadas
          FROM Cursos c
          JOIN InscripcionesCursos ic ON c.id_curso = ic.id_curso
          LEFT JOIN Lecciones l ON c.id_curso = l.id_curso
          LEFT JOIN CompletadoLecciones cl ON l.id_leccion = cl.id_leccion 
               AND cl.id_usuario = ?
          WHERE ic.id_usuario = ?
          GROUP BY c.id_curso
          ORDER BY ic.fecha_inscripcion DESC";

$cursos = [];
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('ii', $_SESSION['id_usuario'], $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($curso = $result->fetch_assoc()) {
        $cursos[] = $curso;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - OnliClub</title>
    <?php echo studentStyles(); ?>
    <style>
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .course-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .course-progress {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <?php echo studentNavbar('cursos'); ?>
    
    <div class="container">
        <main class="main-content">
            <h1>Mis Cursos</h1>
            
            <div class="courses-grid">
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card">
                        <h2><?php echo htmlspecialchars($curso['titulo']); ?></h2>
                        <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                        
                        <?php
                        $progreso = $curso['total_lecciones'] > 0 
                            ? round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100)
                            : 0;
                        ?>
                        
                        <div class="course-progress">
                            <div class="progress-bar">
                                <div style="width: <?php echo $progreso; ?>%;"></div>
                            </div>
                            <p><?php echo $progreso; ?>% completado</p>
                        </div>
                        
                        <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>" 
                           class="btn btn-primary">Continuar curso</a>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($cursos)): ?>
                    <p>No estás inscrito en ningún curso todavía.</p>
                    <a href="index.php#cursos" class="btn btn-primary">Explorar cursos</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>