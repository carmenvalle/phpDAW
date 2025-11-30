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

            // Guardar en sesión
            $_SESSION['style'] = $sel;
            // Guardar en cookie sólo si el usuario tiene activo 'recordarme' (cookie con hash de login)
            $remember_active = !empty($_COOKIE['usuario']) && !empty($_COOKIE['clave']);
            if ($remember_active) {
                setcookie('style', $sel, time() + 90*24*60*60, '/');
            } else {
                // Asegurar que no queda cookie previa si el usuario decide cambiar y no está recordado
                if (isset($_COOKIE['style'])) setcookie('style', '', time() - 3600, '/');
            }

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

// Procesar checkboxes adicionales (modo oscuro, letra grande + alto contraste)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determinar si el usuario tiene 'recordarme' activo (cookie con hash)
    $remember_active = !empty($_COOKIE['usuario']) && !empty($_COOKIE['clave']);

    // Modo oscuro
    if (!empty($_POST['modo_oscuro'])) {
        $_SESSION['modo_oscuro'] = 1;
        if ($remember_active) setcookie('modo_oscuro', '1', time() + 90*24*60*60, '/');
    } else {
        unset($_SESSION['modo_oscuro']);
        if ($remember_active && isset($_COOKIE['modo_oscuro'])) setcookie('modo_oscuro', '', time() - 3600, '/');
    }

    // Letra grande + alto contraste
    if (!empty($_POST['accesibilidad_plus'])) {
        $_SESSION['accesibilidad_plus'] = 1;
        if ($remember_active) setcookie('accesibilidad_plus', '1', time() + 90*24*60*60, '/');
    } else {
        unset($_SESSION['accesibilidad_plus']);
        if ($remember_active && isset($_COOKIE['accesibilidad_plus'])) setcookie('accesibilidad_plus', '', time() - 3600, '/');
    }

    // Si no hemos redirigido ya, mostrar mensaje
    if (!isset($_SESSION['mensaje_estilo'])) {
        $_SESSION['mensaje_estilo'] = "Preferencias guardadas.";
        header("Location: configurar.php");
        exit();
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

        <fieldset style="margin-top:1em;">
            <legend>Ajustes adicionales</legend>
            <p>
                <label><input type="checkbox" name="modo_oscuro" value="1" <?= (!empty($_SESSION['modo_oscuro']) || !empty($_COOKIE['modo_oscuro'])) ? 'checked' : '' ?>> Modo oscuro</label>
            </p>
            <p>
                <label><input type="checkbox" name="accesibilidad_plus" value="1" <?= (!empty($_SESSION['accesibilidad_plus']) || !empty($_COOKIE['accesibilidad_plus'])) ? 'checked' : '' ?>> Letra grande + Alto contraste</label>
            </p>
        </fieldset>

        <button type="submit" class="btn-guardar">Guardar estilo</button>
    </form>

    <p><a href="index_logueado.php" class="volver">Volver al inicio</a></p>
</main>

<?php require_once('pie.inc'); ?>
