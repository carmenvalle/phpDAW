<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "nuevo_anuncio.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
// Inicializar $valores: usar flash si existe, si no usar $_POST o valores por defecto
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$valores = [
    'tipo_anuncio' => '',
    'vivienda' => '',
    'titulo' => '',
    'ciudad' => '',
    'pais' => '',
    'precio' => '',
    'descripcion' => '',
    'superficie' => '',
    'habitaciones' => '',
    'banos' => '',
    'planta' => '',
    'anio' => ''
];

// Si venimos de un intento fallido, recuperar valores y errores desde flash
if (!empty($_SESSION['flash']['nuevo_anuncio_values'])) {
    $valores = $_SESSION['flash']['nuevo_anuncio_values'];
    $errors = $_SESSION['flash']['nuevo_anuncio_errors'] ?? [];
    unset($_SESSION['flash']['nuevo_anuncio_values'], $_SESSION['flash']['nuevo_anuncio_errors']);
} else {
    $errors = [];
}

// Forzar que el formulario pida imagenes por defecto en creación
$valores['imagenes_required'] = true;
$form_action = 'procesar_nuevo_anuncio.php';
?>

<main>
    <section>
        <h2>NUEVO ANUNCIO</h2>
    </section>

    <section>
        <?php include __DIR__ . '/includes/anuncio_form.php'; ?>
    </section>

    <?php require_once("salto.inc"); ?>
</main>
<?php
require_once("pie.inc");
?>