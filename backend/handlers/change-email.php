<?php
/**
 * SOUND Group — Change Email AJAX Endpoint
 *
 * Handles the multi-step "Change Email Address" flow:
 *   verify_password -> send_otp / resend_otp -> verify_otp (cancel)
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

        setSession('change_email_verified', true);
        echo json_encode(['success' => true]);
        exit;

    // ---------- Step 2: send 4-digit OTP to the new email ----------
    case 'send_otp':
    case 'resend_otp':
        if (!getSession('change_email_verified')) {
            echo json_encode(['success' => false, 'error' => 'Please verify your identity first.']);
            exit;
        }

        $admin = findAdminById($adminId);

        if ($action === 'send_otp') {
            $newEmail = strtolower(trim($_POST['new_email'] ?? ''));

            if (empty($newEmail)) {
                echo json_encode(['success' => false, 'error' => 'The new email address is required.']);
                exit;
            }

            if (!isValidEmail($newEmail)) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid email address.']);
                exit;
            }

            if (strcasecmp($newEmail, $admin['email']) === 0) {
                echo json_encode(['success' => false, 'error' => 'The new email address must be different from your current email.']);
                exit;
            }

            $existing = findAdminByEmail($newEmail);
            if ($existing && (int) $existing['id'] !== $adminId) {
                echo json_encode(['success' => false, 'error' => 'An account with this email address already exists.']);
                exit;
            }
        } else {
            // Resend: reuse the email stored in the session
            $newEmail = getSession('change_email_new_email');

            if (!$newEmail) {
                echo json_encode(['success' => false, 'error' => 'Please start the email change process again.']);
                exit;
            }
        }

        $otp = generateOtp(4);
        $otpId = storeEmailChangeOtp($adminId, $newEmail, $otp);

        $mailResult = sendEmailChangeOtpEmail($newEmail, $admin['name'], $otp);

        if (!$mailResult['success']) {
            deleteEmailChangeOtps($adminId);
            echo json_encode(['success' => false, 'error' => $mailResult['error']]);
            exit;
        }

        setSession('change_email_otp_id', $otpId);
        setSession('change_email_new_email', $newEmail);

        echo json_encode([
            'success'    => true,
            'expires_at' => time() + 180,
            'sent_to'    => $newEmail,
        ]);
        exit;

    // ---------- Step 3: verify the OTP and update the email ----------
    case 'verify_otp':
        if (!getSession('change_email_verified')) {
            echo json_encode(['success' => false, 'error' => 'Please verify your identity first.']);
            exit;
        }

        $otpId = getSession('change_email_otp_id');
        $otpRecord = $otpId ? getEmailChangeOtpRecord($otpId, $adminId) : null;

        if (!$otpRecord) {
            echo json_encode(['success' => false, 'error' => 'Please request a new verification code.']);
            exit;
        }

        $otpInput = trim($_POST['otp'] ?? '');

        if (empty($otpInput)) {
            echo json_encode(['success' => false, 'error' => 'The verification code is required.']);
            exit;
        }

        if (!preg_match('/^\d{4}$/', $otpInput)) {
            echo json_encode(['success' => false, 'error' => 'The verification code must be exactly 4 digits.']);
            exit;
        }

        if (isEmailChangeOtpExpired($otpRecord)) {
            echo json_encode(['success' => false, 'error' => 'The verification code has expired. Please request a new one.']);
            exit;
        }

        if (isEmailChangeOtpVerified($otpRecord)) {
            echo json_encode(['success' => false, 'error' => 'This verification code has already been used.']);
            exit;
        }

        if (!verifyEmailChangeOtp($otpRecord, $otpInput)) {
            echo json_encode(['success' => false, 'error' => 'The verification code is invalid. Please use the code from your most recent email, or request a new one.']);
            exit;
        }

        // Success: mark verified, update the email (server-side value only),
        // clean up, invalidate the session, and send the admin back to login.
        $newEmail = $otpRecord['new_email'];

        markEmailChangeOtpVerified($otpRecord['id']);
        updateAdminEmail($adminId, $newEmail);
        deleteEmailChangeOtps($adminId);

        removeSession('change_email_verified');
        removeSession('change_email_otp_id');
        removeSession('change_email_new_email');
        destroySession();

        echo json_encode([
            'success'  => true,
            'redirect' => baseUrl() . '/frontend/admin/authentication/login.php?credential=email_changed',
        ]);
        exit;

    // ---------- Cancel: clean up any pending OTP and session keys ----------
    case 'cancel':
        deleteEmailChangeOtps($adminId);
        removeSession('change_email_verified');
        removeSession('change_email_otp_id');
        removeSession('change_email_new_email');
        echo json_encode(['success' => true]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
