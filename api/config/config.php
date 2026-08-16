<?php
/**
 * ACMS Global Configuration File
 * Contains database credentials and system-wide settings.
 */

// Global Constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'acms');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_URL', 'http://localhost/acms');
define('API_URL', 'http://localhost/acms/api');

// Timezone Setup
date_default_timezone_set('Europe/Istanbul');

// Error Reporting (Set to 0 in Production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable CORS for Webview Apps (Multi-domain support) - Only for API requests
$isApiRequest = (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) || 
                (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false);

if ($isApiRequest) {
    header('Access-Control-Allow-Origin: *'); // In production, restrict to allowed app domains
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Content-Type: application/json; charset=utf-8');

    // Handle preflight OPTIONS request
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}
