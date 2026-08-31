/**
 * SOUND Group — User Management (AJAX-powered)
 * Handles: modal open/close, view/edit/delete, search & status filter,
 *          empty state, profile image preview, pagination (6 per page)
 */
(function () {
    'use strict';

    var PAGE_SIZE = 6;
    var currentUser = null;
    var currentPage = 1;
    var totalPages = 1;
    var handlerUrl = (window.APP_BASE_URL || '') + '/backend/handlers/user-handler.php';

    function getCsrfToken() {
        var grid = document.getElementById('umCardGrid');
        return grid ? (grid.getAttribute('data-csrf') || '') : '';
    }

    function getBaseUrl() {
        var grid = document.getElementById('umCardGrid');
        return grid ? (grid.getAttribute('data-base-url') || '') : '';
    }

    function init() {
        if (!document.getElementById('umCardGrid')) return;

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

        function textValue(attr, fallback) {
            return currentUser ? (currentUser.getAttribute(attr) || fallback) : fallback;
        }

        function initials(name) {
            var parts = (name || '?').trim().split(/\s+/);
            return ((parts[0] || '?').charAt(0) + (parts[1] || '').charAt(0)).toUpperCase();
        }

        function setBadge(el, status) {
            if (!el) return;
            el.className = 'um-badge';
            if (status === 'active') {
                el.classList.add('um-badge--active');
                el.textContent = 'Active';
            } else {
                el.classList.add('um-badge--inactive');
                el.textContent = 'Inactive';
            }
        }

        function getFilteredCards() {
            var query = (document.getElementById('umSearchInput').value || '').toLowerCase().trim();
            var status = document.getElementById('umStatusFilter').value;

            return Array.prototype.slice.call(
                document.querySelectorAll('#umCardGrid .um-user-card')
            ).filter(function (card) {
                var name = (card.getAttribute('data-name') || '').toLowerCase();
                var userId = (card.getAttribute('data-user-id') || '').toLowerCase();
                var cardStatus = (card.getAttribute('data-status') || '').toLowerCase();

                var matchQuery = !query || name.indexOf(query) !== -1 || userId.indexOf(query) !== -1;
                var matchStatus = status === 'all' || cardStatus === status;

                return matchQuery && matchStatus;
            });
        }

        function renderPagination(total) {
            totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;

            var pagesEl = document.getElementById('umPaginationPages');
            if (pagesEl) {
                pagesEl.innerHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    (function (page) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'um-pagination__btn' + (page === currentPage ? ' um-pagination__btn--active' : '');
                        btn.textContent = page;
                        btn.addEventListener('click', function () { currentPage = page; renderGrid(); });
                        pagesEl.appendChild(btn);
                    })(i);
                }
            }

            var prevBtn = document.getElementById('umPrevPage');
            if (prevBtn) prevBtn.disabled = currentPage <= 1;
            var nextBtn = document.getElementById('umNextPage');
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
        }

        function renderGrid() {
            var cards = getFilteredCards();
            var total = cards.length;
            var displayedPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            if (currentPage > displayedPages) currentPage = displayedPages;

            var firstIndex = (currentPage - 1) * PAGE_SIZE;
            var lastIndex = firstIndex + PAGE_SIZE;

            document.querySelectorAll('#umCardGrid .um-user-card').forEach(function (card) {
                var idx = cards.indexOf(card);
                card.style.display = (idx !== -1 && idx >= firstIndex && idx < lastIndex) ? '' : 'none';
            });

            var countEl = document.getElementById('umCount');
            if (countEl) {
                if (total === 0) {
                    countEl.textContent = 'Showing 0 of 0 users';
                } else {
                    var start = (currentPage - 1) * PAGE_SIZE + 1;
                    var end = Math.min(currentPage * PAGE_SIZE, total);
                    countEl.textContent = 'Showing ' + start + '\u2013' + end + ' of ' + total + ' users';
                }
            }

            var emptyEl = document.getElementById('umEmptyState');
            if (emptyEl) emptyEl.hidden = total !== 0;

            renderPagination(total);
        }

        // --- Close Buttons ---
        document.querySelectorAll('[data-um-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-um-close');
                if (target === 'view') closeModal('umViewModal');
                else if (target === 'edit') closeModal('umEditModal');
                else if (target === 'delete') closeModal('umDeleteModal');
            });
        });

        // --- Close on Escape ---
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal('umViewModal');
                closeModal('umEditModal');
                closeModal('umDeleteModal');
            }
        });

        // --- Close on overlay click ---
        ['umViewModal', 'umEditModal', 'umDeleteModal'].forEach(function (id) {
            var modal = document.getElementById(id);
            if (!modal) return;
            var overlay = modal.querySelector('.sg-modal__overlay');
            if (overlay) {
                overlay.addEventListener('click', function () { closeModal(id); });
            }
        });

        // --- AJAX helper ---
        function ajaxPost(data, onSuccess, onError) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', handlerUrl, true);
            xhr.responseType = 'json';
            xhr.onload = function () {
                var resp = xhr.response;
                if (xhr.status === 200 && resp && resp.success) {
                    onSuccess(resp);
                } else if (resp && resp.redirect) {
                    window.location.href = resp.redirect;
                } else {
                    onError(resp ? resp.error : 'An unexpected error occurred.');
                }
            };
            xhr.onerror = function () { onError('Network error. Please try again.'); };
            var fd = new FormData();
            data.csrf_token = getCsrfToken();
            Object.keys(data).forEach(function (k) {
                if (data[k] !== undefined && data[k] !== null) fd.append(k, data[k]);
            });
            xhr.send(fd);
        }

        // --- View User ---
        document.querySelectorAll('[data-um-action="view"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentUser = btn.closest('.um-user-card');
                if (!currentUser) return;

                var dbId = textValue('data-db-id', '0');
                var cardName = textValue('data-name', '');
                var cardEmail = textValue('data-email', '');
                var cardPhone = textValue('data-phone', '');
                var cardAddress = textValue('data-address', '');
                var cardRegistered = textValue('data-registered', '');
                var cardLogin = textValue('data-login', '');
                var cardLogout = textValue('data-logout', '');
                var cardStatus = (textValue('data-status', 'active') || '').toLowerCase();
                var cardImage = textValue('data-image', '');
                var cardUserId = textValue('data-user-id', '');

                var avatarEl = document.getElementById('um-view-avatar');
                if (avatarEl) {
                    if (cardImage) {
                        avatarEl.innerHTML = '<img src="' + cardImage + '" alt="' + (cardName || 'User') + '">';
                        avatarEl.className = 'um-avatar um-avatar--large';
                    } else {
                        avatarEl.innerHTML = initials(cardName);
                        avatarEl.className = 'um-avatar um-avatar--large um-avatar--violet';
                    }
                }

                var setText = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.textContent = val || '—';
                };

                setText('um-view-name', cardName);
                setText('um-view-id', cardUserId);
                setText('um-view-email', cardEmail);
                setText('um-view-phone', cardPhone);
                setText('um-view-address', cardAddress || '—');
                setText('um-view-registered', cardRegistered || '—');
                setText('um-view-login', cardLogin || 'Never');
                setText('um-view-logout', cardLogout || '—');
                setText('um-view-status-text', cardStatus === 'active' ? 'Active' : 'Inactive');

                setBadge(document.getElementById('um-view-status-badge'), cardStatus);

                openModal('umViewModal');
            });
        });

        // --- Edit User ---
        document.querySelectorAll('[data-um-action="edit"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentUser = btn.closest('.um-user-card');
                if (!currentUser) return;

                populateEditForm();
                openModal('umEditModal');
            });
        });

        function populateEditForm() {
            if (!currentUser) return;

            var cardName = textValue('data-name', '');
            var cardImage = textValue('data-image', '');

            var setValue = function (id, val) {
                var el = document.getElementById(id);
                if (el) el.value = val || '';
            };

            setValue('um-edit-db-id', textValue('data-db-id', ''));
            setValue('um-edit-name', cardName);
            setValue('um-edit-email', textValue('data-email', ''));
            setValue('um-edit-phone', textValue('data-phone', ''));
            setValue('um-edit-address', textValue('data-address', ''));
            setValue('um-edit-status', (textValue('data-status', 'active') || '').toLowerCase());

            var nameEl = document.getElementById('um-edit-user-name');
            if (nameEl) nameEl.textContent = cardName;

            var titleEl = document.getElementById('umEditTitle');
            if (titleEl) titleEl.textContent = 'Edit User';

            var avatarEl = document.getElementById('um-edit-avatar');
            if (avatarEl) {
                if (cardImage) {
                    avatarEl.innerHTML = '<img src="' + cardImage + '" alt="' + (cardName || 'User') + '">';
                    avatarEl.className = 'um-avatar um-avatar--large';
                } else {
                    avatarEl.innerHTML = initials(cardName);
                    avatarEl.style.backgroundImage = '';
                    avatarEl.className = 'um-avatar um-avatar--large um-avatar--violet';
                }
            }

            // Reset file input
            var fileInput = document.getElementById('umEditImageInput');
            if (fileInput) fileInput.value = '';
        }

        // --- View -> Edit button ---
        var viewEditBtn = document.getElementById('umViewEditBtn');
        if (viewEditBtn) {
            viewEditBtn.addEventListener('click', function () {
                closeModal('umViewModal');
                populateEditForm();
                openModal('umEditModal');
            });
        }

        // --- Update User (AJAX) ---
        var editForm = document.getElementById('umEditForm');
        if (editForm) {
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!currentUser) return;

                var dbId = document.getElementById('um-edit-db-id').value;
                var fullName = document.getElementById('um-edit-name').value.trim();
                var email = document.getElementById('um-edit-email').value.trim();
                var phone = document.getElementById('um-edit-phone').value.trim();
                var address = document.getElementById('um-edit-address').value.trim();
                var status = document.getElementById('um-edit-status').value;

                if (!fullName || !email || !phone) return;

                var updateBtn = document.getElementById('umUpdateUserBtn');
                if (updateBtn) updateBtn.disabled = true;

                var fd = new FormData();
                fd.append('action', 'edit');
                fd.append('csrf_token', getCsrfToken());
                fd.append('id', dbId);
                fd.append('full_name', fullName);
                fd.append('email', email);
                fd.append('phone', phone);
                fd.append('address', address);
                fd.append('status', status);

                var fileInput = document.getElementById('umEditImageInput');
                if (fileInput && fileInput.files.length > 0) {
                    fd.append('profile_image', fileInput.files[0]);
                }

                var xhr = new XMLHttpRequest();
                xhr.open('POST', handlerUrl, true);
                xhr.responseType = 'json';
                xhr.onload = function () {
                    var resp = xhr.response;
                    if (updateBtn) updateBtn.disabled = false;

                    if (xhr.status === 200 && resp && resp.success && resp.record) {
                        // Update the card
                        var rec = resp.record;
                        currentUser.setAttribute('data-name', rec.full_name);
                        currentUser.setAttribute('data-email', rec.email);
                        currentUser.setAttribute('data-phone', rec.phone);
                        currentUser.setAttribute('data-address', rec.address || '');
                        currentUser.setAttribute('data-status', rec.status);

                        if (rec.profile_image) {
                            currentUser.setAttribute('data-image', rec.profile_image.indexOf('http') === 0 ? rec.profile_image : getBaseUrl() + '/' + rec.profile_image.replace(/^\//, ''));
                        }

                        // Update card display
                        var nameEl = currentUser.querySelector('.um-user-card__name');
                        if (nameEl) nameEl.textContent = rec.full_name;

                        var emailEl = currentUser.querySelector('.um-user-card__value--email');
                        if (emailEl) emailEl.textContent = rec.email;

                        var phoneEl = currentUser.querySelector('.um-user-card__value--phone');
                        if (phoneEl) phoneEl.textContent = rec.phone;

                        var addressEl = currentUser.querySelector('.um-user-card__value--address');
                        if (addressEl) addressEl.textContent = rec.address || '—';

                        var avatar = currentUser.querySelector('.um-avatar');
                        if (avatar) {
                            if (rec.profile_image) {
                                var imgUrl = rec.profile_image.indexOf('http') === 0 ? rec.profile_image : getBaseUrl() + '/' + rec.profile_image.replace(/^\//, '');
                                avatar.innerHTML = '<img src="' + imgUrl + '" alt="' + (rec.full_name || 'User') + '">';
                                avatar.className = 'um-avatar um-avatar--card';
                            } else {
                                avatar.innerHTML = initials(rec.full_name);
                                avatar.style.backgroundImage = '';
                                avatar.className = 'um-avatar um-avatar--card um-avatar--violet';
                            }
                        }

                        var badge = currentUser.querySelector('.um-badge');
                        setBadge(badge, rec.status);

                        closeModal('umEditModal');
                        renderGrid();

                        if (typeof showSuccess === 'function') {
                            showSuccess(resp.message || 'User updated successfully.', 2000);
                        }
                    } else if (resp && resp.redirect) {
                        window.location.href = resp.redirect;
                    } else {
                        var errMsg = resp ? (resp.error || 'Update failed.') : 'An unexpected error occurred.';
                        if (typeof showError === 'function') {
                            showError(errMsg, 3000);
                        }
                    }
                };
                xhr.onerror = function () {
                    if (updateBtn) updateBtn.disabled = false;
                    if (typeof showError === 'function') {
                        showError('Network error. Please try again.', 3000);
                    }
                };
                xhr.send(fd);
            });
        }

        // --- Delete User ---
        document.querySelectorAll('[data-um-action="delete"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentUser = btn.closest('.um-user-card');
                if (!currentUser) return;

                var name = textValue('data-name', textValue('data-user-id', ''));
                var deleteName = document.getElementById('um-delete-name');
                if (deleteName) deleteName.textContent = name;

                openModal('umDeleteModal');
            });
        });

        // --- Confirm Delete (AJAX) ---
        var confirmDeleteBtn = document.getElementById('umConfirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function () {
                if (!currentUser) return;

                var dbId = textValue('data-db-id', '0');
                confirmDeleteBtn.disabled = true;

                ajaxPost({ action: 'delete', id: dbId }, function (resp) {
                    if (currentUser && currentUser.parentNode) {
                        currentUser.parentNode.removeChild(currentUser);
                    }
                    currentUser = null;
                    confirmDeleteBtn.disabled = false;
                    closeModal('umDeleteModal');
                    renderGrid();

                    if (typeof showSuccess === 'function') {
                        showSuccess(resp.message || 'User deleted successfully.', 2000);
                    }
                }, function (errMsg) {
                    confirmDeleteBtn.disabled = false;
                    if (typeof showError === 'function') {
                        showError(errMsg, 3000);
                    }
                });
            });
        }

        // --- Search + Status Filter ---
        var searchInput = document.getElementById('umSearchInput');
        if (searchInput) searchInput.addEventListener('input', renderGrid);

        var statusFilter = document.getElementById('umStatusFilter');
        if (statusFilter) statusFilter.addEventListener('change', renderGrid);

        // --- Pagination Prev / Next ---
        var prevBtn = document.getElementById('umPrevPage');
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) { currentPage--; renderGrid(); }
            });
        }
        var nextBtn = document.getElementById('umNextPage');
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (currentPage < totalPages) { currentPage++; renderGrid(); }
            });
        }

        // --- Profile image preview (Edit modal) ---
        var imageInput = document.getElementById('umEditImageInput');
        var imageBtn = document.getElementById('umEditImageBtn');
        var editAvatar = document.getElementById('um-edit-avatar');

        if (imageBtn && imageInput) {
            imageBtn.addEventListener('click', function () { imageInput.click(); });
        }
        if (imageInput) {
            imageInput.addEventListener('change', function () {
                if (imageInput.files && imageInput.files.length > 0) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        if (editAvatar) {
                            editAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                        }
                    };
                    reader.readAsDataURL(imageInput.files[0]);
                }
            });
        }

        // --- Initial render ---
        renderGrid();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
