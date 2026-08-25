<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireGuest();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/login.php');
    exit;
}

$otpRequestId = getSession('otp_request_id');
$otpEmail = getSession('otp_email');

if (!$otpRequestId || !$otpEmail) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

$otpRecord = getOtpRecord($otpRequestId);

if (!$otpRecord) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

// Delete old OTP record
deleteOtpRecord($otpRecord['id']);

// Get admin
$admin = findAdminByEmail($otpEmail);

if (!$admin) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

// Generate and store new OTP
$otp = generateOtp();
$newOtpRequestId = storeOtp($admin['id'], $otpEmail, $otp);

$mailResult = sendOtpEmail($otpEmail, $admin['name'], $otp);

if ($mailResult['success']) {
    // Update session with new request ID
    setSession('otp_request_id', $newOtpRequestId);
    setFlash('otp_resent', true);
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/verify-otp.php');
    exit;
} else {
    // Delete the newly stored OTP since email failed
    deleteOtpRecords($admin['id']);
    setFlash('otp_send_error', $mailResult['error']);
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/verify-otp.php');
    exit;
}
