<?php
$title = "PI - Ver fotos";
$cssPagina = "miperfil.css";
require_once("cabecera.inc");
require_once(__DIR__ . "/privado.inc");
require_once("inicioLog.inc");

if (session_status() !== PHP_SESSION_ACTIVE) session_start();


require_once __DIR__ . '/includes/ver_fotos_common.php';
require_once __DIR__ . '/includes/precio.php';

$anuncio = $vf_anuncio;
$fotos = $vf_fotos;
$totalFotos = $vf_total;

if (!$anuncio) {
    echo "<main><p style='color:red;'>Anuncio no encontrado.</p></main>";
    require_once("pie.inc");
    exit();
}

?>

<main>
    <section class="profile-box">
        <?php $fotoPrincipal = (function_exists('resolve_image_url') ? resolve_image_url($anuncio['FPrincipal']) : ($anuncio['FPrincipal'] ?: 'DAW/practica/imagenes/default-list.png')); ?>
        <div style="display:flex;gap:18px;align-items:center;">
            <img src="<?php echo htmlspecialchars($fotoPrincipal, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($anuncio['Titulo']); ?>" style="width:180px;height:130px;object-fit:cover;border-radius:8px;border:1px solid rgba(0,0,0,0.06);">
            <div>
                <h2 style="margin:0;color:var(--color-principal);"><?php echo htmlspecialchars($anuncio['Titulo']); ?></h2>
                <p style="margin:6px 0;color:var(--color-texto);"><?php echo htmlspecialchars($anuncio['Ciudad']); ?> — <?php echo isset($anuncio['Precio']) ? number_format((float)$anuncio['Precio'],2,',','.') . ' €' : '—'; ?></p>
                <p style="margin:6px 0;color:#666;">Total de fotos: <strong><?php echo $totalFotos; ?></strong></p>
                <p style="margin:6px 0;color:#666;"><?php echo htmlspecialchars(mb_strimwidth($anuncio['Texto'],0,200,'...')); ?></p>
            </div>
        </div>
    </section>

    <section style="max-width:1100px;margin:1rem auto;padding:0 1rem;">
        <h3>Galería de fotos</h3>

        <?php if (empty($fotos)): ?>
            <p>Este anuncio no contiene fotos.</p>
        <?php else: ?>
            <div class="galeria-fotos" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
                <?php foreach ($fotos as $foto):
                    $ruta = function_exists('resolve_image_url') ? resolve_image_url($foto['Foto']) : ($foto['Foto'] ?: 'DAW/practica/imagenes/default-list.png');
                ?>
                    <figure style="margin:0;background:#fff;border-radius:8px;overflow:hidden;border:1px solid rgba(0,0,0,0.04);box-shadow:var(--sombra);">
                        <img src="<?php echo htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($foto['Alternativo'] ?: $foto['Titulo']); ?>" style="width:100%;height:160px;object-fit:cover;display:block;">
                        <figcaption style="padding:8px;font-size:0.95rem;color:var(--color-texto);">
                            <?php echo htmlspecialchars($foto['Titulo'] ?: 'Foto'); ?>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="anuncio.php?id=<?php echo (int)($anuncio['IdAnuncio'] ?? 0); ?>" class="btn">Volver al anuncio</a>

    </section>

</main>

<?php
require_once("pie.inc");
?>
