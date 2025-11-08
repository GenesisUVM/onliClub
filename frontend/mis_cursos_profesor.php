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
</head>
<body>
    <?php echo teacherNavbar('cursos'); ?>

    <div class="container">
        <div class="create-course">
            <a href="crear_curso.php" class="btn btn-primary">Crear Nuevo Curso</a>
        </div>

        <h1>Mis Cursos</h1>

        <div class="courses-grid">
            <?php if (empty($cursos)): ?>
                <p>Aún no has creado ningún curso.</p>
            <?php else: ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card">
                        <div class="content">
                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 100)) . '...'; ?></p>
                            <div class="actions">
                                <a href="editar_curso.php?id=<?php echo $curso['id_curso']; ?>" class="btn btn-outline">Editar</a>
                                <a href="ver_curso.php?id=<?php echo $curso['id_curso']; ?>" class="btn btn-primary">Ver Curso</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>