<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/anuncio_filter.php';

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

// For modification images are not required
$_POST['imagenes_required'] = false;
$res = filtrar_anuncio($_POST, $_FILES);
$valores = $res['values'];
$errors = $res['errors'];

if (!empty($errors)) {
    $_SESSION['flash']['nuevo_anuncio_errors'] = $errors;
    // Preserve submitted values to refill form
    $_SESSION['flash']['nuevo_anuncio_values'] = $valores;
    header('Location: anuncio/' . $id . '/modificar');
    exit();
}

// Verify ownership before updating
try {
    $s = $conexion->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    $s->execute([$id]);
    $row = $s->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $_SESSION['flash']['error'] = 'Anuncio no encontrado.';
        header('Location: /phpDAW/mis-anuncios');
        exit();
    }
    if ((int)$row['Usuario'] !== (int)$userId) {
        $_SESSION['flash']['error'] = 'No tienes permiso para modificar este anuncio.';
        header('Location: /phpDAW/mis-anuncios');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error comprobando permisos.';
    header('Location: /phpDAW/mis-anuncios');
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
        // For certain fields store human-readable names instead of raw IDs
        $displayOld = $oldVal;
        $displayNew = $newVal;
        if ($col === 'TAnuncio') {
            // lookup in TiposAnuncios
            try {
                $sTa = $conexion->prepare('SELECT NomTAnuncio FROM TiposAnuncios WHERE IdTAnuncio = ? LIMIT 1');
                $sTa->execute([(int)$oldVal]);
                $rta = $sTa->fetch(PDO::FETCH_ASSOC);
                if ($rta && !empty($rta['NomTAnuncio'])) $displayOld = $rta['NomTAnuncio'];
            } catch (Exception $e) { /* ignore, fallback to id */ }
            try {
                $sTa2 = $conexion->prepare('SELECT NomTAnuncio FROM TiposAnuncios WHERE IdTAnuncio = ? LIMIT 1');
                $sTa2->execute([(int)$newVal]);
                $rta2 = $sTa2->fetch(PDO::FETCH_ASSOC);
                if ($rta2 && !empty($rta2['NomTAnuncio'])) $displayNew = $rta2['NomTAnuncio'];
            } catch (Exception $e) { /* ignore */ }
        } elseif ($col === 'TVivienda') {
            // lookup in TiposViviendas
            try {
                $sTv = $conexion->prepare('SELECT NomTVivienda FROM TiposViviendas WHERE IdTVivienda = ? LIMIT 1');
                $sTv->execute([(int)$oldVal]);
                $rtv = $sTv->fetch(PDO::FETCH_ASSOC);
                if ($rtv && !empty($rtv['NomTVivienda'])) $displayOld = $rtv['NomTVivienda'];
            } catch (Exception $e) { }
            try {
                $sTv2 = $conexion->prepare('SELECT NomTVivienda FROM TiposViviendas WHERE IdTVivienda = ? LIMIT 1');
                $sTv2->execute([(int)$newVal]);
                $rtv2 = $sTv2->fetch(PDO::FETCH_ASSOC);
                if ($rtv2 && !empty($rtv2['NomTVivienda'])) $displayNew = $rtv2['NomTVivienda'];
            } catch (Exception $e) { }
        }

        if ($displayOld !== $displayNew) {
            $changed[] = "$col: '" . htmlspecialchars($displayOld) . "' → '" . htmlspecialchars($displayNew) . "'";
        }
    }

    if (!empty($changed)) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['flash']['ok'] = 'Anuncio modificado correctamente.';
        $_SESSION['flash']['anuncio_modificado_detalle'] = $changed;
    } else {
        $_SESSION['flash']['ok'] = 'No se han realizado cambios.';
    }
    header('Location: /phpDAW/anuncio/' . $id);
    exit();
} catch (Exception $e) {
    $_SESSION['flash']['error'] = 'Error actualizando el anuncio.';
    header('Location: /phpDAW/anuncio/' . $id . '/modificar');
    exit();
}
