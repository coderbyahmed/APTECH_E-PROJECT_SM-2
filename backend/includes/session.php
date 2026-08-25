<?php
/**
 * SOUND Group — Session Management
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7200,
        'path'     => '/',
        'httponly'  => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// No-cache headers
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

/**
 * Check if admin is authenticated
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require admin authentication — redirect to login if not authenticated
 */
function requireAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: /Aptech_E_Project_02/sound_management/frontend/admin/authentication/login.php');
        exit;
    }
}

/**
 * Require guest — redirect to dashboard if already authenticated
 */
function requireGuest() {
    if (isAdminLoggedIn()) {
        header('Location: /Aptech_E_Project_02/sound_management/frontend/admin/dashboard/index.php');
        exit;
    }
}

/**
 * Set a flash message
 */
function setFlash($key, $value) {
    $_SESSION['flash'][$key] = $value;
}

/**
 * Get and clear a flash message
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    return null;
}

/**
 * Set session value
 */
function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Get session value
 */
function getSession($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

/**
 * Remove session value
 */
function removeSession($key) {
    unset($_SESSION[$key]);
}

/**
 * Destroy admin session
 */
function destroySession() {
    session_unset();
    session_destroy();
}

/**
 * Get the base URL path
 */
function baseUrl() {
    return '/Aptech_E_Project_02/sound_management';
}

/**
 * Redirect to a path
 */
function redirect($path) {
    header('Location: ' . baseUrl() . $path);
    exit;
}

/**
 * Get or generate the CSRF token for the current session
 */
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a submitted CSRF token against the session token
 */
function verifyCsrfToken($token) {
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
