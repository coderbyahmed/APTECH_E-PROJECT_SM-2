<?php
require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';

requireGuest();

$errors = [];
$email = '';
$credentialNotice = '';

$credential = $_GET['credential'] ?? '';
if ($credential === 'email_changed') {
    $credentialNotice = 'Email address updated successfully. Please sign in with your new email.';
} elseif ($credential === 'password_changed') {
    $credentialNotice = 'Password updated successfully. Please sign in with your new password.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors[] = 'The email field is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'The password field is required.';
    }

    if (empty($errors)) {
        $admin = findAdminByEmail($email);

        if (!$admin) {
            $errors[] = 'No Admin account exists with this email address.';
        } elseif (!password_verify($password, $admin['password'])) {
            $errors[] = 'Incorrect password.';
        } else {
            loginAdmin($admin);
            setFlash('login_success', true);
            header('Location: ' . baseUrl() . '/frontend/admin/dashboard/index.php');
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
    <title>Login — SOUND Group Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/authentication/login.css">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/components/notifications/notification.css">
    <link rel="stylesheet" href="/Aptech_E_Project_02/sound_management/frontend/admin/css/components/loaders/button-spinner.css">
</head>
<body>
    <div class="login-wrapper">
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

        <!-- Right Login Panel -->
        <div class="login-panel">
            <div class="login-card">
                <div class="login-brand-label">SOUND GROUP</div>

                <?php if (!empty($errors)): ?>
                <div class="login-error">
                    <span class="login-error-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <span class="login-error-text"><?php echo htmlspecialchars($errors[0]); ?></span>
                </div>
                <?php endif; ?>

                <h2 class="login-heading">Welcome Back</h2>
                <p class="login-subheading">Sign in to your Admin Panel</p>

                <form class="login-form" method="POST" action="">
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
                                placeholder="you@example.com"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="form-input-wrapper">
                            <span class="form-input-icon">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input form-input--with-toggle"
                                placeholder="Enter your password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword(this)">
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

                    <div class="form-row">
                        <a href="/Aptech_E_Project_02/sound_management/frontend/admin/authentication/forgot.php" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="submit-btn" id="loginBtn">
                        <span>Login</span>
                    </button>
                </form>

                <p class="login-footer">Protected administration area. Authorized personnel only.</p>
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

    document.querySelector('.login-form').addEventListener('submit', function() {
        startButtonLoading(document.getElementById('loginBtn'), 'Logging in...');
    });

    <?php if ($credentialNotice): ?>
    document.addEventListener('DOMContentLoaded', function () {
        showSuccess('<?php echo addslashes($credentialNotice); ?>', 6000);
    });
    <?php endif; ?>
    </script>

    <script src="/Aptech_E_Project_02/sound_management/frontend/admin/js/components/notifications/notification.js"></script>
    <script src="/Aptech_E_Project_02/sound_management/frontend/admin/js/components/loaders/button-spinner.js"></script>
    <?php include __DIR__ . '/../components/notifications/notification.php'; ?>
</body>
</html>
