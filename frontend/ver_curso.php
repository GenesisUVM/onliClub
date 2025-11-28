<?php
session_start();
require_once __DIR__ . '/../backend/db.php';

$curso_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

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
    LEFT JOIN Modulos m ON c.id_curso = m.id_curso
    LEFT JOIN Lecciones l ON m.id_modulo = l.id_modulo
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
        SELECT l.id_leccion, l.titulo, m.titulo as modulo_titulo
        FROM Lecciones l
        JOIN Modulos m ON l.id_modulo = m.id_modulo
        WHERE m.id_curso = ?
        ORDER BY m.orden, l.orden
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .course-header {
            background: #FFFFFF;
            padding: 40px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            color: #000000;
            margin-bottom: 16px;
        }

        .course-header p {
            font-family: 'Inter', sans-serif;
            color: #6B7280;
            font-size: 16px;
            line-height: 1.6;
        }

        .course-stats {
            display: flex;
            gap: 30px;
            margin-top: 30px;
        }

        .stat-box {
            flex: 1;
            text-align: center;
            padding: 20px;
            background: #F9FAFB;
            border-radius: 8px;
        }

        .stat-box h4 {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .stat-box p {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            color: #00A3BF;
            font-weight: bold;
        }

        .course-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .course-details {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-details h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            color: #000000;
            margin-bottom: 20px;
        }

        .lesson-list {
            list-style: none;
            padding: 0;
        }

        .lesson-list li {
            padding: 15px;
            border-bottom: 1px solid #E5E7EB;
            font-family: 'Inter', sans-serif;
            color: #374151;
        }

        .lesson-list li:last-child {
            border-bottom: none;
        }

        .course-sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .enrollment-box {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .enrollment-box p {
            font-family: 'Inter', sans-serif;
            color: #6B7280;
            margin-bottom: 20px;
        }

        .price {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            color: #00A3BF;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary {
            background: #00A3BF;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #008BA3;
        }

        .btn-secondary {
            background: #6B7280;
            color: #FFFFFF;
        }

        .btn-secondary:hover {
            background: #4B5563;
        }

        @media (max-width: 768px) {
            .course-content {
                grid-template-columns: 1fr;
            }

            .course-stats {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">OnliClub</a>
            <a href="index.php#cursos">Cursos</a>
            <a href="index.php#categorias">Categorías</a>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></span>
                <a href="../backend/logout.php" class="logout">Cerrar sesión</a>
            <?php else: ?>
                <a href="alumno_login.php" class="btn-ingresar">Ingresar</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['inscription_success'])): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                ✅ <?php echo $_SESSION['inscription_success']; unset($_SESSION['inscription_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['inscription_error'])): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                ⚠️ <?php echo $_SESSION['inscription_error']; unset($_SESSION['inscription_error']); ?>
            </div>
        <?php endif; ?>

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
                        <p><?php echo htmlspecialchars($curso['profesor_nombre'] . ' ' . $curso['profesor_apellido']); ?>
                        </p>
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
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay lecciones disponibles.</p>
                    <?php endif; ?>
                </div>

                <div class="course-sidebar">
                    <div class="enrollment-box">
                        <?php if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $curso['id_profesor']): ?>
                            <p>Eres el instructor de este curso</p>
                            <a href="editar_curso.php?id=<?php echo $curso_id; ?>" class="btn btn-secondary"
                                style="margin-bottom: 10px;">Editar Curso</a>
                            <a href="curso.php?curso_id=<?php echo $curso_id; ?>" class="btn btn-primary">Ver como
                                estudiante</a>
                        <?php elseif ($inscrito): ?>
                            <p>Ya estás inscrito en este curso</p>
                            <a href="curso.php?curso_id=<?php echo $curso_id; ?>" class="btn btn-primary">Ir al curso</a>
                        <?php else: ?>
                            <?php if (isset($_SESSION['id_usuario'])): ?>
                                <div class="price">
                                    <?php echo $curso['precio'] > 0 ? '$' . number_format($curso['precio'], 2) : 'Gratis'; ?>
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

                    <div style="margin-top: 20px; background: #FFFFFF; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <h3 style="font-family: 'Poppins', sans-serif; font-size: 18px; margin-bottom: 15px;">Este curso incluye:</h3>
                        <ul style="list-style: none; padding: 0; font-family: 'Inter', sans-serif; color: #374151;">
                            <li style="padding: 8px 0;">✓ <?php echo $curso['total_lecciones']; ?> lecciones</li>
                            <li style="padding: 8px 0;">✓ Acceso de por vida</li>
                            <li style="padding: 8px 0;">✓ Certificado de finalización</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="course-header">
                <h1>Curso no encontrado</h1>
                <p>El curso que buscas no existe o no está disponible.</p>
                <a href="index.php#cursos" class="btn btn-primary" style="display: inline-block; width: auto; margin-top: 20px;">Ver otros cursos</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-left">
            <div class="logo">OnliClub</div>
            <p>Plataforma de cursos en línea</p>
        </div>
        <div class="footer-center">
            <a href="#ayuda">Ayuda</a>
            <a href="#terminos">Términos Privacidad</a>
        </div>
        <div class="footer-right">
            © <?php echo date('Y'); ?> OnliClub
        </div>
    </footer>
</body>

</html>