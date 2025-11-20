<?php
// Landing pública
require_once __DIR__ . '/../backend/db.php';

$popularCourses = [];
$categories = [];

$coursesQuery = "SELECT id_curso, titulo, descripcion, '' AS imagen FROM cursos ORDER BY id_curso DESC LIMIT 6";
if ($result = $conn->query($coursesQuery)) {
    while ($row = $result->fetch_assoc()) {
        $popularCourses[] = $row;
    }
    $result->free();
} else {
    // fallback de ejemplo
    $popularCourses = [
        ['id_curso' => 1, 'titulo' => 'Introducción a PHP', 'descripcion' => 'Aprende los fundamentos de PHP y cómo crear aplicaciones web.', 'imagen' => ''],
        ['id_curso' => 2, 'titulo' => 'HTML y CSS desde cero', 'descripcion' => 'Construye páginas web modernas y responsivas.', 'imagen' => ''],
        ['id_curso' => 3, 'titulo' => 'JavaScript para principiantes', 'descripcion' => 'Domina lo esencial de JavaScript y la interacción en el navegador.', 'imagen' => ''],
    ];
}

$catQuery = "SELECT id_categoria, nombre FROM Categorias ORDER BY nombre LIMIT 20";
if ($result = $conn->query($catQuery)) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    $result->free();
} else {
        $categories = [
        ['id_categoria' => 1, 'nombre' => 'Desarrollo Web'],
        ['id_categoria' => 2, 'nombre' => 'Diseño'],
        ['id_categoria' => 3, 'nombre' => 'Marketing'],
    ];
}

$conn->close();

$categoryImages = [
    'PHP' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=300&h=140&fit=crop',
    'HTML' => 'https://images.unsplash.com/photo-1621839673705-6617adf9e890?w=300&h=140&fit=crop',
    'CSS' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=140&fit=crop',
    'JavaScript' => 'https://images.unsplash.com/photo-1579468118864-1b9ea3c0db4a?w=300&h=140&fit=crop',
    'default' => 'https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?w=300&h=140&fit=crop'
];

function getCourseImage($courseTitle, $courseId) {
    global $categoryImages;
    
    foreach ($categoryImages as $key => $image) {
        if (stripos($courseTitle, $key) !== false) {
            return $image . "&random=" . $courseId;
        }
    }
    
    return $categoryImages['default'] . "&random=" . $courseId;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnliClub - Cursos Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">OnliClub</a>
            <a href="#cursos">Cursos</a>
            <a href="#categorias">Categorías</a>
            <a href="#precios">Precios</a>
        </div>
        <div class="nav-right">
            <a href="alumno_login.php" class="btn-ingresar">Ingresar</a>
        </div>
    </nav>

    <div style="position: relative; width: 100%;">
        <section class="hero-section">
            <h1>Aprende nuevas habilidades en línea</h1>
            <p>Explora cursos prácticos creados por profesionales. Mejora tu perfil y avanza en tu carrera con proyectos reales.</p>
            <form class="search-container" action="buscar_cursos.php" method="GET">
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       placeholder='Buscar cursos, p.ej. "Programación en PHP"'>
                <button type="submit" class="search-btn">Buscar</button>
            </form>
            <div class="category-filters">
                <button class="category-filter">Programación</button>
                <button class="category-filter">Diseño</button>
                <button class="category-filter">Negocios</button>
            </div>
            <div class="hero-image">Imagen / Ilustración</div>
        </section>

        <section class="popular-categories" id="categorias">
            <h2>Categorías populares</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <h3>Programación</h3>
                    <p>120 cursos</p>
                </div>
                <div class="category-card">
                    <h3>Diseño</h3>
                    <p>60 cursos</p>
                </div>
                <div class="category-card">
                    <h3>Marketing</h3>
                    <p>45 cursos</p>
                </div>
                <div class="category-card">
                    <h3>Excel</h3>
                    <p>37 cursos</p>
                </div>
            </div>
        </section>

        <section class="featured-courses" id="cursos">
            <h2>Cursos destacados</h2>
            <div class="courses-grid">
                <?php 
                $courseIndex = 0;
                foreach (array_slice($popularCourses, 0, 3) as $c): 
                    $courseIndex++;
                ?>
                    <article class="course-card">
                        <img src="<?php echo getCourseImage($c['titulo'], $c['id_curso']); ?>" 
                             alt="<?php echo htmlspecialchars($c['titulo']); ?>" 
                             class="course-image"
                             loading="lazy">
                        <div class="course-info">
                            <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                            <p class="duration">Duración: <?php echo rand(6, 12); ?> horas</p>
                            <div class="rating">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <a href="ver_curso.php?id=<?php echo $c['id_curso']; ?>" class="btn-inscribirme">Inscribirme</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
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