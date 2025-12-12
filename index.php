<?php
// Front controller flag
define('APP_INIT', true);

// --- Simple router for clean URLs -------------------------------------------------------------
// Determine the request path and dispatch to existing scripts when appropriate.
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = parse_url($requestUri, PHP_URL_PATH);
// Remove script directory prefix if present
if ($scriptName && $scriptName !== '/') {
  $path = preg_replace('#^' . preg_quote($scriptName, '#') . '#', '', $path);
}
$path = '/' . trim($path, '/');
$segments = array_values(array_filter(explode('/', $path)));

// Helper to include a target script safely
function include_target($file) {
  $target = __DIR__ . '/' . ltrim($file, '/');
  if (file_exists($target)) {
    // Preserve original globals and include
    include $target;
    exit;
  }
  return false;
}

// Dispatch rules (minimal, non-destructive)
if (!empty($segments)) {
  // /anuncio/{id} and subroutes
  if ($segments[0] === 'anuncio' && isset($segments[1])) {
    // If second segment is not a digit, handle common misroutes (e.g. /anuncio/miperfil)
    if (!ctype_digit($segments[1])) {
      // If user accidentally navigated to /anuncio/miperfil, redirect to the profile page
      if ($segments[1] === 'miperfil') {
        header('Location: /phpDAW/miperfil');
        exit();
      }
      // Unknown non-numeric second segment: redirect to homepage to avoid unexpected behavior
      header('Location: /phpDAW/');
      exit();
    }

    // Now segments[1] is numeric
    if (ctype_digit($segments[1])) {
      $id = (int)$segments[1];
      // If there's a third segment, handle known subroutes first
      if (isset($segments[2])) {
        // /anuncio/{id}/modificar
        if ($segments[2] === 'modificar') {
          $controllerFile = __DIR__ . '/app/Controllers/AnuncioController.php';
          if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $c = new \App\Controllers\AnuncioController();
            $c->edit($id);
          }
          $_GET['id'] = $segments[1];
          include_target('modificar_anuncio.php');
        }
        // Unknown extra segment: redirect to canonical announcement URL
        header('Location: /phpDAW/anuncio/' . $id);
        exit();
      }

      // No extra segment: show anuncio
      $controllerFile = __DIR__ . '/app/Controllers/AnuncioController.php';
      if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $c = new \App\Controllers\AnuncioController();
        $c->show($id);
      }
      $_GET['id'] = $segments[1];
      include_target('anuncio.php');
    }
  }
  // /mis-anuncios
  if ($segments[0] === 'mis-anuncios') include_target('mis_anuncios.php');
  // /nuevo-anuncio
  if ($segments[0] === 'nuevo-anuncio') include_target('nuevo_anuncio.php');
  // /folleto
  if ($segments[0] === 'folleto') include_target('folleto.php');
  // /registro
  if ($segments[0] === 'registro') include_target('registro.php');
  // /mensaje
  if ($segments[0] === 'mensaje') include_target('mensaje.php');

  // /index_logueado (zona privada - enrutado al front controller)
  if ($segments[0] === 'index_logueado') include_target('index_logueado.php');
  // /control_acceso (login POST handler)
  if ($segments[0] === 'control_acceso') include_target('control_acceso.php');
  // /resultados (search results)
  if ($segments[0] === 'resultados' || $segments[0] === 'resultados.php') include_target('resultados.php');
  // /ver_fotos and /ver_foto
  if ($segments[0] === 'ver_fotos' || $segments[0] === 'ver_fotos.php') include_target('ver_fotos.php');
  if ($segments[0] === 'ver_foto' || $segments[0] === 'ver_foto.php') include_target('ver_foto.php');
  // /ver_fotos_priv
  if ($segments[0] === 'ver_fotos_priv' || $segments[0] === 'ver_fotos_priv.php') include_target('ver_fotos_priv.php');
  // /anyadir_foto and its processor
  if ($segments[0] === 'anyadir_foto' || $segments[0] === 'anyadir_foto.php') include_target('anyadir_foto.php');
  if ($segments[0] === 'procesar_anadir_foto' || $segments[0] === 'procesar_anadir_foto.php') include_target('procesar_anadir_foto.php');
  // /editar_foto and its processor
  if ($segments[0] === 'editar_foto' || $segments[0] === 'editar_foto.php') include_target('editar_foto.php');
  if ($segments[0] === 'procesar_editar_foto' || $segments[0] === 'procesar_editar_foto.php') include_target('procesar_editar_foto.php');
  // /mis_anuncios and /mis-anuncios
  if ($segments[0] === 'mis_anuncios' || $segments[0] === 'mis_anuncios.php' || $segments[0] === 'mis-anuncios') include_target('mis_anuncios.php');
  // /miperfil
  if ($segments[0] === 'miperfil' || $segments[0] === 'miperfil.php') include_target('miperfil.php');
  // /configurar (visual preferences)
  if ($segments[0] === 'configurar' || $segments[0] === 'configurar.php') include_target('configurar.php');

  // Fallback: if the path maps to an existing file name, include it
  $maybeFile = ltrim($path, '/');
  // Avoid including the front controller itself (prevents self-include / redeclare errors)
  if ($maybeFile !== '' && $maybeFile !== basename(__FILE__)) {
    // Prefer exact match, otherwise try with .php extension so requests like '/miperfil' include 'miperfil.php'
    $direct = __DIR__ . '/' . $maybeFile;
    $withPhp = __DIR__ . '/' . $maybeFile . '.php';
    if (file_exists($direct)) {
      include_target($maybeFile);
    } elseif (file_exists($withPhp)) {
      include_target($maybeFile . '.php');
    }
  }
  // Otherwise continue to render the homepage as before
}
//-----------------------------------------------------------------------------------------------

