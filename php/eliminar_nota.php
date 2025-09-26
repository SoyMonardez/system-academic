<?php
include("conexion.php");

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM notas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../profesor.php");
exit();
?>
