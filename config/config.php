<?php
// Database configuration
// Use environment variables if available (for Vercel/production), otherwise use Wasmer defaults
define('DB_HOST', getenv('DB_HOST') ?: 'db.fr-pari1.bengt.wasmernet.com:10272');
define('DB_NAME', getenv('DB_NAME') ?: 'db_dengue');
define('DB_USER', getenv('DB_USER') ?: 'c545581b7516800070e930ad2164');
define('DB_PASS', getenv('DB_PASS') ?: '0690c545-581b-767f-8000-8a9f555c671e');
define('DB_PORT', getenv('DB_PORT') ?: 10272);
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/disease-tracker/');
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