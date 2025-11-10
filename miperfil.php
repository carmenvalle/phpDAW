<?php
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "miperfil.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
?>

<main>

    <section>
        <h2>OPCIONES DE USUARIO</h2>
        <ul>
            <li><a href="modificar_datos.php">
                    <i class="icon-edit"></i>
                    Editar mis datos
                </a></li>
            <li><a href="mis_anuncios.php">
                    <i class="icon-anuncio"></i>
                    Mis anuncios
                </a></li>
            <li><a href="nuevo_anuncio.php">
                    <i class="icon-crear-anuncio"></i>
                    Crear anuncio nuevo
                </a></li>
            <li><a href="mis_mensajes.php">
                    <i class="icon-mensaje"></i>
                    Mis mensajes
                </a></li>
            <li><a href="folleto.php">
                    <i class="icon-form"></i>
                    Solicitar folleto publicitario impreso
                </a></li>
            <li><a href="index.php">
                    <i class="icon-baja"></i>
                    Darme de baja
                </a></li>
        </ul>
    </section>


<?php
    require_once("salto.inc");  
?>

</main>

<?php
require_once("pie.inc");    
?>