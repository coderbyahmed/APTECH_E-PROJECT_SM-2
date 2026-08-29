/**
 * SOUND Group — Website Info Management (Database-connected)
 * Handles: section edit modals (open/close), AJAX save to backend,
 *          logo file selection, remove logo, live display updates.
 */
(function () {
    'use strict';

    var HANDLER_URL = (window.APP_BASE_URL || '') + '/backend/handlers/website-settings-handler.php';

    var MODALS = {
        site: 'wiSiteModal',
        contact: 'wiContactModal',
        social: 'wiSocialModal',
        footer: 'wiFooterModal'
    };

    var logoRemoved = false;

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

    /* -----------------------------------------------------------
       Collect ALL current form values and POST to backend.
       Uses FormData so file uploads work.
       ----------------------------------------------------------- */
    function collectAllSettings() {
        var fd = new FormData();
        fd.append('website_name',      val('wiWebsiteName'));
        fd.append('contact_email',     val('wiContactEmail'));
        fd.append('contact_phone',     val('wiContactPhone'));
        fd.append('contact_address',   val('wiContactAddress'));
        fd.append('facebook_url',      val('wiFacebook'));
        fd.append('tiktok_url',        val('wiTikTok'));
        fd.append('linkedin_url',      val('wiLinkedIn'));
        fd.append('github_url',        val('wiGithub'));
        fd.append('footer_description',val('wiFooterDesc'));
        fd.append('copyright_text',    val('wiCopyright'));

        var logoInput = document.getElementById('wiLogoInput');
        if (logoInput && logoInput.files && logoInput.files[0]) {
            fd.append('site_logo', logoInput.files[0]);
        }

        if (logoRemoved) {
            fd.append('remove_logo', '1');
        }

        return fd;
    }

    function postSettings(fd, onSuccess) {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) fd.append('csrf_token', meta.getAttribute('content'));

        var xhr = new XMLHttpRequest();
        xhr.open('POST', HANDLER_URL, true);
        xhr.responseType = 'json';
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.response && xhr.response.success) {
                onSuccess(xhr.response);
            } else {
                var msg = (xhr.response && xhr.response.error) ? xhr.response.error : 'Failed to save settings.';
                if (typeof showError === 'function') showError(msg, 3000);
            }
        };
        xhr.onerror = function () {
            if (typeof showError === 'function') showError('Server error. Please try again.', 3000);
        };
        xhr.send(fd);
    }

    /* -----------------------------------------------------------
       Update all display values on the page from settings object
       ----------------------------------------------------------- */
    function refreshDisplays(s) {
        setText('wiSiteNameValue', s.website_name);
        setText('wiContactEmailValue', s.contact_email);
        setText('wiContactPhoneValue', s.contact_phone);
        setField('wiContactAddressValue', s.contact_address);
        setField('wiFacebookValue', s.facebook_url);
        setField('wiGithubValue', s.github_url);
        setField('wiLinkedInValue', s.linkedin_url);
        setField('wiTikTokValue', s.tiktok_url);
        setText('wiFooterDescValue', s.footer_description);
        setText('wiCopyrightValue', s.copyright_text);

        if (s.site_logo) {
            var logoName = document.getElementById('wiLogoName');
            if (logoName) logoName.textContent = s.site_logo.split('/').pop();
            var logoFileName = document.getElementById('wiLogoFileName');
            if (logoFileName) logoFileName.textContent = s.site_logo.split('/').pop();
        }
    }

    /* -----------------------------------------------------------
       Update the logo display section (show image or badge)
       ----------------------------------------------------------- */
    function updateLogoDisplay(logoPath, websiteName) {
        var logoContainer = document.querySelector('#wiSectionSite .wi-logo');
        if (!logoContainer) return;

        var img = logoContainer.querySelector('img');
        var badge = logoContainer.querySelector('.wi-logo__badge');
        var nameEl = document.getElementById('wiLogoName');

        if (logoPath) {
            if (img) {
                img.src = logoPath;
            } else {
                if (badge) {
                    var newImg = document.createElement('img');
                    newImg.src = logoPath;
                    newImg.alt = 'Site Logo';
                    newImg.style.cssText = 'width:36px;height:36px;object-fit:contain;border-radius:6px;background:#f3f4f6;padding:2px;';
                    badge.parentNode.replaceChild(newImg, badge);
                }
            }
            if (nameEl) nameEl.textContent = logoPath.split('/').pop();
        } else {
            if (img) {
                var newBadge = document.createElement('span');
                newBadge.className = 'wi-logo__badge';
                newBadge.id = 'wiLogoBadge';
                newBadge.textContent = initialsFrom(websiteName || 'SG');
                img.parentNode.replaceChild(newBadge, img);
            } else if (badge) {
                badge.textContent = initialsFrom(websiteName || 'SG');
            }
            if (nameEl) nameEl.textContent = 'No logo uploaded';
        }
    }

    /* -----------------------------------------------------------
       Reset logo remove state in the modal
       ----------------------------------------------------------- */
    function resetLogoRemoveState() {
        logoRemoved = false;
        var hiddenInput = document.getElementById('wiLogoRemoved');
        if (hiddenInput) hiddenInput.value = '0';

        var removeBtn = document.getElementById('wiLogoRemoveBtn');
        if (removeBtn) {
            removeBtn.classList.remove('wi-btn-remove--active');
            removeBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Remove Logo';
        }

        var logoFileName = document.getElementById('wiLogoFileName');
        if (logoFileName && logoFileName.dataset.originalText) {
            logoFileName.textContent = logoFileName.dataset.originalText;
        }

        var logoInput = document.getElementById('wiLogoInput');
        if (logoInput) logoInput.value = '';
    }

    // --- Open buttons ---
    Object.keys(MODALS).forEach(function (key) {
        document.querySelectorAll('[data-wi-open="' + key + '"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (key === 'site') {
                    resetLogoRemoveState();
                    var logoFileName = document.getElementById('wiLogoFileName');
                    if (logoFileName) logoFileName.dataset.originalText = logoFileName.textContent;
                }
                openModal(MODALS[key]);
            });
        });
    });

    // --- Close buttons ---
    Object.keys(MODALS).forEach(function (key) {
        document.querySelectorAll('[data-wi-close="' + key + '"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (key === 'site') resetLogoRemoveState();
                closeModal(MODALS[key]);
            });
        });
    });

    // --- Close on Escape ---
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            Object.keys(MODALS).forEach(function (key) {
                if (key === 'site') resetLogoRemoveState();
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
                if (key === 'site') resetLogoRemoveState();
                closeModal(MODALS[key]);
            });
        }
    });

    // --- Logo file selection (frontend only — updates label) ---
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
            if (logoInput.files && logoInput.files[0]) {
                logoFileName.textContent = logoInput.files[0].name;
                if (logoRemoved) {
                    logoRemoved = false;
                    var hiddenInput = document.getElementById('wiLogoRemoved');
                    if (hiddenInput) hiddenInput.value = '0';
                    var removeBtn = document.getElementById('wiLogoRemoveBtn');
                    if (removeBtn) {
                        removeBtn.classList.remove('wi-btn-remove--active');
                        removeBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Remove Logo';
                    }
                }
            } else {
                logoFileName.textContent = 'No file chosen';
            }
        });
    }

    // --- Remove Logo button ---
    var logoRemoveBtn = document.getElementById('wiLogoRemoveBtn');
    if (logoRemoveBtn) {
        logoRemoveBtn.addEventListener('click', function () {
            logoRemoved = !logoRemoved;
            var hiddenInput = document.getElementById('wiLogoRemoved');

            if (logoRemoved) {
                hiddenInput.value = '1';
                logoRemoveBtn.classList.add('wi-btn-remove--active');
                logoRemoveBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Logo Removed';
                if (logoFileName) logoFileName.textContent = 'Logo will be removed';
                if (logoInput) logoInput.value = '';
            } else {
                resetLogoRemoveState();
            }
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
            var fd = collectAllSettings();
            postSettings(fd, function (resp) {
                setText('wiSiteNameValue', name);
                updateLogoDisplay(resp.settings.site_logo, name);
                closeModal('wiSiteModal');
                logoRemoved = false;
                if (typeof showSuccess === 'function') {
                    showSuccess('Site information updated successfully.', 2000);
                }
            });
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
            var fd = collectAllSettings();
            postSettings(fd, function (resp) {
                setText('wiContactEmailValue', email);
                setText('wiContactPhoneValue', val('wiContactPhone'));
                setField('wiContactAddressValue', val('wiContactAddress'));
                closeModal('wiContactModal');
                if (typeof showSuccess === 'function') {
                    showSuccess('Contact information updated successfully.', 2000);
                }
            });
        });
    }

    // --- Save: Social Media ---
    var socialSave = document.getElementById('wiSocialSaveBtn');
    if (socialSave) {
        socialSave.addEventListener('click', function () {
            var fd = collectAllSettings();
            postSettings(fd, function (resp) {
                setField('wiFacebookValue', val('wiFacebook'));
                setField('wiGithubValue', val('wiGithub'));
                setField('wiLinkedInValue', val('wiLinkedIn'));
                setField('wiTikTokValue', val('wiTikTok'));
                closeModal('wiSocialModal');
                if (typeof showSuccess === 'function') {
                    showSuccess('Social media links updated successfully.', 2000);
                }
            });
        });
    }

    // --- Save: Footer Information ---
    var footerSave = document.getElementById('wiFooterSaveBtn');
    if (footerSave) {
        footerSave.addEventListener('click', function () {
            var fd = collectAllSettings();
            postSettings(fd, function (resp) {
                setText('wiFooterDescValue', val('wiFooterDesc'));
                setText('wiCopyrightValue', val('wiCopyright'));
                closeModal('wiFooterModal');
                if (typeof showSuccess === 'function') {
                    showSuccess('Footer information updated successfully.', 2000);
                }
            });
        });
    }

    // --- Form submit prevention ---
    document.querySelectorAll('.wi-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
        });
    });
})();
