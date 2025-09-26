<?php
if (!isset($_GET['archivo'])) {
    http_response_code(400);
    echo "Parámetro inválido.";
    exit;
}

$archivo = basename($_GET['archivo']); // Evita rutas peligrosas
$ruta = "../guias/" . $archivo;

if (!file_exists($ruta)) {
    http_response_code(404);
    echo "El archivo no existe.";
    exit;
}

// Forzar descarga segura
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $archivo . "\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($ruta));

readfile($ruta);
exit;
?>
