<?php
if (!defined('APP_INIT')) { define('APP_INIT', true); }
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: /phpDAW/');
    exit;
}

require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones-ficheros.php';

$userId = (int)($_SESSION['id'] ?? 0);
$idAnuncio = isset($_POST['anuncio']) ? (int)$_POST['anuncio'] : 0;

if (!$idAnuncio || !isset($_FILES['fotos'])) {
    header('Location: /phpDAW/subir_multiples.php?error=Parametros');
    exit;
}

// Verificar que el anuncio pertenece al usuario
try {
    $stmt = $conexion->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $stmt->execute([$idAnuncio]);
    $owner = (int)$stmt->fetchColumn();
    if ($owner !== $userId) {
        header('Location: /phpDAW/subir_multiples.php?error=NoPropietario');
        exit;
    }
} catch (Throwable $e) {
    header('Location: /phpDAW/subir_multiples.php?error=BD');
    exit;
}

$ok = 0; $fail = 0;
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];

$files = $_FILES['fotos'];
for ($i = 0; $i < count($files['name']); $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) { $fail++; continue; }

    $tmp = $files['tmp_name'][$i];
    $origName = $files['name'][$i];
    $size = (int)$files['size'][$i];

    if (!is_uploaded_file($tmp)) { $fail++; continue; }
    if ($size <= 0 || $size > 15*1024*1024) { $fail++; continue; } // hasta 15MB

    $finfo = @finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? @finfo_file($finfo, $tmp) : null;
    if ($finfo) @finfo_close($finfo);
    if (!$mime || !isset($allowed[$mime])) { $fail++; continue; }

    $finalName = unique_image_name($idAnuncio, $origName);
    $dest = imagenes_dir() . $finalName;
    if (!@move_uploaded_file($tmp, $dest)) { $fail++; continue; }

    // Insertar en BD (intenta con FRegistro y si falla, sin FRegistro)
    $titulo = pathinfo($origName, PATHINFO_FILENAME);
    $alt = 'Foto de anuncio';
    try {
        $stmt = $conexion->prepare('INSERT INTO Fotos (Anuncio, Foto, Titulo, Alternativo, FRegistro) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$idAnuncio, $finalName, $titulo, $alt]);
        $ok++;
    } catch (Throwable $e1) {
        try {
            $stmt = $conexion->prepare('INSERT INTO fotos (Anuncio, Foto, Titulo, Alternativo, FRegistro) VALUES (?,?,?,?,NOW())');
            $stmt->execute([$idAnuncio, $finalName, $titulo, $alt]);
            $ok++;
        } catch (Throwable $e1b) {
            try {
                $stmt = $conexion->prepare('INSERT INTO Fotos (Anuncio, Foto, Titulo, Alternativo) VALUES (?,?,?,?)');
                $stmt->execute([$idAnuncio, $finalName, $titulo, $alt]);
                $ok++;
            } catch (Throwable $e2) {
                try {
                    $stmt = $conexion->prepare('INSERT INTO fotos (Anuncio, Foto, Titulo, Alternativo) VALUES (?,?,?,?)');
                    $stmt->execute([$idAnuncio, $finalName, $titulo, $alt]);
                    $ok++;
                } catch (Throwable $e3) {
                    @unlink($dest);
                    $fail++;
                }
            }
        }
    }
}

// Si el anuncio no tiene FPrincipal, asignar la primera subida como principal
try {
    $stmt = $conexion->prepare('SELECT FPrincipal FROM Anuncios WHERE IdAnuncio = ?');
    $stmt->execute([$idAnuncio]);
    $principal = $stmt->fetchColumn();
    if (!$principal) {
        $stmt = $conexion->prepare('SELECT Foto FROM Fotos WHERE Anuncio = ? ORDER BY IdFoto ASC LIMIT 1');
        $stmt->execute([$idAnuncio]);
        $first = $stmt->fetchColumn();
        if ($first) {
            $upd = $conexion->prepare('UPDATE Anuncios SET FPrincipal = ? WHERE IdAnuncio = ?');
            $upd->execute([$first, $idAnuncio]);
        }
    }
} catch (Throwable $e) {
}

$msg = 'Subidas:' . $ok . ';Fallos:' . $fail;
header('Location: /phpDAW/ver_fotos?id=' . $idAnuncio . '&msg=' . urlencode($msg));
exit;
