<?php

/**
 * Project Configuration
 */

// Environment check
$isLocal = isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'local') !== false);
define('APP_ENV', $isLocal ? 'development' : 'production');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database constants
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'tourism_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// URL and Paths
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
} else {
    define('BASE_URL', 'http://tourism.local/');
}
define('UPLOAD_DIR', '/uploads/');

