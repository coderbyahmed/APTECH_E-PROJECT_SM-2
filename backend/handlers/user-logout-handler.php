<?php
/**
 * SOUND Group — User Logout Handler (Public)
 *
 * Handles user logout from the website.
 * Updates last_logout timestamp and destroys user session data.
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/user-auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!isUserLoggedIn()) {
    echo json_encode(['success' => true, 'message' => 'Already logged out.']);
    exit;
}

// Updates last_logout + clears user session keys
logoutUser();

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully.',
]);
exit;
