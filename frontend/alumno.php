<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: alumno_login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/student_layout.php';

// Obtener estadísticas del alumno
$stmt = $conn->prepare("
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
$stmt->bind_param('i', $_SESSION['id_usuario']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener cursos recientes
$stmt = $conn->prepare("
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
    LIMIT 3
");
$stmt->bind_param('ii', $_SESSION['id_usuario'], $_SESSION['id_usuario']);
$stmt->execute();
$cursos_recientes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Alumno - OnliClub</title>
    <?php echo studentStyles(); ?>
    <style>
        .welcome-section {
            background: linear-gradient(135deg, #1A4D63 0%, #00A3BF 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #00A3BF;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #1A4D63;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .course-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-5px);
        }
        .progress-bar {
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            margin: 15px 0;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #00A3BF;
            transition: width 0.3s ease;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .action-btn {
            background: white;
            border: 2px solid #00A3BF;
            color: #1A4D63;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        .action-btn:hover {
            background: #00A3BF;
            color: white;
        }
        .welcome-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php echo studentNavbar('inicio'); ?>
    
    <div class="container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <div class="welcome-icon">🎓</div>
            <h1>¡Bienvenido de vuelta, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
            <p>Continúa tu journey de aprendizaje y alcanza tus metas</p>
        </section>

        <!-- Estadísticas rápidas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_cursos'] ?? 0; ?></div>
                <div class="stat-label">Cursos Inscritos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['lecciones_completadas'] ?? 0; ?></div>
                <div class="stat-label">Lecciones Completadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $progreso = ($stats['total_lecciones'] > 0) ? 
                        round(($stats['lecciones_completadas'] / $stats['total_lecciones']) * 100) : 0;
                    echo $progreso . '%';
                    ?>
                </div>
                <div class="stat-label">Progreso General</div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="quick-actions">
            <a href="mis_cursos.php" class="action-btn">
                📚 Mis Cursos
            </a>
            <a href="perfil.php" class="action-btn">
                👤 Mi Perfil
            </a>
            <a href="../frontend/index.php#cursos" class="action-btn">
                🔍 Explorar Cursos
            </a>
            <a href="../backend/logout.php" class="action-btn">
                🚪 Cerrar Sesión
            </a>
        </div>

        <!-- Cursos recientes -->
        <section>
            <h2>Tus Cursos Recientes</h2>
            <?php if (!empty($cursos_recientes)): ?>
                <div class="courses-grid">
                    <?php foreach ($cursos_recientes as $curso): ?>
                        <div class="course-card">
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 100)); ?>...</p>
                            
                            <?php
                            $progreso_curso = ($curso['total_lecciones'] > 0) ? 
                                round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100) : 0;
                            ?>
                            
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progreso_curso; ?>%"></div>
                            </div>
                            <p><small><?php echo $progreso_curso; ?>% completado</small></p>
                            <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>" class="btn btn-primary">
                                <?php echo $progreso_curso > 0 ? 'Continuar' : 'Comenzar'; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="mis_cursos.php" class="btn btn-outline">Ver todos mis cursos</a>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
                    <h3>📝 Aún no estás inscrito en ningún curso</h3>
                    <p>Explora nuestro catálogo y encuentra el curso perfecto para ti</p>
                    <a href="../frontend/index.php#cursos" class="btn btn-primary">Explorar Cursos</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Próximas metas -->
        <section style="margin-top: 40px; background: #f8f9fa; padding: 25px; border-radius: 8px;">
            <h2>🎯 Tus Próximas Metas</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
                <div style="background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #F59E0B;">
                    <h4>Completa tu primer curso</h4>
                    <p>Termina al menos un curso al 100%</p>
                </div>
                <div style="background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #00A3BF;">
                    <h4>5 lecciones esta semana</h4>
                    <p>Mantén tu ritmo de aprendizaje</p>
                </div>
                <div style="background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #1A4D63;">
                    <h4>Explora 3 categorías</h4>
                    <p>Amplía tus conocimientos</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>