<?php
/**
 * Language Helper Class
 */

class Language {
    private static $language = DEFAULT_LANGUAGE;
    private static $strings = [];

    public static function init($lang = DEFAULT_LANGUAGE) {
        if (in_array($lang, SUPPORTED_LANGUAGES)) {
            self::$language = $lang;
        } else {
            self::$language = DEFAULT_LANGUAGE;
        }

        // Load language file
        $langFile = LANGUAGES_PATH . '/' . self::$language . '.php';
        if (file_exists($langFile)) {
            self::$strings = require $langFile;
        }
    }

    public static function get($key, $default = '') {
        if (isset(self::$strings[$key])) {
            return self::$strings[$key];
        }
        return $default;
    }

    public static function set($language) {
        self::init($language);
    }

    public static function current() {
        return self::$language;
    }

    public static function all() {
        return self::$strings;
    }

    // Get user's language
    public static function getUserLanguage() {
        if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
            self::init($_GET['lang']);
            $_SESSION['user_language'] = $_GET['lang'];
            if (isset($_SESSION['user_id'])) {
                global $db;
                if ($db) {
                    $db->update('users', ['language' => $_GET['lang']], "id = {$_SESSION['user_id']}");
                }
            }
        } elseif (isset($_SESSION['user_language'])) {
            self::init($_SESSION['user_language']);
        } else {
            self::init(DEFAULT_LANGUAGE);
        }
    }
}

// Load language on initialization
Language::getUserLanguage();

?>
