<!-- LOGIN MODAL -->
<div class="wg-login-overlay" id="wgLoginOverlay">
    <div class="wg-login-modal" id="wgLoginModal" role="dialog" aria-modal="true" aria-labelledby="wgLoginTitle">

        <!-- Close Button -->
        <button class="wg-login-modal__close" id="wgLoginClose" type="button" aria-label="Close login modal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <!-- Scrollable Content -->
        <div class="wg-login-modal__scroll">

            <!-- Header -->
            <div class="wg-login-modal__header">
                <a href="<?php echo $websiteBase; ?>/index.php" class="wg-login-modal__logo">
                    <span class="wg-login-modal__logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </span>
                    <span class="wg-login-modal__logo-text">Sound Group</span>
                </a>
                <h2 class="wg-login-modal__title" id="wgLoginTitle">Welcome Back</h2>
                <p class="wg-login-modal__subtitle">Login to continue enjoying Sound Group.</p>
            </div>

            <!-- Form -->
            <form class="wg-login-form" id="wgLoginForm" novalidate>

                <!-- Email -->
                <div class="wg-login-form__field">
                    <label class="wg-login-form__label" for="loginEmail">Email Address <span class="wg-login-form__required">*</span></label>
                    <div class="wg-login-form__input-wrap">
                        <span class="wg-login-form__input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" class="wg-login-form__input" id="loginEmail" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="wg-login-form__field">
                    <label class="wg-login-form__label" for="loginPassword">Password <span class="wg-login-form__required">*</span></label>
                    <div class="wg-login-form__input-wrap">
                        <span class="wg-login-form__input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" class="wg-login-form__input" id="loginPassword" name="password" placeholder="Enter your password" required>
                        <button type="button" class="wg-login-form__toggle-pass" data-target="loginPassword" aria-label="Show password">
                            <svg class="wg-login-form__eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="wg-login-form__eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div class="wg-login-form__message wg-login-form__message--error" id="loginError" style="display:none;"></div>
                <div class="wg-login-form__message wg-login-form__message--success" id="loginSuccess" style="display:none;"></div>

                <!-- Submit -->
                <button type="submit" class="wg-btn wg-btn--primary wg-btn--lg wg-login-form__submit" id="loginSubmit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span>Login</span>
                </button>

                <!-- Signup Link -->
                <p class="wg-login-form__signup">Don't have an account? <a href="#" class="wg-login-form__signup-link" id="wgLoginSignupLink">Sign Up</a></p>
            </form>

        </div>
    </div>
</div>