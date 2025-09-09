<?php
include("conexion.php");

$curso = $_GET['curso'] ?? '';
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;

if ($curso === '') {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

if ($materia_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM guias WHERE curso = ? AND materia_id = ? ORDER BY fecha_subida DESC");
    $stmt->bind_param("si", $curso, $materia_id);
} else {
    $stmt = $conn->prepare("SELECT * FROM guias WHERE curso = ? ORDER BY fecha_subida DESC");
    $stmt->bind_param("s", $curso);
}
$stmt->execute();

$resultado = $stmt->get_result();
$guias = [];

while ($g = $resultado->fetch_assoc()) {
    $guias[] = $g;
}

header('Content-Type: application/json');
echo json_encode($guias);