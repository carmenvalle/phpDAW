-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2025 a las 11:44:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pibd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `IdAnuncio` int(11) NOT NULL,
  `TAnuncio` smallint(6) DEFAULT NULL,
  `TVivienda` smallint(6) DEFAULT NULL,
  `FPrincipal` varchar(200) DEFAULT NULL,
  `Alternativo` varchar(255) NOT NULL,
  `Titulo` varchar(255) DEFAULT NULL,
  `Precio` double(10,2) DEFAULT NULL,
  `Texto` text DEFAULT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `Pais` int(11) DEFAULT NULL,
  `Superficie` double(10,2) DEFAULT NULL,
  `NHabitaciones` int(11) DEFAULT NULL,
  `NBanyos` int(11) DEFAULT NULL,
  `Planta` int(11) DEFAULT NULL,
  `Anyo` int(11) DEFAULT NULL,
  `FRegistro` datetime DEFAULT NULL,
  `Usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estilos`
--

CREATE TABLE `estilos` (
  `IdEstilo` int(11) NOT NULL,
  `Nombre` text NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fichero` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estilos`
--

INSERT INTO `estilos` (`IdEstilo`, `Nombre`, `Descripcion`, `Fichero`) VALUES
(1, 'Normal', 'Estilo por defecto del sistema', 'general.css'),
(2, 'Letra grande', 'Versión accesible con tamaño de letra aumentado', 'letra_grande.css'),
(3, 'Alto contraste', 'Versión accesible de alto contraste', 'alto_contraste.css');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos`
--

CREATE TABLE `fotos` (
  `IdFoto` int(11) NOT NULL,
  `Titulo` varchar(200) DEFAULT NULL,
  `Foto` varchar(200) DEFAULT NULL,
  `Alternativo` varchar(255) NOT NULL,
  `Anuncio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `IdMensaje` int(11) NOT NULL,
  `TMensaje` smallint(6) DEFAULT NULL,
  `Texto` text DEFAULT NULL,
  `Anuncio` int(11) DEFAULT NULL,
  `UsuOrigen` int(11) DEFAULT NULL,
  `UsuDestino` int(11) DEFAULT NULL,
  `FRegistro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `IdPaises` int(11) NOT NULL,
  `NomPais` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`IdPaises`, `NomPais`) VALUES
(1, 'España'),
(2, 'Francia'),
(3, 'Italia'),
(4, 'Alemania'),
(5, 'Portugal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `IdSolicitud` int(11) NOT NULL,
  `Anuncio` int(11) DEFAULT NULL,
  `Texto` text DEFAULT NULL,
  `Nombre` varchar(200) DEFAULT NULL,
  `Email` varchar(254) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Color` varchar(100) DEFAULT NULL,
  `Copias` int(11) DEFAULT NULL,
  `Resolucion` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `IColor` tinyint(1) DEFAULT NULL,
  `IPrecio` tinyint(1) DEFAULT NULL,
  `FRegistro` datetime DEFAULT NULL,
  `Coste` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanuncios`
--

CREATE TABLE `tiposanuncios` (
  `IdTAnuncio` smallint(6) NOT NULL,
  `NomTAnuncio` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposanuncios`
--

INSERT INTO `tiposanuncios` (`IdTAnuncio`, `NomTAnuncio`) VALUES
(1, 'Venta'),
(2, 'Alquiler');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmensajes`
--

CREATE TABLE `tiposmensajes` (
  `IdTMensaje` smallint(6) NOT NULL,
  `NomTMensaje` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposmensajes`
--

INSERT INTO `tiposmensajes` (`IdTMensaje`, `NomTMensaje`) VALUES
(1, 'Consulta'),
(2, 'Respuesta'),
(3, 'Sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposviviendas`
--

CREATE TABLE `tiposviviendas` (
  `IdTVivienda` smallint(6) NOT NULL,
  `NomTVivienda` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposviviendas`
--

INSERT INTO `tiposviviendas` (`IdTVivienda`, `NomTVivienda`) VALUES
(1, 'Piso'),
(2, 'Casa'),
(3, 'Chalet'),
(4, 'Estudio'),
(5, 'Ático');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL,
  `NomUsuario` varchar(15) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Email` varchar(254) NOT NULL,
  `Sexo` smallint(11) DEFAULT NULL,
  `FNacimiento` date DEFAULT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `Pais` int(11) DEFAULT NULL,
  `Foto` varchar(200) DEFAULT NULL,
  `FRegistro` datetime DEFAULT NULL,
  `Estilo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`IdAnuncio`),
  ADD KEY `CAjTAnuncio` (`TAnuncio`),
  ADD KEY `CAjTVivienda` (`TVivienda`),
  ADD KEY `CAjPais` (`Pais`),
  ADD KEY `CAjUsuario` (`Usuario`);

--
-- Indices de la tabla `estilos`
--
ALTER TABLE `estilos`
  ADD PRIMARY KEY (`IdEstilo`);

--
-- Indices de la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`IdFoto`),
  ADD KEY `CAjAnuncio` (`Anuncio`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`IdMensaje`),
  ADD KEY `CAjMensajes` (`TMensaje`),
  ADD KEY `CAjAnuncios` (`Anuncio`),
  ADD KEY `CAjUsOrg` (`UsuOrigen`),
  ADD KEY `CAjUsDst` (`UsuDestino`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`IdPaises`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`IdSolicitud`),
  ADD KEY `CAjAnun` (`Anuncio`);

--
-- Indices de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  ADD PRIMARY KEY (`IdTAnuncio`);

--
-- Indices de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  ADD PRIMARY KEY (`IdTMensaje`);

--
-- Indices de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  ADD PRIMARY KEY (`IdTVivienda`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuario`),
  ADD UNIQUE KEY `NomUsuario` (`NomUsuario`),
  ADD KEY `CAjPaises` (`Pais`),
  ADD KEY `CAjEstilo` (`Estilo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `IdAnuncio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estilos`
--
ALTER TABLE `estilos`
  MODIFY `IdEstilo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `fotos`
--
ALTER TABLE `fotos`
  MODIFY `IdFoto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `IdMensaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `IdPaises` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `IdSolicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposanuncios`
--
ALTER TABLE `tiposanuncios`
  MODIFY `IdTAnuncio` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiposmensajes`
--
ALTER TABLE `tiposmensajes`
  MODIFY `IdTMensaje` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposviviendas`
--
ALTER TABLE `tiposviviendas`
  MODIFY `IdTVivienda` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD CONSTRAINT `CAjPais` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPaises`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CAjTAnuncio` FOREIGN KEY (`TAnuncio`) REFERENCES `tiposanuncios` (`IdTAnuncio`),
  ADD CONSTRAINT `CAjTVivienda` FOREIGN KEY (`TVivienda`) REFERENCES `tiposviviendas` (`IdTVivienda`),
  ADD CONSTRAINT `CAjUsuario` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `CAjAnuncio` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `CAjAnuncios` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CAjMensajes` FOREIGN KEY (`TMensaje`) REFERENCES `tiposmensajes` (`IdTMensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CAjUsDst` FOREIGN KEY (`UsuDestino`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CAjUsOrg` FOREIGN KEY (`UsuOrigen`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `CAjAnun` FOREIGN KEY (`Anuncio`) REFERENCES `anuncios` (`IdAnuncio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `CAjEstilo` FOREIGN KEY (`Estilo`) REFERENCES `estilos` (`IdEstilo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CAjPaises` FOREIGN KEY (`Pais`) REFERENCES `paises` (`IdPaises`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Seed data for testing phpDAW
-- Date: 2025-11-17
-- WARNING: This script contains example password hashes.
-- The `Clave` values below are bcrypt-style hashes (strings starting with $2y$).
-- They are intended to be compatible with PHP's password_verify()/password_hash() usage.

START TRANSACTION;

-- Add extra countries (if not present)
INSERT IGNORE INTO `paises` (`IdPaises`, `NomPais`) VALUES
(6, 'Reino Unido'),
(7, 'Estados Unidos');

-- Users (explicit IdUsuario to reference from other inserts)
-- Clave values are bcrypt hashes (already formatted as strings).
INSERT INTO `usuarios` (`IdUsuario`,`NomUsuario`,`Clave`,`Email`,`Sexo`,`FNacimiento`,`Ciudad`,`Pais`,`Foto`,`FRegistro`,`Estilo`) VALUES
(1001, 'silvia', '$2y$10$gB6C7AeeH8JWeC3XOZD/Y.fmrbDw/nIsbO3aNgm08I.kBT.YXchla', 'silvia@example.com', 2, '1990-05-15', 'Madrid', 1, NULL, '2025-11-17 12:00:00', 1),
(1002, 'clara', '$2y$10$mqGHj/Hj9Ai78.sK2M62iexZerELOsszQVEID3UTLuhjFU4.jT1Dm', 'clara@example.com', 1, '1985-03-20', 'Barcelona', 1, NULL, '2025-11-17 12:05:00', 1),
(1003, 'fran', '$2y$10$KGK15zD/DxFzL0shy.QjweCA9nwGemZ1OC21RoTs.PTe47hkWrM66', 'fran@example.com', 2, '1992-08-02', 'Valencia', 1, NULL, '2025-11-17 12:10:00', 1);

-- Anuncios (explicit IdAnuncio)
INSERT INTO `anuncios` (`IdAnuncio`,`TAnuncio`,`TVivienda`,`FPrincipal`,`Alternativo`,`Titulo`,`Precio`,`Texto`,`Ciudad`,`Pais`,`Superficie`,`NHabitaciones`,`NBanyos`,`Planta`,`Anyo`,`FRegistro`,`Usuario`) VALUES
(2001, 1, 1, 'DAW/practica/imagenes/house1_main.jpg', 'foto1.jpg', 'Piso luminoso en Madrid', 120000.00, 'Piso muy luminoso, cerca del metro y colegios.', 'Madrid', 1, 85.00, 3, 2, 2, 2005, '2025-11-17 12:30:00', 1001),
(2002, 2, 2, 'DAW/practica/imagenes/house2_main.jpg', 'foto2.jpg', 'Casa con jardín en Barcelona', 850.00, 'Casa amplia con jardín y garaje. Ideal familias.', 'Barcelona', 1, 140.00, 4, 3, 0, 1998, '2025-11-17 12:35:00', 1002),
(2003, 1, 3, 'DAW/practica/imagenes/house3_main.jpg', 'foto3.jpg', 'Estudio céntrico en Valencia', 60000.00, 'Estudio pequeño pero céntrico, buena inversión.', 'Valencia', 1, 35.00, 0, 1, 3, 2010, '2025-11-17 12:40:00', 1003),
(2004, 1, 2, 'DAW/practica/imagenes/house4_main.jpg', 'foto4.jpg', 'Chalet con piscina en Sevilla', 250000.00, 'Chalet independiente con parcela y piscina privada.', 'Sevilla', 1, 220.00, 5, 4, 0, 2000, '2025-11-17 12:50:00', 1002),
(2005, 2, 1, 'DAW/practica/imagenes/house5_main.jpg', 'foto5.jpg', 'Piso en alquiler en Granada', 550.00, 'Piso céntrico en Granada, amueblado y con calefacción.', 'Granada', 1, 75.00, 2, 1, 1, 1995, '2025-11-17 12:55:00', 1003),
(2006, 1, 4, 'DAW/practica/imagenes/house6_main.jpg', 'foto6.jpg', 'Local comercial en Zaragoza', 95000.00, 'Local con escaparate en calle principal, apto para varios negocios.', 'Zaragoza', 1, 60.00, 0, 1, 0, 2008, '2025-11-17 13:00:00', 1001);

-- Fotos para anuncios
INSERT INTO `fotos` (`IdFoto`,`Titulo`,`Foto`,`Alternativo`,`Anuncio`) VALUES
(3001, 'Salón', 'house1_salon.jpg', 'Salón luminoso', 2001),
(3002, 'Fachada', 'house1_facade.jpg', 'Fachada del edificio', 2001),
(3003, 'Jardín', 'house2_jardin.jpg', 'Jardín trasero', 2002),
(3004, 'Cocina', 'house3_cocina.jpg', 'Cocina del estudio', 2003);

-- Mensajes de ejemplo
INSERT INTO `mensajes` (`IdMensaje`,`TMensaje`,`Texto`,`Anuncio`,`UsuOrigen`,`UsuDestino`,`FRegistro`) VALUES
(4001, 1, '¿Sigue disponible el piso? Estoy interesado en visitarlo.', 2001, 1002, 1001, '2025-11-17 12:45:00'),
(4002, 2, 'Sí, está disponible. ¿Qué día te viene bien?', 2001, 1001, 1002, '2025-11-17 12:47:00');

-- Solicitud de folleto de ejemplo
INSERT INTO `solicitudes` (`IdSolicitud`,`Anuncio`,`Texto`,`Nombre`,`Email`,`Direccion`,`Telefono`,`Color`,`Copias`,`Resolucion`,`Fecha`,`IColor`,`IPrecio`,`FRegistro`,`Coste`) VALUES
(5001, 2001, 'Solicito folleto para reparto local.', 'Silvia Pérez', 'silvia@example.com', 'C/ Mayor 1, 28001 Madrid', '600111222', '#000000', 50, 150, '2025-11-25', 1, 1, '2025-11-17 12:50:00', 120.00);

COMMIT;

-- Notes:
-- - If you want PHP-compatible password hashes (bcrypt), generate them on your machine using:
--     php -r "echo password_hash('silvia1', PASSWORD_DEFAULT) . PHP_EOL;"
--   Then update the `usuarios.Clave` value for that user with the generated string.
-- - The inserted file paths in `FPrincipal` / `Foto` are examples; ensure the files exist under the indicated folders if you plan to render images.
