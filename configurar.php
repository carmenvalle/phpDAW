<?php
// configurar.php
// Procesar selección de tema antes de enviar cualquier salida (setcookie debe enviar headers).
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$title = "Configurar estilo visual";
$cssPagina = "configurar.css";

// Lista blanca de estilos: clave => etiqueta
$estilos = [
    "default" => "Estilo clásico",
    "modo_oscuro" => "Modo oscuro",
    "letra_grande" => "Letra grande / accesibilidad",
    "alto_contraste" => "Alto contraste",
    "contraste_letra" => "Alto contraste + accesibilidad"
];

// Incluir conexión solo si vamos a necesitarla (opcional guardar en BD)
if (file_exists(__DIR__ . '/includes/conexion.php')) {
    require_once __DIR__ . '/includes/conexion.php';
}

// Manejo de POST: validar clave y guardar en sesión + cookie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style'])) {
    $sel = (string)$_POST['style'];
    if (array_key_exists($sel, $estilos)) {
        // Guardar en sesión (prioridad)
        $_SESSION['style'] = $sel;
        $_SESSION['estilo'] = $sel; // compatibilidad

        // Guardar cookie (90 días). Esto debe ocurrir antes de imprimir HTML.
        setcookie('style', $sel, time() + 90 * 24 * 60 * 60, '/');

        // Si el usuario está logueado, opcionalmente guardar en BD
        if (!empty($_SESSION['id']) && isset($conexion) && $conexion instanceof PDO) {
            try {
                $upd = $conexion->prepare('UPDATE Usuarios SET Estilo = ? WHERE IdUsuario = ?');
                $upd->execute([$sel, (int)$_SESSION['id']]);
            } catch (Exception $e) {
                // No detener el flujo por fallo en BD
            }
        }

        // Redirigir para evitar reenvío de formulario
        header('Location: configurar.php');
        exit();
    }
}

// A partir de aquí, podemos incluir la cabecera y empezar a enviar HTML
require_once('cabecera.inc');
require_once('inicioLog.inc');
?>

<main>
    <h2>Configurar apariencia visual</h2>
    <p>Selecciona el estilo visual que prefieres para la web:</p>

    <form action="configurar.php" method="post" class="form-estilos">
        <ul class="lista-estilos">

            <?php foreach ($estilos as $valor => $texto): ?>
                <li>
                    <label>
                        <input type="radio" name="style" value="<?php echo htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo (isset($_SESSION['style']) && $_SESSION['style'] === $valor) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                </li>
            <?php endforeach; ?>

        </ul>

        <button type="submit" class="btn-guardar">Guardar estilo</button>
    </form>

    <p><a href="index_logueado.php" class="volver">Volver al inicio</a></p>
</main>

<?php require_once('pie.inc'); ?>
