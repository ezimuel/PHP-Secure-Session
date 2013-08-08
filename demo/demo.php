<?php
/**
 * Demo script for SecureSession
 * 
 * Requirement: PHP 5.2.1+
 *
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @copyright GNU General Public License 
 */
require_once '../SecureSession.php';

// change the default session folder in a temporary dir
$sessionPath = sys_get_temp_dir();
session_save_path($sessionPath);
session_start();

if (empty($_SESSION['time'])) {
    $_SESSION['time'] = time();
}    

$filename = $sessionPath . '/' . session_name() . '_' . session_id();

echo "<h1>SecureSession Demo</h1>";
echo "<br>Session created at <strong>" . date("G:i:s ", $_SESSION['time']) . "</strong>";
echo "<br>Session file: <strong>" . $filename . "</strong>";
echo "<br><br>Encrypted content:<br><pre>" . file_get_contents($filename). "</pre>";
echo "<br><strong>Note:</strong> If you reload the page you see the encrypted data change each time";
