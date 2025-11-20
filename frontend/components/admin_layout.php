<?php
function adminStyles() {
    return '
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    ';
}

function adminNavbar($active_item = '') {
    $user_name = $_SESSION['nombre'] ?? 'Administrador';
    
    $items = [
        'inicio' => ['url' => 'admin.php', 'icon' => 'fa-tachometer-alt', 'text' => 'Dashboard'],
        'usuarios' => ['url' => '#', 'icon' => 'fa-users', 'text' => 'Gestionar Usuarios'],
        'cursos' => ['url' => '#', 'icon' => 'fa-book', 'text' => 'Gestionar Cursos'],
        'reportes' => ['url' => '#', 'icon' => 'fa-chart-line', 'text' => 'Reportes'],
    ];

    $nav_links = '';
    foreach ($items as $key => $item) {
        $class = ($key === $active_item) ? 'active' : '';
        $nav_links .= "
            <a href='{$item['url']}' class='{$class}'>
                <i class='fas {$item['icon']}'></i>
                <span>{$item['text']}</span>
            </a>
        ";
    }

    return "
    <nav class='sidebar'>
        <div class='sidebar-header'>
            <h3>OnliClub Admin</h3>
        </div>
        <div class='nav-links'>
            {$nav_links}
        </div>
        <div class='sidebar-footer'>
            <a href='perfil.php'>
                <i class='fas fa-user-cog'></i>
                <span>{$user_name}</span>
            </a>
            <a href='../backend/logout.php'>
                <i class='fas fa-sign-out-alt'></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </nav>
    <main class='main-content'>
    ";
}
?>