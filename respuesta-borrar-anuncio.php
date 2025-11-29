<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mis_anuncios.php');
    exit();
}

$idAnuncio = filter_input(INPUT_POST, 'idAnuncio', FILTER_VALIDATE_INT);
if (!$idAnuncio) {
    $_SESSION['flash']['error'] = 'Anuncio inválido.';
    header('Location: mis_anuncios.php');
    exit();
}

$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: index.php');
    exit();
}

try {
    // Verificar propietario
    $s = $conexion->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $s->execute([$idAnuncio]);
    $row = $s->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $_SESSION['flash']['error'] = 'Anuncio no encontrado.';
        header('Location: mis_anuncios.php');
        exit();
    }
    if ((int)$row['Usuario'] !== (int)$userId) {
        $_SESSION['flash']['error'] = 'No tienes permiso para eliminar este anuncio.';
        header('Location: mis_anuncios.php');
        exit();
    }

    // Iniciar transacción
    $conexion->beginTransaction();

    // Obtener fotos asociadas. NOTA: no se borran los ficheros físicos en esta práctica,
    // solo se eliminarán las filas en la BD. En una práctica posterior se limpiarán los ficheros.
    $st = $conexion->prepare('SELECT Foto FROM Fotos WHERE Anuncio = ?');
    $st->execute([$idAnuncio]);
    $fotos = $st->fetchAll(PDO::FETCH_COLUMN, 0);

    // Borrar filas relacionadas
    $d1 = $conexion->prepare('DELETE FROM Fotos WHERE Anuncio = ?');
    $d1->execute([$idAnuncio]);

    $d2 = $conexion->prepare('DELETE FROM Mensajes WHERE Anuncio = ?');
    $d2->execute([$idAnuncio]);

    // Finalmente borrar el anuncio
    $d3 = $conexion->prepare('DELETE FROM Anuncios WHERE IdAnuncio = ?');
    $d3->execute([$idAnuncio]);

    $conexion->commit();

    $_SESSION['flash']['ok'] = 'Anuncio eliminado correctamente.';
    header('Location: mis_anuncios.php');
    exit();

} catch (Exception $e) {
    if ($conexion->inTransaction()) $conexion->rollBack();
    $_SESSION['flash']['error'] = 'Error eliminando el anuncio: ' . $e->getMessage();
    header('Location: mis_anuncios.php');
    exit();
}

?>
