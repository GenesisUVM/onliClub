<?php
// Landing pública - página principal sin verificación
// Intenta leer cursos populares y categorías desde la base de datos; si falla, muestra ejemplos.
require_once __DIR__ . '/../backend/db.php';

$popularCourses = [];
$categories = [];

// Intentar leer cursos populares
$coursesQuery = "SELECT id_curso, titulo, descripcion, IFNULL(imagen, '') AS imagen, IFNULL(popular_score, 0) AS score FROM Cursos ORDER BY score DESC LIMIT 6";
if ($result = $conn->query($coursesQuery)) {
    while ($row = $result->fetch_assoc()) {
        $popularCourses[] = $row;
    }
    $result->free();
} else {
    // fallback de ejemplo
    $popularCourses = [
        ['id_curso' => 1, 'titulo' => 'Introducción a PHP', 'descripcion' => 'Aprende los fundamentos de PHP y cómo crear aplicaciones web.', 'imagen' => '', 'score' => 100],
        ['id_curso' => 2, 'titulo' => 'HTML y CSS desde cero', 'descripcion' => 'Construye páginas web modernas y responsivas.', 'imagen' => '', 'score' => 90],
        ['id_curso' => 3, 'titulo' => 'JavaScript para principiantes', 'descripcion' => 'Domina lo esencial de JavaScript y la interacción en el navegador.', 'imagen' => '', 'score' => 85],
    ];
}

// Intentar leer categorías
$catQuery = "SELECT id_categoria, nombre FROM Categorias ORDER BY nombre LIMIT 20";
if ($result = $conn->query($catQuery)) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    $result->free();
} else {
    // fallback de ejemplo
    $categories = [
        ['id_categoria' => 1, 'nombre' => 'Desarrollo Web'],
        ['id_categoria' => 2, 'nombre' => 'Diseño'],
        ['id_categoria' => 3, 'nombre' => 'Marketing'],
    ];
}

// Cerrar conexión (opcional)
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnliClub - Cursos Online</title>
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">OnliClub</a>
            <a href="#cursos">Cursos</a>
            <a href="#precios">Precios</a>
            <a href="#categorias">Categorías</a>
        </div>
        <div class="nav-right">
            <a href="alumno_login.php">Login Alumno</a>
            <a href="profesor_login.php">Login Profesor</a>
        </div>
    </nav>

    <div class="container">
        <section class="hero">
            <h1>Aprende nuevas habilidades online</h1>
            <p>Elige entre cientos de cursos impartidos por profesionales. Empieza hoy y mejora tu carrera.</p>
        </section>

        <section id="cursos">
            <h2>Cursos populares</h2>
            <div class="grid">
                <?php foreach ($popularCourses as $c): ?>
                    <article class="card">
                        <?php if (!empty($c['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($c['imagen']); ?>" alt="<?php echo htmlspecialchars($c['titulo']); ?>">
                        <?php else: ?>
                            <div style="width:100%;height:140px;background:linear-gradient(90deg,#e2e2e2,#f5f5f5);display:flex;align-items:center;justify-content:center;border-radius:6px;color:#888;">Imagen</div>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($c['descripcion'], 0, 120)); ?><?php echo strlen($c['descripcion'])>120? '...':''; ?></p>
                        <p><a href="ver_curso.php?id=<?php echo $c['id_curso']; ?>" class="btn btn-primary">Ver curso</a></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="categorias" style="margin-top:24px;">
            <h2>Categorías</h2>
            <div class="categories">
                <?php foreach ($categories as $cat): ?>
                    <div class="cat"><?php echo htmlspecialchars($cat['nombre']); ?></div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="precios" style="margin-top:24px;">
            <h2>Precios</h2>
            <div class="grid">
                <div class="card">
                    <h3>Plan Básico</h3>
                    <p>Acceso limitado a cursos gratuitos y algunos cursos pagos.</p>
                    <p><strong>$0 / mes</strong></p>
                </div>
                <div class="card">
                    <h3>Plan Pro</h3>
                    <p>Acceso ilimitado a todos los cursos y certificaciones.</p>
                    <p><strong>$9.99 / mes</strong></p>
                </div>
                <div class="card">
                    <h3>Plan Empresa</h3>
                    <p>Planes para equipos con administración centralizada.</p>
                    <p><strong>Contactar</strong></p>
                </div>
            </div>
        </section>

    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> OnliClub — Plataforma de cursos online
    </footer>
</body>
</html>