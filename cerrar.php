<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
$title = "PI - Pisos & Inmuebles";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("acceso.inc");
?>

<main>
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $msg = $_SESSION['flash']['ok'] ?? null;
    if ($msg) {
        unset($_SESSION['flash']['ok']);
    }
    ?>

    <h1><?= isset($msg) ? htmlspecialchars($msg) : 'Tu cuenta ha sido eliminada correctamente' ?></h1>

    <a href="/phpDAW/">Volver a la página principal</a>
    <?php
    // Finalmente destruir sesión si aún existe
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    ?>
</main>

<?php
require_once("pie.inc");
?>