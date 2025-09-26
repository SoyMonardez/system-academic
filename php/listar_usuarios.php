<?php
include("conexion.php");

$resultado = $conn->query("SELECT * FROM usuarios ORDER BY rol, apellido");
$usuarios = [];

while ($fila = $resultado->fetch_assoc()) {
    $usuarios[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($usuarios);
?>
