<?php
session_start();
require_once(__DIR__ . '/includes/conexion.php');

$idFoto = filter_input(INPUT_POST, "idFoto", FILTER_VALIDATE_INT);
$idAnuncio = filter_input(INPUT_POST, "idAnuncio", FILTER_VALIDATE_INT);

if (!$idFoto || !$idAnuncio) {
    $_SESSION['flash']['error'] = 'Parámetros inválidos para eliminar la foto.';
    header("Location: /phpDAW/ver_fotos?id=" . ($idAnuncio ?: 0));
    exit();
}

// Comprobar autenticación y que el usuario sea propietario del anuncio
$idUsuarioSession = $_SESSION['id'] ?? null;
if (!$idUsuarioSession && !empty($_SESSION['usuario'])) {
    // intentar recuperar IdUsuario desde NomUsuario
    $s = $conexion->prepare('SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ? LIMIT 1');
    $s->execute([$_SESSION['usuario']]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    if ($r) $idUsuarioSession = (int)$r['IdUsuario'];
}

if (!$idUsuarioSession) {
        $_SESSION['flash']['error'] = 'Debes iniciar sesión para eliminar fotos.';
    header('Location: /phpDAW/');
    exit();
}

try {
    // Verificar que la foto existe y obtener su nombre
    $s1 = $conexion->prepare('SELECT Foto, Anuncio, Titulo FROM Fotos WHERE IdFoto = ? LIMIT 1');
    $s1->execute([$idFoto]);
    $fotoRow = $s1->fetch(PDO::FETCH_ASSOC);
    if (!$fotoRow) {
        $_SESSION['flash']['error'] = 'Foto no encontrada.';
        header('Location: /phpDAW/ver_fotos?id=' . $idAnuncio);
        exit();
    }

    // Verificar propietario del anuncio
    $s2 = $conexion->prepare('SELECT Usuario, FPrincipal FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $s2->execute([$idAnuncio]);
    $an = $s2->fetch(PDO::FETCH_ASSOC);
    if (!$an) {
        $_SESSION['flash']['error'] = 'Anuncio no encontrado.';
        header('Location: /phpDAW/ver_fotos?id=' . $idAnuncio);
        exit();
    }
    if ((int)$an['Usuario'] !== (int)$idUsuarioSession) {
        $_SESSION['flash']['error'] = 'No tienes permiso para eliminar esta foto.';
        header('Location: /phpDAW/ver_fotos?id=' . $idAnuncio);
        exit();
    }

    $fotoNombre = $fotoRow['Foto'];

    // Borrar el fichero físico si existe
    $rutaFoto = __DIR__ . '/DAW/practica/imagenes/' . $fotoNombre;
    if (file_exists($rutaFoto) && is_file($rutaFoto)) {
        @unlink($rutaFoto);
    }

    // Borrar la fila en la BD
    $del = $conexion->prepare('DELETE FROM Fotos WHERE IdFoto = ?');
    $del->execute([$idFoto]);

    // Si la foto era la principal del anuncio, intentar asignar otra foto como principal
    if (!empty($an['FPrincipal']) && $an['FPrincipal'] === $fotoNombre) {
        $s3 = $conexion->prepare('SELECT Foto FROM Fotos WHERE Anuncio = ? ORDER BY IdFoto ASC LIMIT 1');
        $s3->execute([$idAnuncio]);
        $n = $s3->fetch(PDO::FETCH_ASSOC);
        $nuevo = $n ? $n['Foto'] : null;
        $u = $conexion->prepare('UPDATE Anuncios SET FPrincipal = ? WHERE IdAnuncio = ?');
        $u->execute([$nuevo, $idAnuncio]);
    }

    // Prepare user-friendly message including title if available
    $tituloFoto = $fotoRow['Titulo'] ?? '';
    if ($tituloFoto) {
        $_SESSION['flash']['ok'] = 'Foto "' . htmlspecialchars($tituloFoto) . '" eliminada correctamente.';
    } else {
        $_SESSION['flash']['ok'] = 'Foto eliminada correctamente.';
    }
    header('Location: /phpDAW/ver_fotos?id=' . $idAnuncio . '&msg=FotoEliminada');
    exit();

} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error al eliminar la foto: ' . $e->getMessage();
    header('Location: /phpDAW/ver_fotos?id=' . $idAnuncio);
    exit();
}
?>
