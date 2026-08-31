/**
 * SOUND Group — My Profile Modal
 * Two-view flow: view profile -> edit profile.
 */
(function () {
    'use strict';

    var modal = document.getElementById('myProfileModal');
    if (!modal) return;

    var HANDLER_URL = (window.APP_BASE_URL || '') + '/backend/handlers/admin-profile-handler.php';
    var trigger     = document.getElementById('myProfileTrigger');
    var editForm    = document.getElementById('mpEditForm');
    var saveBtn     = document.getElementById('mpSaveBtn');
    var editBtn     = document.getElementById('mpEditProfileBtn');
    var backBtn     = document.getElementById('mpBackToView');

    var nameInput    = document.getElementById('mpEditName');
    var emailInput   = document.getElementById('mpEditEmail');
    var addressInput = document.getElementById('mpEditAddress');
    var imageInput   = document.getElementById('mpProfileImageInput');
    var removeImgBtn = document.getElementById('mpRemoveImageBtn');

    var currentData = null;
    var selectedFile = null;

    /* ------------------------------------------
       Utility helpers
       ------------------------------------------ */
    function showStep(id) {
        modal.querySelectorAll('.sg-modal__step').forEach(function (step) {
            step.hidden = step.id !== id;
        });
    }

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        showStep('mpViewStep');
        loadProfile();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        resetEditForm();
        showStep('mpViewStep');
    }

    function resetEditForm() {
        if (editForm) editForm.reset();
        selectedFile = null;
        if (imageInput) imageInput.value = '';
        if (removeImgBtn) removeImgBtn.style.display = 'none';
        clearImagePreview();
        // Re-render edit preview from current data
        if (currentData) {
            setEditPreview(currentData.profile_image, currentData.name);
        }
    }

    function formatDate(ts) {
        if (!ts || ts === '0000-00-00 00:00:00') return '—';
        var d = new Date(ts.replace(/-/g, '/'));
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var h = d.getHours();
        var m = d.getMinutes();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var mStr = m < 10 ? '0' + m : '' + m;
        return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ', ' + h + ':' + mStr + ' ' + ampm;
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str || ''));
        return div.innerHTML;
    }

    function getInitials(name) {
        if (!name) return '?';
        var parts = name.trim().split(/\s+/);
        var initials = parts[0].charAt(0).toUpperCase();
        if (parts.length > 1) {
            initials += parts[parts.length - 1].charAt(0).toUpperCase();
        } else if (parts[0].length > 1) {
            initials += parts[0].charAt(1).toUpperCase();
        }
        return initials;
    }

    function normalizeImagePath(path) {
        if (!path) return '';
        if (path.indexOf('http') === 0) return path;
        if (path.charAt(0) === '/') return path;
        return (window.APP_BASE_URL || '') + '/' + path.replace(/^\//, '');
    }

    /* ------------------------------------------
       Avatar rendering
       ------------------------------------------ */
    function setViewAvatar(imgPath, name) {
        var avatar   = document.getElementById('mpViewAvatar');
        var img      = document.getElementById('mpViewAvatarImg');
        var initials = document.getElementById('mpViewAvatarInitials');

        if (imgPath) {
            img.src = normalizeImagePath(imgPath);
            img.style.display = 'block';
            initials.style.display = 'none';
        } else {
            img.style.display = 'none';
            initials.style.display = '';
            initials.textContent = getInitials(name);
        }
    }

    function setEditPreview(imgPath, name) {
        var previewImg      = document.getElementById('mpEditPreviewImg');
        var previewInitials = document.getElementById('mpEditPreviewInitials');

        if (selectedFile) {
            // Local preview from selected file
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewInitials.style.display = 'none';
            };
            reader.readAsDataURL(selectedFile);
        } else if (imgPath) {
            previewImg.src = normalizeImagePath(imgPath);
            previewImg.style.display = 'block';
            previewInitials.style.display = 'none';
        } else {
            previewImg.style.display = 'none';
            previewInitials.style.display = '';
            previewInitials.textContent = getInitials(nameInput ? nameInput.value : '');
        }
    }

    function clearImagePreview() {
        var previewImg      = document.getElementById('mpEditPreviewImg');
        var previewInitials = document.getElementById('mpEditPreviewInitials');
        if (previewImg) previewImg.style.display = 'none';
        if (previewInitials) {
            previewInitials.style.display = '';
            previewInitials.textContent = getInitials(nameInput ? nameInput.value : '');
        }
    }

    /* ------------------------------------------
       Load profile data
       ------------------------------------------ */
    function loadProfile() {
        var fd = new FormData();
        fd.append('action', 'get_profile');

        fetch(HANDLER_URL, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) {
                    showError(data.error || 'Failed to load profile.');
                    return;
                }
                currentData = data.record;
                renderViewProfile(currentData);
                populateEditForm(currentData);
            })
            .catch(function () {
                showError('Could not load profile. Please try again.');
            });
    }

    function renderViewProfile(rec) {
        var nameEl  = document.getElementById('mpViewName');
        var emailEl = document.getElementById('mpViewEmail');
        var addrEl  = document.getElementById('mpViewAddress');
        var crEl    = document.getElementById('mpViewCreated');
        var upEl    = document.getElementById('mpViewUpdated');

        if (nameEl)  nameEl.textContent  = rec.name || '—';
        if (emailEl) emailEl.textContent = rec.email || '—';
        if (addrEl)  addrEl.textContent  = rec.address || 'Not provided';
        if (crEl)    crEl.textContent    = formatDate(rec.created_at);
        if (upEl)    upEl.textContent    = formatDate(rec.updated_at);

        setViewAvatar(rec.profile_image, rec.name);
    }

    function populateEditForm(rec) {
        if (nameInput)    nameInput.value    = rec.name || '';
        if (emailInput)   emailInput.value   = rec.email || '';
        if (addressInput) addressInput.value = rec.address || '';
        setEditPreview(rec.profile_image, rec.name);
    }

    /* ------------------------------------------
       Image upload handling
       ------------------------------------------ */
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            var file = imageInput.files && imageInput.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                showError('Profile image must not exceed 2MB.');
                imageInput.value = '';
                return;
            }

            var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (allowedTypes.indexOf(file.type) === -1) {
                showError('Invalid image type. Allowed: JPG, PNG, WebP.');
                imageInput.value = '';
                return;
            }

            selectedFile = file;
            setEditPreview(null, nameInput ? nameInput.value : '');
            if (removeImgBtn) removeImgBtn.style.display = '';
        });
    }

    if (removeImgBtn) {
        removeImgBtn.addEventListener('click', function () {
            selectedFile = null;
            if (imageInput) imageInput.value = '';
            removeImgBtn.style.display = 'none';
            clearImagePreview();
            // Set preview back to initials
            if (currentData) {
                setEditPreview(currentData.profile_image, currentData.name);
            }
        });
    }

    /* ------------------------------------------
       Form submission — Update Profile
       ------------------------------------------ */
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var nameVal = nameInput ? nameInput.value.trim() : '';

            if (!nameVal) {
                showError('Name is required.');
                if (nameInput) nameInput.focus();
                return;
            }

            startButtonLoading(saveBtn, 'Saving...');

            var fd = new FormData(editForm);

            var controller = new AbortController();
            var timeoutId = setTimeout(function () { controller.abort(); }, 10000);

            fetch(editForm.dataset.endpoint, {
                method: 'POST',
                body: fd,
                signal: controller.signal
            })
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
                    stopButtonLoading(saveBtn);
                    return;
                }

                // Update current data
                currentData = r.data.record;

                // Update navbar avatar/name
                updateNavbarProfile(r.data.record);

                // Show success notification
                showSuccess(r.data.message || 'Profile updated successfully.', 3000);

                // Switch back to view mode
                renderViewProfile(currentData);
                populateEditForm(currentData);
                showStep('mpViewStep');
                stopButtonLoading(saveBtn);
            })
            .catch(function () {
                clearTimeout(timeoutId);
                showError('Something went wrong. Server could not be reached. Please try again.');
                stopButtonLoading(saveBtn);
            });
        });
    }

    /* ------------------------------------------
       Update navbar profile display
       ------------------------------------------ */
    function updateNavbarProfile(rec) {
        // Update navbar name
        var navName = document.querySelector('.admin-navbar__name');
        if (navName) navName.textContent = rec.name || '';

        // Update navbar avatar - check for image or initials
        var navAvatar = document.querySelector('.admin-navbar__avatar');
        if (navAvatar) {
            if (rec.profile_image) {
                navAvatar.innerHTML = '<img src="' + escapeHtml(normalizeImagePath(rec.profile_image)) + '" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
            } else {
                navAvatar.textContent = getInitials(rec.name);
            }
        }

        // Update sidebar brand if present
        // Session-based name will be used on next page load
    }

    /* ------------------------------------------
       Open / Close event listeners
       ------------------------------------------ */
    if (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openModal();
        });
    }

    if (editBtn) {
        editBtn.addEventListener('click', function () {
            showStep('mpEditStep');
            if (nameInput) nameInput.focus();
        });
    }

    if (backBtn) {
        backBtn.addEventListener('click', function () {
            resetEditForm();
            showStep('mpViewStep');
        });
    }

    modal.querySelectorAll('[data-mp-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
