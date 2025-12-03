<?php
// Formulario reutilizable para crear/modificar anuncios.
// Variables esperadas por el include:
// - $valores: array con campos del anuncio (titulo, descripcion, tipo_anuncio, vivienda, ciudad, pais, precio, superficie, habitaciones, banos, planta, anio)
// - $errors: array con claves de errores opcionales
// - $form_action: URL a la que enviará el formulario (por defecto 'procesar_nuevo_anuncio.php')
// Si las opciones no están cargadas, el formulario las cargará desde la BD.
if (!isset($form_action)) $form_action = 'procesar_nuevo_anuncio.php';
if (!isset($valores) || !is_array($valores)) $valores = [];
if (!isset($errors) || !is_array($errors)) $errors = [];

// Cargar opciones si es necesario
if (!isset($conexion)) {
    if (file_exists(__DIR__ . '/conexion.php')) require_once __DIR__ . '/conexion.php';
}

$tiposAnuncio = [];
$tiposV = [];
$paisesDb = [];
try {
    if (isset($conexion)) {
        $rs1 = $conexion->query('SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio');
        $tiposAnuncio = $rs1->fetchAll(PDO::FETCH_ASSOC);

        $rs2 = $conexion->query('SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda');
        $tiposV = $rs2->fetchAll(PDO::FETCH_ASSOC);

        $rs3 = $conexion->query('SELECT IdPaises AS IdPais, NomPais FROM Paises ORDER BY NomPais');
        $paisesDb = $rs3->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // ignore and keep empty lists
}
?>

<?php if (!empty($errors)): ?>
    <div class="errores">
        <p><strong>Corrige los siguientes errores:</strong></p>
        <ul>
                    <?php foreach ($errors as $e): ?>
                        <?php if ($e === 'titulo'): ?><li>El título es obligatorio.</li><?php endif; ?>
                        <?php if ($e === 'descripcion'): ?><li>La descripción es obligatoria.</li><?php endif; ?>
                        <?php if ($e === 'usuario'): ?><li>No hay usuario identificado. Inicia sesión.</li><?php endif; ?>
                        <?php if ($e === 'imagenes'): ?><li>Debes subir al menos una imagen válida (JPG/PNG/GIF/WEBP).</li><?php endif; ?>
                        <?php if ($e === 'superficie_negative'): ?><li>La superficie no puede ser negativa.</li><?php endif; ?>
                        <?php if ($e === 'habitaciones_negative'): ?><li>El número de habitaciones no puede ser negativo.</li><?php endif; ?>
                        <?php if ($e === 'banos_negative'): ?><li>El número de baños no puede ser negativo.</li><?php endif; ?>
                        <?php if ($e === 'planta_negative'): ?><li>La planta no puede ser negativa.</li><?php endif; ?>
                        <?php if ($e === 'anio_invalid'): ?><li>El año debe estar entre 1800 y 2100.</li><?php endif; ?>
                    <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form id="formNuevoAnuncio" action="<?= htmlspecialchars($form_action, ENT_QUOTES, 'UTF-8') ?>" method="post" enctype="multipart/form-data" novalidate>
    <fieldset>
        <legend>Tipo de anuncio</legend>
        <?php foreach ($tiposAnuncio as $ta):
            $valor = $ta['IdTAnuncio'];
            $texto = htmlspecialchars($ta['NomTAnuncio']);
            $checked = (isset($valores['tipo_anuncio']) && $valores['tipo_anuncio'] == $valor) ? 'checked' : '';
        ?>
            <label><input type="radio" name="tipo_anuncio" value="<?= $valor ?>" <?= $checked ?>> <?= $texto ?></label>
        <?php endforeach; ?>
    </fieldset>

    <p>
        <label for="titulo">Título del anuncio:</label><br>
        <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($valores['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label for="vivienda">Tipo de vivienda:</label><br>
        <select id="vivienda" name="vivienda">
            <option value="">Seleccione un tipo de vivienda</option>
            <?php foreach ($tiposV as $t):
                $valor = $t['IdTVivienda'];
                $texto = htmlspecialchars($t['NomTVivienda']);
                $sel = (isset($valores['vivienda']) && $valores['vivienda'] == $valor) ? 'selected' : '';
            ?>
                <option value="<?= $valor ?>" <?= $sel ?>><?= $texto ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="ciudad">Ciudad:</label><br>
        <input type="text" id="ciudad" name="ciudad" value="<?= htmlspecialchars($valores['ciudad'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label for="pais">País:</label><br>
        <select id="pais" name="pais">
            <option value="">Seleccione un país</option>
            <?php foreach ($paisesDb as $p):
                $valor = $p['IdPais'];
                $texto = htmlspecialchars($p['NomPais']);
                $sel = (isset($valores['pais']) && $valores['pais'] == $valor) ? 'selected' : '';
            ?>
                <option value="<?= $valor ?>" <?= $sel ?>><?= $texto ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="precio">Precio (€):</label><br>
        <input type="number" id="precio" name="precio" step="1000" min="0" value="<?= htmlspecialchars($valores['precio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="6"><?= htmlspecialchars($valores['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </p>

    <fieldset>
        <legend>Características</legend>
        <p>
            <label for="superficie">Superficie (m²):</label><br>
            <input type="number" id="superficie" name="superficie" min="0" value="<?= htmlspecialchars($valores['superficie'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </p>
        <p>
            <label for="habitaciones">Habitaciones:</label><br>
            <input type="number" id="habitaciones" name="habitaciones" min="0" value="<?= htmlspecialchars($valores['habitaciones'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </p>
        <p>
            <label for="banos">Baños:</label><br>
            <input type="number" id="banos" name="banos" min="0" value="<?= htmlspecialchars($valores['banos'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </p>
        <p>
            <label for="planta">Planta:</label><br>
            <input type="number" id="planta" name="planta" min="0" value="<?= htmlspecialchars($valores['planta'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </p>
        <p>
            <label for="anio">Año de construcción:</label><br>
            <input type="number" id="anio" name="anio" min="1800" max="2100" value="<?= htmlspecialchars($valores['anio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </p>
    </fieldset>

    <p>
        <label for="imagenes">Imágenes (puede seleccionar varias):</label><br>
        <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple <?= empty($valores['imagenes_required']) ? '' : 'required' ?>>
    </p>

    <p>
        <button type="submit"><strong>PUBLICAR</strong></button>
    </p>
</form>
