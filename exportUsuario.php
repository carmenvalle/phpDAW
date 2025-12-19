<?php
define('APP_INIT', true);
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/xml-utils.php';

$idUsuario = $_GET['id'] ?? null;
if (!$idUsuario) {
    http_response_code(400);
    exit('Falta id de usuario');
}

// Usuario
$stmt = $conexion->prepare("SELECT * FROM Usuarios WHERE IdUsuario = ?");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    exit('Usuario no encontrado');
}

// Anuncios del usuario
$stmt = $conexion->prepare("
    SELECT * FROM Anuncios WHERE Usuario = ?
");
$stmt->execute([$idUsuario]);
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doc = new DOMDocument('1.0', 'UTF-8');
$doc->formatOutput = true;

header('Content-Type: application/xml; charset=UTF-8');

$raiz = $doc->createElement('ExportacionUsuario');
$raiz->setAttribute('IdUsuario', $idUsuario);
$doc->appendChild($raiz);

// Datos usuario
$u = $doc->createElement('Usuario');
$raiz->appendChild($u);

foreach ($usuario as $campo => $valor) {
    crearElementoTexto($doc, $u, $campo, (string)$valor);
}

// Anuncios
$lista = $doc->createElement('Anuncios');
$raiz->appendChild($lista);

foreach ($anuncios as $a) {
    $an = $doc->createElement('Anuncio');
    $an->setAttribute('IdAnuncio', $a['IdAnuncio']);
    $lista->appendChild($an);

    foreach ($a as $campo => $valor) {
        crearElementoTexto($doc, $an, $campo, (string)$valor);
    }
}

echo $doc->saveXML();
