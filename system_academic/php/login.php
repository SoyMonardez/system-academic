<?php
session_start();
include("conexion.php");

$nombre_usuario = $_POST['nombre_usuario'] ?? '';
$dni            = $_POST['dni'] ?? '';
$password       = $_POST['password'] ?? '';

if (empty($nombre_usuario) || empty($dni) || empty($password)) {
    header("Location: ../InicioSesion.php?error=1");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? AND dni = ?");
$stmt->bind_param("ss", $nombre_usuario, $dni);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($password, $usuario['password'])) {
        $_SESSION['usuario'] = [
            'id'             => $usuario['id'],
            'nombre_usuario' => $usuario['nombre_usuario'],
            'nombre'         => $usuario['nombre'],
            'apellido'       => $usuario['apellido'],
            'rol'            => $usuario['rol'],
            'curso'          => $usuario['curso'],
        ];
        $_SESSION['rol'] = $usuario['rol'];

        switch ($usuario['rol']) {
            case 'admin':
                header("Location: ../admin.php");
                break;
            case 'profesor':
                header("Location: ../profesor.php");
                break;
            case 'alumno':
                header("Location: ../alumno.php");
                break;
        }
        exit();
    }
}

header("Location: ../InicioSesion.php?error=1");
exit();
