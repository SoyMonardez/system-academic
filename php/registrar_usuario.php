<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];
    $rol = $_POST['rol'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, nombre, apellido, dni, email, curso, rol, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nombre_usuario, $nombre, $apellido, $dni, $email, $curso, $rol, $password);

    if ($stmt->execute()) {
        header("Location: ../admin.php");
    } else {
        echo "Error al registrar el usuario.";
    }
}
?>
