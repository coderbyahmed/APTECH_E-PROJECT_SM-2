/**
 * SOUND Group — Admin Sidebar Collapse Component
 */

(function () {
    'use strict';

    var STORAGE_KEY = 'sg-admin-sidebar-collapsed';

    function init() {
        var sidebar = document.getElementById('adminSidebar');
        var collapseBtn = document.getElementById('sidebarCollapseToggle');

        if (!sidebar || !collapseBtn) {
            return;
        }

        var isMobile = function () {
            return window.innerWidth <= 1024;
        };

        function applySavedState() {
            if (isMobile()) {
                sidebar.classList.remove('is-collapsed');
                return;
            }
            var saved = localStorage.getItem(STORAGE_KEY);
            if (saved === 'true') {
                sidebar.classList.add('is-collapsed');
            } else {
                sidebar.classList.remove('is-collapsed');
            }
        }

        collapseBtn.addEventListener('click', function () {
            if (isMobile()) {
                return;
            }
            sidebar.classList.toggle('is-collapsed');
            var collapsed = sidebar.classList.contains('is-collapsed');
            try {
                localStorage.setItem(STORAGE_KEY, collapsed ? 'true' : 'false');
            } catch (e) {}
        });

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                applySavedState();
            }
        });

        applySavedState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
