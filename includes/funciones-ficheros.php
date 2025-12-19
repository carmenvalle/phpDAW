<?php
// includes/funciones_ficheros.php
// Utilidades para gestión de imágenes (subida, resolución URL, borrado físico)
if (!defined('APP_INIT')) { http_response_code(403); exit; }


function imagenes_dir(): string {
    // __DIR__ => phpDAW/includes
    return realpath(__DIR__ . '/../DAW/practica/imagenes') . DIRECTORY_SEPARATOR;
}


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
 * Elimina miniaturas cacheadas para un archivo dado en thumbs/* subcarpetas.
 */
function delete_all_thumbnails(string $filename): void {
    if (!$filename) return;
    $base = imagenes_dir() . 'thumbs';
    if (!is_dir($base)) return;
    foreach (scandir($base) as $sub) {
        if ($sub === '.' || $sub === '..') continue;
        $dir = $base . DIRECTORY_SEPARATOR . $sub;
        if (!is_dir($dir)) continue;
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($path) && is_file($path)) @unlink($path);
    }
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

/**
 * Devuelve la URL de un thumbnail (w x h). Si GD no está disponible o falla,
 * devuelve la URL original como fallback.
 */
function get_thumbnail_url(string $filename, int $w, int $h): string {
    if (!$filename) return resolve_image_url('');
    $srcPath = imagenes_dir() . $filename;
    if (!file_exists($srcPath) || !is_file($srcPath)) return resolve_image_url('');

    $thumbRelDir = 'thumbs' . DIRECTORY_SEPARATOR . $w . 'x' . $h;
    $thumbDir = imagenes_dir() . $thumbRelDir;
    if (!is_dir($thumbDir)) @mkdir($thumbDir, 0775, true);
    $thumbPath = $thumbDir . DIRECTORY_SEPARATOR . $filename;

    // Si ya existe y es más reciente que el original, usarlo
    if (file_exists($thumbPath) && filemtime($thumbPath) >= filemtime($srcPath)) {
        return imagenes_base_url() . rawurlencode('thumbs/' . $w . 'x' . $h . '/' . $filename);
    }

    // Intentar crear thumbnail con GD si está disponible
    if (extension_loaded('gd')) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $create = null; $save = null; $mime = '';
        switch ($ext) {
            case 'jpg': case 'jpeg': $create = 'imagecreatefromjpeg'; $save = 'imagejpeg'; $mime = 'image/jpeg'; break;
            case 'png': $create = 'imagecreatefrompng'; $save = 'imagepng'; $mime = 'image/png'; break;
            case 'gif': $create = 'imagecreatefromgif'; $save = 'imagegif'; $mime = 'image/gif'; break;
            case 'webp': if (function_exists('imagecreatefromwebp')) { $create = 'imagecreatefromwebp'; $save = 'imagewebp'; $mime = 'image/webp'; } break;
        }
        if ($create && $save && function_exists($create) && function_exists($save)) {
            $src = @$create($srcPath);
            if ($src) {
                $srcW = imagesx($src); $srcH = imagesy($src);
                // Mantener aspecto: cubrir el cuadro (cover)
                $ratio = max($w / $srcW, $h / $srcH);
                $newW = (int)ceil($srcW * $ratio);
                $newH = (int)ceil($srcH * $ratio);
                $tmp = imagecreatetruecolor($newW, $newH);
                // PNG/GIF transparencia
                if (in_array($ext, ['png','gif'], true)) {
                    imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                    imagealphablending($tmp, false); imagesavealpha($tmp, true);
                }
                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
                // recortar al centro
                $cropX = (int)max(0, ($newW - $w) / 2);
                $cropY = (int)max(0, ($newH - $h) / 2);
                $dst = imagecreatetruecolor($w, $h);
                if (in_array($ext, ['png','gif'], true)) {
                    imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
                    imagealphablending($dst, false); imagesavealpha($dst, true);
                }
                imagecopy($dst, $tmp, 0, 0, $cropX, $cropY, $w, $h);
                // guardar
                @$save($dst, $thumbPath);
                imagedestroy($dst); imagedestroy($tmp); imagedestroy($src);
                if (file_exists($thumbPath)) {
                    return imagenes_base_url() . rawurlencode('thumbs/' . $w . 'x' . $h . '/' . $filename);
                }
            }
        }
    }

    // Fallback: sin GD, devolver original
    return resolve_image_url($filename);
}
