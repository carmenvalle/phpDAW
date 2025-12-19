<?php
// Grafico últimos 7 días (SVG inline) — sin GD, robusto y autocontenido

// 1) Preparar estructura de fechas (hoy y los 6 días anteriores)
$fechas = [];
for ($i = 6; $i >= 0; $i--) {
    $fechas[date('Y-m-d', strtotime("-{$i} day"))] = 0;
}

// 2) Intentar cargar datos desde la BD si existe conexión y columna FRegistro
$loaded = false;
try {
    require_once __DIR__ . '/conexion.php';
    if (isset($conexion)) {
        $sql = "SELECT DATE(FRegistro) AS dia, COUNT(*) AS total\n"
             . "FROM Fotos\n"
             . "WHERE FRegistro >= CURDATE() - INTERVAL 6 DAY\n"
             . "GROUP BY DATE(FRegistro)\n"
             . "ORDER BY dia ASC";
        $stmt = $conexion->query($sql);
        foreach ($stmt as $fila) {
            $d = $fila['dia'] ?? null;
            if ($d !== null && isset($fechas[$d])) {
                $fechas[$d] = (int) $fila['total'];
            }
        }
        $loaded = true;
    }
} catch (Throwable $e) {
    $loaded = false;
}

// 3) Fallback: recorrer carpeta de imágenes y contar por mtime del archivo
if (!$loaded) {
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

// 4) Construir SVG
$width = 600; $height = 300; $padding = 40; $barWidth = 40; $gap = 20;
$max = max($fechas);
if ($max <= 0) $max = 1;

$svg = "<svg width=\"$width\" height=\"$height\" role=\"img\" aria-label=\"Fotos subidas en los últimos 7 días\" xmlns=\"http://www.w3.org/2000/svg\">";
$svg .= "<rect width=\"100%\" height=\"100%\" fill=\"#fff\"/>";
$svg .= "<text x=\"20\" y=\"24\" font-family=\"sans-serif\" font-size=\"14\" fill=\"#333\">Fotos subidas en los últimos 7 días</text>";

// Eje X
$svg .= "<line x1=\"$padding\" y1=\"" . ($height - $padding) . "\" x2=\"" . ($width - $padding) . "\" y2=\"" . ($height - $padding) . "\" stroke=\"#aaa\"/>";

$x = $padding;
foreach ($fechas as $dia => $valor) {
    $h = (int)(($height - 2 * $padding) * ($valor / $max));
    $y = $height - $padding - $h;
    $label = substr($dia, 5);
    $svg .= "<rect x=\"$x\" y=\"$y\" width=\"$barWidth\" height=\"$h\" fill=\"#3c78c8\" rx=\"4\"><title>$dia: $valor</title></rect>";
    $svg .= "<text x=\"" . ($x + $barWidth/2) . "\" y=\"" . ($height - $padding + 16) . "\" text-anchor=\"middle\" font-family=\"sans-serif\" font-size=\"12\" fill=\"#555\">$label</text>";
    $svg .= "<text x=\"" . ($x + $barWidth/2) . "\" y=\"" . ($y - 6) . "\" text-anchor=\"middle\" font-family=\"sans-serif\" font-size=\"12\" fill=\"#333\">$valor</text>";
    $x += $barWidth + $gap;
}

$svg .= "</svg>";

// 5) Variable expuesta para la vista
$graficoSVG = $svg;