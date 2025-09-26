<?php
include("conexion.php");

// Permite asignar múltiples materias a un profesor para un curso dado.
// Espera: POST profesor_id, curso, materias[] (array de IDs)  O  profesor_id, curso, materia_id (single)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin.php");
    exit();
}

$profesor_id = isset($_POST['profesor_id']) ? intval($_POST['profesor_id']) : 0;
$curso = $_POST['curso'] ?? '';
$materias = $_POST['materias'] ?? null;
$materia_id_single = isset($_POST['materia_id']) ? intval($_POST['materia_id']) : 0;

if ($profesor_id <= 0 || $curso === '') {
    header("Location: ../admin.php?error=parametros");
    exit();
}

$conn->begin_transaction();

try {
    if (is_array($materias)) {
        // Reemplazo total de asignaciones para ese profesor+curso
        $del = $conn->prepare("DELETE FROM profesor_materia WHERE profesor_id = ? AND curso = ?");
        $del->bind_param("is", $profesor_id, $curso);
        $del->execute();

        $ins = $conn->prepare("INSERT IGNORE INTO profesor_materia (profesor_id, curso, materia_id) VALUES (?, ?, ?)");
        foreach ($materias as $mid) {
            $mid = intval($mid);
            if ($mid > 0) {
                $ins->bind_param("isi", $profesor_id, $curso, $mid);
                $ins->execute();
            }
        }
    } elseif ($materia_id_single > 0) {
        $ins = $conn->prepare("INSERT IGNORE INTO profesor_materia (profesor_id, curso, materia_id) VALUES (?, ?, ?)");
        $ins->bind_param("isi", $profesor_id, $curso, $materia_id_single);
        $ins->execute();
    }

    $conn->commit();
    header("Location: ../admin.php?ok=asignado");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../admin.php?error=asignacion");
    exit();
}