<?php
namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

class AnuncioController extends BaseController
{
    // Show an anuncio by id. For incremental migration we reuse existing script.
    public function show(int $id)
    {
        // set GET param expected by legacy script
        $_GET['id'] = $id;
        // include legacy script which already renders header/footer
        $legacy = __DIR__ . '/../../anuncio.php';
        if (file_exists($legacy)) {
            include $legacy;
            exit;
        }
        // fallback: render a simple view if legacy script missing
        $this->render('anuncio/missing.php', ['id' => $id]);
    }

    // Example: edit action can forward to modificar_anuncio.php
    public function edit(int $id)
    {
        $_GET['id'] = $id;
        $legacy = __DIR__ . '/../../modificar_anuncio.php';
        if (file_exists($legacy)) {
            include $legacy;
            exit;
        }
        $this->render('anuncio/missing.php', ['id' => $id]);
    }
}
