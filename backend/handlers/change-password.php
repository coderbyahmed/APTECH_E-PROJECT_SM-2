<?php
/**
 * SOUND Group — Change Password AJAX Endpoint
 *
 * Handles the multi-step "Change Password" flow:
 *   verify_password -> update_password
 *
 * Always JSON responses. Admin identity comes from the session only.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success'  => false,
        'error'    => 'Your session has expired. Please sign in again.',
        'redirect' => baseUrl() . '/frontend/admin/authentication/login.php',
    ]);
    exit;
}

$adminId = (int) $_SESSION['admin_id'];
$action  = trim($_POST['action'] ?? '');

// CSRF protection
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page and try again.']);
    exit;
}

switch ($action) {

    // ---------- Step 1: verify identity with current password ----------
    case 'verify_password':
        $password = $_POST['current_password'] ?? '';

        if (empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Please enter your current password.']);
            exit;
        }

        if (!verifyAdminPassword($adminId, $password)) {
            echo json_encode(['success' => false, 'error' => 'The current password is incorrect.']);
            exit;
        }

        setSession('change_password_verified', true);
        echo json_encode(['success' => true]);
        exit;

    // ---------- Step 2: validate and update the password ----------
    case 'update_password':
        if (!getSession('change_password_verified')) {
            echo json_encode(['success' => false, 'error' => 'Please verify your identity first.']);
            exit;
        }

        $admin = findAdminById($adminId);

        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if (empty($password)) {
            echo json_encode(['success' => false, 'error' => 'The new password field is required.']);
            exit;
        }

        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'error' => 'The new password must be at least 8 characters.']);
            exit;
        }

        if ($password !== $passwordConfirmation) {
            echo json_encode(['success' => false, 'error' => 'The password confirmation does not match.']);
            exit;
        }

        if ($admin && password_verify($password, $admin['password'])) {
            echo json_encode(['success' => false, 'error' => 'The new password must be different from your current password.']);
            exit;
        }

        // Success: update the password, clean up pending OTP records,
        // invalidate the session, and send the admin back to login.
        updateAdminPassword($adminId, $password);
        deleteOtpRecords($adminId);

        removeSession('change_password_verified');
        destroySession();

        echo json_encode([
            'success'  => true,
            'redirect' => baseUrl() . '/frontend/admin/authentication/login.php?credential=password_changed',
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
