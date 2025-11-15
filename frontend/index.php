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

// Array de imágenes de placeholder por categoría
$categoryImages = [
    'PHP' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=300&h=140&fit=crop',
    'HTML' => 'https://images.unsplash.com/photo-1621839673705-6617adf9e890?w=300&h=140&fit=crop',
    'CSS' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=140&fit=crop',
    'JavaScript' => 'https://images.unsplash.com/photo-1579468118864-1b9ea3c0db4a?w=300&h=140&fit=crop',
    'default' => 'https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?w=300&h=140&fit=crop'
];

// Función para obtener imagen según el título del curso
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
    <link rel="stylesheet" href="css/app.css">
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .course-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
        }
        .hero {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #1A4D63 0%, #00A3BF 100%);
            color: white;
            border-radius: 8px;
            margin: 20px 0;
        }
        .hero h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }
    </style>
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
            <a href="#cursos" class="btn btn-primary" style="font-size: 1.1em; padding: 12px 30px;">Explorar Cursos</a>
        </section>

        <section id="cursos">
            <h2>Cursos populares</h2>
            <div class="grid">
                <?php foreach ($popularCourses as $c): ?>
                    <article class="card">
                        <img src="<?php echo getCourseImage($c['titulo'], $c['id_curso']); ?>" 
                             alt="<?php echo htmlspecialchars($c['titulo']); ?>" 
                             class="course-image"
                             loading="lazy">
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