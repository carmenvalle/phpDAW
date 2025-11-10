<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['mensaje'])) {
    echo "<p class='info'>" . htmlspecialchars($_GET['mensaje'], ENT_QUOTES, 'UTF-8') . "</p>";
}

$nombreUsuario = isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8') : '';
$hora = (int) date('H');
if ($hora >= 6 && $hora <= 11) {
  $saludoPrefijo = 'Buenos días';
} elseif ($hora >= 12 && $hora <= 15) {
  $saludoPrefijo = 'Hola';
} elseif ($hora >= 16 && $hora <= 19) {
  $saludoPrefijo = 'Buenas tardes';
} else {
  $saludoPrefijo = 'Buenas noches';
}
$saludoCompleto = $saludoPrefijo . ' ' . $nombreUsuario . '.';


$title = "PI - Pisos & Inmuebles";
$cssPagina = "index.css";
require_once("cabecera.inc");
require_once("inicioLog.inc");
?>

<main>
  <?php if ($nombreUsuario !== ''): ?>
    <section class="saludo">
      <div class="saludo__box">
        <span class="saludo__prefix"><?php echo $saludoPrefijo; ?></span>
        &nbsp;
        <span class="saludo__user"><?php echo $nombreUsuario; ?></span>
        <span class="saludo__dot">.</span>
      </div>
    </section>
  <?php endif; ?>

  <section>
    <h2>BÚSQUEDA RÁPIDA</h2>
    <form action="resultados.php" method="get">
      <p>
        <label for="consulta">Ciudad:</label>
        <input type="text" id="consulta" name="q" placeholder="Ej. Madrid">
        <button type="submit"><strong>BUSCAR</strong></button>
      </p>
    </form>
  </section>

  <section class="anuncios">
    <h2>ÚLTIMOS 5 ANUNCIOS PUBLICADOS</h2>
    <ul>
      <li>
        <article>
          <a href="anuncio.php?id=1">
            <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg" 
                 alt="Foto del anuncio 1" width="150">
            <h3>Anuncio 1 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>

      <li>
        <article>
          <a href="anuncio.php?id=2">
            <img src="DAW/practica/imagenes/anuncio2.jpg" 
                 alt="Foto del anuncio 2" width="150">
            <h3>Anuncio 2 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>

      <li>
        <article>
          <a href="anuncio.php?id=3">
            <img src="DAW/practica/imagenes/anuncio3.jpg" 
                 alt="Foto del anuncio 3" width="150">
            <h3>Anuncio 3 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>

      <li>
        <article>
          <a href="anuncio.php?id=4">
            <img src="DAW/practica/imagenes/anuncio4.jpg" 
                 alt="Foto del anuncio 4" width="150">
            <h3>Anuncio 4 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>

      <li>
        <article>
          <a href="anuncio.php?id=1">
            <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg" 
                 alt="Foto del anuncio 5" width="150">
            <h3>Anuncio 5 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>
    </ul>
  </section>

  <?php
  require_once("panelVisitados.inc");
  require_once("salto.inc");
  ?>

</main>

<?php
require_once("pie.inc");
?>