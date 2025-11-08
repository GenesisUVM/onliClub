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
        .container { display: flex; min-height: calc(100vh - 60px); }
        .sidebar { width: 250px; background: #fff; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .main-content { flex: 1; padding: 20px; }
        .lesson { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .lesson-nav { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        .lesson-list { list-style: none; padding: 0; margin: 0; }
        .lesson-list li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .lesson-list li.completed { color: #4CAF50; }
        .lesson-list li.current { font-weight: bold; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-outline { background: none; border: 1px solid #007bff; color: #007bff; }
        .progress-bar { height: 4px; background: #eee; margin: 10px 0; }
        .progress-bar div { height: 100%; background: #4CAF50; transition: width 0.3s; }
    </style>
<?php
    return ob_get_clean();
}
?>