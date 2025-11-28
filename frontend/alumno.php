<?php
// Bloque de autenticación original
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
    header('Location: alumno_login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/db_helper.php';
require_once __DIR__ . '/components/student_layout.php';

$db = new DBHelper($conn);
$stats = $db->getStudentStats($_SESSION['id_usuario']);
$cursos_recientes = $db->getRecentCourses($_SESSION['id_usuario']);

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Alumno - OnliClub</title>
    <?php echo studentStyles(); ?>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php echo studentNavbar('inicio'); ?>

    <div class="container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section"
            style="background: linear-gradient(135deg, #1A4D63 0%, #00A3BF 100%); padding: 40px; border-radius: 20px; color: white; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0, 163, 191, 0.2);">
            <div style="font-size: 3em; margin-bottom: 10px;">🎓</div>
            <h1 style="margin: 0; font-family: 'Poppins', sans-serif; font-size: 2.5em;">¡Bienvenido de vuelta,
                <?php echo htmlspecialchars($_SESSION['nombre']); ?>!
            </h1>
            <p style="font-size: 1.1em; opacity: 0.9; margin-top: 10px;">Continúa tu journey de aprendizaje y alcanza
                tus metas</p>
        </section>

        <!-- Estadísticas rápidas -->
        <div class="stats-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">
            <div class="stat-card"
                style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5e9; text-align: center;">
                <div style="font-size: 3em; font-weight: 700; color: #1A4D63; margin-bottom: 5px;">
                    <?php echo $stats['total_cursos'] ?? 0; ?>
                </div>
                <div style="color: #64748b; font-weight: 500;">Cursos Inscritos</div>
            </div>
            <div class="stat-card"
                style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5e9; text-align: center;">
                <div style="font-size: 3em; font-weight: 700; color: #1A4D63; margin-bottom: 5px;">
                    <?php echo $stats['lecciones_completadas'] ?? 0; ?>
                </div>
                <div style="color: #64748b; font-weight: 500;">Lecciones Completadas</div>
            </div>
            <div class="stat-card"
                style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5e9; text-align: center;">
                <div style="font-size: 3em; font-weight: 700; color: #00A3BF; margin-bottom: 5px;">
                    <?php
                    $progreso = ($stats['total_lecciones'] > 0) ?
                        round(($stats['lecciones_completadas'] / $stats['total_lecciones']) * 100) : 0;
                    echo $progreso . '%';
                    ?>
                </div>
                <div style="color: #64748b; font-weight: 500;">Progreso General</div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="quick-actions"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 40px;">
            <a href="mis_cursos.php" class="btn btn-outline"
                style="justify-content: center; padding: 15px; font-size: 1em;">
                📚 Mis Cursos
            </a>
            <a href="perfil.php" class="btn btn-outline"
                style="justify-content: center; padding: 15px; font-size: 1em;">
                👤 Mi Perfil
            </a>
            <a href="../frontend/index.php#cursos" class="btn btn-outline"
                style="justify-content: center; padding: 15px; font-size: 1em;">
                🔍 Explorar Cursos
            </a>
        </div>

        <!-- Cursos recientes -->
        <section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin: 0; color: #1e293b; font-family: 'Poppins', sans-serif;">Tus Cursos Recientes</h2>
                <a href="mis_cursos.php" style="color: #00A3BF; text-decoration: none; font-weight: 500;">Ver todos
                    →</a>
            </div>

            <?php if (!empty($cursos_recientes)): ?>
                <div class="courses-grid">
                    <?php foreach ($cursos_recientes as $curso): ?>
                        <article class="course-card">
                            <div class="content">
                                <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 100)); ?>...</p>

                                <?php
                                $progreso_curso = ($curso['total_lecciones'] > 0) ?
                                    round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100) : 0;
                                ?>

                                <div class="progress-container">
                                    <div class="progress-bar">
                                        <div style="width: <?php echo $progreso_curso; ?>%;"></div>
                                    </div>
                                    <span class="progress-text"><?php echo $progreso_curso; ?>% completado</span>
                                </div>

                                <div class="actions">
                                    <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>" class="btn btn-primary"
                                        style="width: 100%;">
                                        <?php echo $progreso_curso > 0 ? 'Continuar' : 'Comenzar'; ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>📝 Aún no estás inscrito en ningún curso</h3>
                    <p>Explora nuestro catálogo y encuentra el curso perfecto para ti</p>
                    <a href="../frontend/index.php#cursos" class="btn btn-primary">Explorar Cursos</a>
                </div>
            <?php endif; ?>
            <?php
            // Bloque de autenticación original
            session_start();
            if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Alumno') {
                header('Location: alumno_login.php');
                exit;
            }

            require_once __DIR__ . '/../backend/db.php';
            require_once __DIR__ . '/../backend/db_helper.php';
            require_once __DIR__ . '/components/student_layout.php';

            $db = new DBHelper($conn);
            $stats = $db->getStudentStats($_SESSION['id_usuario']);
            $cursos_recientes = $db->getRecentCourses($_SESSION['id_usuario']);

            $conn->close();
            ?>
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Panel Alumno - OnliClub</title>
                <?php echo studentStyles(); ?>
                <link
                    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap"
                    rel="stylesheet">
            </head>

            <body>
                <?php echo studentNavbar('inicio'); ?>

                <div class="container">
                    <!-- Sección de bienvenida -->
                    <section class="welcome-section"
                        style="background: linear-gradient(135deg, #1A4D63 0%, #00A3BF 100%); padding: 40px; border-radius: 20px; color: white; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0, 163, 191, 0.2);">
                        <div style="font-size: 3em; margin-bottom: 10px;">🎓</div>
                        <h1 style="margin: 0; font-family: 'Poppins', sans-serif; font-size: 2.5em;">¡Bienvenido de
                            vuelta,
                            <?php echo htmlspecialchars($_SESSION['nombre']); ?>!
                        </h1>
                        <p style="font-size: 1.1em; opacity: 0.9; margin-top: 10px;">Continúa tu journey de aprendizaje
                            y alcanza
                            tus metas</p>
                    </section>

                    <!-- Estadísticas rápidas -->
                    <div class="stats-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">
                        <div class="stat-card"
                            style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5e9; text-align: center;">
                            <div style="font-size: 3em; font-weight: 700; color: #1A4D63; margin-bottom: 5px;">
                                <?php echo $stats['total_cursos'] ?? 0; ?>
                            </div>
                            <div style="color: #64748b; font-weight: 500;">Cursos Inscritos</div>
                        </div>
                        <div class="stat-card"
                            style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5e9; text-align: center;">
                            <div style="font-size: 3em; font-weight: 700; color: #1A4D63; margin-bottom: 5px;">
                                <?php echo $stats['lecciones_completadas'] ?? 0; ?>
                            </div>
                            <div style="color: #64748b; font-weight: 500;">Lecciones Completadas</div>
                        </div>
                        <div class="stat-card"
                            style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5e9; text-align: center;">
                            <div style="font-size: 3em; font-weight: 700; color: #00A3BF; margin-bottom: 5px;">
                                <?php
                                $progreso = ($stats['total_lecciones'] > 0) ?
                                    round(($stats['lecciones_completadas'] / $stats['total_lecciones']) * 100) : 0;
                                echo $progreso . '%';
                                ?>
                            </div>
                            <div style="color: #64748b; font-weight: 500;">Progreso General</div>
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="quick-actions"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 40px;">
                        <a href="mis_cursos.php" class="btn btn-outline"
                            style="justify-content: center; padding: 15px; font-size: 1em;">
                            📚 Mis Cursos
                        </a>
                        <a href="perfil.php" class="btn btn-outline"
                            style="justify-content: center; padding: 15px; font-size: 1em;">
                            👤 Mi Perfil
                        </a>
                        <a href="../frontend/index.php#cursos" class="btn btn-outline"
                            style="justify-content: center; padding: 15px; font-size: 1em;">
                            🔍 Explorar Cursos
                        </a>
                    </div>

                    <!-- Cursos recientes -->
                    <section>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                            <h2 style="margin: 0; color: #1e293b; font-family: 'Poppins', sans-serif;">Tus Cursos
                                Recientes</h2>
                            <a href="mis_cursos.php"
                                style="color: #00A3BF; text-decoration: none; font-weight: 500;">Ver todos
                                →</a>
                        </div>

                        <?php if (!empty($cursos_recientes)): ?>
                            <div class="courses-grid">
                                <?php foreach ($cursos_recientes as $curso): ?>
                                    <article class="course-card">
                                        <div class="content">
                                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                            <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 100)); ?>...</p>

                                            <?php
                                            $progreso_curso = ($curso['total_lecciones'] > 0) ?
                                                round(($curso['lecciones_completadas'] / $curso['total_lecciones']) * 100) : 0;
                                            ?>

                                            <div class="progress-container">
                                                <div class="progress-bar">
                                                    <div style="width: <?php echo $progreso_curso; ?>%;"></div>
                                                </div>
                                                <span class="progress-text"><?php echo $progreso_curso; ?>% completado</span>
                                            </div>

                                            <div class="actions">
                                                <a href="curso.php?curso_id=<?php echo $curso['id_curso']; ?>"
                                                    class="btn btn-primary" style="width: 100%;">
                                                    <?php echo $progreso_curso > 0 ? 'Continuar' : 'Comenzar'; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <h3>📝 Aún no estás inscrito en ningún curso</h3>
                                <p>Explora nuestro catálogo y encuentra el curso perfecto para ti</p>
                                <a href="../frontend/index.php#cursos" class="btn btn-primary">Explorar Cursos</a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- Próximas metas -->
                    <section style="margin-top: 50px; margin-bottom: 50px;">
                        <h2 style="margin-bottom: 25px; color: #1e293b; font-family: 'Poppins', sans-serif;">🎯 Tus
                            Próximas Metas
                        </h2>
                        <div class="goals-grid">
                            <div class="goal-card featured">
                                <div>
                                    <h4>Completa tu primer curso</h4>
                                    <p>Termina al menos un curso al 100% para obtener tu certificado y demostrar tus
                                        habilidades.</p>
                                </div>
                                <div class="goal-icon">🏆</div>
                            </div>
                            <div class="goal-card">
                                <div class="goal-icon">🔥</div>
                                <h4>5 lecciones esta semana</h4>
                                <p>Mantén tu ritmo de aprendizaje constante.</p>
                            </div>
                            <div class="goal-card">
                                <div class="goal-icon">🧭</div>
                                <h4>Explora 3 categorías</h4>
                                <p>Amplía tus conocimientos explorando nuevas áreas.</p>
                            </div>
                        </div>
                    </section>
                </div>
            </body>

            </html>