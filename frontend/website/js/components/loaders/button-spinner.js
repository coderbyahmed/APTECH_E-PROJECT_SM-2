/**
 * SOUND Group — Website Button Spinner Component
 * Lightweight loading state for buttons
 */

(function () {
    'use strict';

    var SPINNER_HTML = '<span class="wg-spinner"></span>';

    function startButtonLoading(button, loadingText) {
        if (!button || button.dataset.wgLoading === 'true') {
            return;
        }

        button.dataset.wgOriginalContent = button.innerHTML;
        button.dataset.wgLoading = 'true';
        button.disabled = true;
        button.classList.add('wg-btn-loading');
        button.innerHTML = SPINNER_HTML + '<span>' + (loadingText || 'Loading...') + '</span>';
    }

    function stopButtonLoading(button) {
        if (!button || button.dataset.wgLoading !== 'true') {
            return;
        }

        button.innerHTML = button.dataset.wgOriginalContent;
        button.disabled = false;
        button.classList.remove('wg-btn-loading');
        delete button.dataset.wgLoading;
        delete button.dataset.wgOriginalContent;
    }

    window.startButtonLoading = startButtonLoading;
    window.stopButtonLoading = stopButtonLoading;
})();
