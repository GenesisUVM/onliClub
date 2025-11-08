<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: alumno_login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/student_layout.php';

// Obtener ID del curso y lección actual
$curso_id = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : 0;
$leccion_id = isset($_GET['leccion_id']) ? (int)$_GET['leccion_id'] : 0;

// Obtener datos del curso y sus lecciones
$curso = null;
$lecciones = [];
$leccion_actual = null;

if ($curso_id > 0) {
    // Obtener información del curso
    $stmt = $conn->prepare("SELECT id_curso, titulo FROM Cursos WHERE id_curso = ?");
    $stmt->bind_param('i', $curso_id);
    $stmt->execute();
    $curso = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Obtener todas las lecciones del curso
    $stmt = $conn->prepare("
        SELECT l.*, 
               CASE WHEN cl.fecha_completado IS NOT NULL THEN 1 ELSE 0 END as completada
        FROM Lecciones l
        LEFT JOIN CompletadoLecciones cl ON l.id_leccion = cl.id_leccion 
            AND cl.id_usuario = ?
        WHERE l.id_curso = ?
        ORDER BY l.orden
    ");
    $stmt->bind_param('ii', $_SESSION['id_usuario'], $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($leccion = $result->fetch_assoc()) {
        $lecciones[] = $leccion;
        if ($leccion['id_leccion'] == $leccion_id) {
            $leccion_actual = $leccion;
        }
    }
    $stmt->close();

    // Si no se especificó lección_id, usar la primera no completada o la primera
    if (!$leccion_actual && !empty($lecciones)) {
        foreach ($lecciones as $lec) {
            if (!$lec['completada']) {
                $leccion_actual = $lec;
                break;
            }
        }
        if (!$leccion_actual) {
            $leccion_actual = $lecciones[0];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $curso ? htmlspecialchars($curso['titulo']) : 'Curso'; ?> - OnliClub</title>
    <?php echo studentStyles(); ?>
</head>
<body>
    <?php echo studentNavbar('cursos'); ?>
    
    <div class="container">
        <?php if ($curso): ?>
            <aside class="sidebar">
                <h2><?php echo htmlspecialchars($curso['titulo']); ?></h2>
                
                <?php
                // Calcular progreso
                $total = count($lecciones);
                $completadas = array_reduce($lecciones, function($carry, $item) {
                    return $carry + ($item['completada'] ? 1 : 0);
                }, 0);
                $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                ?>
                
                <div class="progress-bar">
                    <div style="width: <?php echo $porcentaje; ?>%;"></div>
                </div>
                <p><?php echo $porcentaje; ?>% completado</p>
                
                <ul class="lesson-list">
                    <?php foreach ($lecciones as $leccion): ?>
                        <li class="<?php 
                            echo $leccion['completada'] ? 'completed ' : '';
                            echo $leccion_actual && $leccion['id_leccion'] == $leccion_actual['id_leccion'] ? 'current' : '';
                        ?>">
                            <a href="?curso_id=<?php echo $curso_id; ?>&leccion_id=<?php echo $leccion['id_leccion']; ?>">
                                <?php echo htmlspecialchars($leccion['titulo']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <main class="main-content">
                <?php if ($leccion_actual): ?>
                    <div class="lesson">
                        <h1><?php echo htmlspecialchars($leccion_actual['titulo']); ?></h1>
                        
                        <div class="lesson-content">
                            <?php echo $leccion_actual['contenido']; ?>
                        </div>
                        
                        <div class="lesson-nav">
                            <?php
                            // Encontrar índices de lección actual, anterior y siguiente
                            $current_index = array_search($leccion_actual, $lecciones);
                            $prev_index = $current_index - 1;
                            $next_index = $current_index + 1;
                            ?>
                            
                            <?php if ($prev_index >= 0): ?>
                                <a href="?curso_id=<?php echo $curso_id; ?>&leccion_id=<?php echo $lecciones[$prev_index]['id_leccion']; ?>" 
                                   class="btn btn-outline">← Anterior</a>
                            <?php endif; ?>
                            
                            <form method="post" action="../backend/marcar_completado.php" style="display: inline;">
                                <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                                <input type="hidden" name="leccion_id" value="<?php echo $leccion_actual['id_leccion']; ?>">
                                <button type="submit" class="btn btn-primary" 
                                        <?php echo $leccion_actual['completada'] ? 'disabled' : ''; ?>>
                                    <?php echo $leccion_actual['completada'] ? 'Completada ✓' : 'Marcar como completada'; ?>
                                </button>
                            </form>
                            
                            <?php if ($next_index < count($lecciones)): ?>
                                <a href="?curso_id=<?php echo $curso_id; ?>&leccion_id=<?php echo $lecciones[$next_index]['id_leccion']; ?>" 
                                   class="btn btn-outline">Siguiente →</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Selecciona una lección del menú lateral para comenzar.</p>
                <?php endif; ?>
            </main>
        <?php else: ?>
            <main class="main-content">
                <p>Curso no encontrado.</p>
                <a href="mis_cursos.php">Volver a mis cursos</a>
            </main>
        <?php endif; ?>
    </div>
</body>
</html>