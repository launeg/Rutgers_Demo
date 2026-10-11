<?php
// Legacy bootstrap for the model layer. Credentials come from config/config.php.
require_once __DIR__ . '/../../config/config.php';

$dbHost = env('DB_HOST');
$dbUser = env('DB_USER');
$dbPass = env('DB_PASS', '');
define('dbHost', $dbHost);
define('dbUser', $dbUser);
define('dbPass', $dbPass);
