<section>
  <h2>ACCESO DE USUARIOS REGISTRADOS</h2>
  <?php
  // Mostrar errores de acceso usando flashdata en sesión (más seguro que pasar por la URL)
  if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
  }
  if (isset($_SESSION['flash']['acceso_error'])) {
    $msg = htmlspecialchars($_SESSION['flash']['acceso_error'], ENT_QUOTES, 'UTF-8');
    echo '<div class="flash flash--error" role="alert">';
    echo '<div class="flash__text">' . $msg . '</div>';
    echo '<button class="flash__close" aria-label="Cerrar" onclick="this.closest(\'div.flash\').style.display=\'none\'">&times;</button>';
    echo '</div>';
    unset($_SESSION['flash']['acceso_error']);
  } elseif (isset($_GET['acceso_error'])) {
    // Compatibilidad: si viene por URL mostrarlo
    $msg = htmlspecialchars($_GET['acceso_error'], ENT_QUOTES, 'UTF-8');
    echo '<div class="flash flash--error" role="alert">';
    echo '<div class="flash__text">' . $msg . '</div>';
    echo '<button class="flash__close" aria-label="Cerrar" onclick="this.closest(\'div.flash\').style.display=\'none\'">&times;</button>';
    echo '</div>';
  }
  ?>

  <form action="control_acceso" method="post">
    <fieldset>
      <legend>INTRODUCE TUS CREDENCIALES</legend>

      <p>
        <label for="usuario">Nombre de usuario:</label>
        <input type="text" id="usuario" name="usuario" required>
      </p>

      <p>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="contrasena" required>
      </p>

      <p class="recordarme">
        <label>
          <input type="checkbox" id="recordarme" name="recordarme" value="1">
          <span class="switch" aria-hidden="true"></span>
          <span class="recordarme-text">Recordarme</span>
        </label>
      </p>

      <p><button type="submit"><strong>ENTRAR</strong></button></p>
    </fieldset>
  </form>
</section>
