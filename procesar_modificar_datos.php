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
    // Procesar foto si se proporciona o si se elimina
    $nuevaFoto = null;
    $oldFoto = null;
    
    // Obtener foto actual antes de actualizar
    $s2 = $conexion->prepare('SELECT Foto FROM Usuarios WHERE IdUsuario = ? LIMIT 1');
    $s2->execute([$userId]);
    $userData = $s2->fetch(PDO::FETCH_ASSOC);
    $oldFoto = $userData['Foto'] ?? null;
    
    // Si se sube una nueva foto
    if ($_FILES && !empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nuevaFoto = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dirFotos = __DIR__ . '/DAW/practica/imagenes/';
        if (!is_dir($dirFotos)) @mkdir($dirFotos, 0755, true);
        $rutaFoto = $dirFotos . $nuevaFoto;
        if (move_uploaded_file($file['tmp_name'], $rutaFoto)) {
            // Eliminar foto antigua si existe
            if ($oldFoto && file_exists($dirFotos . $oldFoto)) {
                @unlink($dirFotos . $oldFoto);
            }
        } else {
            // Error al guardar, registrar pero continuar
            if (!is_dir(__DIR__ . '/logs')) @mkdir(__DIR__ . '/logs', 0755, true);
            file_put_contents(__DIR__ . '/logs/registro.log', date('[Y-m-d H:i:s] ') . "modificar_datos: photo upload failed for user " . $values['usuario'] . "\n", FILE_APPEND);
            $nuevaFoto = null;
        }
    }
    
    // Si se solicita eliminar la foto
    if (isset($_POST['eliminar_foto']) && $_POST['eliminar_foto'] === 'on') {
        if ($oldFoto && file_exists(__DIR__ . '/DAW/practica/imagenes/' . $oldFoto)) {
            @unlink(__DIR__ . '/DAW/practica/imagenes/' . $oldFoto);
        }
        $nuevaFoto = null; // Para actualizar a NULL en BD
        $userToUpdateFoto = null;
    } else {
        $userToUpdateFoto = $nuevaFoto;
    }
    
    // Update user fields
    $sqlUpdate = 'UPDATE Usuarios SET NomUsuario = ?, Email = ?, Sexo = ?, FNacimiento = ?, Ciudad = ?, Pais = ?';
    $params = [
        $values['usuario'],
        $values['email'],
        $sexo_db,
        $values['nacimiento'],
        $values['ciudad'],
        $values['pais']
    ];
    
    if ($nuevaFoto) {
        $sqlUpdate .= ', Foto = ?';
        $params[] = $nuevaFoto;
    } elseif (isset($_POST['eliminar_foto']) && $_POST['eliminar_foto'] === 'on') {
        $sqlUpdate .= ', Foto = NULL';
    }
    
    $sqlUpdate .= ' WHERE IdUsuario = ?';
    $params[] = $userId;
    
    $upd = $conexion->prepare($sqlUpdate);
    $upd->execute($params);

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
