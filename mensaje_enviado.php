<?php
$title = "Mensaje enviado - PI Pisos & Inmuebles";
$cssPagina = "mensaje.css";
require_once("cabecera.inc");
require_once("inicioLog.inc");

// Sólo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si se accede por GET, redirigir al formulario
    header('Location: mensaje.php');
    exit;
}

// Recoger y sanear datos
$tipo = isset($_POST['tipo_mensaje']) ? trim($_POST['tipo_mensaje']) : '';
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

// Tipos válidos
$tipos_validos = [
    'mas informacion' => 'Más información',
    'solicitar cita' => 'Solicitar cita',
    'comunicar oferta' => 'Comunicar oferta'
];

$errors = [];
if ($tipo === '' || !array_key_exists($tipo, $tipos_validos)) {
    $errors[] = 'tipo_mensaje';
}

// Contar caracteres no blancos
if (mb_strlen(preg_replace('/\s+/', '', $mensaje)) < 10) {
    $errors[] = 'mensaje';
}

?>

<main>
    <section>
        <?php if (empty($errors)): ?>
            <h2>Mensaje enviado con éxito</h2>
            <h3>Datos del mensaje:</h3>
            <ul>
                <li><strong>Tipo de mensaje:</strong> <?php echo htmlspecialchars($tipos_validos[$tipo]); ?></li>
                <li><strong>Descripción del mensaje:</strong> <?php echo nl2br(htmlspecialchars($mensaje)); ?></li>
                <li><strong>Fecha del mensaje:</strong> <?php echo date('d/m/Y'); ?></li>
            </ul>
        <?php else: ?>
            <h2>Error al enviar el mensaje</h2>
            <p class="mensaje-confirmacion">No se ha podido enviar el mensaje por los siguientes motivos:</p>
            <ul>
                <?php if (in_array('tipo_mensaje', $errors)): ?>
                    <li>Debes seleccionar un tipo de mensaje válido.</li>
                <?php endif; ?>
                <?php if (in_array('mensaje', $errors)): ?>
                    <li>Escribe al menos 10 caracteres en el texto del mensaje.</li>
                <?php endif; ?>
            </ul>

            <p>
                <a href="mensaje.php" class="btn"><strong>VOLVER AL FORMULARIO</strong></a>
            </p>
        <?php endif; ?>
    </section>

    <?php require_once("salto.inc"); ?>

</main>

<?php
require_once("pie.inc");
?>
