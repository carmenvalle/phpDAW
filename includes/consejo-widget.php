<?php
// Widget reutilizable: Consejo de compra/venta
// Requiere: nada específico, se puede incluir en cualquier lugar

$ficheroConsejo = __DIR__ . '/consejos.json';

if (file_exists($ficheroConsejo)) {
    $json = file_get_contents($ficheroConsejo);
    $listaConsejos = json_decode($json, true);

    // Comprobar que es un array válido
    if (is_array($listaConsejos) && count($listaConsejos) > 0) {

        // Seleccionar aleatoriamente 1
        $indice = array_rand($listaConsejos);
        $c = $listaConsejos[$indice];

        // Mostrar consejo
        echo "<article class='consejo-box'>";
        echo "<p><strong>Categoría:</strong> " . htmlspecialchars($c['categoria']) . "</p>";
        echo "<p><strong>Importancia:</strong> " . htmlspecialchars($c['importancia']) . "</p>";
        echo "<p><strong>Descripción:</strong> " . htmlspecialchars($c['descripcion']) . "</p>";
        echo "</article>";
    } else {
        echo "<p>No hay consejos disponibles actualmente.</p>";
    }
} else {
    echo "<p>El fichero de consejos no existe.</p>";
}
?>
