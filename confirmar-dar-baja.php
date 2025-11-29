<?php
// Procesador de baja: recibe POST(password), valida y borra datos + ficheros
session_start();
require_once __DIR__ . '/includes/conexion.php';

// Preparar log
if (!is_dir(__DIR__ . '/logs')) @mkdir(__DIR__ . '/logs', 0755, true);
$logFile = __DIR__ . '/logs/dar_baja.log';

if (!isset($_SESSION['usuario'])) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "baja: usuario no autenticado\n", FILE_APPEND);
    $_SESSION['flash']['acceso_error'] = 'Debes iniciar sesión para acceder a esa página.';
    header('Location: index.php');
    exit;
}

$nomUsuario = $_SESSION['usuario'];
$passwordInput = $_POST['password'] ?? '';

if ($passwordInput === '') {
    $_SESSION['flash']['error'] = 'Debes introducir tu contraseña para confirmar.';
    header('Location: dar-baja.php');
    exit;
}

try {
    // Obtener IdUsuario y hash de la BD
    $stmt = $conexion->prepare('SELECT IdUsuario, Clave FROM Usuarios WHERE NomUsuario = ? LIMIT 1');
    $stmt->execute([$nomUsuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !isset($user['Clave']) || !password_verify($passwordInput, $user['Clave'])) {
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "baja: contraseña incorrecta para usuario={$nomUsuario}\n", FILE_APPEND);
        $_SESSION['flash']['error'] = 'Contraseña incorrecta.';
        header('Location: dar-baja.php');
        exit;
    }

    $idUsuario = (int)$user['IdUsuario'];

    // Iniciar transacción
    $conexion->beginTransaction();

    // Obtener anuncios del usuario
    $stmt = $conexion->prepare('SELECT IdAnuncio FROM Anuncios WHERE Usuario = ?');
    $stmt->execute([$idUsuario]);
    $anuncios = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Para cada anuncio, borrar ficheros asociados
    if (!empty($anuncios)) {
        $in = implode(',', array_fill(0, count($anuncios), '?'));
        $stmtFotos = $conexion->prepare("SELECT Foto FROM Fotos WHERE Anuncio IN ($in)");
        $stmtFotos->execute($anuncios);
        $fotos = $stmtFotos->fetchAll(PDO::FETCH_COLUMN, 0);

        foreach ($fotos as $foto) {
            // No eliminamos ficheros físicos en esta práctica; quedan "basura" en el sistema
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "baja: fichero no eliminado (práctica): $foto\n", FILE_APPEND);
        }

        // Borrar filas de Fotos para esos anuncios (solo BD)
        $stmtDelFotos = $conexion->prepare("DELETE FROM Fotos WHERE Anuncio IN ($in)");
        $stmtDelFotos->execute($anuncios);
    }

    // Borrar anuncios del usuario
    $stmtDelAn = $conexion->prepare('DELETE FROM Anuncios WHERE Usuario = ?');
    $stmtDelAn->execute([$idUsuario]);

    // Borrar mensajes relacionados (origen o destino)
    $stmtDelMens = $conexion->prepare('DELETE FROM Mensajes WHERE UsuOrigen = ? OR UsuDestino = ?');
    $stmtDelMens->execute([$idUsuario, $idUsuario]);

    // Borrar usuario
    $stmtDelUser = $conexion->prepare('DELETE FROM Usuarios WHERE IdUsuario = ?');
    $stmtDelUser->execute([$idUsuario]);

    $conexion->commit();

    // Destruir sesión y redirigir a página de cierre
    session_unset();
    session_destroy();
    header('Location: cerrar.php');
    exit;

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) $conexion->rollBack();
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "baja: excepción: " . $e->getMessage() . "\n", FILE_APPEND);
    $_SESSION['flash']['error'] = 'Error eliminando la cuenta. Inténtalo de nuevo.';
    header('Location: dar-baja.php');
    exit;
}
?>
