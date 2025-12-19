<?php
// Genera un SVG embebido con el número de fotografías subidas
// en los últimos 7 días, sin usar la extensión GD.

// Fechas de los últimos 7 días (de hace 6 días a hoy)
$fechas = [];
for ($i = 6; $i >= 0; $i--) {
    $fechas[date('Y-m-d', strtotime("-{$i} day"))] = 0;
}

// Intentar contar por BD (si está disponible Fotos.FRegistro)
$ok = false;
try {
    require_once __DIR__ . '/conexion.php';
    if (isset($conexion)) {
        $sql =
            "SELECT DATE(FRegistro) AS dia, COUNT(*) AS total\n" .
            "FROM Fotos\n" .
            "WHERE FRegistro >= CURDATE() - INTERVAL 6 DAY\n" .
            "GROUP BY DATE(FRegistro)\n" .
            "ORDER BY dia ASC";
        $stmt = $conexion->query($sql);
        foreach ($stmt as $fila) {
            $d = $fila['dia'];
            if (isset($fechas[$d])) {
                $fechas[$d] = (int)$fila['total'];
            }
        }
        $ok = true;
    }
} catch (Throwable $e) {
    $ok = false;
}

// Fallback: contar por fecha de modificación de archivos
if (!$ok) {
    $dir = dirname(__DIR__) . '/DAW/practica/imagenes';
    $exts = ['jpg','jpeg','png','gif','webp'];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $f) {
            if ($f === '.' || $f === '..') continue;
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (!in_array($ext, $exts, true)) continue;
            $full = $dir . '/' . $f;
            if (!is_file($full)) continue;
            $mtime = @filemtime($full);
            if ($mtime === false) continue;
            $d = date('Y-m-d', $mtime);
            if (isset($fechas[$d])) $fechas[$d]++;
        }
    }
}

// Renderizar SVG más atractivo
$width = 700; $height = 340; $padding = 48; $barWidth = 48; $gap = 18;
$sum = array_sum($fechas);
$max = max($fechas);
if ($max <= 0) $max = 1;

// Ejes y rejilla "bonitos": normalizamos a 4 tramos
$ticks = 4;
$stepVal = max(1, (int)ceil($max / $ticks));
$scaleMax = $stepVal * $ticks; // valor máximo para escalar
$chartH = $height - 2 * $padding;

$svg = "<svg viewBox=\"0 0 $width $height\" width=\"100%\" height=\"auto\" role=\"img\" aria-label=\"Fotos subidas en los últimos 7 días\" xmlns=\"http://www.w3.org/2000/svg\">";
$svg .= "<defs>\n"
      . "  <linearGradient id=\"barGrad\" x1=\"0\" y1=\"0\" x2=\"0\" y2=\"1\">\n"
      . "    <stop offset=\"0%\" stop-color=\"#4f8df5\"/>\n"
      . "    <stop offset=\"100%\" stop-color=\"#2d6cdf\"/>\n"
      . "  </linearGradient>\n"
      . "  <filter id=\"shadow\" x=\"-20%\" y=\"-20%\" width=\"140%\" height=\"140%\">\n"
      . "    <feDropShadow dx=\"0\" dy=\"1\" stdDeviation=\"2\" flood-color=\"#000\" flood-opacity=\"0.2\"/>\n"
      . "  </filter>\n"
      . "  <style><![CDATA[\n"
      . "    .title { font-family: sans-serif; font-size: 15px; fill: #222; }\n"
      . "    .label { font-family: sans-serif; font-size: 12px; fill: #555; }\n"
      . "    .value { font-family: sans-serif; font-size: 12px; fill: #222; }\n"
      . "    .grid  { stroke: #e6e6e6; stroke-width: 1; }\n"
      . "    .axis  { stroke: #bfbfbf; stroke-width: 1; }\n"
      . "    .bar   { fill: url(#barGrad); transition: opacity .2s ease; }\n"
      . "    .bar:hover { opacity: .9; }\n"
      . "  ]]></style>\n"
      . "</defs>";

$svg .= "<rect width=\"100%\" height=\"100%\" fill=\"#fff\" rx=\"8\"/>";
$svg .= "<text class=\"title\" x=\"$padding\" y=\"28\">Fotos subidas en los últimos 7 días</text>";

// Rejilla y ejes
for ($i = 0; $i <= $ticks; $i++) {
    $yy = $height - $padding - (int)round($chartH * ($i / $ticks));
    $svg .= "<line class=\"grid\" x1=\"$padding\" y1=\"$yy\" x2=\"" . ($width - $padding) . "\" y2=\"$yy\"/>";
    $val = $i * $stepVal;
    $svg .= "<text class=\"label\" x=\"" . ($padding - 10) . "\" y=\"" . ($yy + 4) . "\" text-anchor=\"end\">$val</text>";
}
$svg .= "<line class=\"axis\" x1=\"$padding\" y1=\"" . ($height - $padding) . "\" x2=\"" . ($width - $padding) . "\" y2=\"" . ($height - $padding) . "\"/>";

// Barras
$x = $padding + 6; // pequeño desplazamiento para estética
$index = 0;
foreach ($fechas as $dia => $valor) {
    $h = (int)round($chartH * ($valor / $scaleMax));
    $y = $height - $padding - $h;
    $label = substr($dia, 5); // mm-dd
    $delay = 0.04 * $index; // escalonado suave

    $svg .= "<g filter=\"url(#shadow)\">";
    $svg .= "<rect class=\"bar\" x=\"$x\" y=\"$y\" width=\"$barWidth\" height=\"$h\" rx=\"5\">"
          . "<title>$dia — $valor foto(s)</title>"
          . "<animate attributeName=\"height\" from=\"0\" to=\"$h\" dur=\"0.4s\" begin=\"$delay" . "s\" fill=\"freeze\"/>"
          . "<animate attributeName=\"y\" from=\"" . ($height - $padding) . "\" to=\"$y\" dur=\"0.4s\" begin=\"$delay" . "s\" fill=\"freeze\"/>"
          . "</rect>";
    $svg .= "</g>";

    $svg .= "<text class=\"label\" x=\"" . ($x + $barWidth/2) . "\" y=\"" . ($height - $padding + 18) . "\" text-anchor=\"middle\">$label</text>";
    $svg .= "<text class=\"value\" x=\"" . ($x + $barWidth/2) . "\" y=\"" . ($y - 6) . "\" text-anchor=\"middle\">$valor</text>";

    $x += $barWidth + $gap;
    $index++;
}

// Mensaje cuando no hay datos
if ($sum === 0) {
    $svg .= "<text class=\"label\" x=\"" . ($width/2) . "\" y=\"" . ($height/2) . "\" text-anchor=\"middle\" fill=\"#888\">Sin datos recientes</text>";
}

$svg .= "</svg>";

$graficoSVG = $svg;
?>