<?php
session_start();
require_once '../backend/db.php';
require_once 'components/teacher_layout.php';

// Protección de ruta
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: profesor_login.php');
    exit;
}

// Obtener estadísticas básicas
$stmt = $conn->prepare("SELECT COUNT(*) as total_cursos FROM cursos WHERE id_profesor = ?");
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Profesor - OnliClub</title>
    <?php echo teacherStyles(); ?>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php echo teacherNavbar('inicio'); ?>

    <div class="container">
        <div class="welcome-section">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
            <p>Desde aquí podrás gestionar tus cursos, ver el progreso de tus estudiantes y administrar tu contenido
                educativo.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📚</div>
                <h3>Total de Cursos</h3>
                <div class="number"><?php echo $stats['total_cursos']; ?></div>
                <a href="mis_cursos_profesor.php" class="btn-link">Ver mis cursos →</a>
            </div>

            <div class="stat-card">
                <div class="icon">👥</div>
                <h3>Estudiantes Activos</h3>
                <div class="number">0</div> <!-- Placeholder for now -->
                <a href="#" class="btn-link">Ver estudiantes →</a>
            </div>

            <div class="stat-card">
                <div class="icon">⭐</div>
                <h3>Valoración Media</h3>
                <div class="number">5.0</div> <!-- Placeholder for now -->
                <a href="#" class="btn-link">Ver reseñas →</a>
            </div>
        </div>

        <div class="actions-bar">
            <a href="crear_curso.php" class="btn btn-primary">
                <span>+</span> Crear Nuevo Curso
            </a>
        </div>
    </div>
</body>

</html>