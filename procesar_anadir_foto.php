<?php
session_start();
require_once(__DIR__ . '/includes/conexion.php');

if (empty($_SESSION['usuario'])) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: /phpDAW/');
    exit();
}

// Validar id del anuncio
if (!isset($_POST['id_anuncio']) || !ctype_digit((string)$_POST['id_anuncio'])) {
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

// Procesar upload de fichero
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['flash']['error'] = 'Debes seleccionar una fotografía.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

$file = $_FILES['foto'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['flash']['error'] = 'Error en la subida del archivo.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

// Validar tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mime, $allowedMimes, true)) {
    $_SESSION['flash']['error'] = 'Tipo de archivo no permitido. Usa JPG, PNG, GIF o WEBP.';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

// Validar tamaño (máximo 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['flash']['error'] = 'El archivo es demasiado grande (máximo 5MB).';
    header('Location: /phpDAW/anyadir_foto?id=' . $idAnuncio);
    exit();
}

// Generar nombre único
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
    $ext = 'jpg'; // Fallback
}
$nombreFoto = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
$dir = __DIR__ . '/DAW/practica/imagenes/';
if (!is_dir($dir)) @mkdir($dir, 0755, true);
$rutaFoto = $dir . $nombreFoto;

if (!move_uploaded_file($file['tmp_name'], $rutaFoto)) {
    $_SESSION['flash']['error'] = 'Error al guardar el archivo en el servidor.';
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
