<?php
$title = "PI - Pisos & Inmuebles";
$cssPagina = "folleto.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
?>

<main>
    <section>
        <p>
            Puede solicitar un folleto publicitario impreso basado en uno de sus anuncios.
            Complete el siguiente formulario con sus datos y las características del folleto.
        </p>
    </section>

    <aside>
        <h2>Tarifas</h2>
        <table class="tarifas">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Tarifa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Coste procesamiento y envío</td>
                    <td>10 €</td>
                </tr>
                <tr>
                    <td>&lt; 5 páginas</td>
                    <td>2 € por pág.</td>
                </tr>
                <tr>
                    <td>Entre 5 y 10 páginas</td>
                    <td>1.8 € por pág.</td>
                </tr>
                <tr>
                    <td>&gt; 10 páginas</td>
                    <td>1.6 € por pág.</td>
                </tr>
                <tr>
                    <td>Blanco y negro</td>
                    <td>0 €</td>
                </tr>
                <tr>
                    <td>Color</td>
                    <td>0.5 € por foto</td>
                </tr>
                <tr>
                    <td>Resolución ≤ 300 dpi</td>
                    <td>0 € por foto</td>
                </tr>
                <tr>
                    <td>Resolución &gt; 300 dpi</td>
                    <td>0.2 € por foto</td>
                </tr>
            </tbody>
        </table>
    </aside>

    <section>
        <h2>Formulario de solicitud</h2>
        <p>
            Los campos marcados con un asterisco (*) son obligatorios.
        </p>
        <form id="formFolleto" action="solicitar_folleto_respuesta.php" method="post" novalidate>

            <p>
                <label for="nombre">Nombre y apellidos (*):</label>
                <input type="text" id="nombre" name="nombre">
            </p>

            <p>
                <label for="correo">Correo electrónico (*):</label>
                <input type="email" id="correo" name="correo">
            </p>

            <p>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono">
            </p>

            <fieldset>
                <legend>Dirección postal (*)</legend>

                <p>
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle">
                </p>

                <p>
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="numero">
                </p>

                <p>
                    <label for="piso">Piso/Puerta:</label>
                    <input type="text" id="piso" name="piso">
                </p>

                <p>
                    <label for="codigo_postal">Código postal:</label>
                    <input type="text" id="codigo_postal" name="codigo_postal">
                </p>

                <p>
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad">
                </p>

                <p>
                    <label for="provincia">Provincia:</label>
                    <input type="text" id="provincia" name="provincia">
                </p>

                <p>
                    <label for="pais">País:</label>
                    <input type="text" id="pais" name="pais">
                </p>
            </fieldset>

            <p>
                <label for="texto">Texto adicional:</label><br>
                <textarea id="texto" name="texto"
                    placeholder="Información adicional a la que ya tiene el propio anuncio"></textarea>
            </p>

            <p>
                <label for="color">Color de la portada:</label>
                <input type="color" id="color" name="color" value="#000000">
            </p>

            <p>
                <label for="paginas">Número de páginas (*):</label>
                <input type="text" id="paginas" name="paginas" value="8">
            </p>

            <p>
                <label for="copias">Número de copias (*):</label>
                <input type="text" id="copias" name="copias" value="1">
            </p>

            <p>
                <label for="resolucion">Resolución (DPI) (*):</label>
                <input type="text" id="resolucion" name="resolucion" value="150">
            </p>

            <p>
                <label for="anuncio">Selecciona el anuncio (*):</label>
                <select id="anuncio" name="anuncio">
                    <option value="">-- Selecciona tu anuncio --</option>
                    <option value="anuncio1">Anuncio 1</option>
                    <option value="anuncio2">Anuncio 2</option>
                </select>
            </p>

            <p>
                <label for="fecha">Fecha aproximada de recepción:</label>
                <input type="date" id="fecha" name="fecha">
            </p>

            <p>
                Impresión a color (*):
                <label><input type="radio" name="impresion_color" value="color"> A color</label>
                <label><input type="radio" name="impresion_color" value="bn"> Blanco y negro</label>
            </p>

            <p>
                Mostrar precio (*):
                <label><input type="radio" name="mostrar_precio" value="si"> Sí</label>
                <label><input type="radio" name="mostrar_precio" value="no"> No</label>
            </p>

            <p>
                <button type="submit"><strong>ENVIAR SOLICITUD</strong></button>
            </p>
        </form>
    </section>

    <section id="tabla-costes-section" style="text-align:center; margin-top:2em;">
        <p class="toggle-cost-btn">
            <button id="toggle-cost-table" type="button" class="btn" aria-expanded="false" aria-controls="cost-table-container"><strong>Mostrar tabla de costes del folleto</strong></button>
        </p>

        <div id="cost-table-container" style="margin-top:1.5em;"></div>

        <?php
        require_once(__DIR__ . '/includes/precio.php');
        ?>

        <?php
        
        echo '<noscript>';
        echo '<div style="margin-top:1.5em;">';
        echo '<h2>Tabla de costes</h2>';
        echo '<table class="costes-folleto" style="margin: 0 auto;">';
        echo '<thead><tr><th>Número de páginas</th><th>Número de fotos</th><th colspan="2">Blanco y negro</th><th colspan="2">Color</th></tr>';
        echo '<tr><th></th><th></th><th>150-300 dpi</th><th>450-900 dpi</th><th>150-300 dpi</th><th>450-900 dpi</th></tr></thead>';
        echo '<tbody>';
        for ($paginas = 1; $paginas <= 15; $paginas++) {
            $fotos = $paginas * 3;
            echo '<tr>';
            echo '<td>' . $paginas . '</td>';
            echo '<td>' . $fotos . '</td>';
            echo '<td>' . formatearPrecio(calcularPrecio($paginas, false, 300, 1)) . '</td>';
            echo '<td>' . formatearPrecio(calcularPrecio($paginas, false, 450, 1)) . '</td>';
            echo '<td>' . formatearPrecio(calcularPrecio($paginas, true, 300, 1)) . '</td>';
            echo '<td>' . formatearPrecio(calcularPrecio($paginas, true, 450, 1)) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
        echo '</noscript>';
        ?>
    </section>
</main>

<?php
echo "<script src=\"DAW/practica/js/solicitar_folleto.js\"></script>\n";
require_once("salto.inc");
require_once("pie.inc");
?>
