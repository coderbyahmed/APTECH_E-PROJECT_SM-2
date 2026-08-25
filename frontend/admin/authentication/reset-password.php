<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireGuest();

$errors = [];

// Check OTP was verified
$otpVerified = getSession('otp_verified');
$otpRequestId = getSession('otp_request_id');

if (!$otpVerified || !$otpRequestId) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

$otpRecord = getOtpRecord($otpRequestId);

if (!$otpRecord || !isOtpVerified($otpRecord)) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (empty($password)) {
        $errors[] = 'The password field is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'The password must be at least 8 characters.';
    }

    if ($password !== $passwordConfirmation) {
        $errors[] = 'The password confirmation does not match.';
    }

    if (empty($errors)) {
        updateAdminPassword($otpRecord['admin_id'], $password);
        deleteOtpRecords($otpRecord['admin_id']);
        clearOtpSession();

        setFlash('password_reset_success', true);
        header('Location: ' . baseUrl() . '/frontend/admin/authentication/reset-password.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SOUND Group Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/authentication/reset-password.css">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/components/notifications/notification.css">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/components/loaders/button-spinner.css">
</head>
<body>
    <div class="confirm-wrapper">
        <!-- Left Brand Panel -->
        <div class="brand-panel">
            <div class="dot-grid"></div>
            <div class="geo-ring geo-ring--1"></div>
            <div class="geo-ring geo-ring--2"></div>
            <div class="geo-ring geo-ring--3"></div>

            <div class="brand-content">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18V5l12-2v13"/>
                        <circle cx="6" cy="18" r="3"/>
                        <circle cx="18" cy="16" r="3"/>
                    </svg>
                </div>
                <h1 class="brand-name">SOUND Group</h1>
                <span class="brand-badge">Admin Panel</span>
                <p class="brand-desc">Manage your music, videos, artists, albums and entertainment content from one secure administration panel.</p>
            </div>

            <div class="brand-footer"></div>
        </div>

        <!-- Right Form Panel -->
        <div class="confirm-panel">
            <div class="confirm-card">
                <div class="confirm-brand-label">SOUND GROUP</div>

                <?php if (!empty($errors)): ?>
                <div class="confirm-error">
                    <span class="confirm-error-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <span class="confirm-error-text"><?php echo htmlspecialchars($errors[0]); ?></span>
                </div>
                <?php endif; ?>

                <h2 class="confirm-heading">Reset Password</h2>
                <p class="confirm-subheading">Enter your new password below. Make sure it's strong and secure.</p>

                <form class="confirm-form" method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input form-input--with-toggle"
                                placeholder="Enter new password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword(this, 'password')">
                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="eye-closed" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                                    <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                    <path d="M14.12 14.12a3 3 0 11-4.24-4.24"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input form-input--with-toggle"
                                placeholder="Confirm new password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword(this, 'password_confirmation')">
                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="eye-closed" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                                    <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                    <path d="M14.12 14.12a3 3 0 11-4.24-4.24"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="resetBtn">
                        <span>Reset Password</span>
                    </button>
                </form>

                <p class="confirm-footer">Protected administration area. Authorized personnel only.</p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword(btn) {
        var input = btn.parentElement.querySelector('input');
        var eyeOpen = btn.querySelector('.eye-open');
        var eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }

    document.querySelector('.confirm-form').addEventListener('submit', function() {
        startButtonLoading(document.getElementById('resetBtn'), 'Resetting...');
    });
    </script>

    <script src="/Aptech_E_Project_02/sound_management/frontend/admin/js/components/notifications/notification.js"></script>
    <script src="/Aptech_E_Project_02/sound_management/frontend/admin/js/components/loaders/button-spinner.js"></script>
    <?php include __DIR__ . '/../../components/admin/notifications/notification.php'; ?>
</body>
</html>
