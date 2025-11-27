<?php
session_start();
require_once '../backend/db.php';
require_once 'components/teacher_layout.php';

// Protección de ruta
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: profesor_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Curso - OnliClub</title>
    <?php echo teacherStyles(); ?>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e1e5e9;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #1e293b;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00A3BF;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 163, 191, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .form-actions button {
            flex: 1;
            padding: 14px;
            font-size: 16px;
        }

        .helper-text {
            font-size: 0.9em;
            color: #64748b;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <?php echo teacherNavbar('cursos'); ?>

    <div class="container">
        <div class="page-header">
            <h1>Crear Nuevo Curso</h1>
            <a href="mis_cursos_profesor.php" class="btn btn-outline">← Volver a Mis Cursos</a>
        </div>

        <div class="form-container">
            <form action="../backend/crear_curso.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título del Curso</label>
                    <input type="text" id="titulo" name="titulo" required
                        placeholder="Ej: Introducción a la Programación Web" maxlength="100">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required
                        placeholder="Describe lo que los estudiantes aprenderán en este curso..."></textarea>
                </div>

                <div class="form-group">
                    <label for="imagen_url">URL de la Imagen de Portada</label>
                    <input type="url" id="imagen_url" name="imagen_url" placeholder="https://ejemplo.com/imagen.jpg">
                    <div class="helper-text">Por ahora, ingresa una URL directa a una imagen.</div>
                </div>

                <!-- 
                <div class="form-group">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria">
                        <option value="">Seleccionar categoría...</option>
                        <option value="programacion">Programación</option>
                        <option value="diseño">Diseño</option>
                        <option value="marketing">Marketing</option>
                        <option value="negocios">Negocios</option>
                    </select>
                </div>
                -->

                <div class="form-actions">
                    <a href="mis_cursos_profesor.php" class="btn btn-outline">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear Curso</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>