<?php
session_start();
require_once(__DIR__ . '/includes/conexion.php');

if (empty($_SESSION['usuario'])) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];

// Validar id del anuncio
if (!isset($_POST['id_anuncio']) || !ctype_digit((string)$_POST['id_anuncio'])) {
    $_SESSION['flash']['error'] = 'Anuncio inválido.';
    header('Location: anyadir_foto.php');
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
    header('Location: anyadir_foto.php?id=' . $idAnuncio);
    exit();
}

if ($alt === '' || strlen($alt) < 10) {
    $_SESSION['flash']['error'] = 'El texto alternativo es obligatorio y debe tener al menos 10 caracteres.';
    header('Location: anyadir_foto.php?id=' . $idAnuncio);
    exit();
}

foreach ($prohibidos as $p) {
    if (str_starts_with($altLower, $p)) {
        $_SESSION['flash']['error'] = 'El texto alternativo no debe empezar por "' . $p . '".';
        header('Location: anyadir_foto.php?id=' . $idAnuncio);
        exit();
    }
}


$nombreFoto = trim($_POST['nombre_foto'] ?? 'house4_main.jpg');

// Insertar en la base de datos
try {
    $stmt = $conexion->prepare('INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)');
    $stmt->execute([$titulo, $nombreFoto, $alt, $idAnuncio]);

    $_SESSION['flash']['ok'] = 'Foto añadida correctamente en la base de datos.';
    header('Location: anuncio.php?id=' . $idAnuncio);
    exit();
} catch (PDOException $e) {
    $_SESSION['flash']['error'] = 'Error al guardar la foto: ' . $e->getMessage();
    header('Location: anyadir_foto.php?id=' . $idAnuncio);
    exit();
}
?>
