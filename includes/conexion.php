<?php
require_once("conect.inc");
try {
    $dsn = "mysql:host=$servidor;dbname=$basedatos;charset=utf8mb4";
    $conexion = new PDO($dsn, $usuario, $clave);

    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Opcional: que los resultados sean devueltos como array asociativo
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si falla la conexión, mostrar mensaje de error y detener ejecución
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
