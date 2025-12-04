<?php
if (!defined('APP_INIT')) define('APP_INIT', true);
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /phpDAW/");
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

// Utilidades
require_once __DIR__ . '/includes/funciones-ficheros.php';
require_once __DIR__ . '/includes/precio.php';
require_once __DIR__ . '/includes/conexion.php';
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
    <form action="resultados" method="get">
      <p>
        <label for="consulta">Ciudad:</label>
        <input type="text" id="consulta" name="q" placeholder="Ej. Madrid">
        <button type="submit"><strong>BUSCAR</strong></button>
      </p>
    </form>
  </section>

  <section class="anuncio-escogido">
    <h2>ANUNCIO ESCOGIDO</h2>

    <?php
    $ficheroAE = __DIR__ . "/includes/anuncio-escogido.txt";

    if (!file_exists($ficheroAE)) {
        echo "<p>No hay anuncios escogidos disponibles.</p>";
    } else {
        $lineas = file($ficheroAE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lineas) {
            echo "<p>El fichero de anuncios escogidos está vacío.</p>";
        } else {
            // Selección aleatoria
            $linea = $lineas[array_rand($lineas)];

            // Formato: ID|Experto|Comentario
            $partes = explode("|", $linea);

            if (count($partes) >= 3) {
                list($idEsc, $expertoAE, $comentarioAE) = $partes;

                // Comprobar en BD
                $stmtAE = $conexion->prepare("
                    SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Ciudad, 
                           p.NomPais AS Pais, a.Precio 
                    FROM Anuncios a
                    LEFT JOIN Paises p ON a.Pais = p.IdPaises
                    WHERE a.IdAnuncio = ?
                ");
                $stmtAE->execute([$idEsc]);
                $anEscogido = $stmtAE->fetch(PDO::FETCH_ASSOC);

                if ($anEscogido) {
                    // Foto principal calculada con tu función
                    $fotoAE = resolve_image_url($anEscogido['FPrincipal']);

                    echo "<article class='anuncioAE'>";
                    echo "<img src='$fotoAE' alt='Foto principal' width='200'>";
                    echo "<h3>{$anEscogido['Titulo']}</h3>";
                    echo "<p><strong>Ciudad:</strong> {$anEscogido['Ciudad']} ({$anEscogido['Pais']})</p>";
                    echo "<p><strong>Precio:</strong> " 
                        . number_format($anEscogido['Precio'], 2, ',', '.') . " €</p>";

                    echo "<p><strong>$expertoAE</strong> opina:</p>";
                    echo "<blockquote>$comentarioAE</blockquote>";

                    echo "<p><a href='/phpDAW/anuncio/{$anEscogido['IdAnuncio']}'>Ver anuncio completo</a></p>";
                    echo "</article>";
                } else {
                    echo "<p>El anuncio escogido ya no existe en la base de datos.</p>";
                }
            } else {
                echo "<p>Formato incorrecto en el fichero de anuncios escogidos.</p>";
            }
        }
    }
    ?>
  </section>


  <section class="anuncios">
    <h2>ÚLTIMOS 5 ANUNCIOS PUBLICADOS</h2>
    <ul>
      <?php
      // Mostrar últimos 5 anuncios desde la BD
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
          // Fallback: anuncios predefinidos para pruebas
          $ultimos = [
            [
              'Titulo' => 'Piso luminoso en el centro',
              'FPrincipal' => 'anuncio1.jpg',
              'FRegistro' => date('Y-m-d'),
              'Ciudad' => 'Madrid',
              'NomPais' => 'España',
              'Precio' => 85000,
              'link' => '/phpDAW/DAW/practica/anuncio.html'
            ],
            [
              'Titulo' => 'Apartamento junto al parque',
              'FPrincipal' => 'anuncio2.jpg',
              'FRegistro' => date('Y-m-d'),
              'Ciudad' => 'Sevilla',
              'NomPais' => 'España',
              'Precio' => 120000,
              'link' => '/phpDAW/DAW/practica/anuncio.html'
            ]
          ];
      }

      foreach ($ultimos as $a) {
          $img = resolve_image_url($a['FPrincipal'] ?? '');
          $titulo = htmlspecialchars($a['Titulo'] ?? 'Sin título');
          $ciudad = htmlspecialchars($a['Ciudad'] ?? '—');
          $pais = htmlspecialchars($a['NomPais'] ?? '—');
          $precio = isset($a['Precio']) ? number_format((float)$a['Precio'], 2, ',', '.') . ' €' : '—';
          $link = isset($a['link']) ? $a['link'] : (isset($a['IdAnuncio']) ? "/phpDAW/anuncio/{$a['IdAnuncio']}" : '#');
          echo "<li><article><a href=\"{$link}\"><img src=\"{$img}\" alt=\"{$titulo}\" width=\"150\"><h3>{$titulo}</h3></a><p>Fecha: {$a['FRegistro']} | Ciudad: {$ciudad} <br>País: {$pais} | Precio: {$precio}</p></article></li>";
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
