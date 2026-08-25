/**
 * SOUND Group — Contact Messages Management (UI Only)
 * Handles: modal open/close, view/mark-as-read/delete,
 *          search & status filter, empty state, mock pagination
 */
(function () {
    'use strict';

    var PAGE_SIZE = 8;
    var currentCard = null;
    var currentPage = 1;
    var totalPages = 1;

    function init() {
        if (!document.querySelector('.cm-message-card') && !document.getElementById('cmSearchInput')) return;

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

        function updateStats() {
            var cards = document.querySelectorAll('.cm-message-card');
            var total = cards.length;
            var newCount = 0;

            cards.forEach(function (card) {
                if ((card.getAttribute('data-status') || 'new') === 'new') {
                    newCount++;
                }
            });

            setText('cmTotalMessages', total);
            setText('cmNewMessages', newCount);
            setText('cmReadMessages', total - newCount);
        }

        function getFilteredCards() {
            var query = (document.getElementById('cmSearchInput').value || '').toLowerCase().trim();
            var status = document.getElementById('cmStatusFilter').value;

            return Array.prototype.slice.call(
                document.querySelectorAll('.cm-message-card')
            ).filter(function (card) {
                var first = (card.getAttribute('data-first') || '').toLowerCase();
                var last = (card.getAttribute('data-last') || '').toLowerCase();
                var email = (card.getAttribute('data-email') || '').toLowerCase();
                var subject = (card.getAttribute('data-subject') || '').toLowerCase();
                var fullName = (first + ' ' + last).trim();

                var matchQuery = !query ||
                    fullName.indexOf(query) !== -1 ||
                    first.indexOf(query) !== -1 ||
                    last.indexOf(query) !== -1 ||
                    email.indexOf(query) !== -1 ||
                    subject.indexOf(query) !== -1;

                var cardStatus = card.getAttribute('data-status') || 'new';
                var matchStatus = status === 'all' || cardStatus === status;

                return matchQuery && matchStatus;
            });
        }

        function renderPagination(total) {
            totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            var pagesEl = document.getElementById('cmPaginationPages');
            if (pagesEl) {
                pagesEl.innerHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    (function (page) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'cm-pagination__btn' +
                            (page === currentPage ? ' cm-pagination__btn--active' : '');
                        btn.textContent = page;
                        btn.addEventListener('click', function () {
                            currentPage = page;
                            renderGrid();
                        });
                        pagesEl.appendChild(btn);
                    })(i);
                }
            }

            var prevBtn = document.getElementById('cmPrevPage');
            if (prevBtn) prevBtn.disabled = currentPage <= 1;

            var nextBtn = document.getElementById('cmNextPage');
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

            document.querySelectorAll('.cm-message-card').forEach(function (card) {
                var idx = cards.indexOf(card);
                card.style.display = (idx !== -1 && idx >= firstIndex && idx < lastIndex) ? '' : 'none';
            });

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

            var emptyEl = document.getElementById('cmEmptyState');
            if (emptyEl) {
                emptyEl.hidden = total !== 0;
            }

            renderPagination(total);
            updateStats();
        }

        function markCardAsRead(card) {
            if (!card) return;
            card.setAttribute('data-status', 'read');
            setBadge(card.querySelector('.cm-badge'), 'read');
            updateStats();
        }

        // --- Close Buttons ---
        document.querySelectorAll('[data-cm-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-cm-close');
                if (target === 'view') closeModal('cmViewModal');
                else if (target === 'delete') closeModal('cmDeleteModal');
            });
        });

        // --- Close on Escape ---
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal('cmViewModal');
                closeModal('cmDeleteModal');
            }
        });

        // --- Close on overlay click ---
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

        // --- View Message ---
        document.querySelectorAll('[data-cm-action="view"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentCard = btn.closest('.cm-message-card');
                if (!currentCard) return;

                var value = function (attr) {
                    return currentCard.getAttribute(attr) || '';
                };

                var first = value('data-first');
                var last = value('data-last');
                var status = value('data-status') || 'new';

                var avatarEl = document.getElementById('cm-view-avatar');
                if (avatarEl) {
                    avatarEl.className = 'cm-avatar cm-avatar--large cm-avatar--' + (value('data-avatar') || 'violet');
                    avatarEl.textContent = value('data-initials') || '?';
                }

                setText('cm-view-name', (first + ' ' + last).trim());
                setText('cm-view-email', value('data-email'));
                setText('cm-view-id', value('data-cmid'));
                setText('cm-view-date', value('data-label'));
                setText('cm-view-subject', value('data-subject'));
                setText('cm-view-text', value('data-text'));

                var badgeEl = document.getElementById('cm-view-status-badge');
                setBadge(badgeEl, status);

                var markReadBtn = document.getElementById('cmMarkReadBtn');
                if (markReadBtn) {
                    markReadBtn.style.display = status === 'new' ? '' : 'none';
                }

                openModal('cmViewModal');
            });
        });

        // --- View Modal -> Mark as Read ---
        var markReadBtn = document.getElementById('cmMarkReadBtn');
        if (markReadBtn) {
            markReadBtn.addEventListener('click', function () {
                markCardAsRead(currentCard);
                closeModal('cmViewModal');
                renderGrid();

                if (typeof window.showSuccess === 'function') {
                    window.showSuccess('Message marked as read.', 2000);
                }
            });
        }

        // --- Delete Message ---
        document.querySelectorAll('[data-cm-action="delete"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentCard = btn.closest('.cm-message-card');
                if (!currentCard) return;

                var first = currentCard.getAttribute('data-first') || '';
                var last = currentCard.getAttribute('data-last') || '';
                var name = (first + ' ' + last).trim();

                setText('cm-delete-name', name);
                setText('cm-delete-subject', currentCard.getAttribute('data-subject') || '');

                openModal('cmDeleteModal');
            });
        });

        // --- Confirm Delete (UI Only, removes the card) ---
        var confirmDeleteBtn = document.getElementById('cmConfirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function () {
                if (currentCard && currentCard.parentNode) {
                    currentCard.parentNode.removeChild(currentCard);
                }
                currentCard = null;
                closeModal('cmDeleteModal');
                renderGrid();

                if (typeof window.showSuccess === 'function') {
                    window.showSuccess('Message deleted successfully.', 2000);
                }
            });
        }

        // --- Search + Status Filter ---
        var searchInput = document.getElementById('cmSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                currentPage = 1;
                renderGrid();
            });
        }

        var statusFilter = document.getElementById('cmStatusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function () {
                currentPage = 1;
                renderGrid();
            });
        }

        // --- Pagination Prev / Next ---
        var prevBtn = document.getElementById('cmPrevPage');
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    renderGrid();
                }
            });
        }

        var nextBtn = document.getElementById('cmNextPage');
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderGrid();
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