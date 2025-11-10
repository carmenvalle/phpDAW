<?php
session_start();

// Restaurar usuario desde cookie si no hay sesión
if (!isset($_SESSION['usuario']) && isset($_COOKIE['usuario'])) {
  // Validar que el usuario de la cookie existe en la lista de usuarios
  include_once __DIR__ . '/usuarios.php';
  $usuarios = crearUsuarios();
  $cookieUser = $_COOKIE['usuario'];
  $found = false;
  foreach ($usuarios as $u) {
    if ($u[0] === $cookieUser) {
      $found = true;
      $_SESSION['usuario'] = $cookieUser;
      $_SESSION['style'] = isset($u[2]) ? $u[2] : 'default';
      break;
    }
  }
  // Si no existe, no restaurar la sesión ni la preferencia
}

// Determinar nombre del usuario
$nombre = 'Invitado';
if (isset($_SESSION['usuario'])) {
    $nombre = htmlspecialchars($_SESSION['usuario']);
} elseif (isset($_COOKIE['usuario'])) {
    $nombre = htmlspecialchars($_COOKIE['usuario']);
}

// Obtener última visita
$ultima = $_COOKIE['ultima_visita'] ?? 'primera vez';

// Determinar saludo según la hora
$hora = date('H');
if ($hora >= 6 && $hora < 12) {
    $saludo = "Buenos días";
} elseif ($hora >= 12 && $hora < 16) {
    $saludo = "Hola";
} elseif ($hora >= 16 && $hora < 20) {
    $saludo = "Buenas tardes";
} else {
    $saludo = "Buenas noches";
}

// Mostrar mensaje solo si hay usuario registrado o cookie
if ($nombre !== 'Invitado') {
    echo "<section class='recordatorio'>
            <p>$saludo <strong>$nombre</strong>, tu última visita fue el <strong>$ultima</strong>.</p>
            <a href='index_logueado.php' class='btn'>Acceder</a>
            <a href='logout.php' class='btn'>Salir</a>
          </section>";
}

// Actualizar cookie de última visita (si hay usuario)
if ($nombre !== 'Invitado') {
    setcookie('ultima_visita', date('d/m/Y H:i'), time() + 90 * 24 * 60 * 60, '/');
}

$title = "PI - Pisos & Inmuebles";
$cssPagina = "index.css";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("acceso.inc");
?>

<main>

  <section>
    <h2>BÚSQUEDA RÁPIDA</h2>
    <form action="resultados.php" method="get">
      <p>
        <label for="consulta">Ciudad:</label>
        <input type="text" id="consulta" name="ciudad" placeholder="Ej. Madrid">
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
            <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg" 
                 alt="Foto del anuncio 2" width="150">
            <h3>Anuncio 2 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>

      <li>
        <article>
          <a href="anuncio.php?id=1">
            <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg" 
                 alt="Foto del anuncio 3" width="150">
            <h3>Anuncio 3 (título)</h3>
          </a>
          <p>Fecha: xx/xx/xxxx | Ciudad: xxxxxx <br>País: España | Precio: 250.000 €</p>
        </article>
      </li>

      <li>
        <article>
          <a href="anuncio.php?id=2">
            <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg" 
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
