<?php
session_start();
require_once '../backend/db.php';
require_once 'components/teacher_layout.php';

// Protección de ruta
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Profesor') {
    header('Location: profesor_login.php');
    exit;
}

$curso_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Obtener datos del curso
$stmt = $conn->prepare("SELECT * FROM Cursos WHERE id_curso = ? AND id_profesor = ?");
$stmt->bind_param('ii', $curso_id, $_SESSION['id_usuario']);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    header('Location: mis_cursos_profesor.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - OnliClub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/teacher.css">
    <link rel="stylesheet" href="css/course.css">
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
            font-size: 15px;
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
            box-sizing: border-box;
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
            font-family: 'Inter', sans-serif;
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23334155' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .form-actions .btn {
            flex: 1;
            padding: 14px;
            font-size: 16px;
            justify-content: center;
        }

        .helper-text {
            font-size: 0.9em;
            color: #64748b;
            margin-top: 5px;
        }

        /* Success/Error messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>

<body>
    <?php echo teacherNavbar('cursos'); ?>

    <div class="container">
        <div class="page-header">
            <h1>Editar Curso</h1>
            <a href="mis_cursos_profesor.php" class="btn btn-outline">← Volver a Mis Cursos</a>
        </div>

        <div class="form-container">
            <form action="../backend/editar_curso.php" method="post">
                <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">

                <div class="form-group">
                    <label for="titulo">Título del Curso</label>
                    <input type="text" id="titulo" name="titulo" required
                        value="<?php echo htmlspecialchars($curso['titulo']); ?>"
                        placeholder="Ej: Introducción a la Programación Web" maxlength="100">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required
                        placeholder="Describe lo que los estudiantes aprenderán en este curso..."><?php echo htmlspecialchars($curso['descripcion']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="precio">Precio (USD)</label>
                    <input type="number" id="precio" name="precio" min="0" step="0.01" required
                        value="<?php echo htmlspecialchars($curso['precio']); ?>" placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="activo" <?php echo $curso['estado'] === 'activo' ? 'selected' : ''; ?>>Activo
                        </option>
                        <option value="inactivo" <?php echo $curso['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo
                        </option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="mis_cursos_profesor.php" class="btn btn-outline">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>