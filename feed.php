<?php
define('APP_INIT', true);
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/xml-utils.php';

$formato = $_GET['format'] ?? 'rss';
$formato = strtolower($formato);

$stmt = $conexion->query("
    SELECT a.IdAnuncio, a.Titulo, a.FRegistro, a.Ciudad,
           p.NomPais, a.Precio
    FROM Anuncios a
    LEFT JOIN Paises p ON a.Pais = p.IdPaises
    ORDER BY a.FRegistro DESC
    LIMIT 5
");
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doc = new DOMDocument('1.0', 'UTF-8');
$doc->formatOutput = true;

header('Content-Type: application/xml; charset=UTF-8');

if ($formato === 'atom') {

    // ================= ATOM =================
    $feed = $doc->createElement('feed');
    $feed->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
    $doc->appendChild($feed);

    crearElementoTexto($doc, $feed, 'title', 'Últimos anuncios');
    crearElementoTexto($doc, $feed, 'id', 'urn:pi:anuncios');
    crearElementoTexto($doc, $feed, 'updated', fechaAtom($anuncios[0]['FRegistro'] ?? date('Y-m-d')));

    foreach ($anuncios as $a) {
        $entry = $doc->createElement('entry');
        $feed->appendChild($entry);

        crearElementoTexto($doc, $entry, 'title', $a['Titulo']);
        crearElementoTexto($doc, $entry, 'id', 'urn:anuncio:' . $a['IdAnuncio']);
        crearElementoTexto($doc, $entry, 'updated', fechaAtom($a['FRegistro']));

        $summary = "{$a['Ciudad']} ({$a['NomPais']}) - {$a['Precio']} €";
        crearElementoTexto($doc, $entry, 'summary', $summary);
    }

} else {

    // ================= RSS 2.0 =================
    $rss = $doc->createElement('rss');
    $rss->setAttribute('version', '2.0');
    $doc->appendChild($rss);

    $channel = $doc->createElement('channel');
    $rss->appendChild($channel);

    crearElementoTexto($doc, $channel, 'title', 'Últimos anuncios');
    crearElementoTexto($doc, $channel, 'link', 'http://localhost/phpDAW/');
    crearElementoTexto($doc, $channel, 'description', 'Últimos anuncios publicados');
    crearElementoTexto($doc, $channel, 'pubDate', fechaRSS($anuncios[0]['FRegistro'] ?? date('Y-m-d')));

    foreach ($anuncios as $a) {
        $item = $doc->createElement('item');
        $channel->appendChild($item);

        crearElementoTexto($doc, $item, 'title', $a['Titulo']);
        crearElementoTexto($doc, $item, 'guid', (string)$a['IdAnuncio']);
        crearElementoTexto($doc, $item, 'pubDate', fechaRSS($a['FRegistro']));

        $desc = "{$a['Ciudad']} ({$a['NomPais']}) - {$a['Precio']} €";
        crearElementoTexto($doc, $item, 'description', $desc);
    }
}

echo $doc->saveXML();
