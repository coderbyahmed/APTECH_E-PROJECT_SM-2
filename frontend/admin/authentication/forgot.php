<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireGuest();

$errors = [];
$email = '';

// Clear OTP session on fresh GET visit
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    clearOtpSession();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $errors[] = 'The email field is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($errors)) {
        $admin = findAdminByEmail($email);

        if (!$admin) {
            $errors[] = 'No admin account found with that email address.';
        } else {
            $otp = generateOtp();
            $otpRequestId = storeOtp($admin['id'], $email, $otp);

            $mailResult = sendOtpEmail($email, $admin['name'], $otp);

            if ($mailResult['success']) {
                setSession('otp_request_id', $otpRequestId);
                setSession('otp_email', $email);

                setFlash('otp_sent', true);
                header('Location: ' . baseUrl() . '/frontend/admin/authentication/verify-otp.php');
                exit;
            } else {
                deleteOtpRecords($admin['id']);
                $errors[] = $mailResult['error'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — SOUND Group Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/authentication/forgot.css">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/components/notifications/notification.css">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/components/loaders/button-spinner.css">
</head>
<body>
    <div class="forgot-wrapper">
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
        <div class="forgot-panel">
            <div class="forgot-card">
                <div class="forgot-brand-label">SOUND GROUP</div>

                <?php if (!empty($errors)): ?>
                <div class="forgot-error">
                    <span class="forgot-error-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <span class="forgot-error-text"><?php echo htmlspecialchars($errors[0]); ?></span>
                </div>
                <?php endif; ?>

                <h2 class="forgot-heading">Forgot Password</h2>
                <p class="forgot-subheading">Enter your admin email address and we'll verify your account.</p>

                <form class="forgot-form" method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="Enter your admin email"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="sendOtpBtn">
                        <span>Send OTP</span>
                    </button>
                </form>

                <div class="back-to-login">
                    <a href="/Aptech_E_Project_02/sound_management/frontend/admin/authentication/login.php">Back to Login</a>
                </div>

                <p class="forgot-footer">Protected administration area. Authorized personnel only.</p>
            </div>
        </div>
    </div>

    <script>
    document.querySelector('.forgot-form').addEventListener('submit', function() {
        startButtonLoading(document.getElementById('sendOtpBtn'), 'Sending OTP...');
    });
    </script>

    <script src="/Aptech_E_Project_02/sound_management/frontend/admin/js/components/notifications/notification.js"></script>
    <script src="/Aptech_E_Project_02/sound_management/frontend/admin/js/components/loaders/button-spinner.js"></script>
    <?php include __DIR__ . '/../../components/admin/notifications/notification.php'; ?>
</body>
</html>
