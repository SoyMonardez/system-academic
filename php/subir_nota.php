<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $profesor_id = $_POST['profesor_id'];
    $alumno_id = $_POST['alumno_id'];
    $nota = $_POST['nota'];
    $fecha = date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO notas (profesor_id, alumno_id, nota, fecha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $profesor_id, $alumno_id, $nota, $fecha);
    $stmt->execute();

    header("Location: ../profesor.php");
    exit();
}
?>
