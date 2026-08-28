(function () {
    'use strict';

    // Header scroll state
    var header = document.getElementById('wgHeader');
    if (header) {
        var onScroll = function () {
            if (window.scrollY > 60) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // Scroll-triggered animations
    var animatedEls = document.querySelectorAll('[data-animate]');
    if (animatedEls.length) {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            animatedEls.forEach(function (el) { el.classList.add('is-visible'); });
        } else if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
            animatedEls.forEach(function (el) { observer.observe(el); });
        } else {
            animatedEls.forEach(function (el) { el.classList.add('is-visible'); });
        }
    }

    // Contact form — validation + backend submission
    var form = document.getElementById('contactForm');
    var submitBtn = document.getElementById('contactSubmit');

    if (!form) return;

    var fields = {
        name:    { el: document.getElementById('contactName'),    errorEl: document.getElementById('contactNameError'),    fieldWrap: null },
        email:   { el: document.getElementById('contactEmail'),   errorEl: document.getElementById('contactEmailError'),   fieldWrap: null },
        phone:   { el: document.getElementById('contactPhone'),   errorEl: document.getElementById('contactPhoneError'),   fieldWrap: null },
        inquiry: { el: document.getElementById('contactInquiry'), errorEl: document.getElementById('contactInquiryError'), fieldWrap: null },
        subject: { el: document.getElementById('contactSubject'), errorEl: document.getElementById('contactSubjectError'), fieldWrap: null },
        message: { el: document.getElementById('contactMessage'), errorEl: document.getElementById('contactMessageError'), fieldWrap: null }
    };

    // Cache parent .wg-contact-form__field wrappers
    Object.keys(fields).forEach(function (key) {
        var f = fields[key];
        if (f.el) {
            f.fieldWrap = f.el.closest('.wg-contact-form__field');
        }
    });

    function showFieldError(key, msg) {
        var f = fields[key];
        if (!f || !f.el) return;
        if (f.fieldWrap) f.fieldWrap.classList.add('is-error');
        if (f.errorEl) {
            f.errorEl.textContent = msg;
            f.errorEl.classList.add('is-visible');
        }
    }

    function clearFieldError(key) {
        var f = fields[key];
        if (!f || !f.el) return;
        if (f.fieldWrap) f.fieldWrap.classList.remove('is-error');
        if (f.errorEl) {
            f.errorEl.textContent = '';
            f.errorEl.classList.remove('is-visible');
        }
    }

    function clearAllErrors() {
        Object.keys(fields).forEach(function (key) {
            clearFieldError(key);
        });
    }

    function validate() {
        clearAllErrors();
        var firstError = null;

        var nameVal = fields.name.el ? fields.name.el.value.trim() : '';
        if (!nameVal) {
            showFieldError('name', 'Please enter your full name.');
            if (!firstError) firstError = fields.name;
        }

        var emailVal = fields.email.el ? fields.email.el.value.trim() : '';
        if (!emailVal) {
            showFieldError('email', 'Please enter your email address.');
            if (!firstError) firstError = fields.email;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            showFieldError('email', 'Please enter a valid email address.');
            if (!firstError) firstError = fields.email;
        }

        var inquiryVal = fields.inquiry.el ? fields.inquiry.el.value : '';
        if (!inquiryVal) {
            showFieldError('inquiry', 'Please select an inquiry type.');
            if (!firstError) firstError = fields.inquiry;
        }

        var subjectVal = fields.subject.el ? fields.subject.el.value.trim() : '';
        if (!subjectVal) {
            showFieldError('subject', 'Please enter a subject.');
            if (!firstError) firstError = fields.subject;
        }

        var messageVal = fields.message.el ? fields.message.el.value.trim() : '';
        if (!messageVal) {
            showFieldError('message', 'Please enter your message.');
            if (!firstError) firstError = fields.message;
        }

        return firstError;
    }

    // Clear individual field errors on input
    Object.keys(fields).forEach(function (key) {
        var f = fields[key];
        if (f.el) {
            f.el.addEventListener('input', function () {
                clearFieldError(key);
            });
            f.el.addEventListener('change', function () {
                clearFieldError(key);
            });
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        clearAllErrors();

        var firstInvalid = validate();
        if (firstInvalid) {
            if (firstInvalid.el) firstInvalid.el.focus();
            return;
        }

        // Start loading
        if (typeof window.startButtonLoading === 'function') {
            window.startButtonLoading(submitBtn, 'Sending...');
        } else if (submitBtn) {
            submitBtn.disabled = true;
        }

        var formData = new FormData();
        formData.append('action', 'submit');
        formData.append('name', fields.name.el.value.trim());
        formData.append('email', fields.email.el.value.trim());
        formData.append('phone', fields.phone.el ? fields.phone.el.value.trim() : '');
        formData.append('inquiry_type', fields.inquiry.el.value);
        formData.append('subject', fields.subject.el.value.trim());
        formData.append('message', fields.message.el.value.trim());

        fetch('/Aptech_E_Project_02/sound_management/backend/handlers/contact-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                if (typeof window.showSuccess === 'function') {
                    window.showSuccess(data.message || 'Your message has been submitted successfully.');
                }
                form.reset();
                clearAllErrors();
            } else {
                var errorMsg = data.error || 'Something went wrong. Please try again.';
                if (typeof window.showError === 'function') {
                    window.showError(errorMsg);
                }
                // Map backend errors to specific fields
                var errLower = errorMsg.toLowerCase();
                if (errLower.indexOf('name') !== -1) {
                    showFieldError('name', errorMsg);
                } else if (errLower.indexOf('email') !== -1) {
                    showFieldError('email', errorMsg);
                } else if (errLower.indexOf('inquiry') !== -1) {
                    showFieldError('inquiry', errorMsg);
                } else if (errLower.indexOf('subject') !== -1) {
                    showFieldError('subject', errorMsg);
                } else if (errLower.indexOf('message') !== -1) {
                    showFieldError('message', errorMsg);
                }
            }
        })
        .catch(function () {
            if (typeof window.showError === 'function') {
                window.showError('Network error. Please check your connection and try again.');
            }
        })
        .finally(function () {
            if (typeof window.stopButtonLoading === 'function') {
                window.stopButtonLoading(submitBtn);
            } else if (submitBtn) {
                submitBtn.disabled = false;
            }
        });
    });

    // Smooth scroll for "Get In Touch" anchor link
    var getInTouchLinks = document.querySelectorAll('a[href="#contactForm"]');
    getInTouchLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = document.getElementById('contactForm');
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
})();
