<?php
if (!defined('APP_INIT')) { define('APP_INIT', true); }
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: /phpDAW/');
    exit;
}

$title = 'Subida masiva de fotos';
$cssPagina = 'miperfil.css';

require_once __DIR__ . '/cabecera.inc';
require_once __DIR__ . '/privado.inc';
require_once __DIR__ . '/inicioLog.inc';
require_once __DIR__ . '/includes/conexion.php';

$misAnuncios = [];
try {
    if (isset($conexion)) {
        $stmt = $conexion->prepare('SELECT IdAnuncio, Titulo FROM Anuncios WHERE Usuario = ? ORDER BY FRegistro DESC');
        $stmt->execute([$_SESSION['id']]);
        $misAnuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    $misAnuncios = [];
}
?>

<main style="max-width:900px;margin:0 auto;padding:1rem;">
  <h2>Subida masiva de fotos</h2>
  <p>Selecciona un anuncio y elige varias fotos desde tu ordenador.</p>

  <?php if (empty($misAnuncios)): ?>
    <p>No tienes anuncios. Crea uno antes de subir fotos.</p>
  <?php else: ?>
    <form action="/phpDAW/procesar_subir_multiples.php" method="post" enctype="multipart/form-data" style="display:grid;gap:14px;">
      <label>
        Anuncio:
        <select name="anuncio" required>
          <option value="">— Selecciona anuncio —</option>
          <?php foreach ($misAnuncios as $a): ?>
            <option value="<?= (int)$a['IdAnuncio'] ?>"><?= htmlspecialchars($a['Titulo'] ?: 'Sin título') ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Fotos (puedes seleccionar varias):
        <input type="file" name="fotos[]" accept="image/*" multiple required>
      </label>

      <div>
        <button type="submit" class="btn"><strong>Subir fotos</strong></button>
        <a class="btn" href="/phpDAW/mis-anuncios">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/pie.inc'; ?>
