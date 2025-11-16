<?php
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "nuevo_anuncio.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
// Inicializar $valores: usar flash si existe, si no usar $_POST o valores por defecto
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$valores = [
    'tipo_anuncio' => '',
    'vivienda' => '',
    'titulo' => '',
    'ciudad' => '',
    'pais' => '',
    'precio' => '',
    'descripcion' => '',
    'superficie' => '',
    'habitaciones' => '',
    'banos' => '',
    'planta' => '',
    'anio' => ''
];
if (isset($_SESSION['flash']['nuevo_anuncio_old'])) {
    $old = $_SESSION['flash']['nuevo_anuncio_old'];
    foreach ($valores as $k => $_) {
        if (isset($old[$k])) $valores[$k] = $old[$k];
    }
    unset($_SESSION['flash']['nuevo_anuncio_old'], $_SESSION['flash']['nuevo_anuncio_errors']);
} else {
    // Poblar desde POST si está presente
    foreach ($valores as $k => $_) {
        if (isset($_POST[$k])) $valores[$k] = trim($_POST[$k]);
    }
}
?>

<main>
    <section>
        <h2>CREAR NUEVO ANUNCIO</h2>
    </section>

    <section>
        <form id="formNuevoAnuncio" action="procesar_nuevo_anuncio.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Tipo de anuncio</legend>
                <?php
                
                $tiposAnuncio = [];
                try {
                    if(!isset($conexion)) require_once __DIR__ . '/includes/conexion.php';
                    $rs1 = $conexion->query('SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio');
                    $tiposAnuncio = $rs1->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $tiposAnuncio = [];
                }
                foreach ($tiposAnuncio as $ta) {
                    $valor = $ta['IdTAnuncio'];
                    $texto = htmlspecialchars($ta['NomTAnuncio']);
                    $checked = (isset($valores["tipo_anuncio"]) && $valores["tipo_anuncio"] == $valor) ? "checked" : "";
                    echo "<label><input type='radio' name='tipo_anuncio' value='$valor' $checked> $texto</label><br>";
                }
                ?>
            </fieldset>

            <p>
                <label for="titulo">Título del anuncio:</label><br>
                <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($valores['titulo'] ?? ''); ?>">
            </p>

            <p>
                <label for="vivienda">Tipo de vivienda:</label><br>
                <select id="vivienda" name="vivienda">
                    <option value="">Seleccione un tipo de vivienda</option>
                    <?php
                    // Cargar tipos de vivienda desde BD
                    $tiposV = [];
                    try {
                        if (!isset($conexion)) require_once __DIR__ . '/includes/conexion.php';
                        $rs2 = $conexion->query('SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda');
                        $tiposV = $rs2->fetchAll(PDO::FETCH_ASSOC);
                    } catch (Exception $e) {
                        $tiposV = [];
                    }
                    foreach ($tiposV as $t) {
                        $valor = $t['IdTVivienda'];
                        $texto = htmlspecialchars($t['NomTVivienda']);
                        $sel = (isset($valores["vivienda"]) && $valores["vivienda"] == $valor) ? "selected" : "";
                        echo "<option value='$valor' $sel>$texto</option>";
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="ciudad">Ciudad:</label><br>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($valores['ciudad'] ?? ''); ?>">
            </p>

            <p>
                <label for="pais">País:</label><br>
                <select id="pais" name="pais">
                    <option value="">Seleccione un país</option>
                    <?php
                    // Cargar países desde la BD
                    $paisesDb = [];
                    try {
                        require_once __DIR__ . '/includes/conexion.php';
                        $rs = $conexion->query('SELECT IdPais, NomPais FROM Paises ORDER BY NomPais');
                        $paisesDb = $rs->fetchAll(PDO::FETCH_ASSOC);
                    } catch (Exception $e) {
                        $paisesDb = [];
                    }
                    foreach ($paisesDb as $p) {
                        $valor = $p['IdPais'];
                        $texto = htmlspecialchars($p['NomPais']);
                        $sel = (isset($valores["pais"]) && $valores["pais"] == $valor) ? "selected" : "";
                        echo "<option value='$valor' $sel>$texto</option>";
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="precio">Precio (€):</label><br>
                <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($valores['precio'] ?? ''); ?>">
            </p>

            <!-- Fecha de publicación se asigna automáticamente -->

            <p>
                <label for="descripcion">Descripción:</label><br>
                <textarea id="descripcion" name="descripcion" rows="6"><?php echo htmlspecialchars($valores['descripcion'] ?? ''); ?></textarea>
            </p>

            <fieldset>
                <legend>Características</legend>
                <p>
                    <label for="superficie">Superficie (m²):</label><br>
                    <input type="number" id="superficie" name="superficie" value="<?php echo htmlspecialchars($valores['superficie'] ?? ''); ?>">
                </p>
                <p>
                    <label for="habitaciones">Habitaciones:</label><br>
                    <input type="number" id="habitaciones" name="habitaciones" value="<?php echo htmlspecialchars($valores['habitaciones'] ?? ''); ?>">
                </p>
                <p>
                    <label for="banos">Baños:</label><br>
                    <input type="number" id="banos" name="banos" value="<?php echo htmlspecialchars($valores['banos'] ?? ''); ?>">
                </p>
                <p>
                    <label for="planta">Planta:</label><br>
                    <input type="number" id="planta" name="planta" value="<?php echo htmlspecialchars($valores['planta'] ?? ''); ?>">
                </p>
                <p>
                    <label for="anio">Año de construcción:</label><br>
                    <input type="number" id="anio" name="anio" min="1800" max="2100" value="<?php echo htmlspecialchars($valores['anio'] ?? ''); ?>">
                </p>
            </fieldset>

            <p>
                <label for="imagenes">Imágenes (puede seleccionar varias):</label><br>
                <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
            </p>

            <!-- Usuario se toma de la sesión, no se solicita en el formulario -->

            <p>
                <button type="submit">PUBLICAR ANUNCIO</button>
            </p>
        </form>
    </section>

    <?php
    require_once("salto.inc");
    ?>

</main>
<?php
require_once("pie.inc");
?>