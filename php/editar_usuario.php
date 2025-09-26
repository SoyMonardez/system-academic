<?php
include("verificar_rol.php");
verificarRol('admin');

include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];
    $rol = $_POST['rol'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $password = $_POST['contraseña']; // Recibimos la nueva contraseña si existe

    if (!empty($password)) {
        // Si hay nueva contraseña, la encriptamos y la actualizamos
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, curso=?, nombre_usuario=?, rol=?, password=? WHERE id=?";
        $params = [$nombre, $apellido, $dni, $email, $curso, $nombre_usuario, $rol, $password_hash, $id];
        $types = "ssssssssi";
    } else {
        // Si no hay contraseña nueva, no tocamos el campo
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, curso=?, nombre_usuario=?, rol=? WHERE id=?";
        $params = [$nombre, $apellido, $dni, $email, $curso, $nombre_usuario, $rol, $id];
        $types = "sssssssi";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: ../admin.php?mensaje=Usuario actualizado correctamente");
        exit();
    } else {
        echo "Error al actualizar el usuario: " . $stmt->error;
    }
}

// Si no hay ID, volvemos al panel admin
if (!isset($_GET['id'])) {
    header("Location: ../admin.php");
    exit();
}

// Obtenemos datos del usuario
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/editar_usuario.css">
</head>
<body>
    <div class="contenedor">
        <h2>Editar Usuario: <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h2>
        <form action="editar_usuario.php" method="POST">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>

            <label>DNI:</label>
            <input type="text" name="dni" value="<?= htmlspecialchars($usuario['dni']) ?>" required>

            <label>Correo electrónico:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

            <label>Curso:</label>
            <select name="curso" required>
                <option value="">Seleccionar curso</option>
                <?php
                for ($anio = 1; $anio <= 7; $anio++) {
                    for ($div = 1; $div <= 3; $div++) {
                        $curso_opcion = "{$anio}°{$div}°";
                        $selected = $usuario['curso'] == $curso_opcion ? 'selected' : '';
                        echo "<option value=\"$curso_opcion\" $selected>$curso_opcion</option>";
                    }
                }
                ?>
            </select>

            <label>Nombre de Usuario:</label>
            <input type="text" name="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>

            <label>Nueva Contraseña (opcional):</label>
            <input type="password" name="contraseña" placeholder="••••••••">

            <label>Rol:</label>
            <select name="rol" required>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="profesor" <?= $usuario['rol'] === 'profesor' ? 'selected' : '' ?>>Profesor</option>
                <option value="alumno" <?= $usuario['rol'] === 'alumno' ? 'selected' : '' ?>>Alumno</option>
            </select>

            <button type="submit">Actualizar Usuario</button>
            <a href="../admin.php" class="boton-volver">Volver</a>
        </form>
    </div>
</body>
</html>
