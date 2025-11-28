<?php
require_once("includes/conexion.php");

$idFoto = filter_input(INPUT_POST, "idFoto", FILTER_VALIDATE_INT);
$idAnuncio = filter_input(INPUT_POST, "idAnuncio", FILTER_VALIDATE_INT);

if (!$idFoto || !$idAnuncio) {
    die("Error al procesar la eliminación");
}

try {
    $stmt = $conexion->prepare("DELETE FROM fotos WHERE IdFoto = :idFoto");
    $stmt->bindParam(':idFoto', $idFoto, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    die("Error al eliminar la foto: " . $e->getMessage());
}

header("Location: ver_fotos.php?id=$idAnuncio&msg=FotoEliminada");
exit();
?>
