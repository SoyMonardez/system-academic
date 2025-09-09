<?php
include("conexion.php");

$accion = $_POST['accion'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

switch ($accion) {
    case 'crear':
        if ($nombre !== '') {
            $stmt = $conn->prepare("INSERT INTO materias (nombre, activo) VALUES (?, 1)");
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
        }
        break;
    case 'renombrar':
        if ($id > 0 && $nombre !== '') {
            $stmt = $conn->prepare("UPDATE materias SET nombre = ? WHERE id = ?");
            $stmt->bind_param("si", $nombre, $id);
            $stmt->execute();
        }
        break;
    case 'eliminar':
        if ($id > 0) {
            // Baja lógica
            $stmt = $conn->prepare("UPDATE materias SET activo = 0 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        break;
}

header("Location: ../admin.php");
exit();