<?php
/**
 * Configuration File
 * Database and Application Settings
 */

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: (getenv('MYSQL_ADDON_HOST') ?: 'localhost'));
define('DB_USER', getenv('DB_USER') ?: (getenv('MYSQL_ADDON_USER') ?: 'root'));
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQL_ADDON_PASSWORD') !== false ? getenv('MYSQL_ADDON_PASSWORD') : ''));
define('DB_NAME', getenv('DB_NAME') ?: (getenv('MYSQL_ADDON_DB') ?: 'rct_app'));
define('DB_PORT', getenv('DB_PORT') ?: (getenv('MYSQL_ADDON_PORT') ?: 3306));

// Application Settings
define('APP_NAME', 'RCT Education Portal');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$isBackup = (strpos($requestUri, 'frontend_php_backup') !== false || strpos($scriptName, 'frontend_php_backup') !== false);
$subfolder = $isBackup ? 'frontend_php_backup' : 'frontend';
define('APP_URL', $protocol . $host . '/rct-education-web/' . $subfolder);
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
