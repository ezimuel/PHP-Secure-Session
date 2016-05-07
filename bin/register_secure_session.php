<?php
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

session_set_save_handler(new PHPSecureSession\SecureHandler(), true);
