<?php
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

// Redirigir a la página principal
header('Location: index.php');
exit;
?>
