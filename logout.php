<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
session_start();

// Borrar todas las variables de sesión
$_SESSION = [];

// Borrar cookie de sesión del servidor
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Borrar cookies del “recordarme” (como exige la práctica)
setcookie('usuario', '', time() - 3600, '/');
setcookie('ultima_visita', '', time() - 3600, '/');
// Borrar cookie de estilo para que al cerrar sesión no persista el tema del usuario
setcookie('style', '', time() - 3600, '/');
// Borrar posible cookie con hash de la contraseña (práctica antigua)
setcookie('clave', '', time() - 3600, '/');

// Asegurar que no quedan variables de estilo en la sesión
if (isset($_SESSION['style'])) unset($_SESSION['style']);
if (isset($_SESSION['estilo'])) unset($_SESSION['estilo']);

// Redirigir a la página principal
header('Location: index.php');
exit;
?>
