<?php
if (!isset($_GET['idFoto']) || !isset($_GET['idAnuncio'])) {
    die("Error: parámetro faltante");
}

$idFoto = (int)$_GET['idFoto'];
$idAnuncio = (int)$_GET['idAnuncio'];

$title = "Confirmar eliminación";
require_once("cabecera.inc");
require_once("inicioLog.inc");
// Enlazar estilos específicos para esta página
echo '<link rel="stylesheet" href="DAW/practica/css/eliminar-foto.css">';
?>

<main class="ef-main-center">
    <div class="ef-card">
        <h2 class="ef-title">Eliminar foto</h2>
        <p>¿Seguro que deseas eliminar esta foto? Esta acción no se puede deshacer.</p>

        <form method="post" action="respuesta-eliminar-foto.php">
            <input type="hidden" name="idFoto" value="<?php echo $idFoto; ?>">
            <input type="hidden" name="idAnuncio" value="<?php echo $idAnuncio; ?>">
            <div class="ef-actions">
                <button type="submit" class="ef-btn ef-btn-danger">
                    Eliminar
                </button>

                <a href="ver_fotos.php?id=<?php echo $idAnuncio; ?>" class="ef-btn ef-btn-cancel">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once("pie.inc"); ?>
