<?php
session_start();

// Cargar conexión
require_once __DIR__ . '/includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, setear flash y redirigir al formulario de acceso (index.php incluye el formulario)
    $_SESSION['flash']['acceso_error'] = 'Debes iniciar sesión para acceder a esa página.';
    header('Location: index.php');
    exit;
}
// Obtener IdUsuario a partir del nombre de usuario de sesión
$nomUsuario = $_SESSION['usuario'];
try {
    $s = $conexion->prepare('SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ? LIMIT 1');
    $s->execute([$nomUsuario]);
    $u = $s->fetch(PDO::FETCH_ASSOC);
    if (!$u) {
        // usuario no encontrado en BD
        $_SESSION['flash']['acceso_error'] = 'Usuario no encontrado.';
        header('Location: index.php');
        exit;
    }
    $idUsuario = (int)$u['IdUsuario'];

    // Obtener listado de anuncios y fotos asociados (usar nombres de tabla/columnas coherentes con la BD)
    $query = "SELECT a.IdAnuncio AS id, a.Titulo AS titulo, COUNT(f.IdFoto) AS fotos
              FROM Anuncios a
              LEFT JOIN Fotos f ON f.Anuncio = a.IdAnuncio
              WHERE a.Usuario = ?
              GROUP BY a.IdAnuncio";

    $result = $conexion->prepare($query);
    $result->execute([$idUsuario]);
    $anuncios = $result->fetchAll();

} catch (Exception $e) {
    // registrar y mostrar mensaje ligero
    if (!is_dir(__DIR__ . '/logs')) @mkdir(__DIR__ . '/logs', 0755, true);
    file_put_contents(__DIR__ . '/logs/dar_baja.log', date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, FILE_APPEND);
    $_SESSION['flash']['error'] = 'Error al cargar tus anuncios.';
    $anuncios = [];
}

// Obtener totales
$totalAnuncios = count($anuncios);
$totalFotos = 0;
foreach ($anuncios as $a) {
    $totalFotos += $a['fotos'];
}

// Incluir cabecera
require_once('cabecera.inc');
require_once('inicioLog.inc');

// Cargar vista con los datos si existe (evitar warning si la carpeta/views no está presente)
$viewPath = __DIR__ . '/views/confirmar-dar-baja.php';
if (file_exists($viewPath)) {
    include $viewPath;
} else {
    // opcional: registrar que falta la vista y continuar mostrando la plantilla embebida
    if (!is_dir(__DIR__ . '/logs')) @mkdir(__DIR__ . '/logs', 0755, true);
    file_put_contents(__DIR__ . '/logs/dar_baja.log', date('[Y-m-d H:i:s] ') . "missing view: $viewPath\n", FILE_APPEND);
}
?>

<main>
    <h2>Confirmar baja de usuario</h2>

    <?php if (isset($_SESSION['flash']) && is_array($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $k => $msg): ?>
            <p class="flash-message flash-<?php echo htmlspecialchars($k); ?>" style="color:darkred; font-weight:600;"><?php echo htmlspecialchars($msg); ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <p>Esta operación eliminará permanentemente tu cuenta y todos tus datos.</p>

    <h3>Tus anuncios:</h3>
    <ul>
        <?php foreach ($anuncios as $a): ?>
            <li><?= $a['titulo'] ?> — <?= $a['fotos'] ?> fotos</li>
        <?php endforeach; ?>
    </ul>

    <p><b>Total anuncios:</b> <?= $totalAnuncios ?></p>
    <p><b>Total fotos:</b> <?= $totalFotos ?></p>

    <form action="confirmar-dar-baja.php" method="POST">
        <label>Introduce tu contraseña para confirmar:</label>
        <input type="password" name="password" required>

        <button type="submit">Eliminar definitivamente mi cuenta</button>
    </form>
</main>

<?php require_once('pie.inc'); ?>