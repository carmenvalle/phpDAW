<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }

session_start();

// Recoger mensaje flash antes de destruir la sesión
$msg = $_SESSION['flash']['ok'] ?? 'Tu cuenta ha sido eliminada correctamente';
unset($_SESSION['flash']['ok']);

// Destruir la sesión
session_unset();
session_destroy();

$title = "PI - Pisos & Inmuebles";

// Cargar cabecera e inicio SIN acceso.inc (porque ya no existe sesión)
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<main>
    <h1><?= htmlspecialchars($msg) ?></h1>

    <p>Tu sesión ha sido cerrada y tu cuenta eliminada.</p>

    <a href="index.php">Volver a la página principal</a>
</main>

<?php require_once("pie.inc"); ?>
