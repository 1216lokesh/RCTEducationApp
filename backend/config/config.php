<?php
/**
 * Configuration File
 * Database and Application Settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rct_app');
define('DB_PORT', 3306);

// Application Settings
define('APP_NAME', 'RCT Education Portal');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('APP_URL', $protocol . $host . '/rct-education-web/frontend');
define('APP_PATH', dirname(dirname(dirname(__FILE__))));

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('REMEMBER_ME_DAYS', 7);

// Upload Settings
define('UPLOAD_PATH', APP_PATH . '/backend/uploads');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting
if (isset($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Default Language
define('DEFAULT_LANGUAGE', 'en');
define('SUPPORTED_LANGUAGES', ['en', 'ta', 'hi', 'te']);

// Directories
define('CLASSES_PATH', APP_PATH . '/backend/classes');
define('VIEWS_PATH', APP_PATH . '/frontend/views');
define('CONTROLLERS_PATH', APP_PATH . '/backend/controllers');
define('LANGUAGES_PATH', APP_PATH . '/backend/languages');
define('ASSETS_PATH', APP_PATH . '/frontend/assets');

// Encryption Key (Change this for production)
define('ENCRYPTION_KEY', 'your-secret-key-here-change-in-production');

?>
