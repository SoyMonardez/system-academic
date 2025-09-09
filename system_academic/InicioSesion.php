<?php
session_start();
if (isset($_SESSION['usuario'])) {
    $rol = $_SESSION['usuario']['rol'];
    switch ($rol) {
        case 'admin':
            header("Location: admin.php");
            exit();
        case 'profesor':
            header("Location: profesor.php");
            exit();
        case 'alumno':
            header("Location: alumno.php");
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>🔐 Iniciar Sesión</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">❌ Credenciales incorrectas. Intentá nuevamente.</div>
        <?php endif; ?>

        <form action="php/login.php" method="POST">
            <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
            <input type="text" name="dni" placeholder="DNI" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button class="boton-ingresar" type="submit">Ingresar</button>
        </form>

        <button onclick="toggleTema()" class="boton-tema" >🌓</button>
    </div>

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
