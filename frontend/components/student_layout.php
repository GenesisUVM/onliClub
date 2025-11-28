<?php

// Función de Navegación Genérica
function createNavbar($pages, $currentPage = '')
{
    ob_start();
    ?>
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="logo">OnliClub</a>
            <?php foreach ($pages as $id => $page): ?>
                <a href="<?php echo $page['url']; ?>" class="<?php echo $currentPage === $id ? 'active' : ''; ?>">
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


// Componente de navegación para el panel de estudiante
function studentNavbar($currentPage = '')
{
    $pages = [
        'inicio' => ['url' => 'alumno.php', 'title' => 'Inicio'],
        'cursos' => ['url' => 'mis_cursos.php', 'title' => 'Mis Cursos'],
        'perfil' => ['url' => 'perfil.php', 'title' => 'Mi Perfil']
    ];
    return createNavbar($pages, $currentPage);
}


// Componente de navegación para el panel de admin
function adminNavbar($currentPage = '')
{
    $pages = [
        'inicio' => ['url' => 'admin.php', 'title' => 'Dashboard'],
        'usuarios' => ['url' => '#', 'title' => 'Usuarios'],
        'cursos' => ['url' => '#', 'title' => 'Cursos'],
        'reportes' => ['url' => '#', 'title' => 'Reportes'],
    ];
    return createNavbar($pages, $currentPage);
}


// Estilos compartidos para los paneles
function studentStyles()
{
    // Devuelve las etiquetas <link> para los CSS externos con cache busting
    $v = time();
    return '<link rel="stylesheet" href="css/app.css?v=' . $v . '">' . "\n" .
        '<link rel="stylesheet" href="css/student.css?v=' . $v . '">';
}

?>