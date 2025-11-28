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