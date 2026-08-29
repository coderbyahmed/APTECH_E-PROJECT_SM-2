(function () {
    'use strict';

    var overlay = document.getElementById('wgSignupOverlay');
    var modal = document.getElementById('wgSignupModal');
    var closeBtn = document.getElementById('wgSignupClose');
    var form = document.getElementById('wgSignupForm');
    var errorEl = document.getElementById('signupError');
    var successEl = document.getElementById('signupSuccess');
    var submitBtn = document.getElementById('signupSubmit');

    if (!overlay || !modal) return;

    // Expose openSignup globally for cross-modal use
    window.openSignupModal = openSignup;

    // Open modal
    function openSignup() {
        overlay.classList.add('is-open');
        document.body.classList.add('wg-signup-open');
        setTimeout(function () {
            var firstInput = modal.querySelector('input:not([type="file"]):not([type="hidden"])');
            if (firstInput) firstInput.focus();
        }, 350);
    }

    // Close modal
    function closeSignup() {
        overlay.classList.remove('is-open');
        document.body.classList.remove('wg-signup-open');
        resetFormState();
    }

    function resetFormState() {
        if (errorEl) { errorEl.style.display = 'none'; errorEl.textContent = ''; }
        if (successEl) { successEl.style.display = 'none'; successEl.textContent = ''; }
        if (form) form.reset();
        // Reset avatar preview
        var preview = document.getElementById('signupAvatarPreview');
        var placeholder = document.getElementById('signupAvatarPlaceholder');
        if (preview) { preview.style.display = 'none'; preview.src = ''; }
        if (placeholder) placeholder.style.display = '';
        // Clear error states
        if (form) {
            form.querySelectorAll('.wg-signup-form__input').forEach(function (inp) {
                inp.style.borderColor = '';
            });
            form.querySelectorAll('.wg-signup-form__field-error').forEach(function (el) {
                el.remove();
            });
        }
        // Re-enable submit
        setSubmitting(false);
    }

    function setSubmitting(loading) {
        if (!submitBtn) return;
        if (loading) {
            startButtonLoading(submitBtn, 'Creating Account...');
        } else {
            stopButtonLoading(submitBtn);
        }
    }

    function showFieldError(fieldId, message) {
        var field = document.getElementById(fieldId);
        if (!field) return;
        var wrap = field.closest('.wg-signup-form__field');
        if (!wrap) return;
        // Remove existing error
        var existing = wrap.querySelector('.wg-signup-form__field-error');
        if (existing) existing.remove();
        // Add error border
        field.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        // Add error text
        var err = document.createElement('span');
        err.className = 'wg-signup-form__field-error';
        err.style.cssText = 'color:#fca5a5;font-size:0.75rem;margin-top:0.25rem;display:block;';
        err.textContent = message;
        wrap.appendChild(err);
    }

    function clearFieldErrors() {
        if (!form) return;
        form.querySelectorAll('.wg-signup-form__input').forEach(function (inp) {
            inp.style.borderColor = '';
        });
        form.querySelectorAll('.wg-signup-form__field-error').forEach(function (el) {
            el.remove();
        });
    }

    // Map backend field names to frontend IDs
    var fieldMap = {
        'full_name':       'signupName',
        'email':           'signupEmail',
        'phone':           'signupPhone',
        'address':         'signupAddress',
        'password':        'signupPassword',
        'confirm_password':'signupConfirmPassword',
        'profile_image':   'signupAvatar',
    };

    // Connect all Sign Up buttons (desktop + mobile)
    var signupBtns = document.querySelectorAll('.wg-header__actions .wg-btn--primary, .wg-mobile-actions .wg-btn--primary');
    signupBtns.forEach(function (btn) {
        if (btn.textContent.trim() === 'Sign Up') {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openSignup();
            });
        }
    });

    // Connect footer / other Sign Up triggers
    var triggers = document.querySelectorAll('.wg-signup-trigger');
    triggers.forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            openSignup();
        });
    });

    // Close button
    closeBtn.addEventListener('click', closeSignup);

    // Close on overlay click
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeSignup();
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeSignup();
    });

    // Profile image preview
    var avatarInput = document.getElementById('signupAvatar');
    var avatarPreview = document.getElementById('signupAvatarPreview');
    var avatarPlaceholder = document.getElementById('signupAvatarPlaceholder');

    if (avatarInput && avatarPreview && avatarPlaceholder) {
        avatarInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                avatarPreview.src = e.target.result;
                avatarPreview.style.display = '';
                avatarPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    // Show/hide password toggles
    document.querySelectorAll('.wg-signup-form__toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            var eyeOpen = btn.querySelector('.wg-signup-form__eye-open');
            var eyeClosed = btn.querySelector('.wg-signup-form__eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                if (eyeOpen) eyeOpen.style.display = 'none';
                if (eyeClosed) eyeClosed.style.display = '';
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                if (eyeOpen) eyeOpen.style.display = '';
                if (eyeClosed) eyeClosed.style.display = 'none';
                btn.setAttribute('aria-label', 'Show password');
            }
        });
    });

    // Form submission — AJAX to backend
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Clear previous errors
            if (errorEl) { errorEl.style.display = 'none'; errorEl.textContent = ''; }
            if (successEl) { successEl.style.display = 'none'; successEl.textContent = ''; }
            clearFieldErrors();

            // Client-side quick validation
            var name = document.getElementById('signupName');
            var email = document.getElementById('signupEmail');
            var phone = document.getElementById('signupPhone');
            var password = document.getElementById('signupPassword');
            var confirmPassword = document.getElementById('signupConfirmPassword');
            var avatar = document.getElementById('signupAvatar');

            var hasError = false;

            if (!avatar.files || !avatar.files.length) {
                showFieldError('signupAvatar', 'Profile image is required.');
                hasError = true;
            }
            if (!name.value.trim()) {
                showFieldError('signupName', 'Full name is required.');
                hasError = true;
            }
            if (!email.value.trim()) {
                showFieldError('signupEmail', 'Email address is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                showFieldError('signupEmail', 'Please enter a valid email address.');
                hasError = true;
            }
            if (!phone.value.trim()) {
                showFieldError('signupPhone', 'Phone number is required.');
                hasError = true;
            } else {
                var digits = phone.value.replace(/\D/g, '');
                if (digits.length !== 11) {
                    showFieldError('signupPhone', 'Phone number must be exactly 11 digits.');
                    hasError = true;
                }
            }
            if (!password.value) {
                showFieldError('signupPassword', 'Password is required.');
                hasError = true;
            } else if (password.value.length < 6) {
                showFieldError('signupPassword', 'Password must be at least 6 characters.');
                hasError = true;
            }
            if (!confirmPassword.value) {
                showFieldError('signupConfirmPassword', 'Please confirm your password.');
                hasError = true;
            } else if (password.value !== confirmPassword.value) {
                showFieldError('signupConfirmPassword', 'Passwords do not match.');
                hasError = true;
            }

            if (hasError) return;

            // Build FormData
            var formData = new FormData();
            formData.append('action', 'register');
            formData.append('full_name', name.value.trim());
            formData.append('email', email.value.trim());
            formData.append('phone', phone.value.trim());
            formData.append('address', document.getElementById('signupAddress').value.trim());
            formData.append('password', password.value);
            formData.append('confirm_password', confirmPassword.value);
            if (avatar.files.length > 0) {
                formData.append('profile_image', avatar.files[0]);
            }

            // Submit
            setSubmitting(true);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', (window.APP_BASE_URL || '') + '/backend/handlers/signup-handler.php', true);
            xhr.responseType = 'json';

            xhr.onload = function () {
                var resp = xhr.response;
                if (!resp || typeof resp !== 'object') {
                    setSubmitting(false);
                    showError('An unexpected error occurred. Please try again.');
                    return;
                }

                if (xhr.status === 200 && resp.success) {
                    closeSignup();
                    showSuccess(resp.message || 'Account created successfully! Please log in.');
                    setTimeout(function () {
                        if (typeof window.openLoginModal === 'function') {
                            window.openLoginModal();
                        }
                    }, 800);
                } else {
                    setSubmitting(false);
                    if (resp.errors && typeof resp.errors === 'object') {
                        var firstError = '';
                        Object.keys(resp.errors).forEach(function (key) {
                            var fieldId = fieldMap[key];
                            if (fieldId) {
                                showFieldError(fieldId, resp.errors[key]);
                            }
                            if (!firstError) firstError = resp.errors[key];
                        });
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
                setSubmitting(false);
                showError('Network error. Please check your connection and try again.');
            };

            xhr.send(formData);
        });
    }

    // Login link — open Login modal
    var loginLink = document.getElementById('wgSignupLoginLink');
    if (loginLink) {
        loginLink.addEventListener('click', function (e) {
            e.preventDefault();
            closeSignup();
            if (typeof window.openLoginModal === 'function') {
                window.openLoginModal();
            }
        });
    }
})();
