<?php
session_start();

function verificarRol($rolEsperado) {
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== $rolEsperado) {
        if (isset($_SESSION['usuario']['rol'])) {
            switch ($_SESSION['usuario']['rol']) {
                case 'admin':
                    header("Location: ../admin.php");
                    break;
                case 'profesor':
                    header("Location: ../profesor.php");
                    break;
                case 'alumno':
                    header("Location: ../alumno.php");
                    break;
            }
        } else {
            header("Location: ../InicioSesion.php");
        }
        exit();
    }
}
?>
