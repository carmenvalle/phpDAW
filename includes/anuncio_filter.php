<?php
// Función para validar y sanear datos de un anuncio (crear/modificar).
// Devuelve array con 'values' y 'errors'.
function filtrar_anuncio(array $post, array $files = null): array {
    $errors = [];
    $values = [];

    $values['tipo_anuncio'] = isset($post['tipo_anuncio']) ? trim((string)$post['tipo_anuncio']) : '';
    $values['vivienda'] = isset($post['vivienda']) ? trim((string)$post['vivienda']) : '';
    $values['titulo'] = isset($post['titulo']) ? trim((string)$post['titulo']) : '';
    $values['ciudad'] = isset($post['ciudad']) ? trim((string)$post['ciudad']) : '';
    $values['pais'] = isset($post['pais']) ? trim((string)$post['pais']) : '';
    // Precio: aceptar decimales con coma o punto; si no es numérico -> null
    if (isset($post['precio']) && $post['precio'] !== '') {
        $p = str_replace(',', '.', trim((string)$post['precio']));
        $values['precio'] = is_numeric($p) ? (float)$p : null;
    } else {
        $values['precio'] = null;
    }
    $values['descripcion'] = isset($post['descripcion']) ? trim((string)$post['descripcion']) : '';
    // Campos numéricos: validar como enteros, si no válidos -> null
    $values['superficie'] = (isset($post['superficie']) && $post['superficie'] !== '') ? (filter_var($post['superficie'], FILTER_VALIDATE_INT) !== false ? (int)$post['superficie'] : null) : null;
    $values['habitaciones'] = (isset($post['habitaciones']) && $post['habitaciones'] !== '') ? (filter_var($post['habitaciones'], FILTER_VALIDATE_INT) !== false ? (int)$post['habitaciones'] : null) : null;
    $values['banos'] = (isset($post['banos']) && $post['banos'] !== '') ? (filter_var($post['banos'], FILTER_VALIDATE_INT) !== false ? (int)$post['banos'] : null) : null;
    $values['planta'] = (isset($post['planta']) && $post['planta'] !== '') ? (filter_var($post['planta'], FILTER_VALIDATE_INT) !== false ? (int)$post['planta'] : null) : null;
    $values['anio'] = (isset($post['anio']) && $post['anio'] !== '') ? (filter_var($post['anio'], FILTER_VALIDATE_INT) !== false ? (int)$post['anio'] : null) : null;

    // Validar no negativos
    if ($values['superficie'] !== null && $values['superficie'] < 0) {
        $errors[] = 'superficie_negative';
        $values['superficie'] = null;
    }
    if ($values['habitaciones'] !== null && $values['habitaciones'] < 0) {
        $errors[] = 'habitaciones_negative';
        $values['habitaciones'] = null;
    }
    if ($values['banos'] !== null && $values['banos'] < 0) {
        $errors[] = 'banos_negative';
        $values['banos'] = null;
    }
    if ($values['planta'] !== null && $values['planta'] < 0) {
        $errors[] = 'planta_negative';
        $values['planta'] = null;
    }
    if ($values['anio'] !== null) {
        if ($values['anio'] < 1800 || $values['anio'] > 2100) {
            $errors[] = 'anio_invalid';
            $values['anio'] = null;
        }
    }

    // Limitar longitudes para evitar entradas excesivas
    $values['titulo'] = mb_substr($values['titulo'], 0, 255);
    $values['descripcion'] = mb_substr($values['descripcion'], 0, 4000);

    // Validaciones básicas (obligatorios)
    if ($values['titulo'] === '') $errors[] = 'titulo';
    if ($values['descripcion'] === '') $errors[] = 'descripcion';

    // Validar imágenes (si se han enviado en files)
    $hasValidImage = false;
    if ($files && isset($files['imagenes']) && is_array($files['imagenes']['name'])) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        $filesArr = $files['imagenes'];
        for ($i = 0; $i < count($filesArr['name']); $i++) {
            if ($filesArr['error'][$i] !== UPLOAD_ERR_OK) continue;
            $tmp = $filesArr['tmp_name'][$i];
            if (!is_uploaded_file($tmp)) continue;
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp);
            finfo_close($finfo);
            if (!in_array($mime, $allowed, true)) continue;
            $hasValidImage = true;
            break;
        }
    }
    // Si no se han enviado imágenes en este request y el form requiere imagen, marcar error
    if (empty($hasValidImage) && (isset($post['imagenes_required']) && $post['imagenes_required'])) {
        $errors[] = 'imagenes';
    }

    return ['values' => $values, 'errors' => $errors];
}
