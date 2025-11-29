<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.php");
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once 'includes/conexion.php';

$errors = [];
$old = [];

// Recoger datos
$campos = ['usuario','contrasena','repetir','email','sexo','nacimiento','ciudad','pais'];
foreach ($campos as $c) $old[$c] = trim($_POST[$c] ?? "");


// Validaciones combinadas: regex, filter_var y comprobaciones propias

// Usuario: debe empezar por letra, sólo letras y números, longitud 3-15
$user_input = $old['usuario'];
if ($user_input === '') {
    $errors[] = 'usuario';
} else {
    $u_len = mb_strlen($user_input);
    if ($u_len < 3) $errors[] = 'usuario_short';
    if ($u_len > 15) $errors[] = 'usuario_long';
    if (!preg_match('/^[A-Za-z]/', $user_input)) $errors[] = 'usuario_start';
    if (!preg_match('/^[A-Za-z0-9]+$/', $user_input)) $errors[] = 'usuario_chars';
    // resumen de reglas si alguna falla
    if (preg_match('/^[A-Za-z].*/', $user_input) && preg_match('/^[A-Za-z0-9]{3,15}$/', $user_input) === 0 && !in_array('usuario_short', $errors, true) && !in_array('usuario_long', $errors, true) && !in_array('usuario_chars', $errors, true)) {
        $errors[] = 'usuario_rules';
    }
}
// Nombres reservados
$reserved = ['admin','root','administrator','system','guest'];
if ($user_input !== '' && in_array(mb_strtolower($user_input), $reserved, true)) {
    $errors[] = 'usuario_reserved';
}

// Contraseña: 6-15, sólo [A-Za-z0-9_-], al menos una mayúscula, una minúscula y un dígito
if ($old['contrasena'] === '') {
    $errors[] = 'contrasena';
} else {
    $pw = $old['contrasena'];
    if (!preg_match('/^[A-Za-z0-9_-]{6,15}$/', $pw)) {
        $errors[] = 'contrasena_rules';
    } else {
        if (!preg_match('/[A-Z]/', $pw) || !preg_match('/[a-z]/', $pw) || !preg_match('/\d/', $pw)) {
            $errors[] = 'contrasena_rules';
        }
    }
}

// Repetir contraseña: obligatoria y debe coincidir
if ($old['repetir'] === '') $errors[] = 'repetir';
if ($old['contrasena'] !== '' && $old['contrasena'] !== $old['repetir']) $errors[] = 'coinciden';

// Email: validación combinada (filter_var + comprobaciones propias)
function validar_email_errores($email) {
    $errs = [];
    if (!is_string($email) || $email === '') { $errs[] = 'email_empty'; return $errs; }
    if (mb_strlen($email) > 254) { $errs[] = 'email_too_long'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errs[] = 'email_filter'; }
    $parts = explode('@', $email);
    if (count($parts) !== 2) { $errs[] = 'email_format'; return $errs; }
    list($local, $domain) = $parts;
    if ($local === '' || $domain === '') { $errs[] = 'email_parts_empty'; return $errs; }
    if (mb_strlen($local) > 64) { $errs[] = 'email_local_too_long'; }
    if (mb_strlen($domain) > 255) { $errs[] = 'email_domain_too_long'; }
    if ($local[0] === '.' || substr($local, -1) === '.') { $errs[] = 'email_local_dot'; }
    if (strpos($local, '..') !== false) { $errs[] = 'email_local_consec_dots'; }
    // comprobar caracteres permitidos en la parte-local (RFC subset)
    // permitidos: A-Z a-z 0-9 and ! # $ % & ' * + - / = ? ^ _ ` { | } ~ and dot
    $allowed_local_extra = "!#\$%&'*+\-/=?^_`{|}~";
    $local_len = strlen($local);
    for ($i = 0; $i < $local_len; $i++) {
        $ch = $local[$i];
        if (ctype_alnum($ch) || $ch === '.') continue;
        if (strpos($allowed_local_extra, $ch) !== false) continue;
        $errs[] = 'email_local_chars';
        break;
    }
    $labels = explode('.', $domain);
    foreach ($labels as $lab) {
        if ($lab === '' || mb_strlen($lab) > 63) { $errs[] = 'email_label_length'; break; }
        if (!preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?$/', $lab)) { $errs[] = 'email_label_chars'; break; }
    }
    return array_unique($errs);
}
$email_errs = validar_email_errores($old['email']);
if (!empty($email_errs)) {
    foreach ($email_errs as $e) $errors[] = $e;
}

// Sexo: obligatorio y válido
$validSex = ['H','M','O','h','m','o'];
if (!isset($old['sexo']) || $old['sexo'] === '' || !in_array($old['sexo'], $validSex, true)) {
    $errors[] = 'sexo';
}

// Fecha de nacimiento: válida y >= 18 años
if (empty($old['nacimiento'])) {
    $errors[] = 'nacimiento';
} else {
    $d = DateTime::createFromFormat('Y-m-d', $old['nacimiento']);
    $d_errors = DateTime::getLastErrors();
    $warn_count = (is_array($d_errors) && isset($d_errors['warning_count'])) ? (int)$d_errors['warning_count'] : 0;
    $err_count = (is_array($d_errors) && isset($d_errors['error_count'])) ? (int)$d_errors['error_count'] : 0;
    if (!$d || $warn_count > 0 || $err_count > 0) {
        $errors[] = 'nacimiento';
    } else {
        $today = new DateTime();
        $age = $today->diff($d)->y;
        if ($age < 18) {
            $errors[] = 'nacimiento_menor';
        }
    }
}

// País: opcional -- si se proporciona, comprobar que existe en la BD
if ($old['pais'] !== '') {
    try {
        $stmtp = $conexion->prepare('SELECT IdPaises FROM Paises WHERE IdPaises = ? LIMIT 1');
        $stmtp->execute([$old['pais']]);
        if (!$stmtp->fetch()) {
            $errors[] = 'pais_invalid';
        }
    } catch (Exception $e) {
        $errors[] = 'pais_invalid';
    }
}

// Foto: validar tipo y tamaño si se ha subido, pero NO guardar en esta práctica
$nombreFoto = null;
if (!empty($_FILES['foto']['name'])) {
    $file = $_FILES['foto'];
    if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = 'foto';
    } elseif ($file['error'] === UPLOAD_ERR_OK) {
        $maxSize = 2 * 1024 * 1024;
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
        if ($file['size'] > $maxSize) $errors[] = 'foto_size';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!isset($allowed[$mime])) $errors[] = 'foto_type';
        // No se mueve ni guarda el fichero en esta práctica
    }
}

