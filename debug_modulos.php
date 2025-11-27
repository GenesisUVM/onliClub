<?php
require_once 'backend/db.php';

$result = $conn->query("SHOW COLUMNS FROM Modulos");
if ($result) {
    echo "Columns in Modulos table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>