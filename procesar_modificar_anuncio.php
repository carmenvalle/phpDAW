<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/anuncio_filter.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    $_SESSION['flash']['error'] = 'Id de anuncio inválido.';
    header('Location: mis_anuncios.php');
    exit();
}
$id = (int)$_GET['id'];

$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: index.php');
    exit();
}

// For modification images are not required
$_POST['imagenes_required'] = false;
$res = filtrar_anuncio($_POST, $_FILES);
$valores = $res['values'];
$errors = $res['errors'];

if (!empty($errors)) {
    $_SESSION['flash']['nuevo_anuncio_errors'] = $errors;
    // Preserve submitted values to refill form
    $_SESSION['flash']['nuevo_anuncio_values'] = $valores;
    header('Location: modificar_anuncio.php?id=' . $id);
    exit();
}

// Verify ownership before updating
try {
    $s = $conexion->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $s->execute([$id]);
    $row = $s->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $_SESSION['flash']['error'] = 'Anuncio no encontrado.';
        header('Location: mis_anuncios.php');
        exit();
    }
    if ((int)$row['Usuario'] !== (int)$userId) {
        $_SESSION['flash']['error'] = 'No tienes permiso para modificar este anuncio.';
        header('Location: mis_anuncios.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error comprobando permisos.';
    header('Location: mis_anuncios.php');
    exit();
}

// Perform update
try {
    // Obtener valores actuales para comparar y listar cambios
    $sel = $conexion->prepare('SELECT TAnuncio, TVivienda, Titulo, Ciudad, Pais, Precio, Texto, Superficie, NHabitaciones, NBanyos, Planta, Anyo FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $sel->execute([$id]);
    $old = $sel->fetch(PDO::FETCH_ASSOC) ?: [];

    $u = $conexion->prepare('UPDATE Anuncios SET TAnuncio = ?, TVivienda = ?, Titulo = ?, Ciudad = ?, Pais = ?, Precio = ?, Texto = ?, Superficie = ?, NHabitaciones = ?, NBanyos = ?, Planta = ?, Anyo = ? WHERE IdAnuncio = ?');
    $u->execute([
        $valores['tipo_anuncio'],
        $valores['vivienda'],
        $valores['titulo'],
        $valores['ciudad'],
        $valores['pais'],
        $valores['precio'],
        $valores['descripcion'],
        $valores['superficie'],
        $valores['habitaciones'],
        $valores['banos'],
        $valores['planta'],
        $valores['anio'],
        $id
    ]);

    // Comparar campos y preparar detalle de cambios
    $changed = [];
    $mapping = [
        'TAnuncio' => 'tipo_anuncio', 'TVivienda' => 'vivienda', 'Titulo' => 'titulo', 'Ciudad' => 'ciudad', 'Pais' => 'pais',
        'Precio' => 'precio', 'Texto' => 'descripcion', 'Superficie' => 'superficie', 'NHabitaciones' => 'habitaciones',
        'NBanyos' => 'banos', 'Planta' => 'planta', 'Anyo' => 'anio'
    ];
    foreach ($mapping as $col => $key) {
        $oldVal = isset($old[$col]) ? (string)$old[$col] : '';
        $newVal = isset($valores[$key]) ? (string)$valores[$key] : '';
        if ($oldVal !== $newVal) {
            $changed[] = "$col: '" . htmlspecialchars($oldVal) . "' → '" . htmlspecialchars($newVal) . "'";
        }
    }

    if (!empty($changed)) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['flash']['ok'] = 'Anuncio modificado correctamente.';
        $_SESSION['flash']['anuncio_modificado_detalle'] = $changed;
    } else {
        $_SESSION['flash']['ok'] = 'No se han realizado cambios.';
    }
    header('Location: anuncio.php?id=' . $id);
    exit();
} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error actualizando el anuncio.';
    header('Location: modificar_anuncio.php?id=' . $id);
    exit();
}
