<?php
include ("./usuarios.php");

// Asegurar sesión activa para poder usar flashdata
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Acepta nombres antiguos o nuevos de los campos del formulario
$hasOld = isset($_POST["nomUsuario"]) && isset($_POST["pass"]);
$hasNew = isset($_POST["usuario"]) && isset($_POST["contrasena"]);

if (!($hasOld || $hasNew)) {
    $error = "Por favor introduzca un Nombre de Usuario y una Contraseña";
    $_SESSION['flash']['acceso_error'] = $error;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: /phpDAW/");
    exit;
}

$userName = $hasOld ? trim((string)$_POST["nomUsuario"]) : trim((string)$_POST["usuario"]);
$pass = $hasOld ? ($_POST["pass"] ?? '') : ($_POST["contrasena"] ?? '');

if ($userName === "" || $pass === "") {
    $error = "Por favor introduzca un Nombre de Usuario y una Contraseña";
    $_SESSION['flash']['acceso_error'] = $error;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: /phpDAW/");
    exit;
}

// Intentar autenticación contra la base de datos si existe la conexión
$autenticado = false;
if (file_exists(__DIR__ . '/includes/conexion.php')) {
    require_once __DIR__ . '/includes/conexion.php';
}

if (isset($conexion)) {
    try {
        $stmt = $conexion->prepare(
            // Incluir `Foto` para poder mostrar el avatar al iniciar sesión
            "SELECT IdUsuario, NomUsuario, Clave, Estilo, Foto FROM Usuarios WHERE NomUsuario = ? LIMIT 1"
        );
        $stmt->execute([$userName]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos && isset($datos['Clave']) && password_verify($pass, $datos['Clave'])) {
            // Login correcto
            $_SESSION["id"] = $datos['IdUsuario'];
            $_SESSION["usuario"] = $datos['NomUsuario'];
            // usar 'estilo' y mantener 'style' para compatibilidad
            $_SESSION["estilo"] = $datos['Estilo'] ?? null;
            $_SESSION['style'] = $datos['Estilo'] ?? 'default';
            // Foto de perfil
            require_once __DIR__ . '/includes/precio.php';
            if (!empty($datos['Foto'])) {
                $_SESSION['foto'] = resolve_image_url($datos['Foto']);
            } else {
                $_SESSION['foto'] = '/phpDAW/DAW/practica/imagenes/default-avatar-profile-icon-vector-260nw-1909596082.webp';
            }
            $autenticado = true;
        }
    } catch (Exception $e) {
        // Si falla la autenticación por BD, caeremos al método antiguo
        $autenticado = false;
    }
}

// Si no autenticado con BD, intentar el array local (compatibilidad antigua)
if (!$autenticado) {
    foreach ($usuarios as $user) {
        if ($user[0] === $userName && $user[1] === $pass) {
            $_SESSION['usuario'] = $userName;
            $_SESSION['style'] = isset($user[2]) ? $user[2] : 'default';
            $autenticado = true;
            break;
        }
    }
}

if (!$autenticado) {
    $error = "Usuario o contraseña incorrectos.";
    $_SESSION['flash']['acceso_error'] = $error;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: /phpDAW/");
    exit;
}

// Si el usuario marcó "Recordarme en este equipo", creamos cookies
if (isset($_POST['recordarme'])) {
    // Caducan en 90 días
    setcookie('usuario', $userName, time() + 90 * 24 * 60 * 60, '/');
    setcookie('ultima_visita', date('d/m/Y H:i'), time() + 90 * 24 * 60 * 60, '/');
    // Si autenticado via BD y disponemos del hash, guardarlo en cookie (nota: almacenar hashes en cookie no es la práctica más segura,
    // pero se solicita para compatibilidad con prácticas). También guardamos el estilo.
    if (isset($datos['Clave'])) {
        setcookie('clave', $datos['Clave'], time() + 90 * 24 * 60 * 60, '/');
    }
    if (isset($datos['Estilo'])) {
        setcookie('style', $datos['Estilo'], time() + 90 * 24 * 60 * 60, '/');
    }
}

// Acceso correcto → redirige a la zona privada (usar ruta absoluta para evitar reescrituras)
header("Location: /phpDAW/index_logueado");
exit;
?>

