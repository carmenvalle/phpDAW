<?php
// includes/ver_fotos_common.php
// Expects: GET param 'id' (IdAnuncio)
// Provides: $vf_anuncio (assoc) or null, $vf_fotos (array), $vf_total (int)

$vf_anuncio = null;
$vf_fotos = [];
$vf_total = 0;

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) return;

if (!file_exists(__DIR__ . '/conexion.php')) return;
require_once __DIR__ . '/conexion.php';
try {
    $st = $conexion->prepare("SELECT a.IdAnuncio, a.Titulo, a.Texto, a.Precio, a.Ciudad, a.FRegistro, p.NomPais AS Pais, u.NomUsuario AS Usuario, a.FPrincipal
                              FROM Anuncios a
                              LEFT JOIN Paises p ON a.Pais = p.IdPaises
                              LEFT JOIN Usuarios u ON a.Usuario = u.IdUsuario
                              WHERE a.IdAnuncio = ? LIMIT 1");
    $st->execute([$id]);
    $vf_anuncio = $st->fetch(PDO::FETCH_ASSOC);

    if ($vf_anuncio) {
        $st2 = $conexion->prepare("SELECT IdFoto, Titulo, Foto, Alternativo FROM Fotos WHERE Anuncio = ? ORDER BY IdFoto ASC");
        $st2->execute([$id]);
        $vf_fotos = $st2->fetchAll(PDO::FETCH_ASSOC);
        $vf_total = count($vf_fotos);
        // Normalize foto paths if needed: keep as stored; caller can use resolve_image_url if required
    }
} catch (Exception $e) {
    $vf_anuncio = null;
    $vf_fotos = [];
    $vf_total = 0;
}
