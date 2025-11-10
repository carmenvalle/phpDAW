<section class="anuncios visitados">
  <h2>Últimos anuncios visitados</h2>

  <?php
  if (isset($_COOKIE['visitados'])) {
      $visitados = json_decode($_COOKIE['visitados'], true);

      if (is_array($visitados) && count($visitados) > 0) {
          echo "<ul class='lista-visitados'>";
          foreach ($visitados as $a) {
              $id = isset($a['id']) ? htmlspecialchars($a['id'], ENT_QUOTES, 'UTF-8') : '';
              $titulo = isset($a['titulo']) ? htmlspecialchars($a['titulo'], ENT_QUOTES, 'UTF-8') : '';
              $ciudad = isset($a['ciudad']) ? htmlspecialchars($a['ciudad'], ENT_QUOTES, 'UTF-8') : '';
              $pais = isset($a['pais']) ? htmlspecialchars($a['pais'], ENT_QUOTES, 'UTF-8') : '';
              $precio = isset($a['precio']) ? htmlspecialchars($a['precio'], ENT_QUOTES, 'UTF-8') : '';
              $imagen = isset($a['imagen']) ? htmlspecialchars($a['imagen'], ENT_QUOTES, 'UTF-8') : '';

              echo "
              <li>
                <article>
                  <a href='anuncio.php?id={$id}'>
                    <img src='{$imagen}' alt='Foto de {$titulo}' width='120' height='80'>
                    <h3>{$titulo}</h3>
                    <p>{$ciudad}, {$pais}<br><strong>{$precio}</strong></p>
                  </a>
                </article>
              </li>";
          }
          echo "</ul>";
      } else {
          echo "<p>No has visitado ningún anuncio todavía.</p>";
      }
  } else {
      echo "<p>No has visitado ningún anuncio todavía.</p>";
  }
  ?>
</section>
