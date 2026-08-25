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

    // Contact form — frontend validation only (no backend submission)
    var form = document.getElementById('contactForm');
    var errorEl = document.getElementById('formError');
    var successEl = document.getElementById('formSuccess');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        errorEl.style.display = 'none';
        successEl.style.display = 'none';

        var name = document.getElementById('contactName');
        var email = document.getElementById('contactEmail');
        var inquiry = document.getElementById('contactInquiry');
        var subject = document.getElementById('contactSubject');
        var message = document.getElementById('contactMessage');

        // Remove previous error states
        var inputs = form.querySelectorAll('.wg-contact-form__input');
        inputs.forEach(function (inp) { inp.style.borderColor = ''; });

        // Validate required fields
        var errors = [];

        if (!name.value.trim()) {
            errors.push('Please enter your full name.');
            name.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        }

        if (!email.value.trim()) {
            errors.push('Please enter your email address.');
            email.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            errors.push('Please enter a valid email address.');
            email.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        }

        if (!inquiry.value) {
            errors.push('Please select an inquiry type.');
            inquiry.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        }

        if (!subject.value.trim()) {
            errors.push('Please enter a subject.');
            subject.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        }

        if (!message.value.trim()) {
            errors.push('Please enter your message.');
            message.style.borderColor = 'rgba(239, 68, 68, 0.5)';
        }

        if (errors.length > 0) {
            errorEl.textContent = errors[0];
            errorEl.style.display = '';
            return;
        }

        // Success — no backend yet
        successEl.textContent = 'Thank you for your message! We will get back to you soon.';
        successEl.style.display = '';
        form.reset();
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
