(function () {
    'use strict';

    var overlay = document.getElementById('wgProfileOverlay');
    var modal = document.getElementById('wgProfileModal');
    var closeBtn = document.getElementById('wgProfileClose');
    var viewEl = document.getElementById('wgProfileView');
    var editEl = document.getElementById('wgProfileEdit');
    var editBtn = document.getElementById('wgProfileEditBtn');
    var cancelViewBtn = document.getElementById('wgProfileCancelViewBtn');
    var cancelEditBtn = document.getElementById('wgProfileCancelEditBtn');
    var form = document.getElementById('wgProfileForm');
    if (!overlay || !modal) return;

    var currentData = {};

    function getHandlerUrl() {
        return (window.APP_BASE_URL || '') + '/backend/handlers/user-profile-handler.php';
    }

    function openProfile() {
        fetchProfile();
        overlay.classList.add('is-open');
        document.body.classList.add('wg-profile-open');
    }

    function closeProfile() {
        overlay.classList.remove('is-open');
        document.body.classList.remove('wg-profile-open');
        showView();
        clearMessages();
    }

    function showView() {
        viewEl.style.display = '';
        editEl.style.display = 'none';
    }

    function showEdit() {
        viewEl.style.display = 'none';
        editEl.style.display = '';
        populateEditForm();
    }

    function clearMessages() {
        ['wgProfileViewError', 'wgProfileViewSuccess', 'wgProfileEditError', 'wgProfileEditSuccess'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.style.display = 'none'; el.textContent = ''; }
        });
    }

    function showError(id, msg) {
        var el = document.getElementById(id);
        if (el) { el.textContent = msg; el.style.display = ''; }
    }

    function showSuccess(id, msg) {
        var el = document.getElementById(id);
        if (el) { el.textContent = msg; el.style.display = ''; }
    }

    function fetchProfile() {
        var fd = new FormData();
        fd.append('action', 'get');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', getHandlerUrl(), true);
        xhr.responseType = 'json';
        xhr.onload = function () {
            var r = xhr.response;
            if (r && r.success && r.user) {
                currentData = r.user;
                populateView(r.user);
            }
        };
        xhr.send(fd);
    }

    function populateView(u) {
        document.getElementById('wgProfileViewName').textContent = u.full_name || '—';
        document.getElementById('wgProfileViewEmail').textContent = u.email || '—';
        document.getElementById('wgProfileViewPhone').textContent = u.phone || '—';
        document.getElementById('wgProfileViewAddress').textContent = u.address || '—';

        var avatar = document.getElementById('wgProfileViewAvatar');
        if (avatar) {
            if (u.profile_image) {
                var baseUrl = window.APP_BASE_URL || '';
                var imgSrc = (u.profile_image.indexOf('http') === 0) ? u.profile_image : (u.profile_image.indexOf('/') === 0 ? u.profile_image : '/' + u.profile_image);
                avatar.innerHTML = '<img src="' + (imgSrc.indexOf('http') === 0 ? imgSrc : baseUrl + imgSrc) + '?v=' + Date.now() + '" alt="' + (u.full_name || 'User') + '" class="wg-profile-avatar__img">';
            } else {
                var name = u.full_name || '?';
                var initial = name.charAt(0).toUpperCase();
                avatar.innerHTML = '<span class="wg-profile-avatar__initial" style="background-color:#8b5cf6;">' + initial + '</span>';
            }
        }
    }

    function populateEditForm() {
        document.getElementById('wgProfileName').value = currentData.full_name || '';
        document.getElementById('wgProfileEmail').value = currentData.email || '';
        document.getElementById('wgProfilePhone').value = currentData.phone || '';
        document.getElementById('wgProfileAddress').value = currentData.address || '';

        var editAvatarWrap = document.getElementById('wgProfileEditAvatarWrap');
        if (editAvatarWrap && currentData.profile_image) {
            var baseUrl = window.APP_BASE_URL || '';
            var imgSrc = (currentData.profile_image.indexOf('http') === 0) ? currentData.profile_image : (currentData.profile_image.indexOf('/') === 0 ? currentData.profile_image : '/' + currentData.profile_image);
            editAvatarWrap.innerHTML = '<img src="' + (imgSrc.indexOf('http') === 0 ? imgSrc : baseUrl + imgSrc) + '?v=' + Date.now() + '" alt="Profile" class="wg-profile-avatar__img" id="wgProfileEditAvatarImg"><div class="wg-profile-avatar__overlay" id="wgProfileChangeImageBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
        } else if (editAvatarWrap) {
            var name = currentData.full_name || '?';
            var initial = name.charAt(0).toUpperCase();
            editAvatarWrap.innerHTML = '<span class="wg-profile-avatar__initial" style="background-color:#8b5cf6;" id="wgProfileEditAvatarInitial">' + initial + '</span><div class="wg-profile-avatar__overlay" id="wgProfileChangeImageBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
        }
        rebindImageBtn();
        clearMessages();
    }

    function rebindImageBtn() {
        var btn = document.getElementById('wgProfileChangeImageBtn');
        var input = document.getElementById('wgProfileImageInput');
        if (btn && input) {
            btn.addEventListener('click', function () { input.click(); });
        }
    }

    // Expose globally
    window.openProfileModal = openProfile;

    // Profile button in dropdown
    var profileBtn = document.getElementById('wgUserProfileBtn');
    if (profileBtn) {
        profileBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openProfile();
        });
    }

    // Close button
    closeBtn.addEventListener('click', closeProfile);

    // Close on overlay
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeProfile();
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeProfile();
    });

    // Edit button
    editBtn.addEventListener('click', showEdit);

    // Cancel view → close
    cancelViewBtn.addEventListener('click', closeProfile);

    // Cancel edit → return to view
    cancelEditBtn.addEventListener('click', function () {
        showView();
        clearMessages();
    });

    // Image input preview
    var imageInput = document.getElementById('wgProfileImageInput');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            if (imageInput.files && imageInput.files.length > 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var wrap = document.getElementById('wgProfileEditAvatarWrap');
                    if (wrap) {
                        wrap.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="wg-profile-avatar__img" id="wgProfileEditAvatarImg"><div class="wg-profile-avatar__overlay" id="wgProfileChangeImageBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
                        rebindImageBtn();
                    }
                };
                reader.readAsDataURL(imageInput.files[0]);
            }
        });
    }

    // Form submit
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearMessages();

            var nameEl = document.getElementById('wgProfileName');
            var phoneEl = document.getElementById('wgProfilePhone');
            var addressEl = document.getElementById('wgProfileAddress');
            var saveBtn = document.getElementById('wgProfileSaveBtn');

            var hasError = false;

            nameEl.style.borderColor = '';
            phoneEl.style.borderColor = '';
            addressEl.style.borderColor = '';

            if (!nameEl.value.trim()) {
                nameEl.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                hasError = true;
            }
            if (!phoneEl.value.trim()) {
                phoneEl.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                hasError = true;
            } else {
                var digits = phoneEl.value.replace(/\D/g, '');
                if (digits.length !== 11) {
                    phoneEl.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                    hasError = true;
                }
            }

            if (hasError) {
                showError('wgProfileEditError', 'Please fill in all required fields correctly.');
                return;
            }

            if (saveBtn) { startButtonLoading(saveBtn, 'Saving...'); }

            var fd = new FormData();
            fd.append('action', 'update');
            fd.append('full_name', nameEl.value.trim());
            fd.append('phone', phoneEl.value.trim());
            fd.append('address', addressEl.value.trim());

            if (imageInput && imageInput.files.length > 0) {
                fd.append('profile_image', imageInput.files[0]);
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', getHandlerUrl(), true);
            xhr.responseType = 'json';
            xhr.onload = function () {
                if (saveBtn) { stopButtonLoading(saveBtn); }
                var r = xhr.response;
                if (!r) {
                    showError('wgProfileEditError', 'An unexpected error occurred.');
                    return;
                }

                if (xhr.status === 200 && r.success) {
                    currentData.full_name = r.user.full_name;
                    currentData.phone = r.user.phone;
                    currentData.address = r.user.address;
                    currentData.profile_image = r.user.profile_image;
                    populateView(currentData);
                    updateNavbar(currentData);

                    overlay.classList.remove('is-open');
                    document.body.classList.remove('wg-profile-open');
                    showView();
                    clearMessages();

                    if (typeof window.showSuccess === 'function') {
                        window.showSuccess(r.message || 'Profile updated successfully.');
                    }
                } else if (r.errors) {
                    var first = '';
                    if (r.errors.full_name) { nameEl.style.borderColor = 'rgba(239, 68, 68, 0.5)'; first = first || r.errors.full_name; }
                    if (r.errors.phone) { phoneEl.style.borderColor = 'rgba(239, 68, 68, 0.5)'; first = first || r.errors.phone; }
                    if (r.errors.address) { addressEl.style.borderColor = 'rgba(239, 68, 68, 0.5)'; first = first || r.errors.address; }
                    if (first) showError('wgProfileEditError', first);
                } else if (r.error) {
                    showError('wgProfileEditError', r.error);
                }
            };
            xhr.onerror = function () {
                if (saveBtn) { stopButtonLoading(saveBtn); }
                showError('wgProfileEditError', 'Network error. Please try again.');
            };
            xhr.send(fd);
        });
    }

    function updateNavbar(data) {
        var nameEls = document.querySelectorAll('.wg-user-menu__name, .wg-drawer__user-name');
        nameEls.forEach(function (el) { el.textContent = data.full_name; });

        var baseUrl = window.APP_BASE_URL || '';
        var bust = '?v=' + Date.now();
        var imgs = document.querySelectorAll('.wg-user-menu__avatar, .wg-drawer__user-avatar');
        imgs.forEach(function (img) {
            if (data.profile_image) {
                var src = (data.profile_image.indexOf('http') === 0) ? data.profile_image : baseUrl + (data.profile_image.indexOf('/') === 0 ? data.profile_image : '/' + data.profile_image);
                img.src = src + bust;
            }
        });
    }

    // Initial image button binding
    rebindImageBtn();
})();
