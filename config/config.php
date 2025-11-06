<?php
// Database configuration - InfinityFree MySQL
define('DB_HOST', getenv('DB_HOST') ?: 'sql102.infinityfree.com');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_40351049_disease_tracker');
define('DB_USER', getenv('DB_USER') ?: 'if0_40351049');
define('DB_PASS', getenv('DB_PASS') ?: 'WD3g0IV0Pji');
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('BASE_URL', getenv('BASE_URL') ?: 'https://diseasetracker.wasmer.app//');
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development'); // 'development' or 'production'

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS in production
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 1800); // 30 minutes
ini_set('session.use_strict_mode', 1);

// Error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

// Timezone
date_default_timezone_set('Asia/Manila');
?>