// Detectar si el navegador ya tiene una cookie de sesión activa (PHPSESSID)
$hadSessionCookie = isset($_COOKIE[session_name()]);

// Iniciar sesión de forma segura siempre para mantener el estado del usuario
// (se usa un guard para no re-iniciar si ya está activa).
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/includes/precio.php';

// Restaurar usuario desde cookie si no había sesión previa (nuevo navegador) y existe cookie 'usuario'
$restored = false;
if (!isset($_SESSION['usuario']) && !$hadSessionCookie && isset($_COOKIE['usuario'])) {
  $cookieUser = $_COOKIE['usuario'];
  $cookieClave = $_COOKIE['clave'] ?? null;

  // Intentar restaurar desde BD si hay conexión y cookie con hash
  if ($cookieClave && file_exists(__DIR__ . '/includes/conexion.php')) {
    try {
      require_once __DIR__ . '/includes/conexion.php';
      $s = $conexion->prepare('SELECT IdUsuario, NomUsuario, Estilo, Clave, Foto FROM Usuarios WHERE NomUsuario = ? LIMIT 1');
      $s->execute([$cookieUser]);
      $row = $s->fetch(PDO::FETCH_ASSOC);
      if ($row && isset($row['Clave']) && hash_equals($row['Clave'], $cookieClave)) {
        $_SESSION['usuario'] = $row['NomUsuario'];
        $_SESSION['id'] = $row['IdUsuario'];
        $_SESSION['estilo'] = $row['Estilo'];
        $_SESSION['style'] = $row['Estilo'] ?? 'default';
        // foto
        if (!empty($row['Foto'])) {
          $_SESSION['foto'] = resolve_image_url($row['Foto']);
        }
        $restored = true;
      }
    } catch (Exception $e) {
      // si falla la BD, seguimos con la compatibilidad estática
      $restored = false;
    }
  }

  // Si no se pudo restaurar desde BD, probar el array estático (compatibilidad antigua)
  if (!$restored) {
    include_once __DIR__ . '/usuarios.php';
    $usuarios = crearUsuarios();
    foreach ($usuarios as $u) {
      if ($u[0] === $cookieUser) {
        $_SESSION['usuario'] = $cookieUser;
        $_SESSION['style'] = isset($u[2]) ? $u[2] : 'default';
        $restored = true;
        break;
      }
    }
  }
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

// Mostrar mensaje solo si la sesión se ha restaurado desde cookie (es decir, el usuario eligió 'recordarme')
if ($restored) {
  echo "<section class='recordatorio'>
      <p>$saludo <strong>$nombre</strong>, tu última visita fue el <strong>$ultima</strong>.</p>
      <a href='index_logueado.php' class='btn'>Acceder</a>
      <a href='logout.php' class='btn'>Salir</a>
      </section>";

  // Actualizar cookie de última visita sólo si hemos restaurado la sesión desde cookie
  setcookie('ultima_visita', date('d/m/Y H:i'), time() + 90 * 24 * 60 * 60, '/');
}

$title = "PI - Pisos & Inmuebles";
$cssPagina = "index.css";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("acceso.inc");
require_once __DIR__ . '/includes/conexion.php';
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

  <section class="anuncio-escogido">
    <h2>ANUNCIO ESCOGIDO</h2>
    <?php include __DIR__ . '/includes/anuncio-escogido-widget.php'; ?>
  </section>

  <section class="consejo">
    <h2>CONSEJO DE COMPRA/VENTA</h2>
    <?php include __DIR__ . '/includes/consejo-widget.php'; ?>
  </section>

  <section class="anuncios">
    <h2>ÚLTIMOS 5 ANUNCIOS PUBLICADOS</h2>
    <ul>
      <?php
      try {
          $stmt = $conexion->query("SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.FRegistro, a.Ciudad, p.NomPais, a.Precio
                                      FROM Anuncios a
                                      LEFT JOIN Paises p ON a.Pais = p.IdPaises
                                      ORDER BY a.FRegistro DESC
                                      LIMIT 5");
          $ultimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
          $ultimos = [];
      }

      if (empty($ultimos)) {
          echo '<li>No hay anuncios disponibles.</li>';
      } else {
          foreach ($ultimos as $a) {
            // Comprobar que la imagen existe; si no, usar imagen por defecto
            $img = '/phpDAW/DAW/practica/imagenes/anuncio2.jpg';
            if (!empty($a['FPrincipal'])) {
              $img = resolve_image_url($a['FPrincipal']);
            }
              $titulo = htmlspecialchars($a['Titulo'] ?: 'Sin título');
              $ciudad = htmlspecialchars($a['Ciudad'] ?: '—');
              $pais = htmlspecialchars($a['NomPais'] ?: '—');
              $precio = $a['Precio'] !== null ? number_format((float)$a['Precio'], 2, ',', '.') . ' €' : '—';
              echo "<li><article><a href=\"anuncio/{$a['IdAnuncio']}\"><img src=\"{$img}\" alt=\"{$titulo}\" width=\"150\"><h3>{$titulo}</h3></a><p>Fecha: {$a['FRegistro']} | Ciudad: {$ciudad} <br>País: {$pais} | Precio: {$precio}</p></article></li>";
          }
      }
      ?>
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
