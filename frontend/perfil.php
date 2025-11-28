<?php
session_start();
require_once __DIR__ . '/../backend/db.php';

// Determinar el rol y cargar el layout correspondiente
$rol = $_SESSION['rol'] ?? '';
if ($rol === 'Profesor') {
    require_once __DIR__ . '/components/teacher_layout.php';
    $navbar = 'teacherNavbar';
    $styles = 'teacherStyles';
} elseif ($rol === 'Alumno') {
    require_once __DIR__ . '/components/student_layout.php';
    $navbar = 'studentNavbar';
    $styles = 'studentStyles';
} else {
    header('Location: login_view.php');
    exit;
}

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT * FROM Usuarios WHERE id_usuario = ?");
$stmt->bind_param('i', $_SESSION['id_usuario']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Lógica específica por rol
$stats = [];
$cursos_progreso = [];

if ($rol === 'Profesor') {
    // Estadísticas para profesor
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM Cursos WHERE id_profesor = ?");
    $stmt->bind_param('i', $_SESSION['id_usuario']);
    $stmt->execute();
    $stats['Cursos Creados'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
} else {
    // Estadísticas y progreso para alumno
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT ic.id_curso) as total_cursos,
               COUNT(DISTINCT cl.id_leccion) as total_lecciones_completadas
        FROM InscripcionesCursos ic
        LEFT JOIN CompletadoLecciones cl ON ic.id_usuario = cl.id_usuario
        WHERE ic.id_usuario = ?
    ");
    $stmt->bind_param('i', $_SESSION['id_usuario']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stats['Cursos Inscritos'] = $res['total_cursos'];
    $stats['Lecciones Completadas'] = $res['total_lecciones_completadas'];
    $stmt->close();

    // Obtener progreso de cursos (solo para alumnos)
    $stmt = $conn->prepare("
        SELECT c.id_curso, c.titulo,
               COUNT(DISTINCT l.id_leccion) as total_lecciones,
               COUNT(DISTINCT cl.id_leccion) as lecciones_completadas
        FROM Cursos c
        JOIN InscripcionesCursos ic ON c.id_curso = ic.id_curso
        LEFT JOIN Modulos m ON c.id_curso = m.id_curso
        LEFT JOIN Lecciones l ON m.id_modulo = l.id_modulo
        LEFT JOIN CompletadoLecciones cl ON l.id_leccion = cl.id_leccion AND cl.id_usuario = ?
        WHERE ic.id_usuario = ?
        GROUP BY c.id_curso
        ORDER BY lecciones_completadas DESC
        LIMIT 5
    ");
    $stmt->bind_param('ii', $_SESSION['id_usuario'], $_SESSION['id_usuario']);
    $stmt->execute();
    $cursos_progreso = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - OnliClub</title>
    <link rel="stylesheet" href="css/app.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php $navbar(); ?>

    <div class="profile-container">
        <!-- Sidebar -->
        <aside class="profile-sidebar">
            <div class="profile-image">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>

            <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h2>
            <p><?php echo htmlspecialchars($usuario['email']); ?></p>
            <span
                style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600;">
                <?php echo $rol; ?>
            </span>

            <div class="profile-stats-list">
                <?php foreach ($stats as $label => $value): ?>
                    <div class="profile-stat-item">
                        <strong><?php echo $label; ?>:</strong>
                        <span><?php echo $value; ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="profile-stat-item">
                    <strong>Miembro desde:</strong>
                    <span><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn btn-primary" onclick="toggleEditForm()" style="width: 100%; margin-bottom: 10px;">Editar
                    Perfil</button>
                <a href="../backend/logout.php" class="btn btn-outline"
                    style="width: 100%; display: block; text-align: center;">Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="profile-main">
            <?php if (isset($_SESSION['profile_success'])): ?>
                <div
                    style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                    ✅ <?php echo $_SESSION['profile_success'];
                        unset($_SESSION['profile_success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['profile_error'])): ?>
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                    ⚠️ <?php echo $_SESSION['profile_error'];
                        unset($_SESSION['profile_error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($rol === 'Alumno'): ?>
                <h1>Mi Progreso</h1>
                <?php if (empty($cursos_progreso)): ?>
                    <div
                        style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px; border: 2px dashed #e1e5e9;">
                        <h3>Aún no has iniciado ningún curso</h3>
                        <p>Explora nuestro catálogo y comienza a aprender hoy mismo.</p>
                        <a href="index.php" class="btn btn-primary">Explorar Cursos</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cursos_progreso as $curso): ?>
                        <div class="course-progress-card">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0;"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                <span style="font-size: 0.9em; color: #64748b;">
                                    <?php echo $curso['lecciones_completadas']; ?>/<?php echo $curso['total_lecciones']; ?> lecciones
                                </span>
                            </div>
                            <?php
                            $progreso = $curso['total_lecciones'] > 0
                                ? round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100)
                                : 0;
                            ?>
                            <div class="progress-bar">
                                <div style="width: <?php echo $progreso; ?>%;"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.9em; font-weight: 600; color: #00A3BF;"><?php echo $progreso; ?>%
                                    completado</span>
                                <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>" class="btn btn-outline"
                                    style="padding: 6px 12px; font-size: 0.9em;">Continuar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <!-- Contenido específico para Profesor -->
                <h1>Panel de Control</h1>
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="dashboard-icon">📚</div>
                        <h3 class="dashboard-title">MIS CURSOS</h3>
                        <p class="dashboard-value">
                            <?php echo $stats['Cursos Creados']; ?>
                        </p>
                        <a href="mis_cursos_profesor.php" class="dashboard-link">Gestionar →</a>
                    </div>
                    <div class="dashboard-card">
                        <div class="dashboard-icon">✍️</div>
                        <h3 class="dashboard-title">CREAR</h3>
                        <p class="dashboard-value">Nuevo Curso</p>
                        <a href="crear_curso.php" class="dashboard-link">Comenzar →</a>
                    </div>
                </div>

                <div class="welcome-box">
                    <h3>👋 ¡Hola Profesor!</h3>
                    <p>Mantén tu perfil actualizado para que tus estudiantes puedan conocerte mejor. Una buena descripción y
                        foto de perfil generan más confianza.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal para editar perfil -->
    <div id="editProfileForm" class="modal-overlay">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-family: 'Poppins', sans-serif;">Editar Perfil</h2>
                <button onclick="toggleEditForm()"
                    style="background: none; border: none; font-size: 1.5em; cursor: pointer; color: #64748b;">&times;</button>
            </div>

            <form action="../backend/actualizar_perfil.php" method="post">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido"
                        value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Nueva Contraseña <span
                            style="font-weight: normal; color: #64748b; font-size: 0.9em;">(opcional)</span></label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para mantener actual">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="button" class="btn btn-outline" onclick="toggleEditForm()"
                        style="flex: 1;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleEditForm() {
            const modal = document.getElementById('editProfileForm');
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            } else {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function (event) {
            const modal = document.getElementById('editProfileForm');
            if (event.target == modal) {
                toggleEditForm();
            }
        }
    </script>
</body>

</html>