<?php
// Componente de navegación compartido para el panel de profesor
function teacherNavbar($currentPage = '') {
    $pages = [
        'inicio' => ['url' => 'profesor.php', 'title' => 'Inicio'],
        'cursos' => ['url' => 'mis_cursos_profesor.php', 'title' => 'Mis Cursos'],
        'perfil' => ['url' => 'perfil.php', 'title' => 'Mi Perfil']
    ];
    
    ob_start();
?>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">OnliClub</a>
            <?php foreach ($pages as $id => $page): ?>
                <a href="<?php echo $page['url']; ?>"
                   class="<?php echo $currentPage === $id ? 'active' : ''; ?>">
                    <?php echo $page['title']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="nav-right">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></span>
            <a href="../backend/logout.php" class="logout">Cerrar sesión</a>
        </div>
    </nav>
<?php
    return ob_get_clean();
}

// Estilos compartidos para el panel de profesor (reutilizamos los mismos que el estudiante)
function teacherStyles() {
    // Devuelve las etiquetas <link> para los CSS externos.
    return '<link rel="stylesheet" href="css/app.css">\n<link rel="stylesheet" href="css/teacher.css">\n<link rel="stylesheet" href="css/course.css">';
}
?>