<?php
include("conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $profesor_id = intval($_POST['profesor_id']);
    $alumno_id   = intval($_POST['alumno_id']);
    $nota        = $_POST['nota'];
    $materia_id  = isset($_POST['materia_id']) ? intval($_POST['materia_id']) : 0;
    $trimestre   = isset($_POST['trimestre']) ? intval($_POST['trimestre']) : 0;
    $curso       = $_POST['curso'] ?? '';

    if (!is_numeric($nota) || $nota < 1 || $nota > 10) {
        die("Nota inválida.");
    }
    if ($materia_id <= 0) {
        die("Materia inválida.");
    }
    if (!in_array($trimestre, [1,2,3], true)) {
        die("Trimestre inválido.");
    }

    // Verificar existencia del alumno y curso
    $stmt_check = $conn->prepare("SELECT curso FROM usuarios WHERE id = ? AND rol = 'alumno'");
    $stmt_check->bind_param("i", $alumno_id);
    $stmt_check->execute();
    $res = $stmt_check->get_result();
    if ($res->num_rows === 0) {
        die("Alumno no encontrado.");
    }
    $alumno = $res->fetch_assoc();
    if ($curso !== '' && $alumno['curso'] !== $curso) {
        die("El alumno no pertenece al curso seleccionado.");
    }

    // Verificar que la materia esté asignada al profesor para ese curso
    $stmt_pm = $conn->prepare("SELECT 1 FROM profesor_materia WHERE profesor_id = ? AND curso = ? AND materia_id = ?");
    $stmt_pm->bind_param("isi", $profesor_id, $curso, $materia_id);
    $stmt_pm->execute();
    $pm_res = $stmt_pm->get_result();
    if ($pm_res->num_rows === 0) {
        die("La materia no está asignada al profesor para este curso.");
    }

    $fecha_actual = date("Y-m-d");

    $stmt = $conn->prepare("
        INSERT INTO notas (alumno_id, profesor_id, materia_id, nota, trimestre, fecha)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiidis", $alumno_id, $profesor_id, $materia_id, $nota, $trimestre, $fecha_actual);
    $stmt->execute();

    header("Location: ../profesor.php?curso=" . urlencode($curso) . "&materia_id=" . intval($materia_id));
    exit();
}