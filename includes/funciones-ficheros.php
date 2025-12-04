<?php
// includes/funciones_ficheros.php
// Utilidades para gestión de imágenes (subida, resolución URL, borrado físico)
if (!defined('APP_INIT')) { http_response_code(403); exit; }

/**
 * Ruta absoluta al directorio donde tú ya guardas las imágenes.
 * Según nos indicas: phpDAW/DAW/practica/imagenes
 */
function imagenes_dir(): string {
    // __DIR__ => phpDAW/includes
    return realpath(__DIR__ . '/../DAW/practica/imagenes') . DIRECTORY_SEPARATOR;
}

/**
 * URL pública base (para <img src="...">). Ajusta si tu app está en subcarpeta distinta.
 * Según tu setup, las imágenes se sirven desde /phpDAW/DAW/practica/imagenes/
 */
function imagenes_base_url(): string {
    return '/phpDAW/DAW/practica/imagenes/';
}

/**
 * Devuelve URL pública de una imagen o placeholder si no existe.
 */
if (!function_exists('resolve_image_url')) {
    function resolve_image_url($filename, string $tipo = 'anuncio'): string {
        $placeholder = '/phpDAW/DAW/practica/imagenes/default-list.png';
        if (!$filename) return $placeholder;
        $path = imagenes_dir() . $filename;
        if (file_exists($path) && is_file($path)) {
            return imagenes_base_url() . rawurlencode($filename);
        }
        return $placeholder;
    }
}

/**
 * Borra el fichero físico si existe. Devuelve true si se borró, false si no existía o no se pudo borrar.
 */
function delete_physical_file(string $filename): bool {
    if (!$filename) return false;
    $path = imagenes_dir() . $filename;
    if (file_exists($path) && is_file($path)) {
        return @unlink($path);
    }
    return false;
}

/**
 * Genera un nombre único seguro manteniendo la extensión.
 * Ej: anuncio_12_1690000000_a1b2c3d4f5_originalname.jpg
 */
function unique_image_name(int $idAnuncio, string $originalName): string {
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $hash = bin2hex(random_bytes(6));
    $time = time();
    $prefix = "anuncio_{$idAnuncio}_{$time}_{$hash}";
    $safeBase = substr($safeBase, 0, 60);
    return $prefix . '_' . $safeBase . ($ext ? '.' . $ext : '');
}
