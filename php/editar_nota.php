<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $nota = $_POST['nota'];

    if (!is_numeric($nota) || $nota < 1 || $nota > 10) {
        die("Nota inválida.");
    }

    $stmt = $conn->prepare("UPDATE notas SET nota = ? WHERE id = ?");
    $stmt->bind_param("ii", $nota, $id);
    $stmt->execute();

    header("Location: ../profesor.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("
    SELECT n.*, u.nombre, u.apellido 
    FROM notas n 
    JOIN usuarios u ON n.alumno_id = u.id 
    WHERE n.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Nota no encontrada.");
}

$nota = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Nota</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Hace que sea responsive -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .panel {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        p {
            margin: 10px 0;
            font-size: 1rem;
        }

        label {
            display: block;
            margin: 12px 0;
            font-weight: bold;
            font-size: 1rem;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            display: block;
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        /* Ajustes para pantallas chicas */
        @media (max-width: 480px) {
            .panel {
                padding: 15px;
                border-radius: 10px;
            }

            h2 {
                font-size: 1.2rem;
            }

            input[type="number"], button {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="panel">
        <h2>✏️ Editar Nota</h2>
        <form method="POST" action="editar_nota.php">
            <input type="hidden" name="id" value="<?= $nota['id'] ?>">
            <p>Alumno: <?= htmlspecialchars($nota['apellido']) ?>, <?= htmlspecialchars($nota['nombre']) ?></p>
            <label>Nueva Nota:
                <input type="number" name="nota" value="<?= $nota['nota'] ?>" min="1" max="10" required>
            </label>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
