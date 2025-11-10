<?php
$title = "PI - Pisos & Inmuebles";
$cssPagina = "mensaje.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
?>

<main>
    <section>
        <h3>Enviar Mensaje</h3>
        <form id="formMensaje" action="mensaje_enviado.php" method="post">
            <textarea name="mensaje" id="mensaje" placeholder="Mensaje al anunciante"></textarea>
            <p class="tipo-mensaje">
                Tipo de mensaje:
            </p>
            <p>
                <button><strong>ENVIAR MENSAJE</strong></button>
            </p>
        </form>
    </section>

    <?php
    require_once("salto.inc");
    ?>

</main>

<script src="DAW/practica/js/mensaje.js"></script>

<?php
require_once("pie.inc");
?>