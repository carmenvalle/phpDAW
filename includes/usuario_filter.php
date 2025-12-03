<?php
// filtrar_usuario: centraliza validación de registro y modificación de usuario
// $context = 'register' | 'modify'
function filtrar_usuario(array $post, array $files = null, string $context = 'register'): array {
    $errors = [];
    $values = [];

    // Campos básicos
    $values['usuario'] = isset($post['usuario']) ? trim((string)$post['usuario']) : '';
    $values['email'] = isset($post['email']) ? trim((string)$post['email']) : '';
    $values['sexo'] = isset($post['sexo']) ? trim((string)$post['sexo']) : '';
    $values['nacimiento'] = isset($post['nacimiento']) ? trim((string)$post['nacimiento']) : '';
    $values['ciudad'] = isset($post['ciudad']) ? trim((string)$post['ciudad']) : '';
    $values['pais'] = isset($post['pais']) ? trim((string)$post['pais']) : '';

    // Passwords: in register context require contrasena and repetir; in modify, optional new_password/repeat
    if ($context === 'register') {
        $values['contrasena'] = $post['contrasena'] ?? '';
        $values['repetir'] = $post['repetir'] ?? '';
    } else {
        $values['new_password'] = $post['new_password'] ?? '';
        $values['repeat_new_password'] = $post['repeat_new_password'] ?? '';
        // current password must be provided by caller before calling filter? we'll not enforce here
    }

    // Usuario validation
    if ($context === 'register') {
        if ($values['usuario'] === '') {
            $errors[] = 'usuario';
        } else {
            $u = $values['usuario'];
            $u_len = mb_strlen($u);
            if ($u_len < 3) $errors[] = 'usuario_short';
            if ($u_len > 15) $errors[] = 'usuario_long';
            if (!preg_match('/^[A-Za-z]/', $u)) $errors[] = 'usuario_start';
            if (!preg_match('/^[A-Za-z0-9]+$/', $u)) $errors[] = 'usuario_chars';
            $reserved = ['admin','root','administrator','system','guest'];
            if (in_array(mb_strtolower($u), $reserved, true)) $errors[] = 'usuario_reserved';
        }
    } else {
        // modify: username optional, but if provided must follow same rules
        if ($values['usuario'] !== '') {
            $u = $values['usuario'];
            $u_len = mb_strlen($u);
            if ($u_len < 3) $errors[] = 'usuario_short';
            if ($u_len > 15) $errors[] = 'usuario_long';
            if (!preg_match('/^[A-Za-z]/', $u)) $errors[] = 'usuario_start';
            if (!preg_match('/^[A-Za-z0-9]+$/', $u)) $errors[] = 'usuario_chars';
            $reserved = ['admin','root','administrator','system','guest'];
            if (in_array(mb_strtolower($u), $reserved, true)) $errors[] = 'usuario_reserved';
        }
    }

    // Password validation
    $pw_pattern_ok = function($pw) {
        if (!preg_match('/^[A-Za-z0-9_-]{6,15}$/', $pw)) return false;
        if (!preg_match('/[A-Z]/', $pw)) return false;
        if (!preg_match('/[a-z]/', $pw)) return false;
        if (!preg_match('/\d/', $pw)) return false;
        return true;
    };
    if ($context === 'register') {
        if (empty($values['contrasena'])) $errors[] = 'contrasena';
        else if (!$pw_pattern_ok($values['contrasena'])) $errors[] = 'contrasena_rules';
        if (empty($values['repetir'])) $errors[] = 'repetir';
        if (!empty($values['contrasena']) && !empty($values['repetir']) && $values['contrasena'] !== $values['repetir']) $errors[] = 'coinciden';
    } else {
        if (!empty($values['new_password']) || !empty($values['repeat_new_password'])) {
            if (!empty($values['new_password']) && !$pw_pattern_ok($values['new_password'])) $errors[] = 'newpw_rules';
            if ($values['new_password'] !== $values['repeat_new_password']) $errors[] = 'newpw_coinciden';
        }
    }

    // Email validation using helper similar to registro.php
    $validate_email = function($email) {
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
    };
    if ($values['email'] !== '') {
        $eErrs = $validate_email($values['email']);
        foreach ($eErrs as $ee) $errors[] = $ee;
    } else {
        $errors[] = 'email';
    }

    // Sexo required and normalized
    $validSex = ['H','M','O','h','m','o', '1','2','0'];
    if (!isset($values['sexo']) || $values['sexo'] === '' || !in_array($values['sexo'], $validSex, true)) {
        $errors[] = 'sexo';
    }

    // Fecha nacimiento: required and >=18
    if (empty($values['nacimiento'])) {
        $errors[] = 'nacimiento';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $values['nacimiento']);
        $d_errors = DateTime::getLastErrors();
        $warn_count = (is_array($d_errors) && isset($d_errors['warning_count'])) ? (int)$d_errors['warning_count'] : 0;
        $err_count = (is_array($d_errors) && isset($d_errors['error_count'])) ? (int)$d_errors['error_count'] : 0;
        if (!$d || $warn_count > 0 || $err_count > 0) {
            $errors[] = 'nacimiento';
        } else {
            $today = new DateTime();
            $age = $today->diff($d)->y;
            if ($age < 18) $errors[] = 'nacimiento_menor';
        }
    }

    // Foto validation (optional in practice)
    if ($files && !empty($files['foto']['name'])) {
        $file = $files['foto'];
        if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'foto';
        } elseif ($file['error'] === UPLOAD_ERR_OK) {
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) $errors[] = 'foto_size';
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
            if (!in_array($mime, $allowed, true)) $errors[] = 'foto_type';
        }
    }

    // Limit lengths to avoid DB issues
    $values['usuario'] = mb_substr($values['usuario'], 0, 50);
    $values['email'] = mb_substr($values['email'], 0, 254);
    $values['ciudad'] = mb_substr($values['ciudad'], 0, 100);

    return ['values' => $values, 'errors' => array_values(array_unique($errors))];
}
