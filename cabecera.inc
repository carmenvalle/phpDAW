<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="author" content="Silvia Balmaseda y Carmen Valle">
  <meta name="description" content="Página principal de PI - Pisos e Inmuebles">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/phpDAW/DAW/practica/fontello/css/fontello.css">
  <link rel="stylesheet" href="/phpDAW/DAW/practica/css/general.css" media="screen">
  <?php
  // CSS específico de cada página
  if (isset($cssPagina) && $cssPagina != '') {
      echo '<link rel="stylesheet" href="/phpDAW/DAW/practica/css/' . $cssPagina . '" media="screen">';
  }
  ?>
  <?php
  // Aplicar el estilo seleccionado del usuario (guardado en la sesión)
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
  // Whitelist: clave => fichero CSS. Mantener sincronía con configurar.php
  $theme_map = [
    'default' => '',
    'modo_oscuro' => 'modo_oscuro.css',
    'letra_grande' => 'accesibilidad.css',
    'alto_contraste' => 'alto-contraste.css',
    'contraste_letra' => 'alto-contraste-accesibilidad.css'
  ];

  // Determinar selección: prioridad sesión, luego cookie
  $selected_key = null;
  if (!empty($_SESSION['style']) && array_key_exists($_SESSION['style'], $theme_map)) {
    $selected_key = $_SESSION['style'];
  } elseif (!empty($_COOKIE['style']) && array_key_exists($_COOKIE['style'], $theme_map)) {
    $selected_key = $_COOKIE['style'];
  }

  // Si no hay coincidencia y el valor es numérico (IdEstilo), intentar mapearlo consultando la BD
  if ($selected_key === null) {
    $numeric = null;
    if (!empty($_SESSION['style']) && ctype_digit((string)$_SESSION['style'])) $numeric = (int)$_SESSION['style'];
    if ($numeric === null && !empty($_COOKIE['style']) && ctype_digit((string)$_COOKIE['style'])) $numeric = (int)$_COOKIE['style'];
    if ($numeric !== null) {
      // intentar recuperar el fichero asociado a ese IdEstilo
      $fich = null;
      if (file_exists(__DIR__ . '/includes/conexion.php')) {
        try {
          require_once __DIR__ . '/includes/conexion.php';
          $st = $conexion->prepare('SELECT Fichero FROM Estilos WHERE IdEstilo = ? LIMIT 1');
          $st->execute([$numeric]);
          $r = $st->fetch(PDO::FETCH_ASSOC);
          if ($r && !empty($r['Fichero'])) $fich = $r['Fichero'];
        } catch (Exception $e) {
          $fich = null;
        }
      }
      if ($fich) {
        // intentar obtener también el nombre de estilo y normalizarlo a clave
        try {
          $st2 = $conexion->prepare('SELECT Nombre FROM Estilos WHERE IdEstilo = ? LIMIT 1');
          $st2->execute([$numeric]);
          $r2 = $st2->fetch(PDO::FETCH_ASSOC);
          if ($r2 && !empty($r2['Nombre'])) {
            $cand = mb_strtolower($r2['Nombre']);
            // normalizar: letras, numeros y guiones bajos
            $cand = preg_replace('/[^\p{L}0-9]+/u', '_', $cand);
            $cand = trim($cand, '_');
            if (array_key_exists($cand, $theme_map)) {
              $selected_key = $cand;
            }
          }
        } catch (Exception $e) {
          // ignore
        }

        if ($selected_key === null) {
          // buscar la clave en $theme_map cuyo valor (fichero) coincida con el fichero
          foreach ($theme_map as $k => $v) {
            if ($v === '') continue;
            // normalizar nombres: convertir '_' <-> '-' para intentar coincidencias
            $vn = basename($v);
            $fn = basename($fich);
            if ($vn === $fn || str_replace('_','-',$vn) === str_replace('_','-',$fn) || str_replace('-','_',$vn) === str_replace('-','_',$fn)) {
              $selected_key = $k;
              break;
            }
          }
        }
      }
    }
  }

  if ($selected_key && $selected_key !== 'default') {
    $cssfile = $theme_map[$selected_key];
    if ($cssfile) {
      echo '<link rel="stylesheet" href="/phpDAW/DAW/practica/css/' . htmlspecialchars($cssfile, ENT_QUOTES, 'UTF-8') . '" media="screen">';
    }
  }

  // Cargar estilos adicionales solicitados vía checkboxes (sesión o cookie)
  $use_dark = !empty($_SESSION['modo_oscuro']) || (!empty($_COOKIE['modo_oscuro']) && $_COOKIE['modo_oscuro'] === '1');
  $use_accplus = !empty($_SESSION['accesibilidad_plus']) || (!empty($_COOKIE['accesibilidad_plus']) && $_COOKIE['accesibilidad_plus'] === '1');

  // Evitar duplicar si el estilo seleccionado ya incluye la funcionalidad
  if ($use_dark) {
    if ($selected_key !== 'modo_oscuro') {
      echo '<link rel="stylesheet" href="/phpDAW/DAW/practica/css/modo_oscuro.css" media="screen">';
    }
  }
  if ($use_accplus) {
    if ($selected_key !== 'contraste_letra') {
      echo '<link rel="stylesheet" href="/phpDAW/DAW/practica/css/alto-contraste-accesibilidad.css" media="screen">';
    }
  }
  ?>
  <link rel="stylesheet" type="text/css" href="/phpDAW/DAW/practica/css/print-index.css" media="print">

  <link rel="alternate stylesheet" href="/phpDAW/DAW/practica/css/modo_oscuro.css" title="Modo oscuro">
  <link rel="alternate stylesheet" href="/phpDAW/DAW/practica/css/alto-contraste.css" title="Alto contraste" media="screen">
  <link rel="alternate stylesheet" href="/phpDAW/DAW/practica/css/accesibilidad.css" title="Letra grande" media="screen">
  <link rel="alternate stylesheet" href="/phpDAW/DAW/practica/css/alto-contraste-accesibilidad.css" title="Alto contraste y accesibilidad" media="screen">

  <title><?php echo $title; ?></title>
</head>

<body>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// Global flash messages (ok / error / details)
if (!empty($_SESSION['flash']) && is_array($_SESSION['flash'])) {
  $ok = $_SESSION['flash']['ok'] ?? null;
  $err = $_SESSION['flash']['error'] ?? null;
  $detail = $_SESSION['flash']['anuncio_modificado_detalle'] ?? null;
  if ($ok) {
    echo '<div class="flash flash-ok" style="max-width:1100px;margin:12px auto;padding:10px;border-radius:6px;background:#d6f5d6;color:#155724;text-align:center;">' . htmlspecialchars($ok, ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['flash']['ok']);
  }
  if ($err) {
    echo '<div class="flash flash-error" style="max-width:1100px;margin:12px auto;padding:10px;border-radius:6px;background:#fdecea;color:#7a1f1f;text-align:center;">' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['flash']['error']);
  }
  if (!empty($detail) && is_array($detail)) {
    echo '<div class="flash flash-detail" style="max-width:1100px;margin:12px auto;padding:10px;border-radius:6px;background:#eef7ff;color:#0b3d91;">';
    echo '<strong>Detalle de cambios:</strong><ul style="margin:8px 0 0;">';
    foreach ($detail as $d) {
      echo '<li>' . htmlspecialchars($d, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul></div>';
    unset($_SESSION['flash']['anuncio_modificado_detalle']);
  }
}
?>
