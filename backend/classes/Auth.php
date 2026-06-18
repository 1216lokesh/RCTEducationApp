<?php
/**
 * User Authentication Class
 */

class Auth {
    private $db;
    private $user = null;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->checkSession();
    }

    // Start session
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session cookie parameters for cross-domain tracking
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '', // blank defaults to host domain
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ]);
            session_start();
        }
    }

    // Login user
    public function login($email, $password, $rememberMe = false) {
        $email = $this->db->escape($email);
        $query = "SELECT * FROM users WHERE email = '{$email}' AND status = 'active'";
        $user = $this->db->fetchOne($query);

        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'] ?? 'User';
            $_SESSION['user_language'] = $user['language'] ?? DEFAULT_LANGUAGE;

            // Update last login
            $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], "id = {$user['id']}");

            if ($rememberMe) {
                $this->setRememberMeCookie($user['id']);
            }

            $this->user = $user;
            return true;
        }

        return false;
    }

    // Register new user
    public function register($data) {
        // Check if email exists
        if ($this->db->recordExists('users', 'email', $data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Prepare data
        $registerData = [
            'role' => $data['role'] ?? 'patient',
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'name' => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            'phone' => $data['phone'] ?? null,
            'language' => $data['language'] ?? DEFAULT_LANGUAGE,
            'status' => 'active'
        ];

        $result = $this->db->insert('users', $registerData);
        return $result;
    }

    // Check session
    public function checkSession() {
        if (!isset($_SESSION['user_id'])) {
            $this->checkRememberMeCookie();
        }
    }

    // Set remember me cookie
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (REMEMBER_ME_DAYS * 24 * 60 * 60);

        $options = [
            'expires' => $expiry,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ];

        setcookie('remember_token', $token, $options);
        setcookie('remember_user', $userId, $options);
    }

    // Check remember me cookie
    private function checkRememberMeCookie() {
        if (isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
            $userId = intval($_COOKIE['remember_user']);
            $user = $this->db->fetchOne("SELECT * FROM users WHERE id = {$userId} AND status = 'active'");

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'] ?? 'User';
                $_SESSION['user_language'] = $user['language'] ?? DEFAULT_LANGUAGE;
                $this->user = $user;
            }
        }
    }

    // Logout user
    public function logout() {
        session_destroy();
        
        $options = [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ];

        setcookie('remember_token', '', $options);
        setcookie('remember_user', '', $options);
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $this->db->fetchOne("SELECT * FROM users WHERE id = {$_SESSION['user_id']}");
        }
        return null;
    }

    // Check user role
    public function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    // Require login
    public static function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/auth/login.php');
            exit;
        }
    }

    // Require role
    public static function requireRole($role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== $role) {
            header('HTTP/1.0 403 Forbidden');
            exit('Access Denied');
        }
    }
}
?>
