<?php
session_start();
require_once '../backend/db.php';
require_once 'components/teacher_layout.php';

// Protección de ruta
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: profesor_login.php');
    exit;
}

// Obtener los cursos del profesor
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id_profesor = ?");
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos - Panel Profesor</title>
    <?php echo teacherStyles(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php echo teacherNavbar('cursos'); ?>

    <div class="container">
        <div class="page-header">
            <h1>Mis Cursos</h1>
            <a href="crear_curso.php" class="btn btn-primary">
                <span>+</span> Crear Nuevo Curso
            </a>
        </div>

        <?php if (empty($cursos)): ?>
            <div class="empty-state">
                <div style="font-size: 4em; margin-bottom: 20px;">📝</div>
                <h3>Aún no has creado ningún curso</h3>
                <p>Comienza a compartir tu conocimiento creando tu primer curso hoy mismo.</p>
                <a href="crear_curso.php" class="btn btn-primary">Crear mi primer curso</a>
            </div>
        <?php else: ?>
            <div class="courses-grid">
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card">
                        <!-- Placeholder image if no image exists, or use a default one -->
                        <div style="height: 160px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                            <?php if (!empty($curso['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($curso['imagen']); ?>" alt="Curso" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <span>Sin imagen</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="content">
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 100)) . (strlen($curso['descripcion']) > 100 ? '...' : ''); ?></p>
                            
                            <div class="meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($curso['fecha_creacion'] ?? 'now')); ?></span>
                                <!-- Add more meta info if available like students count -->
                            </div>

                            <div class="actions">
                                <a href="editar_curso.php?id=<?php echo $curso['id_curso']; ?>" class="btn btn-outline">Editar</a>
                                <a href="ver_curso.php?id=<?php echo $curso['id_curso']; ?>" class="btn btn-primary">Ver Curso</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>