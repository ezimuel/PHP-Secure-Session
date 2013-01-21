<?php
/**
 * Demo script for SecureSession
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @copyright GNU General Public License 
 */
require_once( '../SecureSession.php' );

// change the default session folder in a temporary dir
$sessionPath = '/tmp';
session_save_path( $sessionPath );
session_start();

if( empty( $_SESSION['time'] ) )
    $_SESSION['time'] = time();

$filename = $sessionPath . '/' . session_name() . '_' . session_id();

echo '<h1>SecureSession Demo</h1>
<br>Session created at <strong>' . date( 'G:i:s', $_SESSION['time'] ) . '</strong>
<br>Session file: <strong>' . $filename . '</strong>
<br><br>Encrypted content:<br><pre>' . file_get_contents( $filename ). '</pre>
<br><strong>Note:</strong> If you reload the page you see the encrypted data change each time';
