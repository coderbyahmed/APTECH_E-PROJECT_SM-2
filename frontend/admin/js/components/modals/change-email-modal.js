/**
 * SOUND Group — Change Email Modal
 * Two-step flow: verify password -> new email + 4-digit OTP.
 */

(function () {
    'use strict';

    var modal = document.getElementById('changeEmailModal');
    if (!modal) {
        return;
    }

    var trigger = document.getElementById('changeEmailTrigger');
    var verifyForm = document.getElementById('ceVerifyForm');
    var sendForm = document.getElementById('ceSendForm');
    var otpForm = document.getElementById('ceOtpForm');
    var verifyBtn = document.getElementById('ceVerifyBtn');
    var sendOtpBtn = document.getElementById('ceSendOtpBtn');
    var otpBtn = document.getElementById('ceOtpBtn');
    var resendBtn = document.getElementById('ceResendBtn');
    var currentPassword = document.getElementById('ceCurrentPassword');
    var newEmailInput = document.getElementById('ceNewEmail');
    var otpEmailLabel = document.getElementById('ceOtpEmail');
    var otpHidden = document.getElementById('ceOtpHidden');
    var expiresAtInput = document.getElementById('ceExpiresAt');
    var countdownTimer = document.getElementById('ceCountdownTimer');
    var boxes = modal.querySelectorAll('.sg-otp-box');

    var countdownInterval = null;
    var otpActive = false;

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

        if (otpActive) {
            // Clean up any pending OTP + session keys on the server.
            var fd = new FormData();
            fd.append('csrf_token', getCsrfToken());
            fd.append('action', 'cancel');
            fetch(sendForm.dataset.endpoint, { method: 'POST', body: fd }).catch(function () {});
        }

        resetModal();
    }

    function resetModal() {
        clearInterval(countdownInterval);
        countdownInterval = null;
        otpActive = false;

        currentPassword.value = '';
        newEmailInput.value = '';
        otpHidden.value = '';
        expiresAtInput.value = '';
        otpEmailLabel.textContent = 'your new email';
        countdownTimer.textContent = '03:00';
        otpBtn.disabled = false;
        resendBtn.disabled = false;

        boxes.forEach(function (box) {
            box.value = '';
            box.disabled = false;
        });

        showStep('ceStep1');
    }

    function getCsrfToken() {
        return verifyForm.querySelector('input[name="csrf_token"]').value;
    }

    function postForm(form, extraData, button, onSuccess) {
        var fd = new FormData(form);
        if (extraData) {
            Object.keys(extraData).forEach(function (key) {
                fd.append(key, extraData[key]);
            });
        }

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

    function updateHiddenOtp() {
        var val = '';
        boxes.forEach(function (box) { val += box.value; });
        otpHidden.value = val;
    }

    function focusBox(index) {
        if (index >= 0 && index < boxes.length) {
            boxes[index].focus();
        }
    }

    function startCountdown(expiresAtUnix) {
        clearInterval(countdownInterval);
        var expiresAt = parseInt(expiresAtUnix, 10) * 1000;

        otpBtn.disabled = false;
        resendBtn.disabled = true;

        function tick() {
            var diff = expiresAt - Date.now();

            if (diff <= 0) {
                clearInterval(countdownInterval);
                countdownInterval = null;
                countdownTimer.textContent = '00:00';
                otpBtn.disabled = true;
                resendBtn.disabled = false;
                return;
            }

            var totalSeconds = Math.floor(diff / 1000);
            var minutes = Math.floor(totalSeconds / 60);
            var seconds = totalSeconds % 60;
            countdownTimer.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        }

        tick();
        countdownInterval = setInterval(tick, 500);
    }

    // --- OTP box behavior (mirrors admin verify-otp page) ---
    boxes.forEach(function (box, i) {
        box.addEventListener('input', function (e) {
            var val = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = val;
            if (val && i < boxes.length - 1) {
                focusBox(i + 1);
            }
            updateHiddenOtp();
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!box.value && i > 0) {
                    boxes[i - 1].value = '';
                    focusBox(i - 1);
                } else {
                    box.value = '';
                }
                updateHiddenOtp();
                e.preventDefault();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                focusBox(i - 1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                focusBox(i + 1);
            }
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 4);
            for (var j = 0; j < pasteData.length && j < boxes.length; j++) {
                boxes[j].value = pasteData[j];
            }
            focusBox(Math.min(pasteData.length, boxes.length - 1));
            updateHiddenOtp();
        });

        box.addEventListener('focus', function () {
            box.select();
        });
    });

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
        postForm(verifyForm, null, verifyBtn, function () {
            showSuccess('Current password verified. Please set your new email.', 3000);
            setTimeout(function () {
                showStep('ceStep2');
                newEmailInput.focus();
            }, 900);
        });
    });

    // --- Step 2: send OTP to new email ---
    sendForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var email = newEmailInput.value.trim();

        if (!email) {
            showError('The new email address is required.');
            newEmailInput.focus();
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('Please enter a valid email address.');
            newEmailInput.focus();
            return;
        }

        startButtonLoading(sendOtpBtn, 'Sending...');
        postForm(sendForm, null, sendOtpBtn, function (data) {
            stopButtonLoading(sendOtpBtn);
            otpActive = true;
            otpEmailLabel.textContent = data.sent_to;
            boxes.forEach(function (box) { box.value = ''; });
            updateHiddenOtp();
            showStep('ceStep3');
            startCountdown(data.expires_at);
            showSuccess('A verification code has been sent to your new email address.');
            focusBox(0);
        });
    });

    // --- Step 3: verify OTP ---
    otpForm.addEventListener('submit', function (e) {
        e.preventDefault();
        updateHiddenOtp();

        if (otpHidden.value.length < 4) {
            showError('Please enter the 4-digit verification code.');
            return;
        }

        startButtonLoading(otpBtn, 'Verifying...');
        postForm(otpForm, null, otpBtn, function (data) {
            window.location.href = data.redirect;
        });
    });

    // --- Resend OTP ---
    resendBtn.addEventListener('click', function () {
        startButtonLoading(resendBtn, 'Sending...');
        postForm(sendForm, { action: 'resend_otp' }, resendBtn, function (data) {
            stopButtonLoading(resendBtn);
            otpEmailLabel.textContent = data.sent_to;
            boxes.forEach(function (box) { box.value = ''; });
            updateHiddenOtp();
            startCountdown(data.expires_at);
            showSuccess('A new verification code has been sent.');
            focusBox(0);
        });
    });
})();