// Si hay errores → volver al formulario
if (!empty($errors)) {
    unset($old["contrasena"], $old["repetir"]);
    $_SESSION['flash']['registro_errors'] = $errors;
    $_SESSION['flash']['registro_old'] = $old;
    header("Location: registro.php");
    exit;
}

// Verificar usuario único
$stmt = $conexion->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
$stmt->execute([$old["usuario"]]);
if ($stmt->fetch()) {
    $_SESSION['flash']['registro_errors'] = ['usuario_duplicado'];
    $_SESSION['flash']['registro_old'] = $old;
    header("Location: registro.php");
    exit;
}

// Hash contraseña
$passHash = password_hash($old["contrasena"], PASSWORD_DEFAULT);

// (Photo storage disabled in this practice; photo already validated above if provided.)

// Insertar usuario
$stmt = $conexion->prepare("\n    INSERT INTO Usuarios\n    (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo)\n    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)\n");

// Mapear sexo a código numérico: H=>1, M=>2, otros=>0
$sexo_input = strtoupper(trim($old['sexo'] ?? ''));
if ($sexo_input === 'H' || $sexo_input === '1') {
    $sexo_db = 1;
} elseif ($sexo_input === 'M' || $sexo_input === '2') {
    $sexo_db = 2;
} else {
    $sexo_db = 0;
}

$stmt->execute([
    $old["usuario"],
    $passHash,
    $old["email"],
    $sexo_db,
    $old["nacimiento"],
    $old["ciudad"],
    $old["pais"],
    $nombreFoto
]);

// Iniciar sesión automáticamente tras registro y guardar foto en sesión (no crear cookies)
$_SESSION['usuario'] = $old['usuario'];
$lastId = $conexion->lastInsertId();
if ($lastId) {
    $_SESSION['id'] = (int)$lastId;
}
if (!empty($nombreFoto)) {
    $pathFoto = 'DAW/practica/imagenes/' . $nombreFoto;
    $_SESSION['foto'] = $pathFoto;
} else {
    $_SESSION['foto'] = 'DAW/practica/imagenes/default-avatar-profile-icon-vector-260nw-1909596082.webp';
}

$title = 'Registrado';
$cssPagina = 'registrado.css';
require_once('cabecera.inc');
require_once('inicio.inc');
?>

<main>
<section>
<h2>Registro completado</h2>

<p><strong>Usuario:</strong> <?= htmlspecialchars($old["usuario"]) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($old["email"]) ?></p>
<?php
// Mostrar etiqueta legible del sexo
if (isset($sexo_db)) {
    $sexo_label = ($sexo_db === 1) ? 'Hombre' : (($sexo_db === 2) ? 'Mujer' : 'Otro');
} else {
    $sexo_label = htmlspecialchars($old["sexo"] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<p><strong>Sexo:</strong> <?= $sexo_label ?></p>
<p><strong>Ciudad:</strong> <?= htmlspecialchars($old["ciudad"]) ?></p>

<p><a href="index.php"><strong>INICIAR SESIÓN</strong></a></p>
</section>
</main>

<?php require_once('pie.inc'); ?>