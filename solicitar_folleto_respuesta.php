<?php
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "solicitar_folleto.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
require_once(__DIR__ . '/includes/precio.php');

// ===== RECOGER DATOS DEL FORMULARIO =====
$nombre = $_POST["nombre"] ?? "";
$correo = $_POST["correo"] ?? "";
$telefono = $_POST["telefono"] ?? "";
$calle = $_POST["calle"] ?? "";
$numero = $_POST["numero"] ?? "";
$piso = $_POST["piso"] ?? "";
$codigo_postal = $_POST["codigo_postal"] ?? "";
$localidad = $_POST["localidad"] ?? "";
$provincia = $_POST["provincia"] ?? "";
$pais = $_POST["pais"] ?? "";
$texto = $_POST["texto"] ?? "";
$color_portada = $_POST["color"] ?? "#000000";
$paginas = intval($_POST["paginas"] ?? 8);
$copias = intval($_POST["copias"] ?? 1);
$resolucion = intval($_POST["resolucion"] ?? 150);
$anuncio = $_POST["anuncio"] ?? "";
$fecha = $_POST["fecha"] ?? "";
$impresion_color = $_POST["impresion_color"] ?? "";
$mostrar_precio = $_POST["mostrar_precio"] ?? "";

// ===== CÁLCULO DEL COSTE =====
$isColor = ($impresion_color === "color");
$precioTotal = calcularPrecio($paginas, $isColor, $resolucion, $copias); 
$precioUnitario = calcularPrecio($paginas, $isColor, $resolucion, 1); 
$total_formateado = formatearPrecio($precioTotal);

// Insertar solicitud en la base de datos (una vez recogidos los datos y calculado el precio)
$insertOk = false;
if (file_exists(__DIR__ . '/includes/conexion.php')) {
    require_once __DIR__ . '/includes/conexion.php';
    try {
        $direccion = "$calle, $numero, $piso, $codigo_postal, $localidad, $provincia, $pais";
        $stmt = $conexion->prepare("INSERT INTO Solicitudes (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, Coste) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $anuncio !== '' ? $anuncio : null,
            $texto,
            $nombre,
            $correo,
            $direccion,
            $telefono,
            $color_portada,
            $copias,
            $resolucion,
            $fecha !== '' ? $fecha : null,
            $isColor ? 1 : 0,
            ($mostrar_precio === 'si') ? 1 : 0,
            $precioTotal
        ]);
        $insertOk = true;
    } catch (Exception $e) {
        // Registrar detalle técnico para depuración y notificar al usuario
        error_log('[solicitar_folleto_respuesta] Error al insertar Solicitud: ' . $e->getMessage());
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['flash']['error'] = 'No se ha podido registrar la solicitud. Inténtalo más tarde.';
        $insertOk = false;
    }
} else {
    // Si no existe el fichero de conexión, registrar y avisar
    error_log('[solicitar_folleto_respuesta] No se encontró includes/conexion.php');
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['flash']['error'] = 'No se ha podido procesar la solicitud. Inténtalo más tarde.';
    $insertOk = false;
}
?>

<main>
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $flashError = $_SESSION['flash']['error'] ?? null;
    if ($flashError) {
        unset($_SESSION['flash']['error']);
    }
    ?>

    <?php if ($insertOk): ?>
        <p class="mensaje-confirmacion">Tu solicitud ha sido registrada correctamente.</p>

        <p class="coste-total">
            <strong>Coste total del folleto:</strong>
            <?php echo $total_formateado; ?>
        </p>
    <?php else: ?>
        <p class="mensaje-error"><?php echo htmlspecialchars($flashError ?? 'Se ha producido un error al procesar la solicitud.'); ?></p>
        <p class="coste-total">
            <strong>Coste estimado del folleto:</strong>
            <?php echo $total_formateado; ?>
        </p>
        <p><a href="folleto" class="btn"><strong>VOLVER AL FORMULARIO</strong></a></p>
    <?php endif; ?>

    <section>
        <h2>Respuestas del formulario:</h2>

        <fieldset>
            <legend><strong>RESPUESTA</strong></legend>

            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
            <p><strong>Dirección:</strong>
                <?php
                    echo htmlspecialchars("$calle, $numero, $piso, $codigo_postal, $localidad, $provincia, $pais");
                ?>
            </p>
            <p><strong>Texto adicional:</strong> <?php echo htmlspecialchars($texto ?: "—"); ?></p>
            <p><strong>Color de portada:</strong>
                <input type="color" value="<?php echo htmlspecialchars($color_portada); ?>" disabled>
            </p>
            <p><strong>Número de copias:</strong> <?php echo htmlspecialchars($copias); ?></p>
            <p><strong>Resolución:</strong> <?php echo htmlspecialchars($resolucion); ?> dpi</p>
            <p><strong>Anuncio seleccionado:</strong> <?php echo htmlspecialchars($anuncio ?: "—"); ?></p>
            <p><strong>Fecha de recepción:</strong>
                <?php echo $fecha ? date("d/m/Y", strtotime($fecha)) : "—"; ?>
            </p>
            <p><strong>Impresión a color:</strong>
                <?php echo $isColor ? "A color" : "Blanco y negro"; ?>
            </p>
            <p><strong>Mostrar precio:</strong>
                <?php echo ($mostrar_precio === "si") ? "Sí" : "No"; ?>
            </p>
        </fieldset>
    </section>

    <?php require_once("salto.inc"); ?>
</main>

<?php
require_once("pie.inc");
?>
