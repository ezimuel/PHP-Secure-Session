<?php
/**
 * Demo script for PHP-Secure-Session
 *
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @copyright MIT License
 */
$start = microtime(true);

ini_set('session.save_handler', 'files');

$autoload = __DIR__ . '/../../vendor/autoload.php';
if (! file_exists($autoload)) {
  echo "You need to execute <strong>composer install</strong>!";
  exit;
}
require_once $autoload;

// change the default session folder in a temporary dir
session_save_path(sys_get_temp_dir());
session_start();

if (empty($_SESSION['time'])) {
  $_SESSION['time'] = time(); // set the time
}
session_write_close();

$filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sess_' . session_id();

$time = microtime(true) - $start;

echo "<h1>PHP-Secure-Session Demo</h1>";
echo "<p>Session created at <strong>" . date("G:i:s ", $_SESSION['time']) . "</strong></p>";
echo "<p>Session file: <strong>" . $filename . "</strong></p>";
echo "<p>Content:<br><pre>" . session_encode() . "</pre></p>";
echo "<p>Encrypted content in Base64:<br><pre>" . base64_encode(file_get_contents($filename)). "</pre></p>";
echo "<p><strong>Note:</strong> If you reload the page you will see the encrypted data changing</p>";

printf("Execution time: %.6f", $time * 1000);
