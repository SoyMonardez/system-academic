<?php
include("conexion.php");

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = intval($_GET['id']);

// Buscar guía
$stmt = $conn->prepare("SELECT archivo FROM guias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Guía no encontrada.");
}

$guia = $resultado->fetch_assoc();
$ruta = "../guias/" . $guia['archivo'];

// Eliminar archivo físico
if (file_exists($ruta)) {
    unlink($ruta);
}

// Eliminar de BD
$stmt = $conn->prepare("DELETE FROM guias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../profesor.php");
exit();
?>
