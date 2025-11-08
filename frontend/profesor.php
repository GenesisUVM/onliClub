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
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0;
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
        }
        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }
        .welcome-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php echo teacherNavbar('inicio'); ?>

    <div class="container">
        <div class="welcome-section">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
            <p>Desde aquí podrás gestionar tus cursos y ver el progreso de tus estudiantes.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Cursos</h3>
                <div class="number"><?php echo $stats['total_cursos']; ?></div>
                <a href="mis_cursos_profesor.php" class="btn btn-outline">Ver mis cursos</a>
            </div>
            <!-- Aquí puedes agregar más tarjetas de estadísticas según necesites -->
        </div>

        <div class="actions" style="margin-top: 20px;">
            <a href="crear_curso.php" class="btn btn-primary">Crear Nuevo Curso</a>
        </div>
    </div>
</body>
</html>
