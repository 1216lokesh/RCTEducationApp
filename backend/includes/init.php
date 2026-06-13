<?php
/**
 * Application Initialization File
 * Load all necessary classes and configuration
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load classes
require_once CLASSES_PATH . '/Database.php';
require_once CLASSES_PATH . '/Language.php';
require_once CLASSES_PATH . '/Auth.php';

// Initialize Database
$db = new Database();

// Initialize Auth
Auth::startSession();
$auth = new Auth($db);

// Initialize Language
Language::getUserLanguage();

// Helper function for getting translated strings
function __($key, $default = '') {
    return Language::get($key, $default);
}

// Error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log error if needed
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
});

// CORS headers for API
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

?>
