<?php
// Database configuration for Neon (PostgreSQL)
// Get your connection string from: https://console.neon.tech/

// Example Neon connection string format:
// postgresql://username:password@host/database?sslmode=require

// Parse your Neon connection string or set values directly:
define('DB_HOST', 'your-neon-host.neon.tech');  // e.g., ep-cool-darkness-123456.us-east-2.aws.neon.tech
define('DB_NAME', 'neondb');                     // Usually 'neondb' by default
define('DB_USER', 'your-username');              // Your Neon username
define('DB_PASS', 'your-password');              // Your Neon password
define('DB_PORT', 5432);                         // PostgreSQL default port

// Application configuration
define('BASE_URL', 'https://your-domain.com/');  // Update with your domain
define('ENVIRONMENT', 'production');              // 'development' or 'production'

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);              // Set to 1 for HTTPS (required in production)
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 1800);          // 30 minutes
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
