<?php
// Bloque de autenticación original
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Administrador') {
    header('Location: admin_login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/components/student_layout.php'; // Debería ser admin_layout.php

// --- Estadísticas Globales ---
// Total de usuarios
$total_alumnos = $conn->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'Alumno'")->fetch_assoc()['total'];
$total_profesores = $conn->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'Profesor'")->fetch_assoc()['total'];

// Total de cursos
$total_cursos = $conn->query("SELECT COUNT(*) as total FROM Cursos")->fetch_assoc()['total'];

// Total de inscripciones
$total_inscripciones = $conn->query("SELECT COUNT(*) as total FROM InscripcionesCursos")->fetch_assoc()['total'];

// --- Actividad Reciente (Ejemplos) ---
// Últimos usuarios registrados
$usuarios_recientes = $conn->query("SELECT nombre, rol, fecha_registro FROM Usuarios ORDER BY fecha_registro DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Últimos cursos creados
$cursos_recientes = $conn->query("SELECT titulo, fecha_creacion FROM Cursos ORDER BY fecha_creacion DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$conn->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - OnliClub</title>
    <?php // Se asume que student_layout.php define studentStyles(), aunque debería ser admin_layout.php ?>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            gap: 35px;
            width: 80%;
            margin:auto;
        }

        .welcome-section {
            background: linear-gradient(135deg, #4A5568 0%, #00A3BF 100%);
            color: white;
            padding: 35px;
            border-radius: 12px;
            text-align: center;
        }
        .welcome-icon {
            font-size: 3.5em;
            margin-bottom: 15px;
        }
        .welcome-section h1 {
            margin: 0;
            font-size: 2.2em;
        }
        .welcome-section p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .stats-grid, .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .stat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-left: 5px solid #1A4D63;
        }
        .stat-number {
            font-size: 2.8em;
            font-weight: bold;
            color: #1A4D63;
        }
        .stat-label {
            color: #555;
            font-size: 1em;
            margin-top: 5px;
        }

        .action-btn {
            background: white;
            border: 2px solid #4A5568;
            color: #1A4D63;
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            font-size: 1.1em;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .action-btn:hover {
            background: #00A3BF;
            color: white;
        }

        .activity-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        .activity-list {
            list-style: none;
            padding: 0;
        }
        .activity-list li {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-list .date {
            font-size: 0.9em;
            color: #718096;
        }
    </style>
</head>
<body>
    <?php // Se asume que student_layout.php define studentNavbar(), aunque debería ser admin_layout.php ?>
    
    <div class="container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <div class="welcome-icon">⚙️</div>
            <h1>Panel de Administración</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>. Gestiona la plataforma desde aquí.</p>
        </section>

        <!-- Estadísticas Globales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_alumnos ?? 0; ?></div>
                <div class="stat-label">Alumnos Registrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_profesores ?? 0; ?></div>
                <div class="stat-label">Profesores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_cursos ?? 0; ?></div>
                <div class="stat-label">Cursos Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_inscripciones ?? 0; ?></div>
                <div class="stat-label">Inscripciones</div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="quick-actions">
            <a href="#" class="action-btn">👥 Gestionar Usuarios</a>
            <a href="#" class="action-btn">📚 Gestionar Cursos</a>
            <a href="#" class="action-btn">📊 Ver Reportes</a>
            <a href="../backend/logout.php" class="action-btn">🚪 Cerrar Sesión</a>
        </div>

        <!-- Actividad Reciente -->
        <section>
            <h2>Actividad Reciente en la Plataforma</h2>
            <div class="activity-section">
                <div class="card">
                    <h3>Últimos Usuarios Registrados</h3>
                    <ul class="activity-list">
                        <?php if (!empty($usuarios_recientes)): ?>
                            <?php foreach ($usuarios_recientes as $usuario): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($usuario['nombre']); ?> (<?php echo htmlspecialchars($usuario['rol']); ?>)</span>
                                    <span class="date"><?php echo date("d/m/Y", strtotime($usuario['fecha_registro'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No hay registros de usuarios recientes.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="card">
                    <h3>Últimos Cursos Creados</h3>
                    <ul class="activity-list">
                        <?php if (!empty($cursos_recientes)): ?>
                            <?php foreach ($cursos_recientes as $curso): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($curso['titulo']); ?></span>
                                    <span class="date"><?php echo date("d/m/Y", strtotime($curso['fecha_creacion'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No se han creado cursos recientemente.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</body>
</html>