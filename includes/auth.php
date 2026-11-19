<?php
/**
 * Authentication & Security Helper
 */

// Enable output buffering globally to prevent "headers already sent"
if (ob_get_level() === 0) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    // Secure cookie settings
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 86400 * 7, // 7 days
        'path' => '/',
        'domain' => $cookieParams['domain'],
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function is_logged_in() {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function get_login_attempts_file() {
    $dir = defined('DATA_PATH') ? DATA_PATH : (__DIR__ . '/../data');
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir . '/.login_attempts.json';
}

function check_login_rate_limit() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $attemptsFile = get_login_attempts_file();
    if (!file_exists($attemptsFile)) return ['allowed' => true];

    $data = json_decode(@file_get_contents($attemptsFile), true) ?: [];
    $ipData = $data[$ip] ?? null;
    if (!$ipData) return ['allowed' => true];

    $now = time();
    $lockoutTime = 600; // 10 menit
    $maxAttempts = 5;

    if (($ipData['attempts'] ?? 0) >= $maxAttempts) {
        $elapsed = $now - ($ipData['last_attempt'] ?? 0);
        if ($elapsed < $lockoutTime) {
            $remaining = ceil(($lockoutTime - $elapsed) / 60);
            return [
                'allowed' => false, 
                'error' => "Terlalu banyak percobaan login gagal. Demi keamanan, akun terkunci selama {$remaining} menit lagi."
            ];
        } else {
            // Reset setelah waktu blokir berakhir
            unset($data[$ip]);
            @file_put_contents($attemptsFile, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        }
    }

    return ['allowed' => true];
}

function record_failed_login() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $attemptsFile = get_login_attempts_file();
    $data = file_exists($attemptsFile) ? (json_decode(@file_get_contents($attemptsFile), true) ?: []) : [];

    $now = time();
    if (!isset($data[$ip]) || ($now - ($data[$ip]['last_attempt'] ?? 0) > 600)) {
        $data[$ip] = ['attempts' => 1, 'last_attempt' => $now];
    } else {
        $data[$ip]['attempts'] = ($data[$ip]['attempts'] ?? 0) + 1;
        $data[$ip]['last_attempt'] = $now;
    }

    @file_put_contents($attemptsFile, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

function clear_failed_logins() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $attemptsFile = get_login_attempts_file();
    if (file_exists($attemptsFile)) {
        $data = json_decode(@file_get_contents($attemptsFile), true) ?: [];
        if (isset($data[$ip])) {
            unset($data[$ip]);
            @file_put_contents($attemptsFile, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        }
    }
}

function login_user($username, $password) {
    $limit = check_login_rate_limit();
    if (!$limit['allowed']) {
        return ['success' => false, 'error' => $limit['error']];
    }

    require_once __DIR__ . '/db.php';
    $admin = get_admin();
    if (empty($admin)) {
        return ['success' => false, 'error' => 'Data administrator tidak ditemukan.'];
    }

    if ($admin['username'] === $username && password_verify($password, $admin['password_hash'])) {
        clear_failed_logins();
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['name'] ?? 'Administrator';
        $_SESSION['last_activity'] = time();

        // Update last login
        $admin['last_login'] = date('Y-m-d H:i:s');
        save_admin($admin);
        return ['success' => true];
    } else {
        record_failed_login();
        return ['success' => false, 'error' => 'Username atau password yang dimasukkan salah.'];
    }
}

function logout_user() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
