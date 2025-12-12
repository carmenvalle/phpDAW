
<?php
$title = "PI - Modificar datos";
$cssPagina = "registro.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/precio.php';

// Recuperar errores y valores enviados desde el procesador (flash)
$errors = [];
$old = [];
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['flash']['misdatos_errors'])) {
	$errors = $_SESSION['flash']['misdatos_errors'];
	$old = $_SESSION['flash']['misdatos_values'] ?? [];
	unset($_SESSION['flash']['misdatos_errors'], $_SESSION['flash']['misdatos_values']);
}

$user = null;
if (isset($_SESSION['id']) && isset($conexion)) {
	$stmt = $conexion->prepare('SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto FROM Usuarios WHERE IdUsuario = ?');
	$stmt->execute([$_SESSION['id']]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($user) {
		// Aceptar que Sexo en BD sea numérico (1=Hombre,2=Mujer) o la letra H/M/O
		$sexoStored = $user['Sexo'];
		if (is_numeric($sexoStored)) {
			$user['SexoChar'] = ($sexoStored == 1) ? 'H' : (($sexoStored == 2) ? 'M' : 'O');
		} else {
			$user['SexoChar'] = $sexoStored;
		}
	}
}
?>

<main>
	<section>
		<h2>Modificar mis datos</h2>
		<?php if (!$user): ?>
				<p>No se han podido cargar tus datos. Asegúrate de estar identificado.</p>
			<?php else: ?>
				<form action="procesar_modificar_datos" method="post" enctype="multipart/form-data">
					<p>
						<label>Nombre de usuario: </label>
						<input type="text" name="usuario" value="<?= htmlspecialchars($old['usuario'] ?? $user['NomUsuario']) ?>">
						<label>Email:</label>
						<input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? $user['Email']) ?>">
					</p>
					<p>
						<label>Sexo:</label>
						<?php $sexoSel = $old['sexo'] ?? ($user['SexoChar'] ?? ''); ?>
						<select name="sexo">
							<option value="">--</option>
							<option value="H" <?= ($sexoSel === 'H') ? 'selected' : '' ?>>Hombre</option>
							<option value="M" <?= ($sexoSel === 'M') ? 'selected' : '' ?>>Mujer</option>
							<option value="O" <?= ($sexoSel === 'O') ? 'selected' : '' ?>>Otro</option>
						</select>
					</p>
					<p>
						<label>Fecha nacimiento:</label>
						<input type="date" name="nacimiento" value="<?= htmlspecialchars($old['nacimiento'] ?? $user['FNacimiento']) ?>">
					</p>
					<p>
						<label>Ciudad:</label>
						<input type="text" name="ciudad" value="<?= htmlspecialchars($old['ciudad'] ?? $user['Ciudad']) ?>">
					</p>
					<p>
						<label>País:</label>
						<select name="pais">
							<option value="">--</option>
							<?php
							try {
								$rs = $conexion->query('SELECT IdPaises AS IdPais, NomPais FROM Paises ORDER BY NomPais');
								$ps = $rs->fetchAll(PDO::FETCH_ASSOC);
							} catch (Exception $e) {
								$ps = [];
							}
							foreach ($ps as $p) {
								$sel = (($old['pais'] ?? $user['Pais']) == $p['IdPais']) ? 'selected' : '';
								echo "<option value='{$p['IdPais']}' $sel>" . htmlspecialchars($p['NomPais']) . "</option>";
							}
							?>
						</select>
					</p>
					<p>
						<label>Foto actual:</label>
						<?php 
						$avatar = $user['Foto'] 
							? resolve_image_url($user['Foto']) 
							: '/phpDAW/DAW/practica/imagenes/default-avatar-profile-icon-vector-260nw-1909596082.webp';
						?>
						<br><img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Foto" width="120">
						<?php if ($user['Foto']): ?>
							<br><label><input type="checkbox" name="eliminar_foto"> Eliminar foto</label>
						<?php endif; ?>
					</p>
					<p>
						<label>Subir nueva foto:</label>
						<input type="file" name="foto" accept="image/*">
					</p>

					<hr>
					<p>Para confirmar los cambios introduce tu <strong>contraseña actual</strong>:</p>
					<p>
						<label>Contraseña actual:</label>
						<input type="password" name="current_password" required>
					</p>
					<?php if (in_array('current_password_required', $errors)): ?>
						<span class="error-campo">Debes introducir tu contraseña actual.</span>
					<?php endif; ?>
					<?php if (in_array('current_password_invalid', $errors)): ?>
						<span class="error-campo">La contraseña actual es incorrecta.</span>
					<?php endif; ?>

					<h4>Cambiar contraseña (opcional)</h4>
					<p>
						<label>Nueva contraseña:</label>
						<input type="password" name="new_password">
					</p>
					<p>
						<label>Repetir nueva contraseña:</label>
						<input type="password" name="repeat_new_password">
					</p>

					<p>
						<button type="submit">GUARDAR</button>
					</p>
				</form>
			<?php endif; ?>
	</section>
</main>

<?php
require_once("pie.inc");    
?>