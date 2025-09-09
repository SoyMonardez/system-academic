<?php
include("conexion.php");

$curso = $_GET['curso'] ?? '';
$profesor_id = isset($_GET['profesor_id']) ? intval($_GET['profesor_id']) : 0;
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;
$trimestre  = isset($_GET['trimestre']) ? intval($_GET['trimestre']) : 0;

$sql = "
    SELECT n.*, u.nombre, u.apellido
    FROM notas n
    JOIN usuarios u ON n.alumno_id = u.id
    WHERE u.curso = ? AND n.profesor_id = ?
";
$params = [$curso, $profesor_id];
$types  = "si";

if ($materia_id > 0) { $sql .= " AND n.materia_id = ?"; $params[] = $materia_id; $types .= "i"; }
if (in_array($trimestre, [1,2,3], true)) { $sql .= " AND n.trimestre = ?"; $params[] = $trimestre; $types .= "i"; }

$sql .= " ORDER BY u.apellido";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$resultado = $stmt->get_result();
$notas = [];

while ($n = $resultado->fetch_assoc()) {
    $notas[] = $n;
}

header('Content-Type: application/json');
echo json_encode($notas);