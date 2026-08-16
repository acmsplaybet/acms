<?php
/**
 * ACMS Global Configuration File
 * Smart Dynamic Environment Detection (Local XAMPP vs Live Server)
 */

// 1. Environment Detection (Localhost vs Production)
$isLocal = false;

if (php_sapi_name() === 'cli') {
    // If running via local CLI (Windows development machine)
    $isLocal = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && getenv('APP_ENV') !== 'production');
} else {
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $httpHost   = $_SERVER['HTTP_HOST'] ?? '';
    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';

    if (
        in_array($httpHost, ['localhost', '127.0.0.1', '::1']) ||
        strpos($httpHost, 'localhost:') === 0 ||
        strpos($httpHost, '127.0.0.1:') === 0 ||
        in_array($serverName, ['localhost', '127.0.0.1', '::1']) ||
        $serverAddr === '127.0.0.1' ||
        $serverAddr === '::1'
    ) {
        $isLocal = true;
    }
}

if ($isLocal) {
    // ----------------------------------------------------
    // LOCALHOST (XAMPP) ENVIRONMENT
    // ----------------------------------------------------
    define('ENV_TYPE', 'development');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'acms');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    define('SITE_URL', 'http://localhost/acms');
    define('API_URL', 'http://localhost/acms/api');

    // Debugging enabled in local
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // ----------------------------------------------------
    // PRODUCTION / LIVE SERVER ENVIRONMENT (51.195.31.193 / 185.179.27.23)
    // ----------------------------------------------------
    define('ENV_TYPE', 'production');
    define('DB_HOST', 'localhost'); // Internal socket/localhost on web server
    define('DB_HOST_FALLBACK', '185.179.27.23');
    define('DB_NAME', 'playbet_acms');
    define('DB_USER', 'playbet_acms');
    define('DB_PASS', '-951-QwerOP01-*');

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? '51.195.31.193';
    define('SITE_URL', $protocol . $host . '/acms');
    define('API_URL', $protocol . $host . '/acms/api');

    // Secure error handling in production
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Timezone Setup
date_default_timezone_set('Europe/Istanbul');

// Enable CORS for Webview Apps (Multi-domain & Native Mobile Support)
$isApiRequest = (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) || 
                (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false);

if ($isApiRequest) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Content-Type: application/json; charset=utf-8');

    // Handle preflight OPTIONS request
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}
