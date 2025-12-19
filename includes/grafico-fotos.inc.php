<?php
require_once __DIR__ . '/conexion.php';

/* =========================
   DATOS
   ========================= */
$datos = [];

try {
    $stmt = $conexion->query("
        SELECT DATE(FRegistro) AS dia, COUNT(*) AS total
        FROM Fotos
        WHERE FRegistro >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(FRegistro)
        ORDER BY dia ASC
    ");

    foreach ($stmt as $fila) {
        $datos[$fila['dia']] = (int)$fila['total'];
    }
} catch (Exception $e) {}

/* Asegurar 7 días */
$fechas = [];
for ($i = 6; $i >= 0; $i--) {
    $dia = date('Y-m-d', strtotime("-$i day"));
    $fechas[$dia] = $datos[$dia] ?? 0;
}

/* =========================
   CREAR IMAGEN
   ========================= */
$img = imagecreatetruecolor(600, 300);

$blanco = imagecolorallocate($img, 255, 255, 255);
$negro  = imagecolorallocate($img, 0, 0, 0);
$azul   = imagecolorallocate($img, 60, 120, 200);

imagefill($img, 0, 0, $blanco);

imagestring($img, 5, 150, 10, 'Fotos subidas en los ultimos 7 dias', $negro);

$margen = 50;
$anchoBarra = 40;
$espacio = 20;

$max = max($fechas);
$max = ($max == 0) ? 1 : $max;

/* Barras */
$i = 0;
foreach ($fechas as $dia => $valor) {

    $x1 = $margen + ($anchoBarra + $espacio) * $i;
    $x2 = $x1 + $anchoBarra;

    $altura = ($valor / $max) * 180;
    $y1 = 250 - $altura;
    $y2 = 250;

    imagefilledrectangle($img, $x1, $y1, $x2, $y2, $azul);
    imagestring($img, 3, $x1 + 10, $y1 - 15, (string)$valor, $negro);
    imagestring($img, 2, $x1, 260, substr($dia, 5), $negro);

    $i++;
}

/* =========================
   BASE64
   ========================= */
ob_start();
imagepng($img);
$imgData = base64_encode(ob_get_clean());
imagedestroy($img);

$graficoBase64 = 'data:image/png;base64,' . $imgData;
?>