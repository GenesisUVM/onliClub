<?php
// Archivo de conexión a la base de datos MySQL local con XAMPP

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'onliclub'; // Cambia este nombre si tu base de datos tiene otro nombre

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}
// Puedes usar $conn en otros archivos para realizar consultas
