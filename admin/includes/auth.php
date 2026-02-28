<?php
/**
 * Authentication Check
 * Ensures only logged-in admins can access pages
 */

session_start();

define('SESSION_TIMEOUT', 1800); // 30 minutes

function is_logged_in() {
    // Check for session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_auth() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}
