/**
 * SOUND Group — Change Password Modal
 * Two-step flow: verify password -> new password.
 */

(function () {
    'use strict';

    var modal = document.getElementById('changePasswordModal');
    if (!modal) {
        return;
    }

    var trigger = document.getElementById('changePasswordTrigger');
    var verifyForm = document.getElementById('cpVerifyForm');
    var updateForm = document.getElementById('cpUpdateForm');
    var verifyBtn = document.getElementById('cpVerifyBtn');
    var updateBtn = document.getElementById('cpUpdateBtn');
    var currentPassword = document.getElementById('cpCurrentPassword');
    var newPassword = document.getElementById('cpNewPassword');
    var confirmPassword = document.getElementById('cpConfirmPassword');

    function showStep(id) {
        var steps = modal.querySelectorAll('.sg-modal__step');
        steps.forEach(function (step) {
            step.hidden = step.id !== id;
        });
    }

    function openModal() {
        resetModal();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        currentPassword.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        resetModal();
    }

    function resetModal() {
        currentPassword.value = '';
        newPassword.value = '';
        confirmPassword.value = '';
        showStep('cpStep1');
    }

    function postForm(form, button, onSuccess) {
        var fd = new FormData(form);

        var controller = new AbortController();
        var timeoutId = setTimeout(function () {
            controller.abort();
        }, 10000);

        function restoreButton() {
            clearTimeout(timeoutId);
            if (button) {
                stopButtonLoading(button);
            }
        }

        fetch(form.dataset.endpoint, { method: 'POST', body: fd, signal: controller.signal })
            .then(function (res) {
                clearTimeout(timeoutId);
                return res.json().then(function (data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function (r) {
                if (!r.ok || !r.data.success) {
                    if (r.data.redirect) {
                        window.location.href = r.data.redirect;
                        return;
                    }
                    showError(r.data.error || 'Something went wrong. Please try again.');
                    restoreButton();
                    return;
                }
                onSuccess(r.data);
            })
            .catch(function () {
                showError('Something went wrong. Server could not be reached. Please try again.');
                restoreButton();
            });
    }

    // --- Eye toggles ---
    modal.querySelectorAll('[data-sg-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
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
        });
    });

    // --- Open / close ---
    if (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openModal();
        });
    }

    modal.querySelectorAll('[data-sg-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    // --- Back navigation ---
    modal.querySelectorAll('[data-sg-back]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showStep(btn.dataset.sgBack);
        });
    });

    // --- Step 1: verify current password ---
    verifyForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!currentPassword.value) {
            showError('Please enter your current password.');
            currentPassword.focus();
            return;
        }
        startButtonLoading(verifyBtn, 'Verifying...');
        postForm(verifyForm, verifyBtn, function () {
            showSuccess('Current password verified. Please set your new password.', 3000);
            setTimeout(function () {
                showStep('cpStep2');
                newPassword.focus();
            }, 900);
        });
    });

    // --- Step 2: update password ---
    updateForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var password = newPassword.value;

        if (!password) {
            showError('The new password field is required.');
            newPassword.focus();
            return;
        }
        if (password.length < 8) {
            showError('The new password must be at least 8 characters.');
            newPassword.focus();
            return;
        }
        if (password !== confirmPassword.value) {
            showError('The password confirmation does not match.');
            confirmPassword.focus();
            return;
        }

        startButtonLoading(updateBtn, 'Updating...');
        postForm(updateForm, updateBtn, function (data) {
            window.location.href = data.redirect;
        });
    });
})();