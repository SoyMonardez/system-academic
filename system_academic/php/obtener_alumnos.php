<?php
include("conexion.php");

$curso = $_GET['curso'] ?? '';

$stmt = $conn->prepare("SELECT id, nombre, apellido FROM usuarios WHERE curso = ? AND rol = 'alumno'");
$stmt->bind_param("s", $curso);
$stmt->execute();

$resultado = $stmt->get_result();
$alumnos = [];

while ($a = $resultado->fetch_assoc()) {
    $alumnos[] = $a;
}

header('Content-Type: application/json');
echo json_encode($alumnos);
?>
