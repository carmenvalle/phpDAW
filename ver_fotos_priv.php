<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
// ver_fotos_priv.php - Página privada: accesible sólo al propietario del anuncio
$title = "PI - Ver fotos (privado)";
$cssPagina = "miperfil.css";
require_once __DIR__ . '/privado.inc'; 
require_once __DIR__ . '/cabecera.inc';
require_once __DIR__ . '/inicioLog.inc';

// cargar datos comunes
require_once __DIR__ . '/includes/ver_fotos_common.php';
require_once __DIR__ . '/includes/precio.php';

// comprobar propiedad
$usuarioLog = $_SESSION['usuario'] ?? null;
if (!$vf_anuncio) {
    echo "<main><p style='color:red;'>Anuncio no encontrado.</p></main>";
    require_once("pie.inc");
    exit();
}

if ($usuarioLog !== ($vf_anuncio['Usuario'] ?? null)) {
    echo "<main><p style='color:red;'>Acceso denegado: no eres el propietario de este anuncio.</p></main>";
    require_once("pie.inc");
    exit();
}

?>
<main>
    <section class="profile-box">
        <?php $fotoPrincipal = (function_exists('resolve_image_url') ? resolve_image_url($vf_anuncio['FPrincipal']) : ($vf_anuncio['FPrincipal'] ?: '/phpDAW/DAW/practica/imagenes/default-list.png')); ?>
        <div style="display:flex;gap:18px;align-items:center;">
            <img src="<?php echo htmlspecialchars($fotoPrincipal, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($vf_anuncio['Titulo']); ?>" style="width:180px;height:130px;object-fit:cover;border-radius:8px;border:1px solid rgba(0,0,0,0.06);">
            <div>
                <h2 style="margin:0;color:var(--color-principal);"><?php echo htmlspecialchars($vf_anuncio['Titulo']); ?></h2>
                <p style="margin:6px 0;color:var(--color-texto);"><?php echo htmlspecialchars($vf_anuncio['Ciudad']); ?> — <?php echo isset($vf_anuncio['Precio']) ? number_format((float)$vf_anuncio['Precio'],2,',','.') . ' €' : '—'; ?></p>
                <p style="margin:6px 0;color:#666;">Total de fotos: <strong><?php echo (int)$vf_total; ?></strong></p>
                <p style="margin:6px 0;color:#666;">
                    <?php echo htmlspecialchars(mb_strimwidth($vf_anuncio['Texto'],0,200,'...')); ?>
                </p>
            </div>
        </div>
    </section>

    <section style="max-width:1100px;margin:1rem auto;padding:0 1rem;">
        <h3>Galería de fotos (privado)</h3>

        <?php if (empty($vf_fotos)): ?>
            <p>Este anuncio no contiene fotos.</p>
        <?php else: ?>
            <div class="galeria-fotos" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
                <?php foreach ($vf_fotos as $foto):
                    $ruta = function_exists('resolve_image_url') ? resolve_image_url($foto['Foto']) : ($foto['Foto'] ?: '/phpDAW/DAW/practica/imagenes/default-list.png');
                ?>
                    <figure style="margin:0;background:#fff;border-radius:8px;overflow:hidden;border:1px solid rgba(0,0,0,0.04);box-shadow:var(--sombra);">
                        <img src="<?php echo htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($foto['Alternativo'] ?: $foto['Titulo']); ?>" style="width:100%;height:160px;object-fit:cover;display:block;">
                        <figcaption style="padding:8px;font-size:0.95rem;color:var(--color-texto);">
                            <?php echo htmlspecialchars($foto['Titulo'] ?: 'Foto'); ?>
                            <div style="margin-top:6px;">
                                <a href="/phpDAW/editar_foto?id=<?php echo (int)$foto['IdFoto']; ?>" class="btn btn-small">Editar</a>
                                <a href="/phpDAW/borrar_foto?id=<?php echo (int)$foto['IdFoto']; ?>&anuncio=<?php echo (int)$vf_anuncio['IdAnuncio']; ?>" class="btn btn-small btn-danger" onclick="return confirm('¿Borrar esta foto?');">Borrar</a>
                            </div>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p style="margin-top:1rem;">
            <a href="/phpDAW/anyadir_foto?id=<?php echo (int)$vf_anuncio['IdAnuncio']; ?>" class="btn">Añadir nueva foto</a>
            <a href="/phpDAW/mis-anuncios" class="btn">Volver a mis anuncios</a>
        </p>

    </section>

</main>

<?php require_once("pie.inc"); ?>
