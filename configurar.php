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

// Incluir conexión solo si vamos a necesitarla
if (file_exists(__DIR__ . '/includes/conexion.php')) {
    require_once __DIR__ . '/includes/conexion.php';
}

// Modo lectura: ignorar POST para que la página sea inofensiva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Intencionalmente vacío: no ejecutar lógica que modifique sesión, cookies o BD.
}

// A partir de aquí, podemos incluir la cabecera y empezar a enviar HTML
require_once('cabecera.inc');
require_once('inicioLog.inc');
?>

<main>
    <h2>Configurar apariencia visual</h2>
    <p>Selecciona el estilo visual que prefieres para la web:</p>

    <form action="configurar.php" method="post" class="form-estilos" onsubmit="return false;" aria-disabled="true" role="form">
        <ul class="lista-estilos">

            <?php foreach ($estilos as $valor => $texto): ?>
                <li>
                    <label>
                        <input type="radio" name="style" value="<?php echo htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo (isset($_SESSION['style']) && $_SESSION['style'] === $valor) ? 'checked' : ''; ?> disabled>
                        <?php echo htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                </li>
            <?php endforeach; ?>

        </ul>

        <button type="submit" class="btn-guardar" disabled>Guardar estilo</button>
    </form>

    <p><a href="#" class="volver" onclick="return false;" aria-disabled="true">Volver al inicio</a></p>
</main>

<?php require_once('pie.inc'); ?>
