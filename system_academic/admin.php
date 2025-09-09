<?php
include("php/verificar_rol.php");
verificarRol('admin');
include("php/conexion.php");

// Buscar por nombre o apellido
$buscar = $_GET['buscar'] ?? '';
if (!empty($buscar)) {
    $buscar = $conn->real_escape_string($buscar);
    $usuarios_result = $conn->query("
        SELECT * FROM usuarios 
        WHERE nombre LIKE '%$buscar%' OR apellido LIKE '%$buscar%' 
        ORDER BY rol, apellido
    ");
} else {
    $usuarios_result = $conn->query("SELECT * FROM usuarios ORDER BY rol, apellido");
}

// Listado de profesores (para selects)
$profesores_result = $conn->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'profesor' ORDER BY apellido");

// Asignaciones actuales (profesor => cursos)
$asignaciones = [];
$res = $conn->query("
    SELECT pc.profesor_id, u.nombre, u.apellido, pc.curso
    FROM profesor_curso pc
    JOIN usuarios u ON pc.profesor_id = u.id
    ORDER BY u.apellido, pc.curso
");
while ($row = $res->fetch_assoc()) {
    $pid = $row['profesor_id'];
    if (!isset($asignaciones[$pid])) {
        $asignaciones[$pid] = ['nombre' => $row['apellido'] . ', ' . $row['nombre'], 'cursos' => []];
    }
    $asignaciones[$pid]['cursos'][] = $row['curso'];
}

// Todas las materias activas
$materias = $conn->query("SELECT id, nombre FROM materias WHERE activo = 1 ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<h1>👨‍💼 Panel de Administración</h1>
<button onclick="toggleTema()" class="boton-tema">🌓</button>
<a href="php/cerrar_sesion.php" class="boton-cerrar">Cerrar sesión</a>

<section>
    <h2>📋 Registrar Usuario</h2>
    <form action="php/registrar_usuario.php" method="POST">
        <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="text" name="dni" placeholder="DNI" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <select name="curso">
            <option value="">Sin curso</option>
            <?php
            for ($año = 1; $año <= 7; $año++) {
                for ($div = 1; $div <= 3; $div++) {
                    $curso = "{$año}°{$div}°";
                    echo "<option value=\"$curso\">$curso</option>";
                }
            }
            ?>
        </select>
        <select name="rol">
            <option value="alumno">alumno</option>
            <option value="profesor">profesor</option>
            <option value="admin">admin</option>
        </select>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>
</section>

<section>
    <h2>👥 Usuarios</h2>
    <form method="GET">
        <input type="text" name="buscar" placeholder="Buscar por nombre o apellido" value="<?= htmlspecialchars($buscar) ?>">
        <button type="submit">Buscar</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Correo</th>
                <th>Curso</th>
                <th>Rol</th>
                <th>Password (hash)</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usuarios_result->num_rows > 0): ?>
                <?php while ($u = $usuarios_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($u['apellido'] . ', ' . $u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['dni']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['curso']) ?></td>
                        <td><?= htmlspecialchars($u['rol']) ?></td>
                        <td><?= htmlspecialchars($u['password']) ?></td>
                        <td><a href="php/editar_usuario.php?id=<?= $u['id'] ?>">✏️</a></td>
                        <td><a href="php/eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">🗑️</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9">No se encontraron resultados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section>
    <h2>📚 Asignar Cursos a Profesores</h2>
    <form action="php/asignar_curso.php" method="POST">
        <label>Profesor:</label>
        <select name="profesor_id" required>
            <?php while ($p = $profesores_result->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>"><?= $p['apellido'] . ', ' . $p['nombre'] ?></option>
            <?php endwhile; ?>
        </select>
        <label>Curso:</label>
        <select name="curso" required>
            <?php
            for ($año = 1; $año <= 7; $año++) {
                for ($div = 1; $div <= 3; $div++) {
                    $curso = "{$año}°{$div}°";
                    echo "<option value=\"$curso\">$curso</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Asignar</button>
    </form>
</section>

<section>
    <h2>🧾 Cursos Asignados por Profesor</h2>
    <?php if (!empty($asignaciones)): ?>
        <?php foreach ($asignaciones as $pid => $data): ?>
            <div class="profesor-cursos">
                <h3>👨‍🏫 <?= htmlspecialchars($data['nombre']) ?></h3>
                <div class="cursos-lista">
                    <?php foreach ($data['cursos'] as $curso): ?>
                        <span class="curso-badge"><?= htmlspecialchars($curso) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay asignaciones registradas.</p>
    <?php endif; ?>
</section>


<!-- NUEVO: Gestión de Materias -->
<section>
    <h2>📘 Materias</h2>
    <form action="php/gestionar_materias.php" method="POST" style="margin-bottom:1rem;">
        <input type="hidden" name="accion" value="crear">
        <input type="text" name="nombre" placeholder="Nueva materia (ej: matematica)" required>
        <button type="submit">Crear</button>
    </form>

    <h3>Renombrar / Eliminar</h3>
    <form action="php/gestionar_materias.php" method="POST">
        <input type="hidden" name="accion" value="renombrar">
        <select name="id" required>
            <?php
            $mats = $conn->query("SELECT id, nombre FROM materias WHERE activo = 1 ORDER BY nombre");
            while ($m = $mats->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="nombre" placeholder="Nuevo nombre" required>
        <button type="submit">Renombrar</button>
    </form>

    <form action="php/gestionar_materias.php" method="POST" onsubmit="return confirm('¿Dar de baja esta materia?');">
        <input type="hidden" name="accion" value="eliminar">
        <select name="id" required>
            <?php
            $mats2 = $conn->query("SELECT id, nombre FROM materias WHERE activo = 1 ORDER BY nombre");
            while ($m2 = $mats2->fetch_assoc()): ?>
                <option value="<?= $m2['id'] ?>"><?= htmlspecialchars($m2['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Eliminar</button>
    </form>
</section>

<!-- NUEVO: Asignar Materias a Profesor por Curso -->
 
<section>
    <h2>🧩 Asignar Materias por Curso</h2>
    <form action="php/asignar_materia.php" method="POST">
        <label>Profesor:</label>
        <select name="profesor_id" required>
            <?php
            $profes = $conn->query("SELECT id, nombre, apellido FROM usuarios WHERE rol='profesor' ORDER BY apellido");
            while ($p = $profes->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>"><?= $p['apellido'] . ', ' . $p['nombre'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Curso:</label>
        <select name="curso" required>
            <?php
            for ($año = 1; $año <= 7; $año++) {
                for ($div = 1; $div <= 3; $div++) {
                    $curso = "{$año}°{$div}°";
                    echo "<option value=\"$curso\">$curso</option>";
                }
            }
            ?>
        </select>
        <!-- NUEVO: Listado visual de materias asignadas -->
<section>
    <h2>📋 Materias actuales por profesor y curso</h2>
    <?php
    $sql_mats = "
        SELECT u.id AS profesor_id, u.apellido, u.nombre, pm.curso, GROUP_CONCAT(m.nombre ORDER BY m.nombre SEPARATOR ', ') AS materias
        FROM profesor_materia pm
        JOIN usuarios u ON pm.profesor_id = u.id
        JOIN materias m ON pm.materia_id = m.id
        WHERE m.activo = 1
        GROUP BY u.id, pm.curso
        ORDER BY u.apellido, pm.curso
    ";
    $res_mats = $conn->query($sql_mats);
    
    if ($res_mats && $res_mats->num_rows > 0):
        $profe_actual = null;
        while ($row = $res_mats->fetch_assoc()):
            if ($profe_actual !== $row['profesor_id']):
                if ($profe_actual !== null) echo "</ul>";
                echo "<p><strong>👨‍🏫 " . htmlspecialchars($row['apellido'] . ', ' . $row['nombre']) . "</strong></p><ul>";
                $profe_actual = $row['profesor_id'];
            endif;
            echo "<li><em>" . htmlspecialchars($row['curso']) . ":</em> " . htmlspecialchars($row['materias']) . "</li>";
        endwhile;
        echo "</ul>";
    else:
        echo "<p>No hay materias asignadas todavía.</p>";
    endif;
    ?>
</section>


        <label>Materias (Ctrl/Cmd para multi):</label>
        <select name="materias[]" multiple size="6" required>
            <?php while ($m = $materias->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Guardar Asignaciones</button>
    </form>
</section>



<script>
function toggleTema() {
    document.body.classList.toggle("oscuro");
    localStorage.setItem("tema", document.body.classList.contains("oscuro") ? "oscuro" : "claro");
}
window.addEventListener("DOMContentLoaded", () => {
    if (localStorage.getItem("tema") === "oscuro") {
        document.body.classList.add("oscuro");
    }
});
</script>
</body>
</html>