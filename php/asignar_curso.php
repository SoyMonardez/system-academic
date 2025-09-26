<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $profesor_id = $_POST['profesor_id'];
    $curso = $_POST['curso'];

    // Evitar duplicados
    $verificar = $conn->prepare("SELECT * FROM profesor_curso WHERE profesor_id = ? AND curso = ?");
    $verificar->bind_param("is", $profesor_id, $curso);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO profesor_curso (profesor_id, curso) VALUES (?, ?)");
        $stmt->bind_param("is", $profesor_id, $curso);
        $stmt->execute();
    }

    header("Location: ../admin.php");
}
?>
