<?php
// Comprueba que el usuario está autenticado; si no, setea flash y redirige al index
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    // Mensaje de flash para mostrar en la página de login/index
    $_SESSION['flash']['acceso_error'] = 'Debes estar logueado para acceder a esa página.';
    // Asegurarnos de que la sesión se guarda antes de redirigir
    session_write_close();
    header('Location: index.php');
    exit;
}

?>
