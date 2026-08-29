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
        header('Location: ' . baseUrl() . '/frontend/admin/authentication/login.php');
        exit;
    }
}

/**
 * Require guest — redirect to dashboard if already authenticated
 */
function requireGuest() {
    if (isAdminLoggedIn()) {
        header('Location: ' . baseUrl() . '/frontend/admin/dashboard/index.php');
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
 * Reads from APP_URL in .env, falls back to auto-detection.
 */
function baseUrl() {
    static $baseUrl = null;
    if ($baseUrl !== null) {
        return $baseUrl;
    }

    // Try to get from .env APP_URL first
    require_once __DIR__ . '/../helpers/env.php';
    $appUrl = env('APP_URL', '');

    if ($appUrl) {
        // Extract just the path portion from APP_URL (e.g., "http://localhost/project" -> "/project")
        $parsed = parse_url($appUrl);
        if ($parsed && isset($parsed['path'])) {
            $baseUrl = rtrim($parsed['path'], '/');
        } else {
            $baseUrl = '';
        }
    } else {
        // Auto-detect: use the directory depth from document root
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $baseUrl = '';
    }

    return $baseUrl;
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
