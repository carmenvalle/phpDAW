<?php
if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
$title = "PI - PI Pisos & Inmuebles";
$cssPagina = "miperfil.css";
require_once("cabecera.inc");
require_once(__DIR__ . '/privado.inc');
require_once("inicioLog.inc");
// Asegurar sesión
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Incluir helper de imágenes si existe
if (file_exists(__DIR__ . '/includes/precio.php')) {
    require_once __DIR__ . '/includes/precio.php';
}
// Thumbnails/helper
if (file_exists(__DIR__ . '/includes/funciones-ficheros.php')) {
    require_once __DIR__ . '/includes/funciones-ficheros.php';
}

// Incluir conexión PDO si existe (define $conexion)
if (file_exists(__DIR__ . '/includes/conexion.php')) {
    require_once __DIR__ . '/includes/conexion.php';
}

// Determinar id de usuario a mostrar (GET ?id= o sesión)
$idUsuario = null;
if (isset($_GET['id']) && $_GET['id'] !== '') {
    $idUsuario = intval($_GET['id']);
} elseif (isset($_SESSION['IdUsuario']) && $_SESSION['IdUsuario'] !== '') {
    $idUsuario = intval($_SESSION['IdUsuario']);
} elseif (isset($_SESSION['id']) && $_SESSION['id'] !== '') {
    $idUsuario = intval($_SESSION['id']);
}

// Preparar datos
$usuario = null;
$anuncios = [];
// Si no hay id pero hay nombre de usuario en sesión, intentamos recuperar por nombre
if ((!$idUsuario) && isset($conexion) && $conexion instanceof PDO) {
    if (isset($_SESSION['usuario']) && $_SESSION['usuario'] !== '') {
        try {
            $sname = $conexion->prepare('SELECT IdUsuario, NomUsuario, Foto, FRegistro FROM usuarios WHERE NomUsuario = ? LIMIT 1');
            $sname->execute([$_SESSION['usuario']]);
            $u = $sname->fetch(PDO::FETCH_ASSOC);
            if ($u) {
                $idUsuario = (int)$u['IdUsuario'];
                $usuario = $u;
            }
        } catch (Exception $e) {
            // no hacemos nada, seguiremos con $usuario = null
        }
    } elseif (isset($_SESSION['NomUsuario']) && $_SESSION['NomUsuario'] !== '') {
        try {
            $sname = $conexion->prepare('SELECT IdUsuario, NomUsuario, Foto, FRegistro FROM usuarios WHERE NomUsuario = ? LIMIT 1');
            $sname->execute([$_SESSION['NomUsuario']]);
            $u = $sname->fetch(PDO::FETCH_ASSOC);
            if ($u) {
                $idUsuario = (int)$u['IdUsuario'];
                $usuario = $u;
            }
        } catch (Exception $e) {
        }
    }
}

