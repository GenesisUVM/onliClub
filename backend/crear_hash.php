// ---- CAMBIA ESTA CONTRASEÑA ----
$miPassword = '1234'; 

$hash = password_hash($miPassword, PASSWORD_DEFAULT);

echo "La contraseña que elegiste es: <b>" . htmlspecialchars($miPassword) . "</b><br><br>";
echo "Copia y pega este hash en el comando SQL:<br>";
echo "<b>" . $hash . "</b>";