<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/login.php');
    exit;
}

$otpRequestId = getSession('otp_request_id');

if ($otpRequestId) {
    $otpRecord = getOtpRecord($otpRequestId);

    if ($otpRecord && !isOtpVerified($otpRecord)) {
        deleteOtpRecord($otpRecord['id']);
    }
}

clearOtpSession();

header('Location: ' . baseUrl() . '/frontend/admin/authentication/login.php');
exit;
