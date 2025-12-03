<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/precio.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    $_SESSION['flash']['error'] = 'Id de anuncio inválido.';
    header('Location: /phpDAW/mis-anuncios');
    exit();
}
$id = (int)$_GET['id'];

$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: /phpDAW/');
    exit();
}

try {
    $s = $conexion->prepare('SELECT * FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $s->execute([$id]);
    $an = $s->fetch(PDO::FETCH_ASSOC);
    if (!$an) {
        $_SESSION['flash']['error'] = 'Anuncio no encontrado.';
        header('Location: /phpDAW/mis-anuncios');
        exit();
    }
    if ((int)$an['Usuario'] !== (int)$userId) {
        $_SESSION['flash']['error'] = 'No tienes permiso para modificar este anuncio.';
        header('Location: /phpDAW/mis-anuncios');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error cargando el anuncio.';
    header('Location: /phpDAW/mis-anuncios');
    exit();
}

// Preparar valores para el formulario
$valores = [
    'tipo_anuncio' => $an['TAnuncio'] ?? '',
    'vivienda' => $an['TVivienda'] ?? '',
    'titulo' => $an['Titulo'] ?? '',
    'ciudad' => $an['Ciudad'] ?? '',
    'pais' => $an['Pais'] ?? '',
    'precio' => $an['Precio'] ?? '',
    'descripcion' => $an['Texto'] ?? '',
    'superficie' => $an['Superficie'] ?? '',
    'habitaciones' => $an['NHabitaciones'] ?? '',
    'banos' => $an['NBanyos'] ?? '',
    'planta' => $an['Planta'] ?? '',
    'anio' => $an['Anyo'] ?? ''
];

// Errores (flash)
$errors = $_SESSION['flash']['nuevo_anuncio_errors'] ?? [];
unset($_SESSION['flash']['nuevo_anuncio_errors']);

$form_action = '/phpDAW/procesar_modificar_anuncio?id=' . $id;

$title = 'Modificar anuncio';
$cssPagina = 'nuevo_anuncio.css';
require_once 'cabecera.inc';
require_once 'inicioLog.inc';
?>

<main>
    <section>
        <h2>MODIFICAR ANUNCIO</h2>
    </section>

    <section>
        <?php
        // En modificación no forzamos la subida de imágenes
        $valores['imagenes_required'] = false;
        include __DIR__ . '/includes/anuncio_form.php';
        ?>
    </section>

    <?php require_once('salto.inc'); ?>
</main>

<?php require_once('pie.inc'); ?>
