<?php 
include("php/verificar_rol.php");
verificarRol('alumno');
include("php/conexion.php");

$id_alumno = $_SESSION['usuario']['id'];
$nombre_alumno = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];
$curso = $_SESSION['usuario']['curso'];

// Guías del curso (todas las materias)
$stmt_guias = $conn->prepare("
    SELECT g.*, u.nombre AS profesor_nombre, u.apellido AS profesor_apellido, m.nombre AS materia
    FROM guias g
    JOIN usuarios u ON g.profesor_id = u.id
    LEFT JOIN materias m ON g.materia_id = m.id
    WHERE g.curso = ?
    ORDER BY g.fecha_subida DESC
");
$stmt_guias->bind_param("s", $curso);
$stmt_guias->execute();
$guias = $stmt_guias->get_result();

// Notas del alumno (con materia y trimestre)
$stmt_notas = $conn->prepare("
    SELECT n.*, u.nombre AS prof_nombre, u.apellido AS prof_apellido, m.nombre AS materia
    FROM notas n
    JOIN usuarios u ON n.profesor_id = u.id
    LEFT JOIN materias m ON n.materia_id = m.id
    WHERE n.alumno_id = ?
    ORDER BY n.fecha DESC
");
$stmt_notas->bind_param("i", $id_alumno);
$stmt_notas->execute();
$notas = $stmt_notas->get_result();

$promedio_general = "N/A";
$total = 0; $cantidad = 0;
$promedios_por_materia_trimestre = []; // materia => trimestre => [notas]

while ($n = $notas->fetch_assoc()) {
    $total += floatval($n['nota']);
    $cantidad++;

    $mat = $n['materia'] ?? 'Sin materia';
    $t = intval($n['trimestre'] ?? 0);
    if (!isset($promedios_por_materia_trimestre[$mat])) {
        $promedios_por_materia_trimestre[$mat] = [1=>[],2=>[],3=>[]];
    }
    if ($t >=1 && $t <=3) {
        $promedios_por_materia_trimestre[$mat][$t][] = floatval($n['nota']);
    }
}
$promedio_general = $cantidad > 0 ? number_format($total / $cantidad, 2) : "N/A";
$notas->data_seek(0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Alumno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/alumno.css">
</head>
<body>
<header>
    <h1>🎒 Bienvenido, <?= htmlspecialchars($nombre_alumno) ?></h1>
    <a href="php/cerrar_sesion.php" class="boton-cerrar">Cerrar sesión</a>
    <button onclick="toggleTema()" class="boton-tema">🌓</button>
</header>

<section>
    <h2>📚 Guías disponibles</h2>
    <?php if ($guias->num_rows > 0): ?>
        <ul class="lista-guias">
            <?php while ($g = $guias->fetch_assoc()): ?>
                <li>
                    <?= htmlspecialchars($g['titulo']) ?> - <?= date("d/m/Y", strtotime($g['fecha_subida'])) ?>
                    <?php if (!empty($g['materia'])): ?>
                        <em>(<?= htmlspecialchars($g['materia']) ?>)</em>
                    <?php endif; ?>
                    <strong>- Prof. <?= htmlspecialchars($g['profesor_apellido'] . ', ' . $g['profesor_nombre']) ?></strong>
                    <a href="php/descargar_guia.php?archivo=<?= urlencode($g['archivo']) ?>">📥</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay guías disponibles por ahora.</p>
    <?php endif; ?>
</section>

<section>
    <h2>📝 Notas</h2>
    <?php if ($cantidad > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nota</th>
                    <th>Trimestre</th>
                    <th>Materia</th>
                    <th>Fecha</th>
                    <th>Profesor</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($n = $notas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $n['nota'] ?></td>
                        <td><?= htmlspecialchars($n['trimestre'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($n['materia'] ?? 'Sin materia') ?></td>
                        <td><?= date("d/m/Y", strtotime($n['fecha'])) ?></td>
                        <td><?= htmlspecialchars($n['prof_apellido'] . ', ' . $n['prof_nombre']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><strong>📊 Promedio general:</strong> <?= $promedio_general ?></p>

        <h3>📈 Promedios por materia y trimestre</h3>
        <ul>
            <?php foreach ($promedios_por_materia_trimestre as $mat => $trims): ?>
                <?php
                    $p1 = count($trims[1]) ? round(array_sum($trims[1])/count($trims[1]), 2) : 0;
                    $p2 = count($trims[2]) ? round(array_sum($trims[2])/count($trims[2]), 2) : 0;
                    $p3 = count($trims[3]) ? round(array_sum($trims[3])/count($trims[3]), 2) : 0;
                ?>
                <li><strong><?= htmlspecialchars($mat) ?>:</strong> Primer Trimestre  <?= $p1 ?> | Segundo Trimestre <?= $p2 ?> | Tercer Trimestre <?= $p3 ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tienes notas cargadas todavía.</p>
    <?php endif; ?>
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