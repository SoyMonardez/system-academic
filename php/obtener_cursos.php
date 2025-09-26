<?php
include("conexion.php");

$profesor_id = $_GET['profesor_id'] ?? 0;

$stmt = $conn->prepare("SELECT curso FROM profesor_curso WHERE profesor_id = ?");
$stmt->bind_param("i", $profesor_id);
$stmt->execute();

$resultado = $stmt->get_result();
$cursos = [];

while ($c = $resultado->fetch_assoc()) {
    $cursos[] = $c['curso'];
}

header('Content-Type: application/json');
echo json_encode($cursos);
?>
