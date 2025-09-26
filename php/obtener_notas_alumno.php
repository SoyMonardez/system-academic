<?php
include("conexion.php");

$alumno_id = $_GET['alumno_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT n.*, u.nombre AS profe_nombre, u.apellido AS profe_apellido
    FROM notas n 
    JOIN usuarios u ON n.profesor_id = u.id
    WHERE n.alumno_id = ?
    ORDER BY fecha DESC
");
$stmt->bind_param("i", $alumno_id);
$stmt->execute();

$resultado = $stmt->get_result();
$notas = [];

while ($n = $resultado->fetch_assoc()) {
    $notas[] = $n;
}

header('Content-Type: application/json');
echo json_encode($notas);
?>
