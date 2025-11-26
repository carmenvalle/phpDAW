<?php
// configurar.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$title = "Configurar estilo visual";
$cssPagina = "configurar.css";

// Cargar conexión
require_once __DIR__ . '/includes/conexion.php';

// Obtener estilos desde la BD
$estilos = [];
try {
    $sql = $conexion->prepare("SELECT IdEstilo, Nombre, Fichero FROM estilos ORDER BY IdEstilo");
    $sql->execute();
    $estilos = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $estilos = [];
}

$mensaje = "";

// Procesar selección POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style'])) {

    $sel = (int)$_POST['style'];   // ID numérico

    // Comprobar que existe en la lista cargada
    foreach ($estilos as $est) {
        if ((int)$est['IdEstilo'] === $sel) {

            // Guardar en sesión y cookie
            $_SESSION['style'] = $sel;
            setcookie('style', $sel, time() + 90*24*60*60, '/');

            // Actualizar en BD si el usuario está logueado
            if (!empty($_SESSION['id'])) {
                try {
                    $upd = $conexion->prepare("UPDATE usuarios SET Estilo=? WHERE IdUsuario=?");
                    $upd->execute([$sel, (int)$_SESSION['id']]);
                } catch (Exception $e) {}
            }

            // Mensaje de éxito que se mostrará tras redirect
            $_SESSION['mensaje_estilo'] = "Estilo visual actualizado correctamente.";

            // Redirigir para evitar reenvío
            header("Location: configurar.php");
            exit();
        }
    }
}

// Mostrar mensaje si existe
if (isset($_SESSION['mensaje_estilo'])) {
    $mensaje = $_SESSION['mensaje_estilo'];
    unset($_SESSION['mensaje_estilo']);
}

// Incluir cabecera
require_once('cabecera.inc');
require_once('inicioLog.inc');
?>

<main>
    <h2>Configurar apariencia visual</h2>

    <?php if ($mensaje): ?>
        <p class="mensaje-ok"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <p>Selecciona el estilo visual que prefieres para la web:</p>

    <form action="configurar.php" method="post" class="form-estilos">
        <ul class="lista-estilos">

            <?php foreach ($estilos as $e): ?>
                <li>
                    <label>
                        <input type="radio" name="style"
                               value="<?= htmlspecialchars($e['IdEstilo'], ENT_QUOTES, 'UTF-8'); ?>"
                               <?= (isset($_SESSION['style']) && $_SESSION['style'] == $e['IdEstilo']) ? 'checked' : ''; ?>>
                        <?= htmlspecialchars($e['Nombre'], ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                </li>
            <?php endforeach; ?>

        </ul>

        <button type="submit" class="btn-guardar">Guardar estilo</button>
    </form>

    <p><a href="index_logueado.php" class="volver">Volver al inicio</a></p>
</main>

<?php require_once('pie.inc'); ?>
