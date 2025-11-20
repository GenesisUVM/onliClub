<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: alumno_login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/student_layout.php';

// Obtener datos completos del usuario
$stmt = $conn->prepare("
    SELECT u.*, 
           COUNT(DISTINCT ic.id_curso) as total_cursos,
           COUNT(DISTINCT cl.id_leccion) as total_lecciones_completadas
    FROM Usuarios u
    LEFT JOIN inscripciones_cursos ic ON u.id_usuario = ic.id_usuario
    LEFT JOIN CompletadoLecciones cl ON u.id_usuario = cl.id_usuario
    WHERE u.id_usuario = ?
    GROUP BY u.id_usuario
");
$stmt->bind_param('i', $_SESSION['id_usuario']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener progreso de cursos
$stmt = $conn->prepare("
    SELECT c.id_curso, c.titulo,
           COUNT(DISTINCT l.id_leccion) as total_lecciones,
           COUNT(DISTINCT cl.id_leccion) as lecciones_completadas
    FROM Cursos c
    JOIN InscripcionesCursos ic ON c.id_curso = ic.id_curso
    LEFT JOIN Lecciones l ON c.id_curso = l.id_curso
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - OnliClub</title>
    <?php echo studentStyles(); ?>
</head>
<body>
    <?php echo studentNavbar('perfil'); ?>
    
    <div class="profile-container">
        <!-- Sidebar con datos del usuario -->
        <aside class="profile-sidebar">
            <div class="profile-image">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>
            
            <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h2>
            <p><?php echo htmlspecialchars($usuario['email']); ?></p>
            
            <div class="profile-stats">
                <p><strong>Cursos inscritos:</strong> <?php echo $usuario['total_cursos']; ?></p>
                <p><strong>Lecciones completadas:</strong> <?php echo $usuario['total_lecciones_completadas']; ?></p>
                <p><strong>Miembro desde:</strong> <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></p>
            </div>
            
            <div class="profile-actions">
                <button class="btn btn-primary" onclick="toggleEditForm()">Editar Perfil</button>
                <a href="../backend/logout.php" class="btn btn-outline">Cerrar Sesión</a>
            </div>
        </aside>
        
        <!-- Contenido principal -->
        <main class="profile-main">
            <h1>Mi Progreso</h1>
            
            <!-- Progreso de cursos -->
            <?php foreach ($cursos_progreso as $curso): ?>
                <div class="course-progress-card">
                    <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                    <?php 
                    $progreso = $curso['total_lecciones'] > 0 
                        ? round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100)
                        : 0;
                    ?>
                    <div class="progress-bar">
                        <div style="width: <?php echo $progreso; ?>%;"></div>
                    </div>
                    <p><?php echo $progreso; ?>% completado</p>
                    <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>" class="btn btn-outline">Continuar</a>
                </div>
            <?php endforeach; ?>
            
            <!-- Búsqueda de cursos -->
            <div class="search-courses">
                <h2>Buscar Nuevos Cursos</h2>
                <input type="text" class="search-input" id="searchCourses" 
                       placeholder="Buscar cursos por título o categoría...">
                <button class="btn btn-primary" onclick="searchCourses()">Buscar</button>
                
                <div id="searchResults">
                    <!-- Los resultados de búsqueda se mostrarán aquí -->
                </div>
            </div>
        </main>
    </div>

    <!-- Modal/Form para editar perfil (inicialmente oculto) -->
    <div id="editProfileForm" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000;">
        <h2>Editar Perfil</h2>
        <form action="../backend/actualizar_perfil.php" method="post">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            </div>
            <div>
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>
            <div>
                <label for="password">Nueva Contraseña (dejar en blanco para mantener la actual):</label>
                <input type="password" id="password" name="password">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" class="btn btn-outline" onclick="toggleEditForm()">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
    function toggleEditForm() {
        const form = document.getElementById('editProfileForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function searchCourses() {
        const query = document.getElementById('searchCourses').value;
        const resultsDiv = document.getElementById('searchResults');
        
        // Hacer la búsqueda mediante AJAX
        fetch('../backend/buscar_cursos.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                resultsDiv.innerHTML = data.map(curso => `
                    <div class="course-progress-card">
                        <h3>${curso.titulo}</h3>
                        <p>${curso.descripcion}</p>
                        <a href="curso.php?curso_id=${curso.id_curso}" class="btn btn-primary">Ver curso</a>
                    </div>
                `).join('');
            })
            .catch(error => {
                resultsDiv.innerHTML = '<p>Error al buscar cursos. Intenta de nuevo.</p>';
            });
    }
    </script>
</body>
</html>