<?php
session_start();
require_once(__DIR__ . '/includes/conexion.php');

if (empty($_SESSION['usuario'])) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: /phpDAW/');
    exit();
    $_SESSION['flash']['error'] = 'Anuncio inválido.';
    header('Location: /phpDAW/anyadir_foto');
    exit();
}
$idAnuncio = (int)$_POST['id_anuncio'];

// Validar campos del formulario
$titulo = trim($_POST['titulo_foto'] ?? '');
$alt = trim($_POST['alt'] ?? '');

$prohibidos = ['foto', 'imagen', 'texto'];
$altLower = strtolower($alt);

if ($titulo === '') {
    $_SESSION['flash']['error'] = 'El título de la foto es obligatorio.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

if ($alt === '' || strlen($alt) < 10) {
    $_SESSION['flash']['error'] = 'El texto alternativo es obligatorio y debe tener al menos 10 caracteres.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

foreach ($prohibidos as $p) {
    if (str_starts_with($altLower, $p)) {
        $_SESSION['flash']['error'] = 'El texto alternativo no debe empezar por "' . $p . '".';
        header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
        exit();
    }
}


$
// En esta práctica no gestionamos la subida automática de ficheros.
// El usuario debe subir manualmente el archivo a /phpDAW/DAW/practica/imagenes y proporcionar
// el nombre de fichero en el formulario (`nombre_foto`). Validamos ese nombre aquí.
$nombreFoto = trim($_POST['nombre_foto'] ?? '');
if ($nombreFoto === '') {
    $_SESSION['flash']['error'] = 'Debes indicar el nombre del fichero tal como lo has subido al servidor.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

$name = basename($nombreFoto);
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
$allowedExt = ['jpg','jpeg','png','gif','webp'];
if (!in_array($ext, $allowedExt, true)) {
    $_SESSION['flash']['error'] = 'Extensión de fichero no permitida. Usa JPG, PNG, GIF o WEBP.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

$dir = __DIR__ . DIRECTORY_SEPARATOR . 'DAW' . DIRECTORY_SEPARATOR . 'practica' . DIRECTORY_SEPARATOR . 'imagenes' . DIRECTORY_SEPARATOR;
$fullpath = $dir . $name;
if (!file_exists($fullpath) || !is_file($fullpath)) {
    $_SESSION['flash']['error'] = 'No se encuentra el fichero en el servidor. Sube el archivo manualmente a /phpDAW/DAW/practica/imagenes y escribe el nombre exacto.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

// Insertar en la base de datos
try {
    $stmt = $conexion->prepare('INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)');
    $stmt->execute([$titulo, $name, $alt, $idAnuncio]);

    // Si el anuncio no tiene foto principal, actualizarla
    $u = $conexion->prepare('UPDATE Anuncios SET FPrincipal = ? WHERE IdAnuncio = ? AND (FPrincipal IS NULL OR FPrincipal = "" )');
    $u->execute([$name, $idAnuncio]);

    $_SESSION['flash']['ok'] = 'Foto añadida correctamente (archivo suministrado manualmente).';
    header('Location: /phpDAW/anuncio/' . $idAnuncio);
    exit();
} catch (PDOException $e) {
    // No eliminar fichero físico aquí; dejar fichero como está (práctica actual).
    $_SESSION['flash']['error'] = 'Error al guardar la foto: ' . $e->getMessage();
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}
?>
