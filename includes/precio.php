<?php
// includes/precio.php
// Funciones reutilizables para cálculo y formateo de precios de folletos.

if (!function_exists('calcularPrecio')) {
    function calcularPrecio($numPag, $color, $resolucion, $numCopias = 1){
        $numPag = (int)$numPag;
        $numCopias = max(1, (int)$numCopias);
        $resolucion = (int)$resolucion;
        $precioBase = 10.0;
        $precioPaginas = 0.0;
        $precioColor = 0.0;
        $precioResolucion = 0.0;
        $numFotos = $numPag * 3;

        // Cálculo del precio por páginas
        if ($numPag < 5) {
            $precioPaginas = $numPag * 2.0;
        } elseif ($numPag >= 5 && $numPag <= 10) {
            $precioPaginas = 4.0 * 2.0 + 1.8 * ($numPag - 4);
        } else {
            $precioPaginas = 4.0 * 2.0 + 6.0 * 1.8 + 1.6 * ($numPag - 10);
        }

        // Cálculo del precio por color
        if ($color) {
            $precioColor = $numFotos * 0.5;
        }

        // Cálculo del precio por resolución
        if ($resolucion > 300) {
            $precioResolucion = $numFotos * 0.2;
        }

        $precioUnitario = $precioBase + $precioPaginas + $precioColor + $precioResolucion;
        $precioTotal = $precioUnitario * $numCopias;
        return (float) $precioTotal;
    }
}

if (!function_exists('formatearPrecio')) {
    function formatearPrecio($valor){
        return number_format((float)$valor, 2, ',', '.') . ' €';
    }
}

?>