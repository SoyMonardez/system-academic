<?php
include("conexion.php");

$id = $_GET['id'];

// Primero eliminar notas asociadas al alumno
$conn->query("DELETE FROM notas WHERE alumno_id = $id");

// Luego eliminar guías si es profesor (opcional)
$conn->query("DELETE FROM guias WHERE profesor_id = $id");

// Finalmente eliminar el usuario
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../admin.php");
exit();
?>
