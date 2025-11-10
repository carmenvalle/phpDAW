<?php
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "mis_mensajes.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
?>

<main>
    <section>
        <h3>Mensajes enviados</h3>

        <article>
            <p><strong>Para:</strong> Carmen Valle</p>
            <p><strong>Tipo de mensaje:</strong> Más información</p>
            <p><strong>Texto del mensaje:</strong> Buenos días, me gustaría saber más información sobre el
                alojamiento.</p>
            <p><strong>Fecha del mensaje:</strong> 12/09/2025</p>
        </article>

        <article>
            <p><strong>Para:</strong> Silvia Balmaseda</p>
            <p><strong>Tipo de mensaje:</strong> Solicitar una cita</p>
            <p><strong>Texto del mensaje:</strong> Hola, me gustaría que nos viéramos para hablar sobre algunas
                ofertas.</p>
            <p><strong>Fecha del mensaje:</strong> 09/09/2025</p>
        </article>

        <article>
            <p><strong>Para:</strong> Lucía Martínez</p>
            <p><strong>Tipo de mensaje:</strong> Comunicar una oferta</p>
            <p><strong>Texto del mensaje:</strong> Buenas tardes, tengo una oferta para el piso, me gustaría dejarlo
                en 100.000 €.</p>
            <p><strong>Fecha del mensaje:</strong> 28/08/2025</p>
        </article>
    </section>

    <section>
        <h3>Mensajes recibidos</h3>

        <article>
            <p><strong>De:</strong> Antonio López</p>
            <p><strong>Tipo de mensaje:</strong> Solicitar una cita</p>
            <p><strong>Texto del mensaje:</strong> Buenas, ¿podríamos concertar una visita al piso esta semana?</p>
            <p><strong>Fecha del mensaje:</strong> 10/09/2025</p>
        </article>

        <article>
            <p><strong>De:</strong> Laura Gómez</p>
            <p><strong>Tipo de mensaje:</strong> Más información</p>
            <p><strong>Texto del mensaje:</strong> Estoy interesada en el anuncio, ¿a qué distancia está del centro?
            </p>
            <p><strong>Fecha del mensaje:</strong> 07/09/2025</p>
        </article>
    </section>

<?php
    require_once("salto.inc");  
?>

</main>

<?php
require_once("pie.inc");    
?>