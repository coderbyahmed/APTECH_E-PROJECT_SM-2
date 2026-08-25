/**
 * SOUND Group — Admin Navbar
 * Profile dropdown toggle + live date/time clock.
 */

(function () {
    'use strict';

    var MONTHS = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    function padZero(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function formatDate(d) {
        return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
    }

    function formatTime(d) {
        return padZero(d.getHours()) + ':' + padZero(d.getMinutes()) + ':' + padZero(d.getSeconds());
    }

    function initDropdown() {
        var profile = document.getElementById('adminProfileDropdown');
        var toggle = document.getElementById('adminProfileToggle');
        var menu = document.getElementById('adminProfileMenu');

        if (!profile || !toggle || !menu) {
            return;
        }

        function openDropdown() {
            profile.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
        }

        function closeDropdown() {
            profile.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        function isOpen() {
            return profile.classList.contains('is-open');
        }

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isOpen()) { closeDropdown(); } else { openDropdown(); }
        });

        document.addEventListener('click', function (e) {
            if (isOpen() && !profile.contains(e.target)) {
                closeDropdown();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen()) {
                closeDropdown();
                toggle.focus();
            }
        });
    }

    function initClock() {
        var dateEl = document.getElementById('adminNavbarDate');
        var timeEl = document.getElementById('adminNavbarTime');

        if (!dateEl && !timeEl) {
            return;
        }

        function tick() {
            var now = new Date();
            if (dateEl) { dateEl.textContent = formatDate(now); }
            if (timeEl) { timeEl.textContent = formatTime(now); }
        }

        tick();
        setInterval(tick, 1000);
    }

    function init() {
        initDropdown();
        initClock();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
