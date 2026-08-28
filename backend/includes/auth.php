<?php
/**
 * SOUND Group — Authentication Helpers
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

/**
 * Find admin by email
 */
function findAdminByEmail($email) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}

/**
 * Find admin by ID
 */
function findAdminById($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/**
 * Login admin — set session
 */
function loginAdmin($admin) {
    setSession('admin_id', $admin['id']);
    setSession('admin_name', $admin['name']);
    setSession('admin_email', $admin['email']);
    setSession('admin_profile_image', $admin['profile_image'] ?? null);
    setSession('admin_address', $admin['address'] ?? null);
}

/**
 * Get full admin profile from database
 */
function getAdminProfile($adminId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT id, name, email, profile_image, address, created_at, updated_at FROM admin WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $adminId]);
    return $stmt->fetch();
}

/**
 * Update admin profile (name, address, profile_image)
 */
function updateAdminProfile($adminId, $name, $address, $profileImage = null) {
    $pdo = getDb();
    if ($profileImage !== null) {
        $stmt = $pdo->prepare("UPDATE admin SET name = :name, address = :address, profile_image = :profile_image, updated_at = NOW() WHERE id = :id");
        $stmt->execute(['name' => $name, 'address' => $address, 'profile_image' => $profileImage, 'id' => $adminId]);
    } else {
        $stmt = $pdo->prepare("UPDATE admin SET name = :name, address = :address, updated_at = NOW() WHERE id = :id");
        $stmt->execute(['name' => $name, 'address' => $address, 'id' => $adminId]);
    }
}

/**
 * Logout admin — destroy session
 */
function logoutAdmin() {
    destroySession();
}

/**
 * Clear all OTP-related session keys
 */
function clearOtpSession() {
    $keys = ['otp_request_id', 'otp_email', 'otp_verified', 'otp_verified_success', 'otp_sent', 'otp_resent', 'otp_send_error'];
    foreach ($keys as $key) {
        removeSession($key);
    }
    if (isset($_SESSION['flash'])) {
        foreach (['otp_sent', 'otp_verified_success', 'otp_resent'] as $fk) {
            unset($_SESSION['flash'][$fk]);
        }
    }
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate a numeric OTP of the given length
 */
function generateOtp($length = 6) {
    $min = (int) str_pad('1', $length, '0', STR_PAD_RIGHT);
    $max = (int) str_pad('', $length, '9', STR_PAD_RIGHT);
    return str_pad((string) random_int($min, $max), $length, '0', STR_PAD_LEFT);
}

/**
 * Store OTP in database
 */
function storeOtp($adminId, $email, $otp) {
    $pdo = getDb();
    // Delete old unverified OTPs for this admin
    $stmt = $pdo->prepare("DELETE FROM password_reset_otps WHERE admin_id = :admin_id AND verified_at IS NULL");
    $stmt->execute(['admin_id' => $adminId]);

    $otpHash = password_hash($otp, PASSWORD_DEFAULT);
    $expiresAt = time() + 180; // 3 minutes, unix timestamp (no timezone ambiguity)

    $stmt = $pdo->prepare("INSERT INTO password_reset_otps (admin_id, email, otp_hash, expires_at, created_at, updated_at) VALUES (:admin_id, :email, :otp_hash, :expires_at, NOW(), NOW())");
    $stmt->execute([
        'admin_id'   => $adminId,
        'email'      => $email,
        'otp_hash'   => $otpHash,
        'expires_at' => $expiresAt,
    ]);

    return $pdo->lastInsertId();
}

/**
 * Get OTP record by ID
 */
function getOtpRecord($otpId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM password_reset_otps WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $otpId]);
    return $stmt->fetch();
}

/**
 * Check if OTP is expired
 */
function isOtpExpired($otpRecord) {
    return (int) $otpRecord['expires_at'] < time();
}

/**
 * Check if OTP is verified
 */
function isOtpVerified($otpRecord) {
    return $otpRecord['verified_at'] !== null;
}

/**
 * Verify OTP
 */
function verifyOtp($otpRecord, $otp) {
    return password_verify($otp, $otpRecord['otp_hash']);
}

/**
 * Mark OTP as verified
 */
function markOtpVerified($otpId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("UPDATE password_reset_otps SET verified_at = NOW(), updated_at = NOW() WHERE id = :id");
    $stmt->execute(['id' => $otpId]);
}

/**
 * Delete OTP records for an admin
 */
function deleteOtpRecords($adminId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("DELETE FROM password_reset_otps WHERE admin_id = :admin_id");
    $stmt->execute(['admin_id' => $adminId]);
}

/**
 * Delete specific OTP record
 */
function deleteOtpRecord($otpId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("DELETE FROM password_reset_otps WHERE id = :id AND verified_at IS NULL");
    $stmt->execute(['id' => $otpId]);
}

/**
 * Update admin password
 */
function updateAdminPassword($adminId, $newPassword) {
    $pdo = getDb();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE admin SET password = :password, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['password' => $hashedPassword, 'id' => $adminId]);
}

/**
 * Send OTP email via SMTP (PHPMailer)
 *
 * @return array ['success' => bool, 'error' => string|null]
 */
