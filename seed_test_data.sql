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
(1002, 'clara', '$2y$10$mqGHj/Hj9Ai78.sK2M62iexZerELOsszQVEID3UTLuhjFU4.jT1Dm', 'clara@example.com', 1, '1985-03-20', 'Barcelona', 1, NULL, '2025-11-17 12:05:00', 2),
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

C-- Ensure 'clara' has Estilo = 2 even if the seed was previously imported
UPDATE `usuarios` SET `Estilo` = 2 WHERE `NomUsuario` = 'clara';

COMMIT;

-- Notes:
-- - If you want PHP-compatible password hashes (bcrypt), generate them on your machine using:
--     php -r "echo password_hash('silvia1', PASSWORD_DEFAULT) . PHP_EOL;"
--   Then update the `usuarios.Clave` value for that user with the generated string.
-- - The inserted file paths in `FPrincipal` / `Foto` are examples; ensure the files exist under the indicated folders if you plan to render images.
