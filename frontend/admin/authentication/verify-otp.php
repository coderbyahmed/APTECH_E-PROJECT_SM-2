<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireGuest();

$baseUrl = baseUrl();

$errors = [];

$otpRequestId = getSession('otp_request_id');
$otpEmail = getSession('otp_email');

if (!$otpRequestId) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

$otpRecord = getOtpRecord($otpRequestId);
$otpVerifiedFlag = getSession('otp_verified');

// Allow page load only when:
//  - record exists AND unverified (normal case), OR
//  - record verified AND session flag set (showing success toast before redirect)
if (!$otpRecord || (isOtpVerified($otpRecord) && !$otpVerifiedFlag)) {
    header('Location: ' . baseUrl() . '/frontend/admin/authentication/forgot.php');
    exit;
}

$otpExpiresAt = (int) $otpRecord['expires_at'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpInput = trim($_POST['otp'] ?? '');

    if (empty($otpInput)) {
        $errors[] = 'The verification code is required.';
    } elseif (!preg_match('/^\d{6}$/', $otpInput)) {
        $errors[] = 'The verification code must be exactly 6 digits.';
    }

    if (empty($errors)) {
        $otpRecord = getOtpRecord($otpRequestId);

        if (isOtpExpired($otpRecord)) {
            $errors[] = 'The verification code has expired. Please request a new one.';
        } elseif (!verifyOtp($otpRecord, $otpInput)) {
            $errors[] = 'The verification code is invalid. Please use the code from your most recent email, or request a new one.';
        } else {
            markOtpVerified($otpRecord['id']);
            setSession('otp_verified', true);

            setFlash('otp_verified_success', true);
            header('Location: ' . baseUrl() . '/frontend/admin/authentication/verify-otp.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP — SOUND Group Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/frontend/admin/css/authentication/verify-otp.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/frontend/admin/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/frontend/admin/css/components/loaders/button-spinner.css">
</head>
<body>
    <div class="otp-wrapper">
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
        <div class="otp-panel">
            <div class="otp-card">
                <div class="otp-brand-label">SOUND GROUP</div>

                <?php if (!empty($errors)): ?>
                <div class="otp-error">
                    <span class="otp-error-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <span class="otp-error-text"><?php echo htmlspecialchars($errors[0]); ?></span>
                </div>
                <?php endif; ?>

                <h2 class="otp-heading">Verify OTP</h2>
                <p class="otp-subheading">Enter the 6-digit verification code sent to <strong><?php echo htmlspecialchars($otpEmail); ?></strong></p>

                <form class="otp-form" method="POST" action="" id="otpForm">
                    <input type="hidden" name="otp" id="otpHidden" value="">

                    <div class="otp-inputs">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="0">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="1">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="2">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="3">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="4">
                        <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="5">
                    </div>

                    <div class="otp-countdown" id="countdown">
                        <svg class="otp-countdown-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span>OTP expires in <strong id="countdownTimer">03:00</strong></span>
                    </div>

                    <input type="hidden" name="expires_at" id="expiresAt" value="<?php echo $otpExpiresAt; ?>">

                    <button type="submit" class="submit-btn" id="verifyBtn">
                        <span>Verify OTP</span>
                    </button>
                </form>

                <div class="resend-section">
                    <span class="resend-text">Didn't receive the code?</span>
                    <form class="resend-form" method="POST" action="<?php echo $baseUrl; ?>/frontend/admin/authentication/resend-otp.php">
                        <button type="submit" class="resend-btn" id="resendBtn" disabled>Resend OTP</button>
                    </form>
                </div>

                <div class="back-to-login">
                    <form method="POST" action="<?php echo $baseUrl; ?>/frontend/admin/authentication/cancel-otp.php" style="display:inline">
                        <button type="submit" class="back-to-login-btn">Back to Login</button>
                    </form>
                </div>

                <p class="otp-footer">Protected administration area. Authorized personnel only.</p>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var boxes = document.querySelectorAll('.otp-box');
        var hiddenInput = document.getElementById('otpHidden');
        var form = document.getElementById('otpForm');
        var verifyBtn = document.getElementById('verifyBtn');
        var resendBtn = document.getElementById('resendBtn');
        var countdownTimer = document.getElementById('countdownTimer');
        var expiresAtInput = document.getElementById('expiresAt');
        var expiresAt = parseInt(expiresAtInput.value, 10) * 1000;

        function updateHiddenInput() {
            var val = '';
            boxes.forEach(function(box) { val += box.value; });
            hiddenInput.value = val;
        }

        function focusBox(index) {
            if (index >= 0 && index < boxes.length) {
                boxes[index].focus();
            }
        }

        boxes.forEach(function(box, i) {
            box.addEventListener('input', function(e) {
                var val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;
                if (val && i < boxes.length - 1) {
                    focusBox(i + 1);
                }
                updateHiddenInput();
            });

            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (!box.value && i > 0) {
                        boxes[i - 1].value = '';
                        focusBox(i - 1);
                    } else {
                        box.value = '';
                    }
                    updateHiddenInput();
                    e.preventDefault();
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    focusBox(i - 1);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    focusBox(i + 1);
                }
            });

            box.addEventListener('paste', function(e) {
                e.preventDefault();
                var pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                for (var j = 0; j < pasteData.length && j < boxes.length; j++) {
                    boxes[j].value = pasteData[j];
                }
                focusBox(Math.min(pasteData.length, boxes.length - 1));
                updateHiddenInput();
            });

            box.addEventListener('focus', function() {
                box.select();
            });
        });

        form.addEventListener('submit', function(e) {
            updateHiddenInput();
            if (hiddenInput.value.length < 6) {
                e.preventDefault();
                return;
            }
            startButtonLoading(verifyBtn, 'Verifying...');
        });

        // Countdown timer
        function updateCountdown() {
            var now = Date.now();
            var diff = expiresAt - now;

            if (diff <= 0) {
                countdownTimer.textContent = '00:00';
                verifyBtn.disabled = true;
                resendBtn.disabled = false;
                return;
            }

            var totalSeconds = Math.floor(diff / 1000);
            var minutes = Math.floor(totalSeconds / 60);
            var seconds = totalSeconds % 60;
            countdownTimer.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

            requestAnimationFrame(function() {
                setTimeout(updateCountdown, 250);
            });
        }

        updateCountdown();
        focusBox(0);
    })();
    </script>

    <script src="<?php echo $baseUrl; ?>/frontend/admin/js/components/notifications/notification.js"></script>
    <script src="<?php echo $baseUrl; ?>/frontend/admin/js/components/loaders/button-spinner.js"></script>
    <?php include __DIR__ . '/../components/notifications/notification.php'; ?>
</body>
</html>
