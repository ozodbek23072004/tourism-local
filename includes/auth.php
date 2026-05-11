<?php
// includes/auth.php

// Ensure session is started for all auth operations
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Attempt to log in the user
 * 
 * @param string $username
 * @param string $password
 * @param PDO $pdo
 * @return bool
 */
function login(string $username, string $password, PDO $pdo): bool {
    $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        return true;
    }

    return false;
}

/**
 * Log out the user and redirect to the login page
 * 
 * @return void
 */
function logout(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    // Redirect to login
    $loginUrl = defined('BASE_URL') ? BASE_URL . 'admin/login.php' : '/admin/login.php';
    header("Location: " . $loginUrl);
    exit;
}

/**
 * Require authentication to view a page
 * 
 * @return void
 */
function requireAuth(): void {
    if (!isset($_SESSION['admin_id'])) {
        $loginUrl = defined('BASE_URL') ? BASE_URL . 'admin/login.php' : '/admin/login.php';
        header("Location: " . $loginUrl);
        exit;
    }
}
