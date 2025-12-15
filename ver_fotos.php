<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    echo 'Acceso no autorizado.';
    exit;
}

$title = "PI - Ver fotos";
$cssPagina = "miperfil.css";

require_once("cabecera.inc");
require_once(__DIR__ . "/privado.inc");
require_once("inicioLog.inc");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/ver_fotos_common.php';
require_once __DIR__ . '/includes/precio.php';
require_once __DIR__ . '/includes/conexion.php';

/* ==========================
   PAGINACIÓN
   ========================== */
$paginaActual = isset($_GET['page']) && is_numeric($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$limite = FOTOS_POR_PAGINA;
$offset = ($paginaActual - 1) * $limite;

/* ==========================
   DATOS DEL ANUNCIO
   ========================== */
$anuncio = $vf_anuncio;

if (!$anuncio) {
    echo "<main><p style='color:red;'>Anuncio no encontrado.</p></main>";
    require_once("pie.inc");
    exit();
}

/* ==========================
   TOTAL DE FOTOS
   ========================== */
$totalFotos = $vf_total;
$totalPaginas = max(1, ceil($totalFotos / $limite));

/* ==========================
   FOTOS PAGINADAS
   ========================== */
$fotos = [];

if ($totalFotos > 0) {
    $stmt = $conexion->prepare(
        "SELECT IdFoto, Foto, Titulo, Alternativo
         FROM fotos
         WHERE Anuncio = ?
         ORDER BY IdFoto DESC
         LIMIT ? OFFSET ?"
    );

    $stmt->bindValue(1, $anuncio['IdAnuncio'], PDO::PARAM_INT);
    $stmt->bindValue(2, $limite, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $fotos = $stmt->fetchAll();
}

/* ==========================
   ELIMINAR FOTO
   ========================== */
if (isset($_POST['confirmarEliminar']) && $_POST['confirmarEliminar'] == 1) {

    $idFoto = (int)$_POST['idFoto'];
    $idAnuncio = (int)$_POST['id'];

    $stmt = $conexion->prepare("DELETE FROM fotos WHERE IdFoto = ?");
    $stmt->bindValue(1, $idFoto, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: /phpDAW/ver_fotos?id=$idAnuncio&msg=FotoEliminada");
    exit();
}
?>

<main>

    <section class="profile-box">
        <?php
        $fotoPrincipal = function_exists('resolve_image_url')
            ? resolve_image_url($anuncio['FPrincipal'])
            : ($anuncio['FPrincipal'] ?: '/phpDAW/DAW/practica/imagenes/default-list.png');
        ?>

        <div style="display:flex;gap:18px;align-items:center;">
            <img src="<?= htmlspecialchars($fotoPrincipal) ?>"
                alt="<?= htmlspecialchars($anuncio['Titulo']) ?>"
                style="width:180px;height:130px;object-fit:cover;border-radius:8px;">
            <div>
                <h2><?= htmlspecialchars($anuncio['Titulo']) ?></h2>
                <p>
                    <?= htmlspecialchars($anuncio['Ciudad']) ?> —
                    <?= isset($anuncio['Precio']) ? number_format((float)$anuncio['Precio'], 2, ',', '.') . ' €' : '—' ?>
                </p>
                <p>Total de fotos: <strong><?= $totalFotos ?></strong></p>
                <p><?= htmlspecialchars(mb_strimwidth($anuncio['Texto'], 0, 200, '...')) ?></p>
            </div>
        </div>
    </section>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === "FotoEliminada"): ?>
    <div style="margin:15px auto;max-width:600px;padding:12px;border-radius:8px;background:#d6f5d6;text-align:center;">
        Foto eliminada correctamente ✔
    </div>
    <?php endif; ?>

    <section style="max-width:1100px;margin:1rem auto;padding:0 1rem;">
        <h3>Galería de fotos</h3>

        <?php if (empty($fotos)): ?>
            <p>Este anuncio no contiene fotos.</p>
        <?php else: ?>

        <div class="galeria-fotos"
            style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">

        <?php foreach ($fotos as $foto):
            $ruta = function_exists('resolve_image_url')
                ? resolve_image_url($foto['Foto'])
                : ($foto['Foto'] ?: '/phpDAW/DAW/practica/imagenes/default-list.png');
        ?>
            <figure style="margin:0;background:#fff;border-radius:8px;overflow:hidden;">
                <img src="<?= htmlspecialchars($ruta) ?>"
                    alt="<?= htmlspecialchars($foto['Alternativo'] ?: $foto['Titulo']) ?>"
                    style="width:100%;height:160px;object-fit:cover;">
                <figcaption style="padding:8px;">
                    <?= htmlspecialchars($foto['Titulo'] ?: 'Foto') ?><br>
                    <a class="btn-delete btn-small ghost"
                    href="/phpDAW/eliminar-foto?idFoto=<?= (int)$foto['IdFoto'] ?>&idAnuncio=<?= (int)$anuncio['IdAnuncio'] ?>">
                        ELIMINAR
                    </a>
                </figcaption>
            </figure>
        <?php endforeach; ?>
        </div>

        <!-- PAGINACIÓN -->
        <?php if ($totalPaginas > 1): ?>
        <nav class="paginacion" style="margin-top:20px;text-align:center;">

        <?php if ($paginaActual > 1): ?>
            <a href="?id=<?= (int)$anuncio['IdAnuncio'] ?>&page=1">⏮ Primera</a>
            <a href="?id=<?= (int)$anuncio['IdAnuncio'] ?>&page=<?= $paginaActual - 1 ?>">◀ Anterior</a>
        <?php endif; ?>

        <span> Página <?= $paginaActual ?> de <?= $totalPaginas ?> </span>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?id=<?= (int)$anuncio['IdAnuncio'] ?>&page=<?= $paginaActual + 1 ?>">Siguiente ▶</a>
            <a href="?id=<?= (int)$anuncio['IdAnuncio'] ?>&page=<?= $totalPaginas ?>">Última ⏭</a>
        <?php endif; ?>

        </nav>
        <?php endif; ?>

        <?php endif; ?>

        <a href="/phpDAW/anuncio/<?= (int)$anuncio['IdAnuncio'] ?>" class="btn">Volver al anuncio</a>
    </section>

</main>

<?php require_once("pie.inc"); ?>
