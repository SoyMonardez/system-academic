<?php
include("conexion.php");

$viejo = $_POST['curso_viejo'] ?? '';
$nuevo = $_POST['curso_nuevo'] ?? '';

if ($viejo === '' || $nuevo === '') {
    header("Location: ../admin.php?error=renombrar");
    exit();
}

$conn->begin_transaction();
try {
    $stmt1 = $conn->prepare("UPDATE usuarios SET curso = ? WHERE curso = ? AND rol='alumno'");
    $stmt1->bind_param("ss", $nuevo, $viejo);
    $stmt1->execute();

    $stmt2 = $conn->prepare("UPDATE profesor_curso SET curso = ? WHERE curso = ?");
    $stmt2->bind_param("ss", $nuevo, $viejo);
    $stmt2->execute();

    $stmt3 = $conn->prepare("UPDATE guias SET curso = ? WHERE curso = ?");
    $stmt3->bind_param("ss", $nuevo, $viejo);
    $stmt3->execute();

    $stmt4 = $conn->prepare("UPDATE profesor_materia SET curso = ? WHERE curso = ?");
    $stmt4->bind_param("ss", $nuevo, $viejo);
    $stmt4->execute();

    $conn->commit();
    header("Location: ../admin.php?ok=curso_renombrado");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../admin.php?error=curso_renombrado");
    exit();
}