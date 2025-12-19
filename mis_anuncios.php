<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    echo 'Acceso no autorizado.';
    exit;
}

$title = "Mis Anuncios - PI Pisos & Inmuebles";
$cssPagina = "resultados.css";

require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones-ficheros.php';
require_once __DIR__ . '/includes/precio.php';
require_once __DIR__ . '/includes/config.php';

/* ==========================
   USUARIO
   ========================== */
$userId = $_SESSION['id'] ?? null;
$anuncios = [];
$totalAnuncios = 0;
$totalPaginas = 1;

/* ==========================
   PAGINACIÓN
   ========================== */
$paginaActual = isset($_GET['page']) && is_numeric($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$limite = ANUNCIOS_POR_PAGINA;
$offset = ($paginaActual - 1) * $limite;

if ($userId && isset($conexion)) {

    /* Total de anuncios */
    $stmtTotal = $conexion->prepare(
        'SELECT COUNT(*) FROM Anuncios WHERE Usuario = ?'
    );
    $stmtTotal->execute([$userId]);
    $totalAnuncios = (int)$stmtTotal->fetchColumn();

    $totalPaginas = max(1, ceil($totalAnuncios / $limite));

    /* Anuncios paginados */
    $stmt = $conexion->prepare(
        'SELECT IdAnuncio, Titulo, FPrincipal, FRegistro, Ciudad, Precio
         FROM Anuncios
         WHERE Usuario = ?
         ORDER BY FRegistro DESC
         LIMIT ? OFFSET ?'
    );

    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limite, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main>
<section class="mis-anuncios">

<?php if (empty($anuncios)): ?>
    <p>No has publicado anuncios todavía.</p>
<?php else: ?>

    <div class="total-anuncios">
        <strong>Total de anuncios:</strong> <?= $totalAnuncios ?>
    </div>

    <div class="anuncios-list">
    <?php foreach ($anuncios as $a): ?>
        <article>
            <h2><?= htmlspecialchars($a['Titulo'] ?: 'Sin título') ?></h2>

            <a href="anuncio/<?= $a['IdAnuncio'] ?>">
                <?php $imgPath = function_exists('get_thumbnail_url') ? get_thumbnail_url($a['FPrincipal'] ?? '', 200, 200) : (function_exists('resolve_image_url') ? resolve_image_url($a['FPrincipal'] ?? '') : '/phpDAW/DAW/practica/imagenes/default-list.png'); ?>
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="Foto" width="200" height="200" style="object-fit:cover;border-radius:6px;">
            </a>

            <p><strong>Ciudad:</strong> <?= htmlspecialchars($a['Ciudad'] ?: '—') ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($a['FRegistro']) ?></p>

            <p><strong>Precio:</strong>
                <span class="precio">
                <?php
                    $p = $a['Precio'] ?? null;
                    if ($p !== null && is_numeric($p)) {
                        echo formatearPrecio((float)$p);
                    } else {
                        echo '—';
                    }
                ?>
                </span>
            </p>

            <p>
                <a href="anuncio/<?= $a['IdAnuncio'] ?>/modificar">Editar</a> |
                <a href="borrar_anuncio.php?id=<?= $a['IdAnuncio'] ?>">Borrar</a>
            </p>
        </article>
    <?php endforeach; ?>
    </div>

    <!-- PAGINACIÓN -->
    <?php if ($totalPaginas > 1): ?>
    <nav class="paginacion">

        <?php if ($paginaActual > 1): ?>
            <a href="?page=1">⏮ Primera</a>
            <a href="?page=<?= $paginaActual - 1 ?>">◀ Anterior</a>
        <?php endif; ?>

        <span>
            Página <?= $paginaActual ?> de <?= $totalPaginas ?>
        </span>

        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?page=<?= $paginaActual + 1 ?>">Siguiente ▶</a>
            <a href="?page=<?= $totalPaginas ?>">Última ⏭</a>
        <?php endif; ?>

    </nav>
    <?php endif; ?>

<?php endif; ?>

</section>

<a href="anyadir_foto" class="btn">
    <i class="icon-foto"></i>
    <strong>AÑADIR FOTO</strong>
</a>

<a href="subir_multiples" class="btn" style="margin-left:10px;">
    <i class="icon-foto"></i>
    <strong>SUBIDA MASIVA</strong>
 </a>

<?php require_once("salto.inc"); ?>
</main>

<?php require_once("pie.inc"); ?>
