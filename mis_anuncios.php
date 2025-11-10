<?php
$title = "Mi Perfil - PI Pisos & Inmuebles";
$cssPagina = "resultados.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
?>

<main>

  <section>

    <article>
      <h2>Título del anuncio</h2>
    <a href="anuncio.php?id=1">
        <img src="DAW/practica/imagenes/anuncio2.jpg" 
             alt="Foto del anuncio 2" width="200" height="200">
    </a>
      
      <?php
      // --- Recuperar valores enviados por GET ---
      $tipoAnuncio = isset($_GET["tipo-anuncio"]) ? $_GET["tipo-anuncio"] : "";
      $vivienda = isset($_GET["vivienda"]) ? $_GET["vivienda"] : "";
      $ciudad = isset($_GET["ciudad"]) ? $_GET["ciudad"] : "";
      $pais = isset($_GET["pais"]) ? $_GET["pais"] : "";
      $precioMin = isset($_GET["precio_min"]) ? $_GET["precio_min"] : "";
      $precioMax = isset($_GET["precio_max"]) ? $_GET["precio_max"] : "";
      $fechaDesde = isset($_GET["fecha_desde"]) ? $_GET["fecha_desde"] : "";
      $fechaHasta = isset($_GET["fecha_hasta"]) ? $_GET["fecha_hasta"] : "";

      $nombrePais = ($pais != "") ? ucfirst($pais) : "No especificado";

      // --- Tipo de vivienda ---
      $nombreVivienda = ($vivienda != "") ? ucfirst($vivienda) : "No especificado";
      ?>

      <p><strong>TIPO DE VIVIENDA:</strong> <?= $nombreVivienda ?></p>
      <p><strong>CIUDAD:</strong> <?= $ciudad != "" ? htmlspecialchars($ciudad) : "No especificada" ?></p>
      <p><strong>PAÍS:</strong> <?= $nombrePais ?></p>
      <p><strong>FECHA:</strong> <?= $fechaDesde ?> y <?= $fechaHasta ?></p>
      <p><strong>PRECIO:</strong> <?= $precioMin ?> € y <?= $precioMax ?> €</p>

    </article>

    <article>
      <h2>Título del anuncio</h2>
    <a href="anuncio.php?id=2">
        <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg" 
             alt="Foto del anuncio 2" width="200" height="200">
    </a>
      
      <?php
      // --- Recuperar valores enviados por GET ---
      $tipoAnuncio = isset($_GET["tipo-anuncio"]) ? $_GET["tipo-anuncio"] : "";
      $vivienda = isset($_GET["vivienda"]) ? $_GET["vivienda"] : "";
      $ciudad = isset($_GET["ciudad"]) ? $_GET["ciudad"] : "";
      $pais = isset($_GET["pais"]) ? $_GET["pais"] : "";
      $precioMin = isset($_GET["precio_min"]) ? $_GET["precio_min"] : "";
      $precioMax = isset($_GET["precio_max"]) ? $_GET["precio_max"] : "";
      $fechaDesde = isset($_GET["fecha_desde"]) ? $_GET["fecha_desde"] : "";
      $fechaHasta = isset($_GET["fecha_hasta"]) ? $_GET["fecha_hasta"] : "";

      $nombrePais = ($pais != "") ? ucfirst($pais) : "No especificado";

      // --- Tipo de vivienda ---
      $nombreVivienda = ($vivienda != "") ? ucfirst($vivienda) : "No especificado";
      ?>

      <p><strong>TIPO DE VIVIENDA:</strong> <?= $nombreVivienda ?></p>
      <p><strong>CIUDAD:</strong> <?= $ciudad != "" ? htmlspecialchars($ciudad) : "No especificada" ?></p>
      <p><strong>PAÍS:</strong> <?= $nombrePais ?></p>
      <p><strong>FECHA:</strong> <?= $fechaDesde ?> y <?= $fechaHasta ?></p>
      <p><strong>PRECIO:</strong> <?= $precioMin ?> € y <?= $precioMax ?> €</p>

    </article>

    <article>
      <h2>Título del anuncio</h2>
  <a href="anuncio.php?id=1">
        <img src="DAW/practica/imagenes/anuncio2.jpg" 
             alt="Foto del anuncio 2" width="200" height="200">
      </a>
      
      <?php
      // --- Recuperar valores enviados por GET ---
      $tipoAnuncio = isset($_GET["tipo-anuncio"]) ? $_GET["tipo-anuncio"] : "";
      $vivienda = isset($_GET["vivienda"]) ? $_GET["vivienda"] : "";
      $ciudad = isset($_GET["ciudad"]) ? $_GET["ciudad"] : "";
      $pais = isset($_GET["pais"]) ? $_GET["pais"] : "";
      $precioMin = isset($_GET["precio_min"]) ? $_GET["precio_min"] : "";
      $precioMax = isset($_GET["precio_max"]) ? $_GET["precio_max"] : "";
      $fechaDesde = isset($_GET["fecha_desde"]) ? $_GET["fecha_desde"] : "";
      $fechaHasta = isset($_GET["fecha_hasta"]) ? $_GET["fecha_hasta"] : "";

      $nombrePais = ($pais != "") ? ucfirst($pais) : "No especificado";

      // --- Tipo de vivienda ---
      $nombreVivienda = ($vivienda != "") ? ucfirst($vivienda) : "No especificado";
      ?>

      <p><strong>TIPO DE VIVIENDA:</strong> <?= $nombreVivienda ?></p>
      <p><strong>CIUDAD:</strong> <?= $ciudad != "" ? htmlspecialchars($ciudad) : "No especificada" ?></p>
      <p><strong>PAÍS:</strong> <?= $nombrePais ?></p>
      <p><strong>FECHA:</strong> <?= $fechaDesde ?> y <?= $fechaHasta ?></p>
      <p><strong>PRECIO:</strong> <?= $precioMin ?> € y <?= $precioMax ?> €</p>

    </article>
  </section>


  <a href="anyadir_foto.php" class="btn">
      <i class="icon-foto"></i>
      <strong>AÑADIR FOTO</strong>
  </a>

  <?php require_once("salto.inc"); ?>
</main>

<?php
require_once("pie.inc");    
?>