<?php
/**
 * SOUND Group — Notification Component (PHP)
 * Include this on any page that needs flash notifications
 */

if (!function_exists('getFlash')) {
    require_once __DIR__ . '/../../../../backend/includes/session.php';
}

$flashMessages = [
    'login_success'         => ['type' => 'success', 'text' => 'Login successful.', 'duration' => 2000, 'redirect' => '/frontend/admin/dashboard/index.php'],
    'otp_sent'              => ['type' => 'success', 'text' => 'OTP sent successfully to your registered email address.', 'duration' => 5000],
    'otp_verified_success'  => ['type' => 'success', 'text' => 'OTP verified successfully. Redirecting...', 'duration' => 3000, 'redirect' => '/frontend/admin/authentication/reset-password.php'],
    'otp_resent'            => ['type' => 'success', 'text' => 'A new OTP has been sent to your email.', 'duration' => 5000],
    'password_reset_success'=> ['type' => 'success', 'text' => 'Password updated successfully.', 'duration' => 3000, 'redirect' => '/frontend/admin/authentication/login.php', 'redirect_delay' => 4000],
    'otp_send_error'        => ['type' => 'error', 'flash_value' => true, 'duration' => 8000],
];

foreach ($flashMessages as $key => $msg) {
    $value = getFlash($key);
    if ($value !== null) {
        $text = $msg['flash_value'] ? $value : $msg['text'];
        $duration = $msg['duration'];
        $type = $msg['type'];
        $redirect = isset($msg['redirect']) ? $msg['redirect'] : null;
        $redirectDelay = isset($msg['redirect_delay']) ? $msg['redirect_delay'] : ($duration + 1000);
        $fn = $type === 'error' ? 'showError' : 'showSuccess';
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php echo $fn; ?>('<?php echo addslashes($text); ?>', <?php echo $duration; ?>);
            <?php if ($redirect): ?>
            setTimeout(function () {
                window.location.href = '<?php echo baseUrl() . $redirect; ?>';
            }, <?php echo $redirectDelay; ?>);
            <?php endif; ?>
        });
        </script>
        <?php
    }
}
?>
