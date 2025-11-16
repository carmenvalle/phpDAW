<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="author" content="Silvia Balmaseda y Carmen Valle">
  <meta name="description" content="Página principal de PI - Pisos e Inmuebles">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="DAW/practica/fontello/css/fontello.css">
  <link rel="stylesheet" href="DAW/practica/css/general.css" media="screen">
  <?php
  // CSS específico de cada página
  if (isset($cssPagina) && $cssPagina != '') {
      echo '<link rel="stylesheet" href="DAW/practica/css/' . $cssPagina . '" media="screen">';
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

  if ($selected_key && $selected_key !== 'default') {
    $cssfile = $theme_map[$selected_key];
    if ($cssfile) {
      echo '<link rel="stylesheet" href="DAW/practica/css/' . htmlspecialchars($cssfile, ENT_QUOTES, 'UTF-8') . '" media="screen">';
    }
  }
  ?>
  <link rel="stylesheet" type="text/css" href="DAW/practica/css/print-index.css" media="print">

  <link rel="alternate stylesheet" href="DAW/practica/css/modo_oscuro.css" title="Modo oscuro">
  <link rel="alternate stylesheet" href="DAW/practica/css/alto-contraste.css" title="Alto contraste" media="screen">
  <link rel="alternate stylesheet" href="DAW/practica/css/accesibilidad.css" title="Letra grande" media="screen">
  <link rel="alternate stylesheet" href="DAW/practica/css/alto-contraste-accesibilidad.css" title="Alto contraste y accesibilidad" media="screen">

  <title><?php echo $title; ?></title>
</head>

<body>
