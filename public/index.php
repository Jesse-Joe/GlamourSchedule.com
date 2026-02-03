<?php
/**
 * GlamourSchedule - Entry Point
 * 
 * @package GlamourSchedule
 * @version 1.0.0
 */

define('GLAMOUR_LOADED', true);
define('GLAMOUR_START', microtime(true));

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(self), microphone=(), camera=()');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Base path
define('BASE_PATH', dirname(__DIR__));

// Autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// Load configuration
$config = require BASE_PATH . '/config/config.php';

// Configure session for 24 hours (security best practice)
ini_set('session.gc_maxlifetime', 86400); // 24 uur
ini_set('session.cookie_lifetime', 86400); // 24 uur
session_set_cookie_params([
    'lifetime' => 86400, // 24 uur
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session
session_start();

// Initialize application with output buffering
ob_start();
$app = new GlamourSchedule\Core\Application($config);
$app->run();
$output = ob_get_clean();

// Ensure proper status code is sent via header
$code = http_response_code();
if ($code === false || $code === 200) {
    header('HTTP/1.1 200 OK', true, 200);
} elseif ($code === 404) {
    header('HTTP/1.1 404 Not Found', true, 404);
} elseif ($code === 500) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
}

echo $output;
