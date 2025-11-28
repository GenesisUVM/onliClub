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
          LEFT JOIN Modulos m ON c.id_curso = m.id_curso
          LEFT JOIN Lecciones l ON m.id_modulo = l.id_modulo
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
</head>

<body>
    <?php echo studentNavbar('cursos'); ?>

    <div class="container">
        <div class="page-header">
            <h1>Mis Cursos</h1>
        </div>

        <div class="courses-grid">
            <?php foreach ($cursos as $curso): ?>
                <article class="course-card">
                    <div class="content">
                        <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 120)) . '...'; ?></p>

                        <?php
                        $progreso = $curso['total_lecciones'] > 0
                            ? round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100)
                            : 0;
                        ?>

                        <div class="progress-container">
                            <div class="progress-bar">
                                <div style="width: <?php echo $progreso; ?>%;"></div>
                            </div>
                            <span class="progress-text"><?php echo $progreso; ?>% completado</span>
                        </div>

                        <div class="actions">
                            <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>" class="btn btn-primary"
                                style="width: 100%;">
                                <?php echo $progreso > 0 ? 'Continuar' : 'Comenzar'; ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if (empty($cursos)): ?>
                <div class="empty-state">
                    <h3>No estás inscrito en ningún curso</h3>
                    <p>Explora nuestro catálogo y comienza a aprender hoy mismo.</p>
                    <a href="index.php#cursos" class="btn btn-primary">Explorar Cursos</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>