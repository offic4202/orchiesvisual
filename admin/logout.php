<?php
/**
 * Logout Handler
 * Destroys session and redirects to login
 */

session_start();

// Destroy session
$_SESSION = array();
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

redirect('login.php');
