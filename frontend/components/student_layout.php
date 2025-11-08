<?php
// Componente de navegación compartido para el panel de estudiante
function studentNavbar($currentPage = '') {
    $pages = [
        'inicio' => ['url' => 'alumno.php', 'title' => 'Inicio'],
        'cursos' => ['url' => 'mis_cursos.php', 'title' => 'Mis Cursos'],
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

// Estilos compartidos para el panel de estudiante
function studentStyles() {
    // Devuelve las etiquetas <link> para los CSS externos.
    // Se mantienen en una función para compatibilidad con llamadas existentes.
    return '<link rel="stylesheet" href="css/app.css">\n<link rel="stylesheet" href="css/student.css">\n<link rel="stylesheet" href="css/course.css">';
}
?>