if ($idUsuario && isset($conexion) && $conexion instanceof PDO) {
    try {
        $stmt = $conexion->prepare('SELECT NomUsuario, Foto, FRegistro FROM usuarios WHERE IdUsuario = ? LIMIT 1');
        $stmt->execute([$idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $usuario = null;
    }

    if ($usuario) {
        try {
            $s2 = $conexion->prepare('SELECT IdAnuncio, Titulo, Ciudad, Precio, FPrincipal FROM anuncios WHERE Usuario = ? ORDER BY FRegistro DESC');
            $s2->execute([$idUsuario]);
            $anuncios = $s2->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $anuncios = [];
        }
    }
}

// Formatear la fecha de incorporación con fallback a la sesión si es necesario
$fecha_formateada = '';
// Si el usuario existe pero no tiene FRegistro en la BD, intentamos rellenarlo
if ($usuario && empty($usuario['FRegistro']) && isset($conexion) && $conexion instanceof PDO) {
    // Priorizar valor desde la sesión si existe
    $valor_nuevo = null;
    if (!empty($_SESSION['FRegistro'])) {
        try {
            if ($_SESSION['FRegistro'] instanceof DateTime) {
                $dt_tmp = $_SESSION['FRegistro'];
            } else {
                $dt_tmp = new DateTime($_SESSION['FRegistro']);
            }
            $valor_nuevo = $dt_tmp->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            $valor_nuevo = date('Y-m-d H:i:s');
        }
    } else {
        // Si no hay valor en sesión, usamos la fecha actual
        $valor_nuevo = date('Y-m-d H:i:s');
    }

    // Intentar actualizar la BD con el nuevo valor
    try {
        $upd = $conexion->prepare('UPDATE usuarios SET FRegistro = ? WHERE IdUsuario = ?');
        $upd->execute([$valor_nuevo, $idUsuario]);
        // Reflejar el cambio en la variable local para poder formatearla abajo
        $usuario['FRegistro'] = $valor_nuevo;
    } catch (Exception $e) {
        // No hacemos nada, seguiremos con fecha vacía
    }
}
if ($usuario && !empty($usuario['FRegistro'])) {
    try {
        if ($usuario['FRegistro'] instanceof DateTime) {
            $dt = $usuario['FRegistro'];
        } else {
            $dt = new DateTime($usuario['FRegistro']);
        }
        $meses = ['', 'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        $fecha_formateada = $dt->format('j') . ' de ' . $meses[(int)$dt->format('n')] . ' de ' . $dt->format('Y') . ' a las ' . $dt->format('H:i');
    } catch (Exception $e) {
        $fecha_formateada = '';
    }
} elseif (isset($_SESSION['FRegistro']) && !empty($_SESSION['FRegistro'])) {
    try {
        if ($_SESSION['FRegistro'] instanceof DateTime) {
            $dt = $_SESSION['FRegistro'];
        } else {
            $dt = new DateTime($_SESSION['FRegistro']);
        }
        $meses = ['', 'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        $fecha_formateada = $dt->format('j') . ' de ' . $meses[(int)$dt->format('n')] . ' de ' . $dt->format('Y') . ' a las ' . $dt->format('H:i');
    } catch (Exception $e) {
        $fecha_formateada = '';
    }
}

?>

<main>

    <?php if ($usuario): ?>
    <section class="profile-box">
        <div class="profile-left">
            <?php 
            $foto = $usuario['Foto'] 
                ? (function_exists('resolve_image_url') ? resolve_image_url($usuario['Foto']) : $usuario['Foto'])
                : '/phpDAW/DAW/practica/imagenes/default-avatar-profile-icon-vector-260nw-1909596082.webp';
            ?>
            <img src="<?php echo htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto de <?php echo htmlspecialchars($usuario['NomUsuario']); ?>" class="perfil-foto">
        </div>
        <div class="profile-right">
            <h1 class="perfil-nombre">
                <?php echo htmlspecialchars($usuario['NomUsuario']); ?>
                <?php if (!empty($fecha_formateada)): ?>
                    <span class="fecha-alta">Fecha de incorporación: <?php echo htmlspecialchars($fecha_formateada, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php else: ?>
                    <span class="fecha-alta" style="opacity:0.6;">Fecha de incorporación: —</span>
                <?php endif; ?>
            </h1>
        </div>
    </section>

    <section id="anunciosUsuario">
        <h2>Lista de anuncios publicados</h2>
        <?php if (empty($anuncios)): ?>
            <p>No tiene anuncios publicados.</p>
        <?php else: ?>
            <ul class="lista-anuncios">
                <?php foreach ($anuncios as $an): ?>
                    <?php
                    if (!defined('APP_INIT')) { http_response_code(403); echo 'Acceso no autorizado.'; exit; }
                        // Identificadores y títulos del anuncio (evita variables indefinidas)
                        $idA = isset($an['IdAnuncio']) ? (int)$an['IdAnuncio'] : 0;
                        $titulo = htmlspecialchars($an['Titulo'] ?? 'Sin título', ENT_QUOTES, 'UTF-8');
                        $ciudad = htmlspecialchars($an['Ciudad'] ?? '');
                        $precio = isset($an['Precio']) ? number_format((float)$an['Precio'], 2, ',', '.') . ' €' : '—';
                        $fotoA = (function_exists('get_thumbnail_url')
                                    ? get_thumbnail_url($an['FPrincipal'] ?? '', 220, 160)
                                    : ((function_exists('resolve_image_url') ? resolve_image_url($an['FPrincipal'] ?? '') : ($an['FPrincipal'] ?? '')) ?: '/phpDAW/DAW/practica/imagenes/default-list.png'));
                    ?>
                    <li>
                        <div class="anuncio-card">
                            <a class="anuncio-media" href="anuncio/<?php echo $idA; ?>">
                                <img src="<?php echo htmlspecialchars($fotoA, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $titulo; ?>">
                            </a>
                            <div class="anuncio-body">
                                <h4 class="anuncio-title"><?php echo $titulo; ?></h4>
                                <p class="anuncio-meta">
                                    <span class="anuncio-city"><?php echo $ciudad; ?></span>
                                    <span class="anuncio-sep">—</span>
                                    <span class="anuncio-price"><?php echo $precio; ?></span>
                                </p>
                                <div class="anuncio-actions" style="margin-top:8px;display:flex;gap:8px;align-items:center;">
                                    <a class="btn btn-outline" href="/phpDAW/ver_fotos?id=<?php echo $idA; ?>">Ver fotos</a>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <?php else: ?>
        <section>
            <p style="color:darkred;">No se ha especificado usuario para mostrar el perfil público.</p>
        </section>
    <?php endif; ?>

    <section>
        <h2>OPCIONES DE USUARIO</h2>
        <ul>
            <li><a href="modificar_datos">
                    <i class="icon-edit"></i>
                    Editar mis datos
                </a></li>
            <li><a href="/phpDAW/mis-anuncios">
                    <i class="icon-anuncio"></i>
                    Mis anuncios
                </a></li>
            <li><a href="nuevo_anuncio">
                    <i class="icon-crear-anuncio"></i>
                    Crear anuncio nuevo
                </a></li>
            <li><a href="mis_mensajes">
                    <i class="icon-mensaje"></i>
                    Mis mensajes
                </a></li>
            <li><a href="folleto">
                    <i class="icon-form"></i>
                    Solicitar folleto publicitario impreso
                </a></li>
            <li><a href="dar-baja">
                    <i class="icon-baja"></i>
                    Darme de baja
                </a></li>
        </ul>
    </section>


<?php
    require_once("salto.inc");  
?>

</main>

<?php
require_once("pie.inc");    
?>