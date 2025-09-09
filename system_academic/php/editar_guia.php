<?php
include("conexion.php");
include("verificar_rol.php");
verificarRol('profesor');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de guía inválido.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM guias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$guia = $result->fetch_assoc();

if (!$guia) {
    die("Guía no encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $materia_id = intval($_POST['materia_id']);

    if (!empty($_FILES['archivo']['name'])) {
        $archivo = basename($_FILES['archivo']['name']);
        move_uploaded_file($_FILES['archivo']['tmp_name'], "../guias/" . $archivo);
    } else {
        $archivo = $guia['archivo'];
    }

    $update = $conn->prepare("UPDATE guias SET titulo = ?, materia_id = ?, archivo = ? WHERE id = ?");
    $update->bind_param("sisi", $titulo, $materia_id, $archivo, $id);
    if ($update->execute()) {
        header("Location: ../profesor.php?mensaje=guia_actualizada");
        exit;
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Guía</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }
        :root {
            --bg: #f5f5f5;
            --text: #222;
            --card-bg: white;
            --btn-bg: #007BFF;
            --btn-text: white;
        }
        body.oscuro {
            --bg: #121212;
            --text: #f5f5f5;
            --card-bg: #1e1e1e;
            --btn-bg: #4a90e2;
            --btn-text: white;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="text"], select, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: var(--bg);
            color: var(--text);
        }
        .acciones {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        button, a.btn {
            background-color: var(--btn-bg);
            color: var(--btn-text);
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover, a.btn:hover {
            opacity: 0.85;
        }
        .tema-btn {
            display: block;
            margin: 0 auto 20px auto;
            padding: 5px 10px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            background-color: var(--btn-bg);
            color: var(--btn-text);
        }
        @media (max-width: 600px) {
            .acciones {
                flex-direction: column;
                align-items: center;
                
            }
            button, a.btn {
                width: 70%;
                text-align: center;
                margin: 10px auto 0 auto;

            }
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="tema-btn" onclick="toggleTema()">🌓 Cambiar tema</button>
        <h1>Editar Guía</h1>
        <form method="POST" enctype="multipart/form-data">
            <label>Título:</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($guia['titulo']) ?>" required>

            <label>Materia:</label>
            <select name="materia_id" required>
                <?php
                $materias = $conn->query("SELECT id, nombre FROM materias ORDER BY nombre");
                while ($m = $materias->fetch_assoc()) {
                    $sel = ($m['id'] == $guia['materia_id']) ? "selected" : "";
                    echo "<option value='{$m['id']}' $sel>" . htmlspecialchars($m['nombre']) . "</option>";
                }
                ?>
            </select>

            <label>Archivo actual:</label>
            <?php
            $rutaArchivo = "../guias/" . $guia['archivo'];
            if (file_exists($rutaArchivo)) {
                echo '<a href="' . $rutaArchivo . '" target="_blank">Ver archivo</a>';
            } else {
                echo '<span style="color:red;">Archivo no encontrado</span>';
            }
            ?>

            <label>Subir nuevo (opcional):</label>
            <input type="file" name="archivo" accept=".pdf,.docx">

            <div class="acciones">
                <button type="submit">Guardar cambios</button>
                <a href="../profesor.php" class="btn">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
    function toggleTema() {
        document.body.classList.toggle("oscuro");
        localStorage.setItem("tema", document.body.classList.contains("oscuro") ? "oscuro" : "claro");
    }
    if (localStorage.getItem("tema") === "oscuro") {
        document.body.classList.add("oscuro");
    }
    </script>
</body>
</html>
