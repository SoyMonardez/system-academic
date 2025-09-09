<?php
include("conexion.php");

$titulo = $_POST['titulo'] ?? '';
$curso = $_POST['curso'] ?? '';
$profesor_id = isset($_POST['profesor_id']) ? intval($_POST['profesor_id']) : 0;
$materia_id = isset($_POST['materia_id']) ? intval($_POST['materia_id']) : 0;

if ($titulo === '' || $curso === '' || $profesor_id <= 0 || $materia_id <= 0) {
    die("Parámetros inválidos.");
}

// Validar que la materia pertenezca al profesor+curso
$stmt = $conn->prepare("SELECT 1 FROM profesor_materia WHERE profesor_id = ? AND curso = ? AND materia_id = ?");
$stmt->bind_param("isi", $profesor_id, $curso, $materia_id);
$stmt->execute();
$val = $stmt->get_result();
if ($val->num_rows === 0) {
    die("La materia seleccionada no pertenece a este profesor/curso.");
}

// Asegurar que el directorio exista
$directorio = "../guias/";
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
}

// Obtener archivo subido
$archivo_nombre_original = $_FILES['archivo']['name'] ?? '';
$archivo_tmp = $_FILES['archivo']['tmp_name'] ?? '';

if ($archivo_nombre_original === '' || $archivo_tmp === '') {
    die("Archivo no recibido.");
}

// Generar nombre único y limpio para guardar
$extension = pathinfo($archivo_nombre_original, PATHINFO_EXTENSION);
$nombre_limpio = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($archivo_nombre_original, PATHINFO_FILENAME));
$nombre_final = uniqid("guia_") . "_" . $nombre_limpio . "." . $extension;

$ruta_destino = $directorio . $nombre_final;

// Mover archivo y registrar en la base de datos
if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
    $stmt = $conn->prepare("
        INSERT INTO guias (titulo, curso, materia_id, profesor_id, archivo, fecha_subida)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssiis", $titulo, $curso, $materia_id, $profesor_id, $nombre_final);

    if ($stmt->execute()) {
        header("Location: ../profesor.php?curso=" . urlencode($curso) . "&materia_id=" . intval($materia_id));
        exit();
    } else {
        echo "❌ Error al guardar en la base de datos: " . $stmt->error;
    }
} else {
    echo "❌ Error al mover el archivo al directorio destino.";
}