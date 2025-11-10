<?php
    $title = "PI - PI Pisos & Inmuebles";
    $cssPagina = "registro.css";
    require_once("cabecera.inc");
    require_once(__DIR__ . '/privado.inc');
    require_once("inicioLog.inc");

    // definimos los dos anuncios de ejemplo
    $anunciosEjemplo = [
        1 => [
            'titulo' => 'Piso en venta',
        ],
        2 => [
            'titulo' => 'Apartamento en alquiler',
        ]
    ];

    // Si viene id en la URL, lo usamos para preseleccionar y bloquear el select
    $selectedId = isset($_GET['id']) && ctype_digit($_GET['id']) ? intval($_GET['id']) : null;

?>

<main>
    <section class="formulario-anadir-foto">
        <h2>Añadir foto a un anuncio</h2>

        <form action="procesar_anadir_foto.php" method="post" enctype="multipart/form-data">

            <p>
                <label for="id_anuncio">Anuncio:</label>
                <select name="id_anuncio" id="id_anuncio" <?php if ($selectedId !== null) echo 'disabled'; ?> >
                    <?php if ($selectedId === null): ?>
                        <option value="">-- Selecciona un anuncio --</option>
                    <?php endif; ?>
                    <?php foreach ($anunciosEjemplo as $id => $a): ?>
                        <option value="<?= (int)$id ?>" <?php if ($selectedId !== null && (int)$selectedId === (int)$id) echo 'selected'; ?>>
                            <?= htmlspecialchars($a['titulo'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($selectedId !== null): ?>
                    <input type="hidden" name="id_anuncio" value="<?= (int)$selectedId ?>">
                <?php endif; ?>
            </p>

            <p>
                <label for="alt">Texto alternativo (alt):</label>
                <input type="text" name="alt" id="alt" maxlength="150" placeholder="Texto alternativo para la imagen">
            </p>

            <p>
                <label for="titulo_foto">Título de la foto:</label>
                <input type="text" name="titulo_foto" id="titulo_foto" maxlength="150" placeholder="Título de la foto">
            </p>

            <p>
                <label for="foto">Foto (archivo):</label>
                <input type="file" name="foto" id="foto" accept="image/*">
            </p>

            <p>
                <button type="submit" class="btn">Enviar</button>
            </p>
        </form>
    </section>

    <?php require_once("salto.inc"); ?>
</main>

<?php require_once("pie.inc"); ?>
