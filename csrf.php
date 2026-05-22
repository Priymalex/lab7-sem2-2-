<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Очистка старых токенов (каждые 24 часа)
if (!isset($_SESSION['csrf_time']) || $_SESSION['csrf_time'] < time() - 86400) {
    $_SESSION['csrf_time'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>