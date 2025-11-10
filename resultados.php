<?php
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "resultados.css";

// ==============================
// VALIDACIÓN EN PHP
// ==============================

// Guardamos errores y valores previos
$errors = [];
$old = [];

// Función auxiliar
function getParam($name) {
    return isset($_GET[$name]) ? trim($_GET[$name]) : '';
}

// Recogemos los valores del formulario
$campos = ['tipo_anuncio', 'vivienda', 'ciudad', 'pais', 'precio_min', 'precio_max', 'fecha_desde', 'fecha_hasta'];
foreach ($campos as $campo) {
    $old[$campo] = getParam($campo);
}

// Aceptar también el parámetro rápido 'q'
if (empty($old['ciudad']) && isset($_GET['q']) && trim((string)$_GET['q']) !== '') {
    $old['ciudad'] = trim((string)$_GET['q']);
}

// ==============================
// VALIDACIONES
// ==============================

$advancedFields = ['tipo_anuncio','vivienda','precio_min','precio_max','fecha_desde','fecha_hasta','pais'];
$shouldValidate = false;
foreach ($advancedFields as $f) {
    if (isset($_GET[$f]) && trim((string)$_GET[$f]) !== '') { $shouldValidate = true; break; }
}

if ($shouldValidate) {
    // Tipo de anuncio
    if ($old['tipo_anuncio'] === '') {
        $errors[] = 'tipo_anuncio';
        $old['msg_tipo_anuncio'] = 'Debes seleccionar un tipo de anuncio.';
    }

    // Precios
    if ($old['precio_min'] !== '' && (!is_numeric($old['precio_min']) || $old['precio_min'] < 0)) {
        $errors[] = 'precio_min';
        $old['msg_precio_min'] = 'El precio mínimo no puede ser negativo.';
    }
    if ($old['precio_max'] !== '' && (!is_numeric($old['precio_max']) || $old['precio_max'] < 0)) {
        $errors[] = 'precio_max';
        $old['msg_precio_max'] = 'El precio máximo no puede ser negativo.';
    }
    if ($old['precio_max'] !== '' && $old['precio_min'] === '') {
        $errors[] = 'precio_min';
        $old['msg_precio_min'] = 'Si indicas un precio máximo, debes indicar también el mínimo.';
    }
    if ($old['precio_min'] !== '' && $old['precio_max'] !== '' && $old['precio_min'] > $old['precio_max']) {
        $errors[] = 'precio_max';
        $old['msg_precio_max'] = 'El precio máximo no puede ser menor que el mínimo.';
    }

    // Fechas
    $hoy = date('Y-m-d');
    if ($old['fecha_hasta'] !== '' && $old['fecha_desde'] === '') {
        $errors[] = 'fecha_desde';
        $old['msg_fecha_desde'] = 'Si indicas una fecha final, debes indicar también la inicial.';
    }
    if ($old['fecha_desde'] !== '' && $old['fecha_hasta'] !== '' &&
        strtotime($old['fecha_desde']) > strtotime($old['fecha_hasta'])) {
        $errors[] = 'fecha_hasta';
        $old['msg_fecha_hasta'] = 'La fecha final no puede ser anterior a la inicial.';
    }
    if ($old['fecha_desde'] !== '' && strtotime($old['fecha_desde']) > strtotime($hoy)) {
        $errors[] = 'fecha_desde';
        $old['msg_fecha_desde'] = 'La fecha inicial no puede ser posterior a hoy.';
    }
    if ($old['fecha_hasta'] !== '' && strtotime($old['fecha_hasta']) > strtotime($hoy)) {
        $errors[] = 'fecha_hasta';
        $old['msg_fecha_hasta'] = 'La fecha final no puede ser posterior a hoy.';
    }
}

// ==============================
// REDIRECCIÓN SI HAY ERRORES
// ==============================
if (!empty($errors)) {
    $query = ['errors' => implode(',', $errors)];
    foreach ($old as $k => $v) {
        if ($v !== '') $query["old_" . $k] = $v;
    }
    header("Location: busqueda.php?" . http_build_query($query));
    exit;
}

require_once("cabecera.inc");
require_once("inicioLog.inc");
?>

<main>
    <h2>Resultados de la búsqueda</h2>
    <section>
        <article>
            <h2>Anuncio 1</h2>
            <a href="anuncio.php?id=1">
                <img src="DAW/practica/imagenes/consejos-para-vender-un-piso-en-madrid-01.jpg"
                     alt="Foto del anuncio 1" width="200" height="200">
            </a>

            <p><strong>TIPO DE ANUNCIO:</strong> <?= ucfirst($old['tipo_anuncio']); ?></p>
            <p><strong>TIPO DE VIVIENDA:</strong> <?= $old['vivienda'] ? ucfirst($old['vivienda']) : 'No especificada'; ?></p>
            <p><strong>CIUDAD:</strong> <?= $old['ciudad'] ? htmlspecialchars($old['ciudad']) : 'No especificada'; ?></p>
            <p><strong>PAÍS:</strong> <?= $old['pais'] ? strtoupper(htmlspecialchars($old['pais'])) : 'No especificado'; ?></p>
            <p><strong>FECHA PUBLICACIÓN:</strong>
                <?= $old['fecha_desde'] ?: '—'; ?> a <?= $old['fecha_hasta'] ?: '—'; ?>
            </p>
            <p><strong>PRECIO:</strong>
                <?= $old['precio_min'] !== '' ? htmlspecialchars($old['precio_min']) . ' €' : '—'; ?>
                <?php if ($old['precio_max'] !== ''): ?> - <?= htmlspecialchars($old['precio_max']) . ' €'; endif; ?>
            </p>
        </article>

        <article>
            <h2>Anuncio 2</h2>
            <a href="anuncio.php?id=2">
                <img src="DAW/practica/imagenes/anuncio2.jpg"
                     alt="Foto del anuncio 2" width="200" height="200">
            </a>

            <p><strong>TIPO DE ANUNCIO:</strong> <?= ucfirst($old['tipo_anuncio']); ?></p>
            <p><strong>TIPO DE VIVIENDA:</strong> <?= $old['vivienda'] ? ucfirst($old['vivienda']) : 'No especificada'; ?></p>
            <p><strong>CIUDAD:</strong> <?= $old['ciudad'] ? htmlspecialchars($old['ciudad']) : 'No especificada'; ?></p>
            <p><strong>PAÍS:</strong> <?= $old['pais'] ? strtoupper(htmlspecialchars($old['pais'])) : 'No especificado'; ?></p>
            <p><strong>FECHA PUBLICACIÓN:</strong>
                <?= $old['fecha_desde'] ?: '—'; ?> a <?= $old['fecha_hasta'] ?: '—'; ?>
            </p>
            <p><strong>PRECIO:</strong>
                <?= $old['precio_min'] !== '' ? htmlspecialchars($old['precio_min']) . ' €' : '—'; ?>
                <?php if ($old['precio_max'] !== ''): ?> - <?= htmlspecialchars($old['precio_max']) . ' €'; endif; ?>
            </p>
        </article>
    </section>

    <?php require_once("salto.inc"); ?>
</main>

<?php require_once("pie.inc"); ?>
