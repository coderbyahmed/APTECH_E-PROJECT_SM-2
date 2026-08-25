<?php
/**
 * SOUND Group — Change Email Modal Component
 * Two-step modal (verify identity -> new email + OTP).
 * Include inside the admin layout body.
 */

$ceCsrf = csrfToken();
$ceEndpoint = '/Aptech_E_Project_02/sound_management/backend/handlers/change-email.php';
?>
<div class="sg-modal" id="changeEmailModal" aria-hidden="true">
    <div class="sg-modal__overlay" data-sg-close></div>
    <div class="sg-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="ceTitle">
        <button type="button" class="sg-modal__close" data-sg-close aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <div class="sg-modal__body">

            <!-- Step 1: Verify Identity -->
            <div class="sg-modal__step" id="ceStep1">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h3 class="sg-modal__title" id="ceTitle">Change Email Address</h3>
                <p class="sg-modal__subtitle">Verify your identity before changing your email address.</p>

                <form id="ceVerifyForm" class="sg-form" data-endpoint="<?php echo $ceEndpoint; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $ceCsrf; ?>">
                    <input type="hidden" name="action" value="verify_password">

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="ceCurrentPassword">Current Password</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <input type="password" id="ceCurrentPassword" name="current_password" class="sg-form-input" placeholder="Enter your current password" autocomplete="current-password">
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

                    <button type="submit" class="sg-btn sg-btn--primary sg-btn--block" id="ceVerifyBtn">
                        <span>Verify</span>
                    </button>
                </form>
            </div>

            <!-- Step 2: New Email -->
            <div class="sg-modal__step" id="ceStep2" hidden>
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h3 class="sg-modal__title">Enter New Email</h3>
                <p class="sg-modal__subtitle">We'll send a 4-digit verification code to your new email address.</p>

                <form id="ceSendForm" class="sg-form" data-endpoint="<?php echo $ceEndpoint; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $ceCsrf; ?>">
                    <input type="hidden" name="action" value="send_otp">

                    <div class="sg-form-group">
                        <label class="sg-form-label" for="ceNewEmail">New Email Address</label>
                        <div class="sg-form-input-wrap">
                            <span class="sg-form-input-icon">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </span>
                            <input type="email" id="ceNewEmail" name="new_email" class="sg-form-input" placeholder="newemail@example.com" autocomplete="email">
                        </div>
                    </div>

                    <button type="submit" class="sg-btn sg-btn--primary sg-btn--block" id="ceSendOtpBtn">
                        <span>Send OTP</span>
                    </button>
                </form>

                <button type="button" class="sg-modal__back" data-sg-step="1" data-sg-back="ceStep1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Back
                </button>
            </div>

            <!-- Step 3: Verify OTP -->
            <div class="sg-modal__step" id="ceStep3" hidden>
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h3 class="sg-modal__title">Verify OTP</h3>
                <p class="sg-modal__subtitle">Enter the 4-digit code sent to <strong id="ceOtpEmail">your new email</strong></p>

                <form id="ceOtpForm" class="sg-form" data-endpoint="<?php echo $ceEndpoint; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $ceCsrf; ?>">
                    <input type="hidden" name="action" value="verify_otp">
                    <input type="hidden" name="otp" id="ceOtpHidden" value="">
                    <input type="hidden" id="ceExpiresAt" value="">

                    <div class="sg-otp-inputs">
                        <input type="text" class="sg-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="0">
                        <input type="text" class="sg-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="1">
                        <input type="text" class="sg-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="2">
                        <input type="text" class="sg-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="3">
                    </div>

                    <div class="sg-otp-countdown">
                        <svg class="sg-otp-countdown-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span>OTP expires in <strong id="ceCountdownTimer">03:00</strong></span>
                    </div>

                    <button type="submit" class="sg-btn sg-btn--primary sg-btn--block" id="ceOtpBtn">
                        <span>Verify OTP</span>
                    </button>
                </form>

                <div class="sg-modal__resend">
                    <span class="sg-modal__resend-text">Didn't receive the code?</span>
                    <button type="button" class="sg-btn sg-btn--link" id="ceResendBtn">Resend OTP</button>
                </div>

                <button type="button" class="sg-modal__back" data-sg-step="2" data-sg-back="ceStep2">
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
