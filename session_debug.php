<?php
// Endpoint temporal para depurar sesión
// Acceso rápido: http://localhost/phpDAW/session_debug.php

define('APP_INIT', true);
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Session debug</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;padding:16px}pre{background:#f4f4f4;padding:10px;border-radius:6px}</style>
</head>
<body>
  <h1>Session debug</h1>
  <p><strong>Request URI:</strong> <?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? ''); ?></p>
  <p><strong>Session status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE'; ?></p>
  <p><strong>session_id():</strong> <?php echo session_id(); ?></p>
  <p><strong>PHPSESSID cookie (<?php echo session_name(); ?>):</strong> <?php echo isset($_COOKIE[session_name()]) ? htmlspecialchars($_COOKIE[session_name()]) : '<em>(no existe)</em>'; ?></p>
  <h2>_SESSION</h2>
  <pre><?php echo htmlspecialchars(print_r($_SESSION, true)); ?></pre>
  <h2>Cookies</h2>
  <pre><?php echo htmlspecialchars(print_r($_COOKIE, true)); ?></pre>
  <p><a href="/phpDAW/">Ir a INICIO</a> | <a href="/phpDAW/index_logueado">Ir a INICIO (logueado)</a></p>
  <p>Instrucciones: inicia sesión en otra pestaña, vuelve aquí y comprueba que <code>session_id()</code> y la cookie <code><?php echo session_name(); ?></code> se mantienen tras pulsar en "INICIO".</p>
</body>
</html>
