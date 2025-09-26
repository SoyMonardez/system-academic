<?php
include("conexion.php");
include("verificar_rol.php");
verificarRol('profesor');

$id_profesor = $_SESSION['usuario']['id'];
$curso = $_GET['curso'] ?? '';
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="notas_curso_'.$curso.'.xls"');
header("Pragma: no-cache");
header("Expires: 0");

// Abrir salida en modo escritura
$output = fopen('php://output', 'w');

// Encabezado
fputcsv($output, ["Alumno", "Materia", "Trimestre", "Nota", "Promedio Alumno"], "\t");

// Consulta de notas
$sql = "
    SELECT u.apellido, u.nombre, m.nombre AS materia, n.trimestre, n.nota
    FROM notas n
    JOIN usuarios u ON n.alumno_id = u.id
    LEFT JOIN materias m ON n.materia_id = m.id
    WHERE n.profesor_id = ? AND u.curso = ?
";
$params = [$id_profesor, $curso];
$types  = "is";

if ($materia_id > 0) { 
    $sql .= " AND n.materia_id = ?"; 
    $params[] = $materia_id; 
    $types .= "i"; 
}

$sql .= " ORDER BY u.apellido, n.trimestre";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calcular promedios por alumno
$notas_por_alumno = [];
while ($row = $result->fetch_assoc()) {
    $alumno = $row['apellido'] . ', ' . $row['nombre'];
    $notas_por_alumno[$alumno][] = $row['nota'];
}
$promedios = [];
foreach ($notas_por_alumno as $alumno => $notas) {
    $promedios[$alumno] = round(array_sum($notas) / count($notas), 2);
}

// Volver a ejecutar para imprimir filas
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $alumno = $row['apellido'] . ', ' . $row['nombre'];
    fputcsv($output, [
        $alumno,
        $row['materia'] ?? '',
        $row['trimestre'],
        $row['nota'],
        $promedios[$alumno]
    ], "\t");
}

fclose($output);
exit;
?>