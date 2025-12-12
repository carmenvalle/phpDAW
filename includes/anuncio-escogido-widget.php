<?php
// Widget reutilizable: Anuncio escogido
// Requiere: $conexion (PDO) y resolve_image_url() disponibles en el scope

if (!isset($conexion)) {
    return;
}

$ficheroAE = __DIR__ . '/anuncio-escogido.txt';

if (!file_exists($ficheroAE)) {
    echo "<p>No hay anuncios escogidos disponibles.</p>";
} else {
    $lineas = file($ficheroAE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (!$lineas) {
        echo "<p>El fichero de anuncios escogidos está vacío.</p>";
    } else {
        // Opción 3: Reintentar hasta 5 veces para encontrar un anuncio válido
        $maxIntentos = 5;
        $anEscogido = null;
        $expertoAE = null;
        $comentarioAE = null;

        for ($intento = 0; $intento < $maxIntentos; $intento++) {
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

                // Si encontramos uno válido, salir del bucle
                if ($anEscogido) {
                    break;
                }
            }
        }

        // Mostrar resultado
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
            echo "<p>No hay anuncios destacados disponibles en este momento.</p>";
        }
    }
}
?>
