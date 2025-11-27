<?php
require_once 'backend/db.php';

$result = $conn->query("SHOW COLUMNS FROM Lecciones");
if ($result) {
    echo "Columns in Lecciones table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>