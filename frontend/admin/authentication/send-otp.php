<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireGuest();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !isValidEmail($email)) {
    setSession('validation_errors', ['The email field is required and must be valid.']);
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

$admin = findAdminByEmail($email);

if (!$admin) {
    setSession('validation_errors', ['No admin account found with that email address.']);
    setSession('old_email', $email);
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

// Delete any old unverified OTPs for this admin
deleteOtpRecords($admin['id']);

$otp = generateOtp();
$otpRequestId = storeOtp($admin['id'], $email, $otp);
sendOtpEmail($email, $admin['name'], $otp);

setSession('otp_request_id', $otpRequestId);
setSession('otp_email', $email);

setFlash('otp_sent', true);
header('Location: ' . baseUrl() . '/frontend/admin/authentication/verify-otp.php');
exit;
