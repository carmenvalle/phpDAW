<header id="inicio">
  <h1>PI - Pisos &amp; Inmuebles</h1>
  <h2>PORTAL DE ANUNCIOS DE VENTA Y ALQUILER DE VIVIENDAS</h2>
</header>


<nav>
    <ul class="menu">
        <li><a href="index_logueado">
                <i class="icon-home"></i>
                <strong>INICIO</strong>
            </a></li>
        <li><a href="logout">
                <i class="icon-baja"></i>
                <strong>CERRAR SESIÓN</strong></a></li>
        <li><a href="busqueda">
                <i class="icon-search"></i>
                <strong>BÚSQUEDA</strong>
            </a></li>
        <li><a href="/phpDAW/configurar">
                <i class="icon-cog"></i>
                <strong>CONFIGURAR</strong>
            </a></li>
        <li class="perfil">
            <a href="miperfil">
                <?php
                // $_SESSION['foto'] ya está completamente resuelto desde control_acceso o registrado
                $avatar = !empty($_SESSION['foto']) 
                    ? $_SESSION['foto'] 
                    : '/phpDAW/DAW/practica/imagenes/default-avatar-profile-icon-vector-260nw-1909596082.webp';
                ?>
                <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil" class="perfil-foto">
                <?php
                if (isset($_SESSION['usuario'])) {
                    echo htmlspecialchars($_SESSION['usuario']);
                } else {
                    echo "Invitado";
                }
                ?>
            </a>
        </li>
    </ul>
</nav>