/**
 * SOUND Group — Admin Notification Component
 * Lightweight toast notification system
 */

(function () {
    'use strict';

    var ICONS = {
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };

    var DURATION = 2000;

    function getContainer() {
        var existing = document.getElementById('sg-notification');
        if (existing) {
            return existing;
        }

        var el = document.createElement('div');
        el.id = 'sg-notification';
        el.className = 'sg-notification';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.innerHTML = '<span class="sg-notification__icon"></span><span class="sg-notification__message"></span>';
        document.body.appendChild(el);
        return el;
    }

    function show(type, message, duration) {
        var el = getContainer();
        var icon = el.querySelector('.sg-notification__icon');
        var text = el.querySelector('.sg-notification__message');

        clearTimeout(el._hideTimer);
        clearTimeout(el._exitTimer);

        el.className = 'sg-notification sg-notification--' + type;
        icon.innerHTML = ICONS[type] || ICONS.info;
        text.textContent = message;

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.classList.add('is-visible');
            });
        });

        var delay = typeof duration === 'number' ? duration : DURATION;

        el._hideTimer = setTimeout(function () {
            el.classList.remove('is-visible');
            el.classList.add('is-exiting');
            el._exitTimer = setTimeout(function () {
                el.classList.remove('is-exiting');
            }, 300);
        }, delay);
    }

    window.showSuccess = function (message, duration) {
        show('success', message, duration);
    };

    window.showError = function (message, duration) {
        show('error', message, duration);
    };

    window.showWarning = function (message, duration) {
        show('warning', message, duration);
    };

    window.showInfo = function (message, duration) {
        show('info', message, duration);
    };
})();
