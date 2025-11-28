<?php
if (!isset($_GET['idFoto']) || !isset($_GET['idAnuncio'])) {
    die("Error: parámetro faltante");
}

$idFoto = (int)$_GET['idFoto'];
$idAnuncio = (int)$_GET['idAnuncio'];

$title = "Confirmar eliminación";
require_once("cabecera.inc");
require_once("inicioLog.inc");
?>

<main style="display:flex;justify-content:center;align-items:center;height:80vh;">
    <div style="background:white;padding:30px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.15);text-align:center;max-width:420px;">
        <h2 style="margin-top:0;">Eliminar foto</h2>
        <p>¿Seguro que deseas eliminar esta foto? Esta acción no se puede deshacer.</p>

        <form method="post" action="respuesta-eliminar-foto.php">
            <input type="hidden" name="idFoto" value="<?php echo $idFoto; ?>">
            <input type="hidden" name="idAnuncio" value="<?php echo $idAnuncio; ?>">

            <button type="submit" style="background:#c00;color:white;padding:10px 25px;border:none;border-radius:6px;margin-right:10px;cursor:pointer;">
                Eliminar
            </button>

            <a href="ver_fotos.php?id=<?php echo $idAnuncio; ?>"
               style="background:#555;color:white;padding:10px 25px;border-radius:6px;text-decoration:none;">
                Cancelar
            </a>
        </form>
    </div>
</main>

<?php require_once("pie.inc"); ?>
