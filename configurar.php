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
// Añadir opciones especiales (compatibilidad): modo_oscuro y contraste+letra
$specials = [
    ['IdEstilo' => 'modo_oscuro', 'Nombre' => 'Modo oscuro', 'Fichero' => 'modo_oscuro.css'],
    ['IdEstilo' => 'contraste_letra', 'Nombre' => 'Alto contraste y letra grande', 'Fichero' => 'alto-contraste-accesibilidad.css']
];
// Si no vienen en la lista desde la BD, añadirlas para que aparezcan como opciones
$found_keys = array_map(function($e){ return (string)$e['IdEstilo']; }, $estilos);
foreach ($specials as $sp) {
    if (!in_array((string)$sp['IdEstilo'], $found_keys, true)) {
        $estilos[] = $sp;
    }
}

// Procesar selección POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style'])) {
    $sel_raw = $_POST['style'];
    $style_selected_special = false;

    // Buscar la opción en $estilos (comparando como string para incluir los especiales)
    $matched = null;
    foreach ($estilos as $est) {
        if ((string)$est['IdEstilo'] === (string)$sel_raw) { $matched = $est; break; }
    }

    if ($matched) {
        // Guardar selección en sesión (puede ser numérico o una clave como 'modo_oscuro')
        $_SESSION['style'] = (string)$matched['IdEstilo'];

        // Si la selección es una de las especiales, marcar las flags correspondientes
        if ($_SESSION['style'] === 'modo_oscuro') {
            $_SESSION['modo_oscuro'] = 1;
            unset($_SESSION['accesibilidad_plus']);
            $style_selected_special = true;
        } elseif ($_SESSION['style'] === 'contraste_letra') {
            $_SESSION['accesibilidad_plus'] = 1;
            unset($_SESSION['modo_oscuro']);
            $style_selected_special = true;
        } else {
            // selección numérica normal: quitar flags adicionales para evitar duplicidades
            unset($_SESSION['modo_oscuro']);
            unset($_SESSION['accesibilidad_plus']);
        }

        // Guardar en cookie sólo si el usuario tiene activo 'recordarme' (cookie con hash de login)
        $remember_active = !empty($_COOKIE['usuario']) && !empty($_COOKIE['clave']);
        if ($remember_active) {
            setcookie('style', (string)$_SESSION['style'], time() + 90*24*60*60, '/');
            if (!empty($_SESSION['modo_oscuro'])) setcookie('modo_oscuro','1', time() + 90*24*60*60, '/');
            if (!empty($_SESSION['accesibilidad_plus'])) setcookie('accesibilidad_plus','1', time() + 90*24*60*60, '/');
        } else {
            if (isset($_COOKIE['style'])) setcookie('style', '', time() - 3600, '/');
            if (isset($_COOKIE['modo_oscuro'])) setcookie('modo_oscuro','', time() - 3600, '/');
            if (isset($_COOKIE['accesibilidad_plus'])) setcookie('accesibilidad_plus','', time() - 3600, '/');
        }

        // Actualizar en BD si el usuario está logueado. Para claves especiales, intentar mapear al IdEstilo
        if (!empty($_SESSION['id'])) {
            try {
                $updId = null;
                if (ctype_digit((string)$matched['IdEstilo'])) {
                    $updId = (int)$matched['IdEstilo'];
                } else {
                    // intentar buscar en la BD por nombre normalizado
                    $cand = mb_strtolower($matched['Nombre']);
                    $cand = preg_replace('/[^\p{L}0-9]+/u', '_', $cand);
                    $cand = trim($cand, '_');
                    $q = $conexion->prepare('SELECT IdEstilo FROM Estilos WHERE LOWER(REPLACE(Nombre, " ", "_")) LIKE ? LIMIT 1');
                    $q->execute(["%" . $cand . "%"]);
                    $r = $q->fetch(PDO::FETCH_ASSOC);
                    if ($r && isset($r['IdEstilo'])) $updId = (int)$r['IdEstilo'];
                }

                if ($updId !== null) {
                    $upd = $conexion->prepare("UPDATE usuarios SET Estilo=? WHERE IdUsuario=?");
                    $upd->execute([$updId, (int)$_SESSION['id']]);
                }

            } catch (Exception $e) {}
        }

        // Mensaje de éxito que se mostrará tras redirect
        $_SESSION['mensaje_estilo'] = "Estilo visual actualizado correctamente.";

        // Redirigir para evitar reenvío (ruta canónica absoluta)
        header("Location: /phpDAW/configurar");
        exit();
    }
}

// Nota: las opciones de Modo oscuro y Alto contraste + Letra grande
// se gestionan ahora como estilos seleccionables (radios) y se
// sincronizan con las flags de sesión en el bloque principal de POST.

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

    <form action="/phpDAW/configurar" method="post" class="form-estilos">
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

        <!-- Las opciones de accesibilidad ahora están disponibles como estilos seleccionables arriba -->

        <button type="submit" class="btn-guardar">Guardar estilo</button>
    </form>

    <p><a href="/phpDAW/index_logueado" class="volver">Volver al inicio</a></p>
</main>

<?php require_once('pie.inc'); ?>
