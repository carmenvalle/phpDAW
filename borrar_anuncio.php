<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/precio.php';

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    $_SESSION['flash']['error'] = 'Anuncio inválido.';
    header('Location: mis_anuncios.php');
    exit();
}
$id = (int)$_GET['id'];

// comprobar propietario
$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: index.php');
    exit();
}

try {
    $s = $conexion->prepare('SELECT IdAnuncio, Titulo, Precio, Ciudad, FRegistro, Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $s->execute([$id]);
    $an = $s->fetch(PDO::FETCH_ASSOC);
    if (!$an) {
        $_SESSION['flash']['error'] = 'Anuncio no encontrado.';
        header('Location: mis_anuncios.php');
        exit();
    }
    if ((int)$an['Usuario'] !== (int)$userId) {
        $_SESSION['flash']['error'] = 'No tienes permiso para borrar este anuncio.';
        header('Location: mis_anuncios.php');
        exit();
    }

    // contar fotos
    $s2 = $conexion->prepare('SELECT COUNT(*) AS total FROM Fotos WHERE Anuncio = ?');
    $s2->execute([$id]);
    $totalFotos = (int)($s2->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error al cargar anuncio.';
    header('Location: mis_anuncios.php');
    exit();
}

$title = 'Confirmar borrado del anuncio';
$cssPagina = 'resultados.css';
require_once 'cabecera.inc';
require_once 'inicioLog.inc';
?>

<main style="display:flex;justify-content:center;">
    <div style="max-width:720px;width:100%;padding:18px;background:#fff;border-radius:8px;border:1px solid rgba(0,0,0,0.04);box-shadow:var(--sombra);">
        <h2>Eliminar anuncio</h2>
        <p>Vas a eliminar el anuncio <strong><?php echo htmlspecialchars($an['Titulo'], ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
        <ul>
            <li><strong>Ciudad:</strong> <?php echo htmlspecialchars($an['Ciudad'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></li>
            <li><strong>Precio:</strong> <?php echo isset($an['Precio']) ? formatearPrecio($an['Precio']) : '—'; ?></li>
            <li><strong>Fecha de publicación:</strong> <?php echo htmlspecialchars($an['FRegistro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
            <li><strong>Total de fotos asociadas:</strong> <?php echo $totalFotos; ?></li>
        </ul>

        <p style="color:#a00;">Esta operación eliminará el anuncio y todos los datos asociados (fotos, mensajes relacionados). No se puede deshacer.</p>

        <form method="post" action="respuesta-borrar-anuncio">
            <input type="hidden" name="idAnuncio" value="<?php echo $id; ?>">
            <p>
                <button type="submit" style="background:#c00;color:#fff;padding:10px 18px;border:none;border-radius:6px;cursor:pointer;">Eliminar anuncio</button>
                <a href="mis_anuncios" style="margin-left:12px;text-decoration:none;color:#333;">Cancelar</a>
            </p>
        </form>
    </div>
</main>

<?php require_once 'pie.inc'; ?>
