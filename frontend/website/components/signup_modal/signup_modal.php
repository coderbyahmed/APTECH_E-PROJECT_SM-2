<!-- SIGNUP MODAL -->
<div class="wg-signup-overlay" id="wgSignupOverlay">
    <div class="wg-signup-modal" id="wgSignupModal" role="dialog" aria-modal="true" aria-labelledby="wgSignupTitle">

        <!-- Close Button -->
        <button class="wg-signup-modal__close" id="wgSignupClose" type="button" aria-label="Close signup modal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <!-- Scrollable Content -->
        <div class="wg-signup-modal__scroll">

            <!-- Header -->
            <div class="wg-signup-modal__header">
                <a href="<?php echo $websiteBase; ?>/index.php" class="wg-signup-modal__logo">
                    <span class="wg-signup-modal__logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </span>
                    <span class="wg-signup-modal__logo-text">Sound Group</span>
                </a>
                <h2 class="wg-signup-modal__title" id="wgSignupTitle">Create Your Account</h2>
                <p class="wg-signup-modal__subtitle">Join Sound Group and enjoy music, videos, reviews and more.</p>
            </div>

            <!-- Form -->
            <form class="wg-signup-form" id="wgSignupForm" novalidate>

                <!-- Profile Image Upload -->
                <div class="wg-signup-form__avatar-wrap">
                    <label class="wg-signup-form__avatar" for="signupAvatar" aria-label="Upload profile image">
                        <img class="wg-signup-form__avatar-img" id="signupAvatarPreview" src="" alt="Profile preview" style="display:none;">
                        <span class="wg-signup-form__avatar-placeholder" id="signupAvatarPlaceholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <span class="wg-signup-form__avatar-overlay">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </span>
                    </label>
                    <input type="file" class="wg-signup-form__avatar-input" id="signupAvatar" name="profile_image" accept="image/*">
                </div>

                <!-- Name + Email Row -->
                <div class="wg-signup-form__row">
                    <div class="wg-signup-form__field">
                        <label class="wg-signup-form__label" for="signupName">Full Name <span class="wg-signup-form__required">*</span></label>
                        <div class="wg-signup-form__input-wrap">
                            <span class="wg-signup-form__input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" class="wg-signup-form__input" id="signupName" name="name" placeholder="Enter your full name" required>
                        </div>
                    </div>
                    <div class="wg-signup-form__field">
                        <label class="wg-signup-form__label" for="signupEmail">Email Address <span class="wg-signup-form__required">*</span></label>
                        <div class="wg-signup-form__input-wrap">
                            <span class="wg-signup-form__input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <input type="email" class="wg-signup-form__input" id="signupEmail" name="email" placeholder="Enter your email address" required>
                        </div>
                    </div>
                </div>

                <!-- Phone + Address Row -->
                <div class="wg-signup-form__row">
                    <div class="wg-signup-form__field">
                        <label class="wg-signup-form__label" for="signupPhone">Phone Number <span class="wg-signup-form__required">*</span></label>
                        <div class="wg-signup-form__input-wrap">
                            <span class="wg-signup-form__input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            <input type="tel" class="wg-signup-form__input" id="signupPhone" name="phone" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                    <div class="wg-signup-form__field">
                        <label class="wg-signup-form__label" for="signupAddress">Address</label>
                        <div class="wg-signup-form__input-wrap">
                            <span class="wg-signup-form__input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <input type="text" class="wg-signup-form__input" id="signupAddress" name="address" placeholder="Enter your address">
                        </div>
                    </div>
                </div>

                <!-- Password + Confirm Password Row -->
                <div class="wg-signup-form__row">
                    <div class="wg-signup-form__field">
                        <label class="wg-signup-form__label" for="signupPassword">Password <span class="wg-signup-form__required">*</span></label>
                        <div class="wg-signup-form__input-wrap">
                            <span class="wg-signup-form__input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" class="wg-signup-form__input" id="signupPassword" name="password" placeholder="Create a password" required>
                            <button type="button" class="wg-signup-form__toggle-pass" data-target="signupPassword" aria-label="Show password">
                                <svg class="wg-signup-form__eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="wg-signup-form__eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="wg-signup-form__field">
                        <label class="wg-signup-form__label" for="signupConfirmPassword">Confirm Password <span class="wg-signup-form__required">*</span></label>
                        <div class="wg-signup-form__input-wrap">
                            <span class="wg-signup-form__input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </span>
                            <input type="password" class="wg-signup-form__input" id="signupConfirmPassword" name="confirm_password" placeholder="Confirm your password" required>
                            <button type="button" class="wg-signup-form__toggle-pass" data-target="signupConfirmPassword" aria-label="Show confirm password">
                                <svg class="wg-signup-form__eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="wg-signup-form__eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="wg-signup-form__message wg-signup-form__message--error" id="signupError" style="display:none;"></div>
                <div class="wg-signup-form__message wg-signup-form__message--success" id="signupSuccess" style="display:none;"></div>

                <!-- Submit -->
                <button type="submit" class="wg-btn wg-btn--primary wg-btn--lg wg-signup-form__submit" id="signupSubmit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    <span>Create Account</span>
                </button>

                <!-- Login Link -->
                <p class="wg-signup-form__login">Already have an account? <a href="#" class="wg-signup-form__login-link" id="wgSignupLoginLink">Login</a></p>
            </form>

        </div>
    </div>
</div>
