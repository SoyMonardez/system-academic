<?php
include("conexion.php");

$curso = $_GET['curso'] ?? '';
$profesor_id = $_GET['profesor_id'] ?? '';

if (empty($curso) || empty($profesor_id)) {
    die("Faltan parámetros.");
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="notas_' . $curso . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Apellido', 'Nombre', 'Nota', 'Fecha']);

$stmt = $conn->prepare("
    SELECT u.nombre, u.apellido, n.nota, n.fecha 
    FROM notas n 
    JOIN usuarios u ON n.alumno_id = u.id 
    WHERE n.profesor_id = ? AND u.curso = ?
    ORDER BY u.apellido
");
$stmt->bind_param("is", $profesor_id, $curso);
$stmt->execute();
$resultado = $stmt->get_result();

while ($row = $resultado->fetch_assoc()) {
    fputcsv($output, [$row['apellido'], $row['nombre'], $row['nota'], $row['fecha']]);
}

fclose($output);
exit();
?>
