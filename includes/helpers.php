<?php

function parse_price_to_float($raw) {
    if ($raw === null || $raw === '') return null;
    if (is_numeric($raw)) return (float)$raw;
    $p = preg_replace('/[^0-9,\.]/', '', (string)$raw);
    if ($p === '') return null;
    if (strpos($p, ',') !== false && strpos($p, '.') !== false) {
        $p = str_replace('.', '', $p);
        $p = str_replace(',', '.', $p);
    } elseif (strpos($p, ',') !== false && strpos($p, '.') === false) {
        $p = str_replace(',', '.', $p);
    }
    return is_numeric($p) ? (float)$p : null;
}

function format_price_display($raw) {
    // Accept float/int or string
    if ($raw === null || $raw === '') return '—';
    if (is_numeric($raw)) return number_format((float)$raw, 2, ',', '.') . ' €';
    $parsed = parse_price_to_float($raw);
    if ($parsed !== null) return number_format($parsed, 2, ',', '.') . ' €';
    // Fallback: return raw cleaned
    return htmlspecialchars((string)$raw, ENT_QUOTES, 'UTF-8');
}

function resolve_image_url($fotoCandidate) {
    // Accept either a basename or a path; prefer project ./imagenes then DAW/practica/imagenes then DAW/imagenes.
    // Return absolute paths under /phpDAW so they resolve correctly when using clean URLs.
    if (empty($fotoCandidate)) return '/phpDAW/DAW/practica/imagenes/anuncio2.jpg';
    $basename = basename($fotoCandidate);
    $pathRootImg = __DIR__ . '/../imagenes/' . $basename;
    if (file_exists($pathRootImg)) return '/phpDAW/imagenes/' . $basename;
    $pathPractica = __DIR__ . '/../DAW/practica/imagenes/' . $basename;
    if (file_exists($pathPractica)) return '/phpDAW/DAW/practica/imagenes/' . $basename;
    $pathDawImg = __DIR__ . '/../DAW/imagenes/' . $basename;
    if (file_exists($pathDawImg)) return '/phpDAW/DAW/imagenes/' . $basename;
    return '/phpDAW/DAW/practica/imagenes/anuncio2.jpg';
}

?>