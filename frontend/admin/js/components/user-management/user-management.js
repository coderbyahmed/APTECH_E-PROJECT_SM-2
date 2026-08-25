/**
 * SOUND Group — User Management (UI Only)
 * Handles: modal open/close, view/edit/delete, search & status filter,
 *          empty state, profile image preview, mock pagination (6 per page)
 */
(function () {
    'use strict';

    var PAGE_SIZE = 6;
    var currentUser = null;
    var currentPage = 1;
    var totalPages = 1;

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

        function initials(first, last) {
            return ((first || '?').charAt(0) + (last || '?').charAt(0)).toUpperCase();
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
                var first = (card.getAttribute('data-first') || '').toLowerCase();
                var last = (card.getAttribute('data-last') || '').toLowerCase();
                var userId = (card.getAttribute('data-user-id') || '').toLowerCase();
                var fullName = (first + ' ' + last).trim();

                var cardStatus = (card.getAttribute('data-status') || '').toLowerCase();

                var matchQuery = !query ||
                    fullName.indexOf(query) !== -1 ||
                    first.indexOf(query) !== -1 ||
                    last.indexOf(query) !== -1 ||
                    userId.indexOf(query) !== -1;

                var matchStatus = status === 'all' || cardStatus === status;

                return matchQuery && matchStatus;
            });
        }

        function renderPagination(total) {
            totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            var pagesEl = document.getElementById('umPaginationPages');
            if (pagesEl) {
                pagesEl.innerHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    (function (page) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'um-pagination__btn' +
                            (page === currentPage ? ' um-pagination__btn--active' : '');
                        btn.textContent = page;
                        btn.addEventListener('click', function () {
                            currentPage = page;
                            renderGrid();
                        });
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
            if (currentPage > displayedPages) {
                currentPage = displayedPages;
            }

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
            if (emptyEl) {
                emptyEl.hidden = total !== 0;
            }

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
                overlay.addEventListener('click', function () {
                    closeModal(id);
                });
            }
        });

        // --- View User ---
        document.querySelectorAll('[data-um-action="view"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentUser = btn.closest('.um-user-card');
                if (!currentUser) return;

                var first = textValue('data-first', '');
                var last = textValue('data-last', '');
                var name = first + ' ' + last;
                var status = (textValue('data-status', 'active') || '').toLowerCase();

                var avatarEl = document.getElementById('um-view-avatar');
                if (avatarEl) avatarEl.textContent = initials(first, last);

                var setText = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.textContent = val;
                };

                var setName = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.innerHTML = val;
                };

                setName('um-view-name', name);
                setName('um-view-id', textValue('data-user-id', ''));
                setText('um-view-email', textValue('data-email', ''));
                setText('um-view-phone', textValue('data-phone', ''));
                setText('um-view-address', textValue('data-address', ''));
                setText('um-view-registered', textValue('data-registered', ''));
                setText('um-view-login', textValue('data-login', ''));
                setText('um-view-logout', textValue('data-logout', ''));
                setText('um-view-status-text', status === 'active' ? 'Active' : 'Inactive');

                setBadge(document.getElementById('um-view-status-badge'), status);

                openModal('umViewModal');
            });
        });

        // --- Edit User ---
        document.querySelectorAll('[data-um-action="edit"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentUser = btn.closest('.um-user-card');
                if (!currentUser) return;

                var first = textValue('data-first', '');
                var last = textValue('data-last', '');
                var name = first + ' ' + last;
                var status = (textValue('data-status', 'active') || '').toLowerCase();

                var setValue = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.value = val;
                };

                setValue('um-edit-first', first);
                setValue('um-edit-last', last);
                setValue('um-edit-email', textValue('data-email', ''));
                setValue('um-edit-phone', textValue('data-phone', ''));
                setValue('um-edit-address', textValue('data-address', ''));
                setValue('um-edit-status', status);

                var nameEl = document.getElementById('um-edit-user-name');
                if (nameEl) nameEl.textContent = name;

                var titleEl = document.getElementById('umEditTitle');
                if (titleEl) titleEl.textContent = 'Edit User';

                var avatarEl = document.getElementById('um-edit-avatar');
                if (avatarEl) {
                    avatarEl.textContent = initials(first, last);
                    avatarEl.style.background = '';
                    avatarEl.className = 'um-avatar um-avatar--large';
                }

                openModal('umEditModal');
            });
        });

        // --- View -> Edit button ---
        var viewEditBtn = document.getElementById('umViewEditBtn');
        if (viewEditBtn) {
            viewEditBtn.addEventListener('click', function () {
                closeModal('umViewModal');
                if (!currentUser) return;

                var first = textValue('data-first', '');
                var last = textValue('data-last', '');
                var name = first + ' ' + last;
                var status = (textValue('data-status', 'active') || '').toLowerCase();

                var setValue = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.value = val;
                };

                setValue('um-edit-first', first);
                setValue('um-edit-last', last);
                setValue('um-edit-email', textValue('data-email', ''));
                setValue('um-edit-phone', textValue('data-phone', ''));
                setValue('um-edit-address', textValue('data-address', ''));
                setValue('um-edit-status', status);

                var nameEl = document.getElementById('um-edit-user-name');
                if (nameEl) nameEl.textContent = name;

                var avatarEl = document.getElementById('um-edit-avatar');
                if (avatarEl) {
                    avatarEl.textContent = initials(first, last);
                    avatarEl.style.backgroundImage = '';
                    avatarEl.style.backgroundSize = '';
                    avatarEl.style.backgroundPosition = '';
                    avatarEl.className = 'um-avatar um-avatar--large';
                }

                openModal('umEditModal');
            });
        }

        // --- Update User (UI Only, updates the card) ---
        var updateBtn = document.getElementById('umUpdateUserBtn');
        if (updateBtn) {
            updateBtn.addEventListener('click', function () {
                if (!currentUser) return;

                var first = document.getElementById('um-edit-first').value.trim();
                var last = document.getElementById('um-edit-last').value.trim();
                var email = document.getElementById('um-edit-email').value.trim();
                var phone = document.getElementById('um-edit-phone').value.trim();
                var address = document.getElementById('um-edit-address').value.trim();
                var status = document.getElementById('um-edit-status').value.toLowerCase();

                if (!first || !last) return;

                // Update card data attributes
                currentUser.setAttribute('data-first', first);
                currentUser.setAttribute('data-last', last);
                currentUser.setAttribute('data-email', email);
                currentUser.setAttribute('data-phone', phone);
                currentUser.setAttribute('data-address', address);
                currentUser.setAttribute('data-status', status);

                // Update displayed card content
                var nameEl = currentUser.querySelector('.um-user-card__name');
                if (nameEl) nameEl.textContent = first + ' ' + last;

                var emailEl = currentUser.querySelector('.um-user-card__value--email');
                if (emailEl) emailEl.textContent = email;

                var phoneEl = currentUser.querySelector('.um-user-card__value--phone');
                if (phoneEl) phoneEl.textContent = phone;

                var addressEl = currentUser.querySelector('.um-user-card__value--address');
                if (addressEl) addressEl.textContent = address;

                var avatar = currentUser.querySelector('.um-avatar');
                if (avatar) avatar.textContent = initials(first, last);

                var badge = currentUser.querySelector('.um-badge');
                setBadge(badge, status);

                closeModal('umEditModal');
                renderGrid();

                if (typeof showSuccess === 'function') {
                    showSuccess('User updated successfully.', 2000);
                }
            });
        }

        // --- Delete User ---
        document.querySelectorAll('[data-um-action="delete"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentUser = btn.closest('.um-user-card');
                if (!currentUser) return;

                var first = textValue('data-first', '');
                var last = textValue('data-last', '');
                var name = (first + ' ' + last).trim() || textValue('data-user-id', '');

                var deleteName = document.getElementById('um-delete-name');
                if (deleteName) deleteName.textContent = name;

                openModal('umDeleteModal');
            });
        });

        // --- Confirm Delete (UI Only, removes the card) ---
        var confirmDeleteBtn = document.getElementById('umConfirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function () {
                if (currentUser && currentUser.parentNode) {
                    currentUser.parentNode.removeChild(currentUser);
                }
                currentUser = null;
                closeModal('umDeleteModal');
                renderGrid();

                if (typeof showSuccess === 'function') {
                    showSuccess('User deleted successfully.', 2000);
                }
            });
        }

        // --- Search + Status Filter ---
        var searchInput = document.getElementById('umSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', renderGrid);
        }

        var statusFilter = document.getElementById('umStatusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', renderGrid);
        }

        // --- Pagination Prev / Next ---
        var prevBtn = document.getElementById('umPrevPage');
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    renderGrid();
                }
            });
        }

        var nextBtn = document.getElementById('umNextPage');
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderGrid();
                }
            });
        }

        // --- Edit form submit prevention ---
        var editForm = document.getElementById('umEditForm');
        if (editForm) {
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();
            });
        }

        // --- Profile image preview (UI Only) ---
        var imageInput = document.getElementById('umEditImageInput');
        var imageBtn = document.getElementById('umEditImageBtn');
        var editAvatar = document.getElementById('um-edit-avatar');

        if (imageBtn && imageInput) {
            imageBtn.addEventListener('click', function () {
                imageInput.click();
            });
        }

        if (imageInput) {
            imageInput.addEventListener('change', function () {
                if (imageInput.files && imageInput.files.length > 0) {
                    var file = imageInput.files[0];
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        if (editAvatar) {
                            editAvatar.textContent = '';
                            editAvatar.style.backgroundImage = 'url(' + e.target.result + ')';
                            editAvatar.style.backgroundSize = 'cover';
                            editAvatar.style.backgroundPosition = 'center';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // --- Initial render ---
        renderGrid();
    }

    // --- Initialize when DOM is ready ---
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();