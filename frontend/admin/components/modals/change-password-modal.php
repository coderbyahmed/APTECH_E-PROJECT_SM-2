<?php
/**
 * SOUND Group — Change Password Modal Component
 * Two-step modal (verify identity -> new password).
 * Include inside the admin layout body.
 */

$cpCsrf = csrfToken();
$cpEndpoint = '/Aptech_E_Project_02/sound_management/backend/handlers/change-password.php';
?>
<div class="sg-modal" id="changePasswordModal" aria-hidden="true">
    <div class="sg-modal__overlay" data-sg-close></div>
    <div class="sg-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cpTitle">
        <button type="button" class="sg-modal__close" data-sg-close aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="sg-modal__body">

            <!-- Step 1: Verify Identity -->
            <div class="sg-modal__step" id="cpStep1">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </div>
                <h3 class="sg-modal__title" id="cpTitle">Change Password</h3>
                <p class="sg-modal__subtitle">Verify your identity before setting a new password.</p>

                <form id="cpVerifyForm" class="sg-form" data-endpoint="<?php echo $cpEndpoint; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $cpCsrf; ?>">
                    <input type="hidden" name="action" value="verify_password">

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cpCurrentPassword">Current Password</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <input type="password" id="cpCurrentPassword" name="current_password" class="sg-form-input" placeholder="Enter your current password" autocomplete="current-password">
                            <button type="button" class="sg-password-toggle" data-sg-toggle>
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

                    <button type="submit" class="sg-btn sg-btn--primary sg-btn--block" id="cpVerifyBtn">
                        <span>Verify</span>
                    </button>
                </form>
            </div>

            <!-- Step 2: New Password -->
            <div class="sg-modal__step" id="cpStep2" hidden>
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </div>
                <h3 class="sg-modal__title">Set New Password</h3>
                <p class="sg-modal__subtitle">Make sure it's strong and secure. At least 8 characters.</p>

                <form id="cpUpdateForm" class="sg-form" data-endpoint="<?php echo $cpEndpoint; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $cpCsrf; ?>">
                    <input type="hidden" name="action" value="update_password">

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cpNewPassword">New Password</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                            </span>
                            <input type="password" id="cpNewPassword" name="password" class="sg-form-input" placeholder="Enter new password" autocomplete="new-password">
                            <button type="button" class="sg-password-toggle" data-sg-toggle>
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

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="cpConfirmPassword">Confirm New Password</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </span>
                            <input type="password" id="cpConfirmPassword" name="password_confirmation" class="sg-form-input" placeholder="Confirm new password" autocomplete="new-password">
                            <button type="button" class="sg-password-toggle" data-sg-toggle>
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

                    <button type="submit" class="sg-btn sg-btn--primary sg-btn--block" id="cpUpdateBtn">
                        <span>Update Password</span>
                    </button>
                </form>

                <button type="button" class="sg-modal__back" data-sg-step="1" data-sg-back="cpStep1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Back
                </button>
            </div>

        </div>
    </div>
</div>
