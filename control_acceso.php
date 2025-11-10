<?php
include ("./usuarios.php");

// Asegurar sesión activa para poder usar flashdata
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Acepta nombres antiguos o nuevos de los campos del formulario
$hasOld = isset($_POST["nomUsuario"]) && isset($_POST["pass"]);
$hasNew = isset($_POST["usuario"]) && isset($_POST["contrasena"]);

if ($hasOld || $hasNew) {
    $userName = $hasOld ? trim($_POST["nomUsuario"]) : trim($_POST["usuario"]);
    $pass = $hasOld ? $_POST["pass"] : $_POST["contrasena"];

    if ($userName === "" || $pass === "") {
        $error = "Por favor introduzca un Nombre de Usuario y una Contraseña";
    // Usar flashdata en sesión en vez de pasar por la URL
    $_SESSION['flash']['acceso_error'] = $error;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: index.php");
    exit;
    }

    $encontrado = false;

    foreach ($usuarios as $user) {
        if ($user[0] === $userName && $user[1] === $pass) {
            $encontrado = true;

            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $_SESSION['usuario'] = $userName;
            // Asignar el estilo del usuario (si está definido)
            $_SESSION['style'] = isset($user[2]) ? $user[2] : 'default';

            // Si el usuario marcó "Recordarme en este equipo", creamos cookies
            if (isset($_POST['recordarme'])) {
                // Caducan en 90 días
                setcookie('usuario', $userName, time() + 90 * 24 * 60 * 60, '/');
                setcookie('ultima_visita', date('d/m/Y H:i'), time() + 90 * 24 * 60 * 60, '/');
            }

            break;
        }
    }

    if (!$encontrado) {
        $error = "Usuario no encontrado, introduce un Nombre de Usuario y una Contraseña válidas.";
    // Flashdata
    $_SESSION['flash']['acceso_error'] = $error;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: index.php");
    exit;
    }

    // Acceso correcto → redirige a la zona privada
    header("Location: index_logueado.php");
    exit;
} else {
    $error = "Por favor introduzca un Nombre de Usuario y una Contraseña";
    header("Location: index.php?acceso_error=" . rawurlencode($error));
    exit;
}
?>

