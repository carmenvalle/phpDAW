<?php
$title = "PI - PI Pisos & Inmuebles";
require_once("cabecera.inc");
require_once("inicioLog.inc");
?>

<main>
    <section>
        <h2>Compromiso con la Accesibilidad</h2>
        <p>
            PI - Pisos &amp; Inmuebles está comprometido con hacer accesible su sitio web de conformidad con las 
            <strong>Pautas de Accesibilidad para el Contenido Web (WCAG) 2.1</strong>, nivel AA. 
            Nuestro objetivo es que todas las personas, independientemente de sus capacidades, puedan acceder 
            a la información y servicios que ofrecemos.
        </p>
    </section>

    <section>
        <h2>1. Etiquetado Semántico</h2>
        <p>
            El sitio web utiliza etiquetas HTML5 semánticas para estructurar correctamente el contenido:
        </p>
        <ul>
            <li><strong>&lt;header&gt;</strong>: Para la cabecera del sitio con el título y subtítulo</li>
            <li><strong>&lt;nav&gt;</strong>: Para la barra de navegación principal</li>
            <li><strong>&lt;main&gt;</strong>: Para el contenido principal de cada página</li>
            <li><strong>&lt;section&gt;</strong>: Para agrupar contenido relacionado temáticamente</li>
            <li><strong>&lt;article&gt;</strong>: Para contenido independiente como anuncios de viviendas</li>
            <li><strong>&lt;footer&gt;</strong>: Para el pie de página con información de copyright</li>
            <li><strong>&lt;form&gt;</strong> y <strong>&lt;fieldset&gt;</strong>: Para formularios bien estructurados</li>
        </ul>
        <p>
            Esta estructura semántica permite a los lectores de pantalla y otras tecnologías de asistencia 
            navegar e interpretar correctamente el contenido del sitio.
        </p>
    </section>

    <section>
        <h2>2. Texto Alternativo en Imágenes</h2>
        <p>
            Todas las imágenes del sitio incluyen el atributo <code>alt</code> con descripciones apropiadas:
        </p>
        <ul>
            <li><strong>Imágenes de contenido</strong>: Incluyen descripciones detalladas (ej: "Foto del anuncio 1 - Piso en Madrid centro")</li>
            <li><strong>Imágenes decorativas</strong>: Incluyen alt vacío (<code>alt=""</code>) para que sean ignoradas por lectores de pantalla</li>
            <li><strong>Iconos funcionales</strong>: Están acompañados de texto descriptivo visible</li>
            <li><strong>Foto de perfil</strong>: Incluye descripción "Foto de perfil"</li>
        </ul>
        <p>
            Esto garantiza que los usuarios que utilizan lectores de pantalla puedan comprender el contenido 
            visual del sitio web.
        </p>
    </section>

    <section>
        <h2>3. Uso Correcto de los Colores</h2>
        <p>
            El sitio web cumple con los requisitos de accesibilidad en el uso de colores:
        </p>
        <ul>
            <li><strong>Contraste adecuado</strong>: La combinación de colores cumple con una relación de contraste de al menos 4.5:1 (nivel AA de WCAG 2.1)</li>
            <li><strong>No dependencia del color</strong>: La información no se transmite únicamente mediante el color. Se utilizan iconos, texto y otros indicadores visuales</li>
            <li><strong>Enlaces distinguibles</strong>: Los enlaces se destacan mediante color y cambio de cursor, además de mantener el subrayado en estados hover</li>
            <li><strong>Estados de formulario</strong>: Los campos de formulario utilizan bordes, fondos y etiquetas claras, no solo color</li>
        </ul>
        <p>
            Paleta de colores principal:
        </p>
        <ul>
            <li>Color principal: <span style="background-color: #4a1c00; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px;">#4a1c00</span></li>
            <li>Color secundario: <span style="background-color: #d8bf9f; color: #4a1c00; padding: 0.2rem 0.5rem; border-radius: 4px;">#d8bf9f</span></li>
            <li>Color de acento: <span style="background-color: #ab875f; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px;">#ab875f</span></li>
            <li>Fondo: <span style="background-color: #f9f9f9; color: #333; padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid #ccc;">#f9f9f9</span></li>
        </ul>
    </section>

    <section>
        <h2>4. Cómo Activar las Hojas de Estilo Accesibles</h2>
        <p>
            Este sitio web ofrece múltiples hojas de estilo alternativas para mejorar la accesibilidad:
        </p>
            
        <h3>Estilos Disponibles:</h3>
        <ul>
            <li><strong>Estilo por defecto</strong>: Diseño principal del sitio con colores cálidos en tonos marrones y beige</li>
            <li><strong>Modo oscuro</strong>: Reduce el brillo de la pantalla con fondos oscuros en tonos granates y texto claro</li>
            <li><strong>Alto contraste</strong>: Maximiza el contraste entre texto y fondo para personas con baja visión</li>
            <li><strong>Letra grande</strong>: Aumenta el tamaño de la fuente para facilitar la lectura</li>
            <li><strong>Alto contraste y letra grande</strong>: Combina ambas mejoras de accesibilidad</li>
        </ul>

        <h3>Activación en Mozilla Firefox:</h3>
        <ol>
            <li>Pulse la tecla <strong>Alt</strong> para mostrar la barra de menú (si no está visible)</li>
            <li>Vaya al menú <strong>Ver</strong></li>
            <li>Seleccione <strong>Estilo de página</strong></li>
            <li>Elija el estilo que desee de la lista</li>
        </ol>

        <h3>Activación en Microsoft Edge:</h3>
        <ol>
            <li>Pulse la tecla <strong>Alt</strong> para mostrar la barra de menú</li>
            <li>Vaya al menú <strong>Ver</strong></li>
            <li>Seleccione <strong>Estilo</strong></li>
            <li>Elija el estilo alternativo deseado</li>
        </ol>

        <p>
            <strong>Nota</strong>: Google Chrome no tiene soporte nativo para estilos alternativos. 
            Se recomienda usar Firefox o Edge para acceder a todas las opciones de accesibilidad.
        </p>
    </section>

    <section>
        <h2>5. Otras Características de Accesibilidad</h2>
            
        <h3>Navegación por Teclado</h3>
        <ul>
            <li>Todo el contenido es accesible mediante navegación por teclado usando la tecla <strong>Tab</strong></li>
            <li>El orden de tabulación sigue una secuencia lógica</li>
            <li>Los elementos interactivos muestran un indicador visual claro cuando reciben el foco</li>
        </ul>

        <h3>Formularios Accesibles</h3>
        <ul>
            <li>Todos los campos de formulario tienen etiquetas (<code>&lt;label&gt;</code>) asociadas correctamente</li>
            <li>Los campos obligatorios están claramente indicados con asterisco (*)</li>
            <li>Se utilizan <code>&lt;fieldset&gt;</code> y <code>&lt;legend&gt;</code> para agrupar campos relacionados</li>
            <li>Los mensajes de error son claros y descriptivos</li>
        </ul>

        <h3>Enlaces Descriptivos</h3>
        <ul>
            <li>Los enlaces contienen texto descriptivo que indica claramente su destino</li>
            <li>Se evitan textos genéricos como "clic aquí" o "más información"</li>
            <li>Los iconos de navegación están acompañados de texto descriptivo</li>
        </ul>

        <h3>Tipografía Legible</h3>
        <ul>
            <li>Se utiliza la fuente <strong>Montserrat</strong>, de alta legibilidad</li>
            <li>Tamaño base de fuente: 16px</li>
            <li>Interlineado adecuado (1.6) para facilitar la lectura</li>
            <li>El texto no está justificado para evitar espacios irregulares</li>
        </ul>

        <h3>Diseño Responsive</h3>
        <ul>
            <li>El sitio se adapta a diferentes tamaños de pantalla y dispositivos</li>
            <li>Compatible con zoom del navegador hasta el 200% sin pérdida de funcionalidad</li>
            <li>Optimizado para dispositivos móviles, tablets y escritorio</li>
        </ul>

        <h3>Versión para Impresión</h3>
        <ul>
            <li>El sitio incluye una hoja de estilo específica para impresión</li>
            <li>Se eliminan elementos innecesarios (navegación, formularios) al imprimir</li>
            <li>Se optimiza el contraste y tamaño de texto para papel</li>
        </ul>
    </section>

    <section>
        <h2>6. Estándares y Tecnologías Utilizadas</h2>
        <ul>
            <li><strong>HTML5</strong>: Lenguaje de marcado semántico</li>
            <li><strong>CSS3</strong>: Hojas de estilo para la presentación visual</li>
            <li><strong>UTF-8</strong>: Codificación de caracteres universal</li>
            <li><strong>Viewport responsive</strong>: Adaptación a diferentes dispositivos</li>
            <li><strong>Google Fonts</strong>: Tipografía web accesible (Montserrat)</li>
            <li><strong>Material Symbols</strong>: Iconografía descriptiva</li>
        </ul>
    </section>

    <section>
        <h2>7. Contacto y Retroalimentación</h2>
        <p>
            Si encuentra alguna barrera de accesibilidad en este sitio web o tiene sugerencias para mejorar 
            la accesibilidad, por favor contáctenos:
        </p>
        <ul>
            <li><strong>Autores</strong>: Silvia Balmaseda y Carmen Valle</li>
            <li><strong>Año</strong>: 2025</li>
        </ul>
        <p>
            Nos comprometemos a responder a todas las consultas relacionadas con la accesibilidad y a realizar 
            las mejoras necesarias en la medida de lo posible.
        </p>
    </section>

    <section>
        <h2>8. Fecha de Esta Declaración</h2>
        <p>
            Esta declaración de accesibilidad fue actualizada por última vez el <strong>19 de octubre de 2025</strong>.
        </p>
    </section>


    <?php
        require_once("salto.inc");  
    ?>

</main>

<?php
require_once("pie.inc");    
?>