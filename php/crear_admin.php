<?php
include("conexion.php");

$admin_existente = $conn->query("SELECT * FROM usuarios WHERE rol = 'admin' LIMIT 1");
if ($admin_existente->num_rows === 0) {
    $nombre_usuario = 'admin';
    $nombre = 'Administrador';
    $apellido = 'Principal';
    $dni = '12345678';
    $email = 'admin@colegio.com';
    $curso = '7°3°';
    $rol = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, nombre, apellido, dni, email, curso, rol, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nombre_usuario, $nombre, $apellido, $dni, $email, $curso, $rol, $password);
    $stmt->execute();
    echo "Administrador creado correctamente.";
} else {
    echo "Ya existe un administrador.";
}
?>
