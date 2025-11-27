<?php
require_once 'db.php';

// Check if column exists
$check = $conn->query("SHOW COLUMNS FROM Cursos LIKE 'imagen'");
if ($check->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE Cursos ADD COLUMN imagen VARCHAR(255) DEFAULT NULL AFTER descripcion";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Columna 'imagen' agregada exitosamente a la tabla 'Cursos'.<br>";
        echo "Ahora puedes crear cursos con imágenes.";
    } else {
        echo "❌ Error al agregar la columna: " . $conn->error;
    }
} else {
    echo "ℹ️ La columna 'imagen' ya existe.";
}
?>