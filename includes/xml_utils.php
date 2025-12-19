<?php
if (!defined('APP_INIT')) die('Acceso directo no permitido');

function crearElementoTexto(DOMDocument $doc, DOMNode $padre, string $nombre, string $valor): DOMElement {
    $el = $doc->createElement($nombre);
    $texto = $doc->createTextNode($valor);
    $el->appendChild($texto);
    $padre->appendChild($el);
    return $el;
}

function fechaRSS(string $fecha): string {
    return date(DATE_RSS, strtotime($fecha));
}

function fechaAtom(string $fecha): string {
    return date(DATE_ATOM, strtotime($fecha));
}
