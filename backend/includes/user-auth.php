<?php
/**
 * SOUND Group — User Authentication Helper
 *
 * Provides functions for website user authentication and status checks.
 * Used by user-facing protected pages (future: user dashboard, profile, etc.)
 */

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/db.php';

/**
 * Check if a website user is logged in.
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the logged-in user's database ID.
 */
function getCurrentUserId() {
    return isset($_SESSION['user_db_id']) ? (int) $_SESSION['user_db_id'] : 0;
}

/**
 * Get the logged-in user's public user_id (e.g., U0001).
 */
function getCurrentUserPublicId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get the logged-in user's full name.
 */
function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
}

/**
 * Get the logged-in user's email.
 */
function getCurrentUserEmail() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
}

/**
 * Get the logged-in user's profile image path.
 */
function getCurrentUserProfileImage() {
    return isset($_SESSION['user_profile_image']) ? $_SESSION['user_profile_image'] : null;
}

/**
 * Check if the current user's account is active.
 * Verifies against the database to catch admin status changes.
 */
function isCurrentUserActive() {
    if (!isUserLoggedIn()) return false;

    $db = getDb();
    $stmt = $db->prepare("SELECT `status` FROM `users` WHERE `id` = :id");
    $stmt->execute([':id' => getCurrentUserId()]);
    $row = $stmt->fetch();

    return $row && $row['status'] === 'active';
}

/**
 * Login a website user — sets session variables.
 */
function loginUser($user) {
    $_SESSION['user_id']           = $user['user_id'];
    $_SESSION['user_db_id']        = $user['id'];
    $_SESSION['user_name']         = $user['full_name'];
    $_SESSION['user_email']        = $user['email'];
    $_SESSION['user_profile_image'] = $user['profile_image'];

    // Update last_login
    $db = getDb();
    $stmt = $db->prepare("UPDATE `users` SET `last_login` = NOW() WHERE `id` = :id");
    $stmt->execute([':id' => $user['id']]);
}

/**
 * Logout a website user — destroys session and updates last_logout.
 */
function logoutUser() {
    if (isUserLoggedIn()) {
        $db = getDb();
        $stmt = $db->prepare("UPDATE `users` SET `last_logout` = NOW() WHERE `id` = :id");
        $stmt->execute([':id' => getCurrentUserId()]);
    }

    // Clear user session keys
    unset($_SESSION['user_id'], $_SESSION['user_db_id'], $_SESSION['user_name'],
          $_SESSION['user_email'], $_SESSION['user_profile_image']);
}

/**
 * Require an authenticated AND active user.
 * Redirects to homepage if not logged in, or shows inactive message if account is inactive.
 */
function requireActiveUser() {
    if (!isUserLoggedIn()) {
        header('Location: ' . baseUrl() . '/frontend/website/index.php');
        exit;
    }

    if (!isCurrentUserActive()) {
        // Account is inactive — destroy user session and redirect with message
        logoutUser();
        header('Location: ' . baseUrl() . '/frontend/website/index.php?account=inactive');
        exit;
    }
}

/**
 * Require guest — redirect logged-in users to homepage.
 */
function requireUserGuest() {
    if (isUserLoggedIn()) {
        header('Location: ' . baseUrl() . '/frontend/website/index.php');
        exit;
    }
}
