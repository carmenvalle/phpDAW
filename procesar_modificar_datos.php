<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/usuario_filter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /phpDAW/modificar_datos');
    exit();
}

$userId = $_SESSION['id'] ?? null;
if (!$userId) {
    $_SESSION['flash']['error'] = 'Debes iniciar sesión.';
    header('Location: /phpDAW/');
    exit();
}

// Validate via usuario_filter in 'modify' context
$filtered = filtrar_usuario($_POST, $_FILES, 'modify');
$values = $filtered['values'];
$errors = $filtered['errors'];

// Ensure current password provided
$current = $_POST['current_password'] ?? '';
if ($current === '') $errors[] = 'current_password_required';

if (!empty($errors)) {
    $_SESSION['flash']['misdatos_errors'] = $errors;
    $_SESSION['flash']['misdatos_values'] = $values;
    header('Location: /phpDAW/modificar_datos');
    exit();
}

// Verify current password matches DB
try {
    $s = $conexion->prepare('SELECT Clave FROM Usuarios WHERE IdUsuario = ? LIMIT 1');
    $s->execute([$userId]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    if (!$r || !isset($r['Clave']) || !password_verify($current, $r['Clave'])) {
        $_SESSION['flash']['misdatos_errors'] = ['current_password_invalid'];
        $_SESSION['flash']['misdatos_values'] = $values;
        header('Location: /phpDAW/modificar_datos');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['flash']['misdatos_errors'] = ['db_error'];
    $_SESSION['flash']['misdatos_values'] = $values;
    header('Location: /phpDAW/modificar_datos');
    exit();
}

// Map sexo to DB code
$sexo_in = strtoupper($values['sexo']);
if ($sexo_in === 'H' || $sexo_in === '1') $sexo_db = 1;
elseif ($sexo_in === 'M' || $sexo_in === '2') $sexo_db = 2;
else $sexo_db = 0;

try {
    // Update user fields
    $upd = $conexion->prepare('UPDATE Usuarios SET NomUsuario = ?, Email = ?, Sexo = ?, FNacimiento = ?, Ciudad = ?, Pais = ? WHERE IdUsuario = ?');
    $upd->execute([
        $values['usuario'],
        $values['email'],
        $sexo_db,
        $values['nacimiento'],
        $values['ciudad'],
        $values['pais'],
        $userId
    ]);

    // If new password provided, update
    if (!empty($values['new_password'])) {
        $h = password_hash($values['new_password'], PASSWORD_DEFAULT);
        $u2 = $conexion->prepare('UPDATE Usuarios SET Clave = ? WHERE IdUsuario = ?');
        $u2->execute([$h, $userId]);
        // If user had 'recordarme' cookies, update cookie with new hash
        if (!empty($_COOKIE['usuario']) && !empty($_COOKIE['clave'])) {
            setcookie('clave', $h, time() + 90*24*60*60, '/');
        }
    }

    // If username changed and recordarme cookie present, update cookie
    if (!empty($_COOKIE['usuario']) && $_COOKIE['usuario'] !== $values['usuario']) {
        setcookie('usuario', $values['usuario'], time() + 90*24*60*60, '/');
    }

    // If style provided in POST (from configurar), update cookie only if recordarme active — handled elsewhere

    $_SESSION['flash']['ok'] = 'Datos actualizados correctamente.';
    header('Location: /phpDAW/miperfil');
    exit();
} catch (Exception $e) {
    $_SESSION['flash']['misdatos_errors'] = ['db_error'];
    $_SESSION['flash']['misdatos_values'] = $values;
    header('Location: /phpDAW/modificar_datos');
    exit();
}
