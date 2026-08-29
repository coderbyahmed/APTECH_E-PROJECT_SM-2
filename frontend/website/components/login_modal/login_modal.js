(function () {
    'use strict';

    var overlay = document.getElementById('wgLoginOverlay');
    var modal = document.getElementById('wgLoginModal');
    var closeBtn = document.getElementById('wgLoginClose');
    var form = document.getElementById('wgLoginForm');
    var errorEl = document.getElementById('loginError');
    var successEl = document.getElementById('loginSuccess');

    if (!overlay || !modal) return;

    // Open modal
    function openLogin() {
        overlay.classList.add('is-open');
        document.body.classList.add('wg-login-open');
        // Focus first input after animation
        setTimeout(function () {
            var firstInput = modal.querySelector('input:not([type="hidden"])');
            if (firstInput) firstInput.focus();
        }, 350);
    }

    // Close modal
    function closeLogin() {
        overlay.classList.remove('is-open');
        document.body.classList.remove('wg-login-open');
        // Reset messages
        if (errorEl) { errorEl.style.display = 'none'; errorEl.textContent = ''; }
        if (successEl) { successEl.style.display = 'none'; successEl.textContent = ''; }
        // Reset form
        if (form) form.reset();
        // Remove error border styles
        if (form) {
            var inputs = form.querySelectorAll('.wg-login-form__input');
            inputs.forEach(function (inp) { inp.style.borderColor = ''; });
        }
    }

    // Expose openLogin globally for cross-modal and trigger use
    window.openLoginModal = openLogin;

    // Connect all Login buttons (desktop + mobile)
    var loginBtns = document.querySelectorAll('.wg-header__actions .wg-btn--ghost, .wg-mobile-actions .wg-btn--ghost');
    loginBtns.forEach(function (btn) {
        if (btn.textContent.trim() === 'Login') {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openLogin();
            });
        }
    });

    // Connect footer / other Login triggers
    var triggers = document.querySelectorAll('.wg-login-trigger');
    triggers.forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            openLogin();
        });
    });

    // Close button
    closeBtn.addEventListener('click', closeLogin);

    // Close on overlay click (not modal)
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closeLogin();
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeLogin();
        }
    });

    // Show/hide password toggle
    var toggleBtn = overlay.querySelector('.wg-login-form__toggle-pass');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var targetId = toggleBtn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            var eyeOpen = toggleBtn.querySelector('.wg-login-form__eye-open');
            var eyeClosed = toggleBtn.querySelector('.wg-login-form__eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                if (eyeOpen) eyeOpen.style.display = 'none';
                if (eyeClosed) eyeClosed.style.display = '';
                toggleBtn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                if (eyeOpen) eyeOpen.style.display = '';
                if (eyeClosed) eyeClosed.style.display = 'none';
                toggleBtn.setAttribute('aria-label', 'Show password');
            }
        });
    }

    // Signup link — open Signup modal
    var signupLink = document.getElementById('wgLoginSignupLink');
    if (signupLink) {
        signupLink.addEventListener('click', function (e) {
            e.preventDefault();
            closeLogin();
            if (typeof window.openSignupModal === 'function') {
                window.openSignupModal();
            }
        });
    }

    // Form submission — AJAX to backend
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (errorEl) { errorEl.style.display = 'none'; errorEl.textContent = ''; }
            if (successEl) { successEl.style.display = 'none'; successEl.textContent = ''; }

            var emailInput = document.getElementById('loginEmail');
            var passwordInput = document.getElementById('loginPassword');
            var submitBtn = document.getElementById('loginSubmit');

            // Clear previous error borders
            var inputs = form.querySelectorAll('.wg-login-form__input');
            inputs.forEach(function (inp) { inp.style.borderColor = ''; });

            // Client-side validation
            var hasError = false;

            if (!emailInput.value.trim()) {
                emailInput.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                emailInput.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                hasError = true;
            }
            if (!passwordInput.value) {
                passwordInput.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                hasError = true;
            }

            if (hasError) {
                if (errorEl) {
                    errorEl.textContent = 'Please fill in all fields correctly.';
                    errorEl.style.display = '';
                }
                return;
            }

            // Set loading state
            if (submitBtn) {
                startButtonLoading(submitBtn, 'Logging in...');
            }

            // Build FormData
            var formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', emailInput.value.trim());
            formData.append('password', passwordInput.value);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', (window.APP_BASE_URL || '') + '/backend/handlers/user-login-handler.php', true);
            xhr.responseType = 'json';

            xhr.onload = function () {
                var resp = xhr.response;
                if (!resp || typeof resp !== 'object') {
                    stopButtonLoading(submitBtn);
                    showError('An unexpected error occurred. Please try again.');
                    return;
                }

                if (xhr.status === 200 && resp.success) {
                    closeLogin();
                    showSuccess(resp.message || 'Login successful! Redirecting...');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    stopButtonLoading(submitBtn);
                    if (resp.errors) {
                        var firstError = '';
                        if (resp.errors.email) {
                            emailInput.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                            firstError = resp.errors.email;
                        }
                        if (resp.errors.password) {
                            passwordInput.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                            if (!firstError) firstError = resp.errors.password;
                        }
                        if (errorEl && firstError) {
                            errorEl.textContent = firstError;
                            errorEl.style.display = '';
                        }
                    } else if (resp.error) {
                        showError(resp.error);
                    }
                }
            };

            xhr.onerror = function () {
                stopButtonLoading(submitBtn);
                showError('Network error. Please check your connection and try again.');
            };

            xhr.send(formData);
        });
    }


})();