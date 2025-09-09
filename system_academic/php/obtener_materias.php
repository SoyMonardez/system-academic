<?php
include("conexion.php");

$profesor_id = isset($_GET['profesor_id']) ? intval($_GET['profesor_id']) : 0;
$curso = $_GET['curso'] ?? '';

if ($profesor_id <= 0 || $curso === '') {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT m.id, m.nombre
    FROM profesor_materia pm
    JOIN materias m ON pm.materia_id = m.id AND m.activo = 1
    WHERE pm.profesor_id = ? AND pm.curso = ?
    ORDER BY m.nombre
");
$stmt->bind_param("is", $profesor_id, $curso);
$stmt->execute();
$res = $stmt->get_result();

$materias = [];
while ($row = $res->fetch_assoc()) {
    $materias[] = $row;
}

header('Content-Type: application/json');
echo json_encode($materias);