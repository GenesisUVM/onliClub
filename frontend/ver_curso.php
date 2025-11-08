<?php
session_start();
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/student_layout.php';

$curso_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar si el usuario ya está inscrito
$inscrito = false;
if (isset($_SESSION['id_usuario'])) {
    $stmt = $conn->prepare("SELECT 1 FROM InscripcionesCursos WHERE id_usuario = ? AND id_curso = ?");
    $stmt->bind_param('ii', $_SESSION['id_usuario'], $curso_id);
    $stmt->execute();
    $inscrito = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}

// Obtener detalles del curso
$stmt = $conn->prepare("
    SELECT c.*, 
           COUNT(DISTINCT ic.id_usuario) as total_inscritos,
           COUNT(DISTINCT l.id_leccion) as total_lecciones,
           u.nombre as profesor_nombre,
           u.apellido as profesor_apellido
    FROM Cursos c
    LEFT JOIN InscripcionesCursos ic ON c.id_curso = ic.id_curso
    LEFT JOIN Lecciones l ON c.id_curso = l.id_curso
    LEFT JOIN Usuarios u ON c.id_profesor = u.id_usuario
    WHERE c.id_curso = ?
    GROUP BY c.id_curso
");
$stmt->bind_param('i', $curso_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener lista de lecciones
$lecciones = [];
if ($curso) {
    $stmt = $conn->prepare("
        SELECT id_leccion, titulo, duracion_minutos
        FROM Lecciones
        WHERE id_curso = ?
        ORDER BY orden
    ");
    $stmt->bind_param('i', $curso_id);
    $stmt->execute();
    $lecciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $curso ? htmlspecialchars($curso['titulo']) : 'Curso no encontrado'; ?> - OnliClub</title>
    <?php echo studentStyles(); ?>
    <style>
        .course-header {
            background: #fff;
            padding: 30px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .course-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .course-details {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .course-sidebar {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .lesson-list {
            list-style: none;
            padding: 0;
        }
        .lesson-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .lesson-list li:last-child {
            border-bottom: none;
        }
        .course-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .enrollment-box {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }
        .price {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <?php echo studentNavbar('cursos'); ?>
    
    <div class="container">
        <?php if ($curso): ?>
            <div class="course-header">
                <h1><?php echo htmlspecialchars($curso['titulo']); ?></h1>
                <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                <div class="course-stats">
                    <div class="stat-box">
                        <h4>Estudiantes</h4>
                        <p><?php echo $curso['total_inscritos']; ?></p>
                    </div>
                    <div class="stat-box">
                        <h4>Lecciones</h4>
                        <p><?php echo $curso['total_lecciones']; ?></p>
                    </div>
                    <div class="stat-box">
                        <h4>Profesor</h4>
                        <p><?php echo htmlspecialchars($curso['profesor_nombre'] . ' ' . $curso['profesor_apellido']); ?></p>
                    </div>
                </div>
            </div>

            <div class="course-content">
                <div class="course-details">
                    <h2>Contenido del curso</h2>
                    <?php if (!empty($lecciones)): ?>
                        <ul class="lesson-list">
                            <?php foreach ($lecciones as $leccion): ?>
                                <li>
                                    <i class="icon-video"></i>
                                    <?php echo htmlspecialchars($leccion['titulo']); ?>
                                    <?php if ($leccion['duracion_minutos']): ?>
                                        <span class="duration"><?php echo $leccion['duracion_minutos']; ?> min</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay lecciones disponibles.</p>
                    <?php endif; ?>
                </div>

                <div class="course-sidebar">
                    <div class="enrollment-box">
                        <?php if ($inscrito): ?>
                            <p>Ya estás inscrito en este curso</p>
                            <a href="curso.php?curso_id=<?php echo $curso_id; ?>" class="btn btn-primary">Ir al curso</a>
                        <?php else: ?>
                            <?php if (isset($_SESSION['id_usuario'])): ?>
                                <div class="price">
                                    <?php echo $curso['precio'] ? '$' . number_format($curso['precio'], 2) : 'Gratis'; ?>
                                </div>
                                <form action="../backend/inscribir_curso.php" method="post">
                                    <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                                    <button type="submit" class="btn btn-primary">Inscribirme ahora</button>
                                </form>
                            <?php else: ?>
                                <p>Inicia sesión para inscribirte en este curso</p>
                                <a href="alumno_login.php" class="btn btn-primary">Iniciar sesión</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 20px;">
                        <h3>Este curso incluye:</h3>
                        <ul>
                            <li><?php echo $curso['total_lecciones']; ?> lecciones</li>
                            <li>Acceso de por vida</li>
                            <li>Certificado de finalización</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="course-header">
                <h1>Curso no encontrado</h1>
                <p>El curso que buscas no existe o no está disponible.</p>
                <a href="index.php#cursos" class="btn btn-primary">Ver otros cursos</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>