<?php
namespace App\Controllers;

class BaseController
{
    // Render a view file from the project's views directory.
    // $view: path relative to /views, e.g. 'anuncio/show.php' or 'anuncio.php'
    // $data: associative array to extract for the view
    protected function render(string $view, array $data = [])
    {
        $viewFile = __DIR__ . '/../../views/' . ltrim($view, '/');
        if (!file_exists($viewFile)) {
            // Fail gracefully: throw or include a minimal message
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "View not found: " . htmlspecialchars($viewFile);
            exit;
        }
        extract($data, EXTR_SKIP);
        include $viewFile;
        exit;
    }
}
