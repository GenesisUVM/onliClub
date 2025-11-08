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
    ob_start();
?>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f7f7f7; }
        .navbar { background: #222; color: #fff; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; }
        .nav-left { display: flex; gap: 15px; align-items: center; }
        .nav-right { display: flex; gap: 15px; align-items: center; }
        .nav-left a, .nav-right a { color: #fff; text-decoration: none; padding: 6px 10px; }
        .nav-left a.logo { font-weight: bold; font-size: 18px; }
        .nav-left a.active { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .user-name { opacity: 0.8; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .course-card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .course-card .content { padding: 20px; }
        .course-card h3 { margin: 0 0 10px 0; }
        .course-card p { margin: 0; color: #666; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-outline { background: none; border: 1px solid #007bff; color: #007bff; }
        .actions { margin-top: 20px; }
        .create-course { margin-bottom: 20px; }
    </style>
<?php
    return ob_get_clean();
}
?>