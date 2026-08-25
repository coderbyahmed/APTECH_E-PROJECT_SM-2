/**
 * SOUND Group — Website Info Management (UI Only)
 * Handles: section edit modals (open/close), save updates the displayed
 *          section values, logo file selection demo (frontend only).
 */
(function () {
    'use strict';

    var MODALS = {
        site: 'wiSiteModal',
        home: 'wiHomeModal',
        contact: 'wiContactModal',
        social: 'wiSocialModal',
        footer: 'wiFooterModal'
    };

    function openModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    function val(id) {
        var el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function setField(id, value) {
        var el = document.getElementById(id);
        if (!el) return;
        if (value) {
            el.textContent = value;
            el.classList.remove('wi-field__value--empty');
        } else {
            el.textContent = 'Not set';
            el.classList.add('wi-field__value--empty');
        }
    }

    function initialsFrom(text) {
        var words = text.trim().split(/\s+/);
        var first = (words[0] || '').charAt(0) || '?';
        var second = words.length > 1
            ? (words[1].charAt(0) || '?')
            : ((words[0] || '').charAt(1) || '?');
        return (first + second).toUpperCase();
    }

    // --- Open buttons ---
    Object.keys(MODALS).forEach(function (key) {
        document.querySelectorAll('[data-wi-open="' + key + '"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openModal(MODALS[key]);
            });
        });
    });

    // --- Close buttons ---
    Object.keys(MODALS).forEach(function (key) {
        document.querySelectorAll('[data-wi-close="' + key + '"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeModal(MODALS[key]);
            });
        });
    });

    // --- Close on Escape ---
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            Object.keys(MODALS).forEach(function (key) {
                closeModal(MODALS[key]);
            });
        }
    });

    // --- Close on overlay click ---
    Object.keys(MODALS).forEach(function (key) {
        var modal = document.getElementById(MODALS[key]);
        if (!modal) return;
        var overlay = modal.querySelector('.sg-modal__overlay');
        if (overlay) {
            overlay.addEventListener('click', function () {
                closeModal(MODALS[key]);
            });
        }
    });

    // --- Logo file selection (frontend only) ---
    var logoPickerBtn = document.getElementById('wiLogoPickerBtn');
    var logoInput = document.getElementById('wiLogoInput');
    var logoFileName = document.getElementById('wiLogoFileName');

    if (logoPickerBtn && logoInput) {
        logoPickerBtn.addEventListener('click', function () {
            logoInput.click();
        });
    }

    if (logoInput && logoFileName) {
        logoInput.addEventListener('change', function () {
            logoFileName.textContent = (logoInput.files && logoInput.files[0])
                ? logoInput.files[0].name
                : 'No file chosen';
        });
    }

    // --- Save: Site / Company Information ---
    var siteSave = document.getElementById('wiSiteSaveBtn');
    if (siteSave) {
        siteSave.addEventListener('click', function () {
            var name = val('wiWebsiteName');
            if (!name) {
                document.getElementById('wiWebsiteName').focus();
                return;
            }
            setText('wiSiteNameValue', name);
            setText('wiShortDescValue', val('wiShortDesc'));
            setText('wiAboutValue', val('wiAbout'));
            if (logoInput && logoInput.files && logoInput.files[0] && logoFileName) {
                setText('wiLogoName', logoFileName.textContent);
            }
            var badge = document.getElementById('wiLogoBadge');
            if (name && badge) {
                badge.textContent = initialsFrom(name);
            }
            closeModal('wiSiteModal');
            if (typeof showSuccess === 'function') {
                showSuccess('Site information updated successfully.', 2000);
            }
        });
    }

    // --- Save: Home Page Content ---
    var homeSave = document.getElementById('wiHomeSaveBtn');
    if (homeSave) {
        homeSave.addEventListener('click', function () {
            setText('wiHomeHeadingValue', val('wiHomeHeading'));
            setText('wiHomeIntroValue', val('wiHomeIntro'));
            setText('wiFeaturedHeadingValue', val('wiFeaturedHeading'));
            setText('wiLatestMusicHeadingValue', val('wiLatestMusicHeading'));
            setText('wiLatestVideoHeadingValue', val('wiLatestVideoHeading'));
            closeModal('wiHomeModal');
            if (typeof showSuccess === 'function') {
                showSuccess('Home page content updated successfully.', 2000);
            }
        });
    }

    // --- Save: Contact Information ---
    var contactSave = document.getElementById('wiContactSaveBtn');
    if (contactSave) {
        contactSave.addEventListener('click', function () {
            var email = val('wiContactEmail');
            if (!email) {
                document.getElementById('wiContactEmail').focus();
                return;
            }
            setText('wiContactEmailValue', email);
            setText('wiContactPhoneValue', val('wiContactPhone'));
            setField('wiContactAddressValue', val('wiContactAddress'));
            closeModal('wiContactModal');
            if (typeof showSuccess === 'function') {
                showSuccess('Contact information updated successfully.', 2000);
            }
        });
    }

    // --- Save: Social Media ---
    var socialSave = document.getElementById('wiSocialSaveBtn');
    if (socialSave) {
        socialSave.addEventListener('click', function () {
            setField('wiFacebookValue', val('wiFacebook'));
            setField('wiGithubValue', val('wiGithub'));
            setField('wiLinkedInValue', val('wiLinkedIn'));
            setField('wiTikTokValue', val('wiTikTok'));
            closeModal('wiSocialModal');
            if (typeof showSuccess === 'function') {
                showSuccess('Social media links updated successfully.', 2000);
            }
        });
    }

    // --- Save: Footer Information ---
    var footerSave = document.getElementById('wiFooterSaveBtn');
    if (footerSave) {
        footerSave.addEventListener('click', function () {
            setText('wiFooterDescValue', val('wiFooterDesc'));
            setText('wiCopyrightValue', val('wiCopyright'));
            closeModal('wiFooterModal');
            if (typeof showSuccess === 'function') {
                showSuccess('Footer information updated successfully.', 2000);
            }
        });
    }

    // --- Form submit prevention ---
    document.querySelectorAll('.wi-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
        });
    });
})();