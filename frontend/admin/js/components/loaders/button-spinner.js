/**
 * SOUND Group — Admin Button Spinner Component
 * Lightweight loading state for buttons
 */

(function () {
    'use strict';

    var SPINNER_HTML = '<span class="sg-spinner"></span>';

    function startButtonLoading(button, loadingText) {
        if (!button || button.dataset.sgLoading === 'true') {
            return;
        }

        button.dataset.sgOriginalContent = button.innerHTML;
        button.dataset.sgLoading = 'true';
        button.disabled = true;
        button.classList.add('sg-btn-loading');
        button.innerHTML = SPINNER_HTML + '<span>' + (loadingText || 'Loading...') + '</span>';
    }

    function stopButtonLoading(button) {
        if (!button || button.dataset.sgLoading !== 'true') {
            return;
        }

        button.innerHTML = button.dataset.sgOriginalContent;
        button.disabled = false;
        button.classList.remove('sg-btn-loading');
        delete button.dataset.sgLoading;
        delete button.dataset.sgOriginalContent;
    }

    window.startButtonLoading = startButtonLoading;
    window.stopButtonLoading = stopButtonLoading;
})();
