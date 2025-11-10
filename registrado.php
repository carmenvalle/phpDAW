<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si se accede por GET, mostrar mensaje simple o redirigir al formulario
    header('Location: registro.php');
    exit;
}

$errors = [];
$old = [];

// Campos que esperamos
$fields = ['usuario', 'contrasena', 'repetir', 'email', 'sexo', 'nacimiento', 'ciudad', 'pais'];
foreach ($fields as $f) {
    if (isset($_POST[$f])) $old[$f] = trim((string)$_POST[$f]);
}

// Validaciones mínimas solicitadas
if (empty($old['usuario'])) {
    $errors[] = 'usuario';
}
if (empty($old['contrasena'])) {
    $errors[] = 'contrasena';
}
if (empty($old['repetir'])) {
    $errors[] = 'repetir';
}
if (!empty($old['contrasena']) && !empty($old['repetir']) && $old['contrasena'] !== $old['repetir']) {
    $errors[] = 'coinciden';
}

if (!empty($errors)) {
    // Guardar errores y valores antiguos en flashdata (sesión) en vez de pasarlos por la URL
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    // No guardamos contraseñas en claro en la flash
    $old_no_pass = $old;
    unset($old_no_pass['contrasena'], $old_no_pass['repetir']);
    $_SESSION['flash']['registro_errors'] = $errors;
    $_SESSION['flash']['registro_old'] = $old_no_pass;
    header('Location: registro.php');
    exit;
}

// Si llegamos aquí, no hay errores: mostramos la página con los datos
$title = 'Registrado - PI Pisos & Inmuebles';
$cssPagina = 'registrado.css';
require_once('cabecera.inc');
require_once('inicio.inc');
?>

<main>
    <section>
        <h2>Registro completado</h2>
        <table>
            <tr><th>Campo</th><th>Respuesta</th></tr>
            <tr><td>Nombre de usuario</td><td><?php echo htmlspecialchars($old['usuario'] ?? ''); ?></td></tr>
            <tr><td>Correo electrónico</td><td><?php echo htmlspecialchars($old['email'] ?? ''); ?></td></tr>
            <tr><td>Sexo</td><td><?php echo htmlspecialchars($old['sexo'] ?? ''); ?></td></tr>
            <tr><td>Fecha de nacimiento</td><td><?php echo htmlspecialchars($old['nacimiento'] ?? ''); ?></td></tr>
            <tr><td>Ciudad</td><td><?php echo htmlspecialchars($old['ciudad'] ?? ''); ?></td></tr>
            <tr><td>País</td><td><?php echo htmlspecialchars($old['pais'] ?? ''); ?></td></tr>
        </table>

        <p class="nota-pequena">Si quieres, ahora puedes iniciar sesión o volver a la página principal.</p>
    </section>

    <?php require_once('salto.inc'); ?>
</main>

<?php require_once('pie.inc'); ?>