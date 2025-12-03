<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro");
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once 'includes/conexion.php';
require_once 'includes/usuario_filter.php';

// Usar el filtro centralizado para validar el registro
$filtered = filtrar_usuario($_POST, $_FILES, 'register');
$values = $filtered['values'];
$errors = $filtered['errors'];

// Si hay errores → volver al formulario
if (!empty($errors)) {
    $_SESSION['flash']['registro_errors'] = $errors;
    // Guardar valores exceptuando contraseñas
    $old = $values;
    unset($old['contrasena'], $old['repetir']);
    $_SESSION['flash']['registro_old'] = $old;
    header("Location: registro");
    exit;
}

// Verificar usuario único
$stmt = $conexion->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
$stmt->execute([$values['usuario']]);
if ($stmt->fetch()) {
    $_SESSION['flash']['registro_errors'] = ['usuario_duplicado'];
    $_SESSION['flash']['registro_old'] = $values;
    header("Location: registro");
    exit;
}

// Hash contraseña
$passHash = password_hash($values['contrasena'], PASSWORD_DEFAULT);

// (Photo storage disabled in this practice; photo already validated above if provided.)

// Insertar usuario
$stmt = $conexion->prepare("\n    INSERT INTO Usuarios\n    (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo)\n    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)\n");

// Mapear sexo a código numérico: H=>1, M=>2, otros=>0
$sexo_input = strtoupper(trim($values['sexo'] ?? ''));
if ($sexo_input === 'H' || $sexo_input === '1') {
    $sexo_db = 1;
} elseif ($sexo_input === 'M' || $sexo_input === '2') {
    $sexo_db = 2;
} else {
    $sexo_db = 0;
}

// Ejecutar INSERT con valores filtrados
$stmt->execute([
    $values["usuario"],
    $passHash,
    $values["email"],
    $sexo_db,
    $values["nacimiento"],
    $values["ciudad"],
    $values["pais"],
    $nombreFoto
]);

// Iniciar sesión automáticamente tras registro y guardar foto en sesión (no crear cookies)
$_SESSION['usuario'] = $values['usuario'];
$lastId = $conexion->lastInsertId();
if ($lastId) {
    $_SESSION['id'] = (int)$lastId;
}
    if (!empty($nombreFoto)) {
    $pathFoto = '/phpDAW/DAW/practica/imagenes/' . $nombreFoto;
    $_SESSION['foto'] = $pathFoto;
} else {
    $_SESSION['foto'] = '/phpDAW/DAW/practica/imagenes/default-avatar-profile-icon-vector-260nw-1909596082.webp';
}

$title = 'Registrado';
$cssPagina = 'registrado.css';
require_once('cabecera.inc');
require_once('inicio.inc');
?>

<main>
<section>
<h2>Registro completado</h2>

<p><strong>Usuario:</strong> <?= htmlspecialchars($values["usuario"]) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($values["email"]) ?></p>
<?php
// Mostrar etiqueta legible del sexo
if (isset($sexo_db)) {
    $sexo_label = ($sexo_db === 1) ? 'Hombre' : (($sexo_db === 2) ? 'Mujer' : 'Otro');
} else {
    $sexo_label = htmlspecialchars($old["sexo"] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<p><strong>Sexo:</strong> <?= $sexo_label ?></p>
<p><strong>Ciudad:</strong> <?= htmlspecialchars($values["ciudad"] ?? '') ?></p>

<p><a href="index.php"><strong>INICIAR SESIÓN</strong></a></p>
</section>
</main>

<?php require_once('pie.inc'); ?>