function sendOtpEmail($toEmail, $adminName, $otp) {
    // Load Composer autoloader
    require_once __DIR__ . '/../../vendor/autoload.php';

    $config = require __DIR__ . '/../config/config.php';
    $mailCfg = $config['mail'];

    // Build email HTML
    $requestedAt = date('d M Y, g:ia');
    ob_start();
    include __DIR__ . '/../mail/templates/otp-verification.php';
    $body = ob_get_clean();

    $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mailer->isSMTP();
        $mailer->Host       = $mailCfg['host'];
        $mailer->SMTPAuth   = true;
        $mailer->Username   = $mailCfg['username'];
        $mailer->Password   = $mailCfg['password'];
        $mailer->SMTPSecure = $mailCfg['encryption']; // tls
        $mailer->Port       = $mailCfg['port'];       // 587
        $mailer->CharSet    = 'UTF-8';

        // Sender / Recipient
        $mailer->setFrom($mailCfg['from_address'], $mailCfg['from_name']);
        $mailer->addAddress($toEmail, $adminName);

        // Content
        $mailer->isHTML(true);
        $mailer->Subject = 'SOUND Group — Password Reset Verification';
        $mailer->Body    = $body;
        $mailer->AltBody = "Your OTP verification code is: $otp\nThis code expires in 3 minutes.";

        $mailer->send();
        return ['success' => true, 'error' => null];
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('OTP email failed: ' . $mailer->ErrorInfo);
        return ['success' => false, 'error' => 'Email delivery failed. Please try again.'];
    }
}

/**
 * Verify an admin's current password
 */
function verifyAdminPassword($adminId, $password) {
    $admin = findAdminById($adminId);
    return $admin ? password_verify($password, $admin['password']) : false;
}

/**
 * Update an admin's email address
 */
function updateAdminEmail($adminId, $newEmail) {
    $pdo = getDb();
    $stmt = $pdo->prepare("UPDATE admin SET email = :email, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['email' => $newEmail, 'id' => $adminId]);
}

/**
 * Store an email-change OTP (4-digit) for an admin
 */
function storeEmailChangeOtp($adminId, $newEmail, $otp) {
    $pdo = getDb();
    // Delete old unverified OTPs for this admin
    $stmt = $pdo->prepare("DELETE FROM email_change_otps WHERE admin_id = :admin_id AND verified_at IS NULL");
    $stmt->execute(['admin_id' => $adminId]);

    $otpHash = password_hash($otp, PASSWORD_DEFAULT);
    $expiresAt = time() + 180; // 3 minutes, unix timestamp (no timezone ambiguity)

    $stmt = $pdo->prepare("INSERT INTO email_change_otps (admin_id, new_email, otp_hash, expires_at, created_at, updated_at) VALUES (:admin_id, :new_email, :otp_hash, :expires_at, NOW(), NOW())");
    $stmt->execute([
        'admin_id'  => $adminId,
        'new_email' => $newEmail,
        'otp_hash'  => $otpHash,
        'expires_at'=> $expiresAt,
    ]);

    return $pdo->lastInsertId();
}

/**
 * Get an email-change OTP record scoped to the admin
 */
function getEmailChangeOtpRecord($otpId, $adminId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM email_change_otps WHERE id = :id AND admin_id = :admin_id LIMIT 1");
    $stmt->execute(['id' => $otpId, 'admin_id' => $adminId]);
    return $stmt->fetch();
}

/**
 * Check if an email-change OTP is expired
 */
function isEmailChangeOtpExpired($otpRecord) {
    return (int) $otpRecord['expires_at'] < time();
}

/**
 * Check if an email-change OTP is already verified
 */
function isEmailChangeOtpVerified($otpRecord) {
    return $otpRecord['verified_at'] !== null;
}

/**
 * Verify an email-change OTP code
 */
function verifyEmailChangeOtp($otpRecord, $otp) {
    return password_verify($otp, $otpRecord['otp_hash']);
}

/**
 * Mark an email-change OTP as verified
 */
function markEmailChangeOtpVerified($otpId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("UPDATE email_change_otps SET verified_at = NOW(), updated_at = NOW() WHERE id = :id");
    $stmt->execute(['id' => $otpId]);
}

/**
 * Delete all email-change OTP records for an admin
 */
function deleteEmailChangeOtps($adminId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("DELETE FROM email_change_otps WHERE admin_id = :admin_id");
    $stmt->execute(['admin_id' => $adminId]);
}

/**
 * Send email-change OTP email via SMTP (PHPMailer)
 *
 * @return array ['success' => bool, 'error' => string|null]
 */
function sendEmailChangeOtpEmail($toEmail, $adminName, $otp) {
    // Load Composer autoloader
    require_once __DIR__ . '/../../vendor/autoload.php';

    $config = require __DIR__ . '/../config/config.php';
    $mailCfg = $config['mail'];

    // Build email HTML
    $requestedAt = date('d M Y, g:ia');
    ob_start();
    include __DIR__ . '/../mail/templates/email-change-otp-verification.php';
    $body = ob_get_clean();

    $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mailer->isSMTP();
        $mailer->Host       = $mailCfg['host'];
        $mailer->SMTPAuth   = true;
        $mailer->Username   = $mailCfg['username'];
        $mailer->Password   = $mailCfg['password'];
        $mailer->SMTPSecure = $mailCfg['encryption']; // tls
        $mailer->Port       = $mailCfg['port'];       // 587
        $mailer->CharSet    = 'UTF-8';

        // Sender / Recipient
        $mailer->setFrom($mailCfg['from_address'], $mailCfg['from_name']);
        $mailer->addAddress($toEmail, $adminName);

        // Content
        $mailer->isHTML(true);
        $mailer->Subject = 'SOUND Group — Email Change Verification';
        $mailer->Body    = $body;
        $mailer->AltBody = "Your email change verification code is: $otp\nThis code expires in 3 minutes.";

        $mailer->send();
        return ['success' => true, 'error' => null];
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Email change OTP email failed: ' . $mailer->ErrorInfo);
        return ['success' => false, 'error' => 'Email delivery failed. Please try again.'];
    }
}
