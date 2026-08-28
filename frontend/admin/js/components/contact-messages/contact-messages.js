/**
 * SOUND Group — Contact Messages Management
 * Handles: AJAX data loading, modal open/close, view/mark-as-read/delete,
 *          search & status filter, empty state, pagination
 */
(function () {
    'use strict';

    var HANDLER_URL = '/Aptech_E_Project_02/sound_management/backend/handlers/contact-handler.php';
    var PAGE_SIZE = 8;
    var currentCard = null;
    var currentPage = 1;
    var totalPages = 1;

    function init() {
        if (!document.getElementById('cmSearchInput')) return;

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

        function setText(id, val) {
            var el = document.getElementById(id);
            if (el) el.textContent = val;
        }

        function setBadge(el, status) {
            if (!el) return;
            el.className = 'cm-badge';
            if (status === 'new') {
                el.classList.add('cm-badge--new');
                el.textContent = 'New';
            } else {
                el.classList.add('cm-badge--read');
                el.textContent = 'Read';
            }
        }

        function showLoading(btn) {
            if (!btn) return;
            btn.disabled = true;
            btn.style.opacity = '0.65';
        }

        function hideLoading(btn) {
            if (!btn) return;
            btn.disabled = false;
            btn.style.opacity = '';
        }

        function updateStats(stats) {
            if (!stats) return;
            setText('cmTotalMessages', stats.total);
            setText('cmNewMessages', stats['new']);
            setText('cmReadMessages', stats.read);
        }

        /* ------------------------------------------
           AJAX Fetch Messages (List)
           ------------------------------------------ */
        function fetchMessages(page) {
            page = page || 1;
            var searchVal = (document.getElementById('cmSearchInput').value || '').trim();
            var statusVal = document.getElementById('cmStatusFilter').value;

            var formData = new FormData();
            formData.append('action', 'list');
            formData.append('page', page);
            formData.append('per_page', PAGE_SIZE);
            formData.append('search', searchVal);
            formData.append('status', statusVal);

            fetch(HANDLER_URL, { method: 'POST', body: formData })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) return;
                    currentPage = data.page;
                    totalPages = data.total_pages;
                    renderCards(data.records);
                    renderPagination(data.total, data.page, data.total_pages);
                    updateStats(data.stats);
                })
                .catch(function () {});
        }

        /* ------------------------------------------
           Render Cards from AJAX data
           ------------------------------------------ */
        function renderCards(records) {
            var grid = document.getElementById('cmMessageGrid');
            var emptyEl = document.getElementById('cmEmptyState');
            if (!grid) return;

            grid.innerHTML = '';

            if (!records || records.length === 0) {
                if (emptyEl) emptyEl.hidden = false;
                return;
            }

            if (emptyEl) emptyEl.hidden = true;

            records.forEach(function (rec) {
                var statusClass = rec.status === 'new' ? 'cm-badge--new' : 'cm-badge--read';
                var statusLabel = rec.status === 'new' ? 'New' : 'Read';
                var dateLabel = rec.created_at ? formatDate(rec.created_at) : '';

                var article = document.createElement('article');
                article.className = 'cm-message-card';
                article.setAttribute('data-cmid', rec.id);
                article.setAttribute('data-message-id', rec.message_id || '');
                article.setAttribute('data-first', rec.first_name || '');
                article.setAttribute('data-last', rec.last_name || '');
                article.setAttribute('data-avatar', rec.avatar_color || 'violet');
                article.setAttribute('data-initials', rec.initials || '?');
                article.setAttribute('data-profile-image', rec.profile_image || '');
                article.setAttribute('data-email', rec.email || '');
                article.setAttribute('data-phone', rec.phone || '');
                article.setAttribute('data-inquiry', rec.inquiry_type || '');
                article.setAttribute('data-subject', rec.subject || '');
                article.setAttribute('data-date', dateLabel);
                article.setAttribute('data-datetime', rec.created_at || '');
                article.setAttribute('data-status', rec.status || 'new');
                article.setAttribute('data-text', rec.message || '');

                article.innerHTML =
                    '<div class="cm-message-card__header">' +
                        '<div class="cm-avatar cm-avatar--card cm-avatar--' + (rec.avatar_color || 'violet') + '">' +
                            (rec.profile_image ? '<img src="/Aptech_E_Project_02/sound_management/' + escapeHtml(rec.profile_image) + '" alt="" class="cm-avatar__img" width="48" height="48">' : (rec.initials || '?')) +
                        '</div>' +
                        '<div class="cm-message-card__user">' +
                            '<h3 class="cm-message-card__user-name">' + escapeHtml(rec.full_name || '') + '</h3>' +
                            '<span class="cm-message-card__user-email">' + escapeHtml(rec.email || '') + '</span>' +
                        '</div>' +
                        '<span class="cm-badge ' + statusClass + '">' + statusLabel + '</span>' +
                    '</div>' +
                    '<div class="cm-message-card__content">' +
                        '<h4 class="cm-message-card__subject">' + escapeHtml(rec.subject || '') + '</h4>' +
                    '</div>' +
                    '<p class="cm-message-card__preview">' + escapeHtml(rec.message || '') + '</p>' +
                    '<div class="cm-message-card__meta">' +
                        '<span class="cm-message-card__meta-item">Received: <strong>' + escapeHtml(dateLabel) + '</strong></span>' +
                    '</div>' +
                    '<div class="cm-message-card__actions">' +
                        '<button type="button" class="cm-action-btn cm-action-btn--view" title="View" data-cm-action="view">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' +
                        '</button>' +
                        '<button type="button" class="cm-action-btn cm-action-btn--delete" title="Delete" data-cm-action="delete">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
                        '</button>' +
                    '</div>';

                grid.appendChild(article);
            });

            bindCardActions();
        }

        /* ------------------------------------------
           Pagination
           ------------------------------------------ */
        function renderPagination(total, page, pages) {
            totalPages = pages || 1;
            currentPage = page || 1;

            var pagesEl = document.getElementById('cmPaginationPages');
            if (pagesEl) {
                pagesEl.innerHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    (function (p) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'cm-pagination__btn' + (p === currentPage ? ' cm-pagination__btn--active' : '');
                        btn.textContent = p;
                        btn.addEventListener('click', function () {
                            fetchMessages(p);
                        });
                        pagesEl.appendChild(btn);
                    })(i);
                }
            }

            var prevBtn = document.getElementById('cmPrevPage');
            if (prevBtn) prevBtn.disabled = currentPage <= 1;

            var nextBtn = document.getElementById('cmNextPage');
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;

            var countEl = document.getElementById('cmCount');
            if (countEl) {
                if (total === 0) {
                    countEl.textContent = 'Showing 0 of 0 messages';
                } else {
                    var start = (currentPage - 1) * PAGE_SIZE + 1;
                    var end = Math.min(currentPage * PAGE_SIZE, total);
                    countEl.textContent = 'Showing ' + start + '\u2013' + end + ' of ' + total + ' messages';
                }
            }
        }

        /* ------------------------------------------
           Utility functions
           ------------------------------------------ */
        function escapeHtml(str) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }

        function formatDate(ts) {
            if (!ts || ts === '0000-00-00 00:00:00') return '';
            var d = new Date(ts.replace(/-/g, '/'));
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
        }

        function formatDateTime(ts) {
            if (!ts || ts === '0000-00-00 00:00:00') return '';
            var d = new Date(ts.replace(/-/g, '/'));
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            var h = d.getHours();
            var m = d.getMinutes();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            if (h === 0) h = 12;
            var mStr = m < 10 ? '0' + m : '' + m;
            return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ', ' + h + ':' + mStr + ' ' + ampm;
        }

        function inquiryLabel(type) {
            var labels = {
                general: 'General Inquiry',
                feedback: 'Feedback',
                report: 'Report an Issue',
                request: 'Music / Video Request',
                business: 'Business / Collaboration',
                partnership: 'Investment / Partnership',
                other: 'Other'
            };
            return labels[type] || (type ? type.charAt(0).toUpperCase() + type.slice(1) : '—');
        }

        /* ------------------------------------------
           Bind card action buttons (View / Delete)
           ------------------------------------------ */
        function bindCardActions() {
            document.querySelectorAll('[data-cm-action="view"]').forEach(function (btn) {
                btn.onclick = function () {
                    currentCard = btn.closest('.cm-message-card');
                    if (!currentCard) return;

                    var value = function (attr) {
                        return currentCard.getAttribute(attr) || '';
                    };

                    var avatarEl = document.getElementById('cm-view-avatar');
                    var avatarText = document.getElementById('cm-view-avatar-text');
                    var avatarImg = document.getElementById('cm-view-avatar-img');
                    if (avatarEl) {
                        avatarEl.className = 'cm-avatar cm-avatar--large cm-avatar--' + (value('data-avatar') || 'violet');
                    }
                    if (value('data-profile-image')) {
                        if (avatarText) avatarText.style.display = 'none';
                        if (avatarImg) {
                            avatarImg.src = '/Aptech_E_Project_02/sound_management/' + value('data-profile-image');
                            avatarImg.alt = value('data-first') + ' ' + value('data-last');
                            avatarImg.style.display = '';
                        }
                    } else {
                        if (avatarText) {
                            avatarText.style.display = '';
                            avatarText.textContent = value('data-initials') || '?';
                        }
                        if (avatarImg) avatarImg.style.display = 'none';
                    }

                    setText('cm-view-name', (value('data-first') + ' ' + value('data-last')).trim() || value('data-email'));
                    setText('cm-view-email', value('data-email'));
                    setText('cm-view-id', value('data-message-id'));
                    setText('cm-view-date', value('data-datetime') ? formatDateTime(value('data-datetime')) : value('data-date'));
                    setText('cm-view-phone', value('data-phone') || 'Not provided');
                    setText('cm-view-inquiry', inquiryLabel(value('data-inquiry')));
                    setText('cm-view-subject', value('data-subject'));
                    setText('cm-view-text', value('data-text'));

                    var badgeEl = document.getElementById('cm-view-status-badge');
                    setBadge(badgeEl, value('data-status') || 'new');

                    var markReadBtn = document.getElementById('cmMarkReadBtn');
                    if (markReadBtn) {
                        markReadBtn.style.display = (value('data-status') === 'new') ? '' : 'none';
                    }

                    openModal('cmViewModal');
                };
            });

            document.querySelectorAll('[data-cm-action="delete"]').forEach(function (btn) {
                btn.onclick = function () {
                    currentCard = btn.closest('.cm-message-card');
                    if (!currentCard) return;

                    var name = (currentCard.getAttribute('data-first') || '') + ' ' + (currentCard.getAttribute('data-last') || '');
                    setText('cm-delete-name', name.trim() || currentCard.getAttribute('data-email'));
                    setText('cm-delete-subject', currentCard.getAttribute('data-subject'));

                    openModal('cmDeleteModal');
                };
            });
        }

        /* ------------------------------------------
           Close Buttons
           ------------------------------------------ */
        document.querySelectorAll('[data-cm-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-cm-close');
                if (target === 'view') closeModal('cmViewModal');
                else if (target === 'delete') closeModal('cmDeleteModal');
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal('cmViewModal');
                closeModal('cmDeleteModal');
            }
        });

        ['cmViewModal', 'cmDeleteModal'].forEach(function (id) {
            var modal = document.getElementById(id);
            if (!modal) return;
            var overlay = modal.querySelector('.sg-modal__overlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    closeModal(id);
                });
            }
        });

        /* ------------------------------------------
           Mark as Read — AJAX
           ------------------------------------------ */
        var markReadBtn = document.getElementById('cmMarkReadBtn');
        if (markReadBtn) {
            markReadBtn.addEventListener('click', function () {
                if (!currentCard) return;
                var msgId = currentCard.getAttribute('data-cmid');
                if (!msgId) return;

                showLoading(markReadBtn);

                var formData = new FormData();
                formData.append('action', 'mark-read');
                formData.append('id', msgId);

                fetch(HANDLER_URL, { method: 'POST', body: formData })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.success) {
                            closeModal('cmViewModal');
                            fetchMessages(currentPage);
                            if (typeof window.showSuccess === 'function') {
                                window.showSuccess('Message marked as read.', 2000);
                            }
                        } else {
                            if (typeof window.showError === 'function') {
                                window.showError(data.error || 'Failed to mark as read.', 2000);
                            }
                        }
                    })
                    .catch(function () {
                        if (typeof window.showError === 'function') {
                            window.showError('Server error. Please try again.', 2000);
                        }
                    })
                    .finally(function () {
                        hideLoading(markReadBtn);
                    });
            });
        }

        /* ------------------------------------------
           Delete — AJAX
           ------------------------------------------ */
        var confirmDeleteBtn = document.getElementById('cmConfirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function () {
                if (!currentCard) return;
                var msgId = currentCard.getAttribute('data-cmid');
                if (!msgId) return;

                showLoading(confirmDeleteBtn);

                var formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', msgId);

                fetch(HANDLER_URL, { method: 'POST', body: formData })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.success) {
                            currentCard = null;
                            closeModal('cmDeleteModal');
                            fetchMessages(currentPage);
                            if (typeof window.showSuccess === 'function') {
                                window.showSuccess('Message deleted successfully.', 2000);
                            }
                        } else {
                            if (typeof window.showError === 'function') {
                                window.showError(data.error || 'Failed to delete message.', 2000);
                            }
                        }
                    })
                    .catch(function () {
                        if (typeof window.showError === 'function') {
                            window.showError('Server error. Please try again.', 2000);
                        }
                    })
                    .finally(function () {
                        hideLoading(confirmDeleteBtn);
                    });
            });
        }

        /* ------------------------------------------
           Search + Status Filter
           ------------------------------------------ */
        var searchInput = document.getElementById('cmSearchInput');
        if (searchInput) {
            var searchTimer = null;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    fetchMessages(1);
                }, 300);
            });
        }

        var statusFilter = document.getElementById('cmStatusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function () {
                fetchMessages(1);
            });
        }

        /* ------------------------------------------
           Pagination Prev / Next
           ------------------------------------------ */
        var prevBtn = document.getElementById('cmPrevPage');
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) {
                    fetchMessages(currentPage - 1);
                }
            });
        }

        var nextBtn = document.getElementById('cmNextPage');
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (currentPage < totalPages) {
                    fetchMessages(currentPage + 1);
                }
            });
        }

        /* ------------------------------------------
           Initial card action bindings (for server-rendered cards)
           ------------------------------------------ */
        bindCardActions();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
