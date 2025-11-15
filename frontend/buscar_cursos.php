<?php
require_once __DIR__ . '/../backend/db.php';

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchResults = [];

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

if (!empty($searchQuery)) {
    $sql = "SELECT id_curso, titulo, descripcion, IFNULL(imagen, '') AS imagen 
            FROM Cursos 
            WHERE titulo LIKE ? OR descripcion LIKE ?
            ORDER BY titulo ASC
            LIMIT 20";
    
    if ($stmt = $conn->prepare($sql)) {
        $searchTerm = "%$searchQuery%";
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
        
        $stmt->close();
    }
} else {
       $sql = "SELECT id_curso, titulo, descripcion, IFNULL(imagen, '') AS imagen, IFNULL(popular_score, 0) AS score 
            FROM Cursos 
            ORDER BY score DESC 
            LIMIT 12";
    
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
        $result->free();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($searchQuery) ? 'Resultados de búsqueda: ' . htmlspecialchars($searchQuery) : 'Explorar Cursos'; ?> - OnliClub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">OnliClub</a>
            <a href="index.php#cursos">Cursos</a>
            <a href="index.php#categorias">Categorías</a>
            <a href="index.php#precios">Precios</a>
        </div>
        <div class="nav-right">
            <a href="alumno_login.php" class="btn-ingresar">Ingresar</a>
        </div>
    </nav>

    <div style="position: relative; width: 100%;">
        <section class="hero-section">
            <h1><?php echo !empty($searchQuery) ? 'Resultados de búsqueda' : 'Explorar Cursos'; ?></h1>
            <p><?php echo !empty($searchQuery) ? 'Cursos encontrados para: "' . htmlspecialchars($searchQuery) . '"' : 'Descubre todos nuestros cursos disponibles'; ?></p>
            <form class="search-container" action="buscar_cursos.php" method="GET">
                <input type="text" 
                       name="q" 
                       class="search-input" 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       placeholder='Buscar cursos, p.ej. "Programación en PHP"'>
                <button type="submit" class="search-btn">Buscar</button>
            </form>
        </section>

        <section class="featured-courses" id="cursos">
            <h2><?php echo !empty($searchQuery) ? 'Resultados encontrados' : 'Todos los cursos'; ?> (<?php echo count($searchResults); ?>)</h2>
            
            <?php if (!empty($searchResults)): ?>
                <div class="courses-grid">
                    <?php foreach ($searchResults as $c): ?>
                        <article class="course-card">
                            <img src="<?php echo getCourseImage($c['titulo'], $c['id_curso']); ?>" 
                                 alt="<?php echo htmlspecialchars($c['titulo']); ?>" 
                                 class="course-image"
                                 loading="lazy">
                            <div class="course-info">
                                <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                                <p class="duration"><?php echo htmlspecialchars(substr($c['descripcion'], 0, 100)); ?><?php echo strlen($c['descripcion']) > 100 ? '...' : ''; ?></p>
                                <div class="rating">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <a href="ver_curso.php?id=<?php echo $c['id_curso']; ?>" class="btn-inscribirme">Ver curso</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px;">
                    <h3 style="font-family: 'Poppins', sans-serif; color: #000000; margin-bottom: 16px;">No se encontraron cursos</h3>
                    <p style="font-family: 'Inter', sans-serif; color: #6B7280; margin-bottom: 24px;">
                        <?php echo !empty($searchQuery) ? 'Intenta con otros términos de búsqueda' : 'No hay cursos disponibles en este momento'; ?>
                    </p>
                    <a href="index.php" class="btn btn-primary">Volver al inicio</a>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <footer>
        <div class="footer-left">
            <div class="logo">OnliClub</div>
            <p>Plataforma de cursos en línea — Genesis Castillo</p>
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

