<?php
$title = "PI - Pisos & Inmuebles";
$cssPagina = "registro.css";

// === LECTURA DE ERRORES Y VALORES ANTIGUOS (desde GET, sin sesiones) ===
// === LECTURA DE ERRORES Y VALORES ANTIGUOS (desde flashdata en sesión o GET) ===
$errors = [];
$old = [];

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['flash']['registro_errors'])) {
    $errors = $_SESSION['flash']['registro_errors'];
    $old = $_SESSION['flash']['registro_old'] ?? [];
    // Borrar flash
    unset($_SESSION['flash']['registro_errors'], $_SESSION['flash']['registro_old']);
} else {
    if (isset($_GET['errors'])) {
        $errors = array_filter(explode(',', $_GET['errors']));
    }

    foreach ($_GET as $k => $v) {
        if (strpos($k, 'old_') === 0) {
            $field = substr($k, 4);
            $old[$field] = $v;
        }
    }
}

require_once("cabecera.inc");
require_once("inicio.inc");
?>

<main>
    <section>
        <h2>FORMULARIO DE REGISTRO</h2>
        <form id="formRegistro" action="registrado.php" method="post" enctype="multipart/form-data" novalidate>

            <!-- Usuario -->
            <p class="<?php echo in_array('usuario', $errors) ? 'campo-error' : ''; ?>">
                <label for="usuario"><strong>Nombre de usuario:</strong></label>
                <input type="text" id="usuario" name="usuario"
                    value="<?php echo htmlspecialchars($old['usuario'] ?? ''); ?>">
            </p>
            <?php if (in_array('usuario', $errors)): ?>
                <span class="error-campo">El nombre de usuario es obligatorio.</span>
            <?php endif; ?>

            <!-- Contraseña -->
            <p class="<?php echo in_array('contrasena', $errors) ? 'campo-error' : ''; ?>">
                <label for="password"><strong>Contraseña:</strong></label>
                <input type="password" id="password" name="contrasena"
                    value="<?php echo htmlspecialchars($old['contrasena'] ?? ''); ?>">
            </p>
            <?php if (in_array('contrasena', $errors)): ?>
                <span class="error-campo">La contraseña es obligatoria.</span>
            <?php endif; ?>

            <!-- Repetir contraseña -->
            <p class="<?php echo (in_array('repetir', $errors) || in_array('coinciden', $errors)) ? 'campo-error' : ''; ?>">
                <label for="password2"><strong>Repetir contraseña:</strong></label>
                <input type="password" id="password2" name="repetir"
                    value="<?php echo htmlspecialchars($old['repetir'] ?? ''); ?>">
            </p>
            <?php if (in_array('repetir', $errors)): ?>
                <span class="error-campo">Debes repetir la contraseña.</span>
            <?php elseif (in_array('coinciden', $errors)): ?>
                <span class="error-campo">Las contraseñas no coinciden.</span>
            <?php endif; ?>

            <!-- Correo electrónico -->
            <p class="<?php echo in_array('email', $errors) ? 'campo-error' : ''; ?>">
                <label for="email"><strong>Correo electrónico:</strong></label>
                <input type="text" id="email" name="email"
                    value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
            </p>
            <?php if (in_array('email', $errors)): ?>
                <span class="error-campo">Debe introducir un correo electrónico válido.</span>
            <?php endif; ?>

            <!-- Sexo -->
            <p class="sexo <?php echo in_array('sexo', $errors) ? 'campo-error' : ''; ?>">
                <strong>Sexo: </strong>
                <?php
                    $sexoOld = $old['sexo'] ?? '';
                    $sexOptions = ['H' => 'Hombre', 'M' => 'Mujer', 'O' => 'Otro'];
                    foreach ($sexOptions as $val => $label) {
                        $checked = ($sexoOld === $val) ? 'checked' : '';
                        echo '<label><input type="radio" name="sexo" value="'.htmlspecialchars($val).'" '.$checked.'> '.htmlspecialchars($label).'</label>';
                    }
                ?>
            </p>
            <?php if (in_array('sexo', $errors)): ?>
                <span class="error-campo">Debes seleccionar un sexo.</span>
            <?php endif; ?>

            <!-- Fecha nacimiento -->
            <p>
                <label for="nacimiento"><strong>Fecha de nacimiento:</strong></label>
                <input type="date" id="nacimiento" name="nacimiento"
                    value="<?php echo htmlspecialchars($old['nacimiento'] ?? ''); ?>">
            </p>

            <!-- Ciudad -->
            <p class="<?php echo in_array('ciudad', $errors) ? 'campo-error' : ''; ?>">
                <label for="ciudad"><strong>Ciudad de residencia:</strong></label>
                <input type="text" id="ciudad" name="ciudad"
                    value="<?php echo htmlspecialchars($old['ciudad'] ?? ''); ?>">
            </p>
            <?php if (in_array('ciudad', $errors)): ?>
                <span class="error-campo">La ciudad es obligatoria.</span>
            <?php endif; ?>

            <!-- País -->
            <p class="<?php echo in_array('pais', $errors) ? 'campo-error' : ''; ?>">
                <label for="pais"><strong>País de residencia:</strong></label>
                <select id="pais" name="pais">
                    <option value="">-- Seleccione --</option>
                    <option value="es" <?php echo (isset($old['pais']) && $old['pais'] === 'es') ? 'selected' : ''; ?>>España</option>
                    <option value="pt" <?php echo (isset($old['pais']) && $old['pais'] === 'pt') ? 'selected' : ''; ?>>Portugal</option>
                    <option value="fr" <?php echo (isset($old['pais']) && $old['pais'] === 'fr') ? 'selected' : ''; ?>>Francia</option>
                    <option value="it" <?php echo (isset($old['pais']) && $old['pais'] === 'it') ? 'selected' : ''; ?>>Italia</option>
                </select>
            </p>
            <?php if (in_array('pais', $errors)): ?>
                <span class="error-campo">El país es obligatorio.</span>
            <?php endif; ?>

            <!-- Foto -->
            <p>
                <label for="foto"><strong>Foto de perfil:</strong></label>
                <input type="file" id="foto" name="foto" accept="image/*">
            </p>

            <p>
                <button><strong>REGISTRARSE</strong></button>
            </p>
        </form>
    </section>

    <?php require_once("salto.inc"); ?>
</main>

<?php require_once("pie.inc"); ?>
