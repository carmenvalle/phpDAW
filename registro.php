<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
$title = "PI - Pisos & Inmuebles";
$cssPagina = "registro.css";

// Recuperar errores y valores antiguos desde sesión flash o GET
$errors = [];
$old = [];
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['flash']['registro_errors'])) {
    $errors = $_SESSION['flash']['registro_errors'];
    $old = $_SESSION['flash']['registro_old'] ?? [];
    unset($_SESSION['flash']['registro_errors'], $_SESSION['flash']['registro_old']);
} else {
    if (isset($_GET['errors'])) $errors = array_filter(explode(',', $_GET['errors']));
    foreach ($_GET as $k => $v) {
        if (strpos($k, 'old_') === 0) $old[substr($k,4)] = $v;
    }
}

require_once('cabecera.inc');
require_once('inicio.inc');
require_once(__DIR__ . '/includes/conexion.php');

// Cargar países
$paises = [];
if (isset($conexion)) {
    try {
        // La columna en la BD se llama IdPaises; la seleccionamos con alias IdPais
        $stmt = $conexion->query("SELECT IdPaises AS IdPais, NomPais FROM Paises ORDER BY NomPais");
        $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $paises = [];
    }
}
?>

<main>
    <section>
        <h2>FORMULARIO DE REGISTRO</h2>
        <form id="formRegistro" action="registrado.php" method="post" enctype="multipart/form-data" novalidate>

            <p class="<?php echo (in_array('usuario', $errors) || in_array('usuario_short', $errors) || in_array('usuario_long', $errors) || in_array('usuario_start', $errors) || in_array('usuario_chars', $errors) || in_array('usuario_reserved', $errors)) ? 'campo-error' : ''; ?>">
                <label for="usuario"><strong>Nombre de usuario:</strong></label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($old['usuario'] ?? ''); ?>">
            </p>
            <?php if (in_array('usuario', $errors)): ?><span class="error-campo">El nombre de usuario es obligatorio.</span><?php endif; ?>
            <?php if (in_array('usuario_reserved', $errors)): ?><span class="error-campo">Ese nombre de usuario está reservado. Elige otro.</span><?php endif; ?>
            <?php
                $userIssues = [];
                if (in_array('usuario_short', $errors)) $userIssues[] = 'Debe tener al menos 3 caracteres.';
                if (in_array('usuario_long', $errors)) $userIssues[] = 'No puede tener más de 15 caracteres.';
                if (in_array('usuario_start', $errors)) $userIssues[] = 'Debe empezar por una letra.';
                if (in_array('usuario_chars', $errors)) $userIssues[] = 'Sólo se permiten letras y números (sin espacios ni símbolos).';
                if (!empty($userIssues)) {
                    echo '<div class="error-campo"><ul>';
                    foreach ($userIssues as $ui) echo '<li>' . htmlspecialchars($ui) . '</li>';
                    echo '</ul></div>';
                }
            ?>

            <p class="<?php echo (in_array('contrasena', $errors) || in_array('contrasena_rules', $errors)) ? 'campo-error' : ''; ?>">
                <label for="password"><strong>Contraseña:</strong></label>
                <input type="password" id="password" name="contrasena">
            </p>
            <?php if (in_array('contrasena', $errors)): ?><span class="error-campo">La contraseña es obligatoria.</span><?php endif; ?>
            <?php if (in_array('contrasena_rules', $errors)): ?>
                <div class="error-campo">
                    <strong>La contraseña debe cumplir:</strong>
                    <ul>
                        <li>Entre 6 y 15 caracteres</li>
                        <li>Al menos una letra mayúscula</li>
                        <li>Al menos una letra minúscula</li>
                        <li>Al menos un número</li>
                        <li>Sólo letras, dígitos, guion y guion bajo; sin espacios</li>
                    </ul>
                </div>
            <?php endif; ?>

            <p class="<?php echo (in_array('repetir', $errors) || in_array('coinciden', $errors)) ? 'campo-error' : ''; ?>">
                <label for="password2"><strong>Repetir contraseña:</strong></label>
                <input type="password" id="password2" name="repetir">
            </p>
            <?php if (in_array('repetir', $errors)): ?><span class="error-campo">Debes repetir la contraseña.</span>
            <?php elseif (in_array('coinciden', $errors)): ?><span class="error-campo">Las contraseñas no coinciden.</span><?php endif; ?>

            <p class="<?php echo (in_array('email', $errors) || in_array('email_empty', $errors) || in_array('email_too_long', $errors) || in_array('email_filter', $errors) || in_array('email_format', $errors) || in_array('email_parts_empty', $errors) || in_array('email_local_too_long', $errors) || in_array('email_domain_too_long', $errors) || in_array('email_local_dot', $errors) || in_array('email_local_consec_dots', $errors) || in_array('email_local_chars', $errors) || in_array('email_label_length', $errors) || in_array('email_label_chars', $errors)) ? 'campo-error' : ''; ?>">
                <label for="email"><strong>Correo electrónico:</strong></label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
            </p>
            <?php
                $emailIssues = [];
                if (in_array('email_empty', $errors)) $emailIssues[] = 'El correo no puede estar vacío.';
                if (in_array('email_too_long', $errors)) $emailIssues[] = 'La dirección supera los 254 caracteres permitidos.';
                if (in_array('email_filter', $errors) || in_array('email_format', $errors)) $emailIssues[] = 'Formato de correo no válido (parte-local@dominio).';
                if (in_array('email_local_too_long', $errors)) $emailIssues[] = 'La parte local (antes de @) supera 64 caracteres.';
                if (in_array('email_domain_too_long', $errors)) $emailIssues[] = 'El dominio supera 255 caracteres.';
                if (in_array('email_local_dot', $errors)) $emailIssues[] = 'La parte local no puede empezar/terminar por un punto.';
                if (in_array('email_local_consec_dots', $errors)) $emailIssues[] = 'La parte local no puede contener dos puntos seguidos.';
                if (in_array('email_label_length', $errors)) $emailIssues[] = 'Cada etiqueta del dominio debe tener como máximo 63 caracteres.';
                if (in_array('email_label_chars', $errors)) $emailIssues[] = 'Los subdominios del dominio sólo pueden contener letras, dígitos y guiones (no empezar/terminar por guion).';
                if (in_array('email_local_chars', $errors)) $emailIssues[] = 'La parte local contiene caracteres no permitidos.';
                if (!empty($emailIssues)) {
                    echo '<div class="error-campo"><ul>';
                    foreach ($emailIssues as $ei) echo '<li>' . htmlspecialchars($ei) . '</li>';
                    echo '</ul></div>';
                }
            ?>

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
            <?php if (in_array('sexo', $errors)): ?><span class="error-campo">Debes seleccionar un sexo.</span><?php endif; ?>

            <p class="<?php echo (in_array('nacimiento', $errors) || in_array('nacimiento_menor', $errors)) ? 'campo-error' : ''; ?>">
                <label for="nacimiento"><strong>Fecha de nacimiento:</strong></label>
                <input type="date" id="nacimiento" name="nacimiento" value="<?php echo htmlspecialchars($old['nacimiento'] ?? ''); ?>">
            </p>
            <?php
                $birthIssues = [];
                if (in_array('nacimiento', $errors)) $birthIssues[] = 'Debes introducir una fecha de nacimiento válida.';
                if (in_array('nacimiento_menor', $errors)) $birthIssues[] = 'Debes tener al menos 18 años para registrarte.';
                if (!empty($birthIssues)) {
                    echo '<div class="error-campo"><ul>';
                    foreach ($birthIssues as $bi) echo '<li>' . htmlspecialchars($bi) . '</li>';
                    echo '</ul></div>';
                }
            ?>

            <p>
                <label for="ciudad"><strong>Ciudad de residencia:</strong></label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($old['ciudad'] ?? ''); ?>">
            </p>

            <p>
                <label for="pais"><strong>País de residencia:</strong></label>
                <select id="pais" name="pais">
                    <option value="">-- Seleccione --</option>
                    <?php if (!empty($paises)): foreach ($paises as $p): ?>
                        <option value="<?php echo $p['IdPais']; ?>" <?php echo ((isset($old['pais']) && $old['pais'] == $p['IdPais'])) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['NomPais']); ?></option>
                    <?php endforeach; else: ?>
                        <option value="es" <?php echo (isset($old['pais']) && $old['pais'] === 'es') ? 'selected' : ''; ?>>España</option>
                        <option value="pt" <?php echo (isset($old['pais']) && $old['pais'] === 'pt') ? 'selected' : ''; ?>>Portugal</option>
                        <option value="fr" <?php echo (isset($old['pais']) && $old['pais'] === 'fr') ? 'selected' : ''; ?>>Francia</option>
                        <option value="it" <?php echo (isset($old['pais']) && $old['pais'] === 'it') ? 'selected' : ''; ?>>Italia</option>
                    <?php endif; ?>
                </select>
            </p>
            <?php if (in_array('pais_invalid', $errors)): ?><span class="error-campo">País no válido.</span><?php endif; ?>

            <p>
                <label for="foto"><strong>Foto de perfil:</strong></label>
                <input type="file" id="foto" name="foto" accept="image/*">
            </p>

            <p>
                <button type="submit"><strong>REGISTRAR</strong></button>
            </p>

        </form>
    </section>

    <?php require_once('salto.inc'); ?>
</main>

<?php require_once('pie.inc'); ?>
