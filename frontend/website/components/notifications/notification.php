<?php
/**
 * SOUND Group — Website Notification Component (PHP)
 * Include this on any page that needs flash notifications
 */

if (!function_exists('getFlash')) {
    require_once __DIR__ . '/../../../../backend/includes/session.php';
}

$flashMessages = [
    'signup_success'       => ['type' => 'success', 'text' => 'Account created successfully! Welcome aboard.', 'duration' => 3000],
    'login_success'        => ['type' => 'success', 'text' => 'Login successful.', 'duration' => 2000],
    'logout_success'       => ['type' => 'success', 'text' => 'You have been logged out.', 'duration' => 2000],
    'profile_updated'      => ['type' => 'success', 'text' => 'Profile updated successfully.', 'duration' => 3000],
    'account_disabled'     => ['type' => 'error', 'text' => 'Your account has been deactivated. Please contact support.', 'duration' => 6000],
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
