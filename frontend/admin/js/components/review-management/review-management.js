/**
 * SOUND Group — Reviews & Ratings Management (UI Only)
 * Handles: modal open/close, view/edit/delete, search & filters
 *          (content type, rating, date), empty state, mock pagination
 */
(function () {
    'use strict';

    var PAGE_SIZE = 6;
    var currentCard = null;
    var currentPage = 1;
    var totalPages = 1;
    var selectedRating = 5;

    var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function init() {
        if (!document.getElementById('rrReviewGrid')) return;

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
            return currentCard ? (currentCard.getAttribute(attr) || fallback) : fallback;
        }

        function initials(first, last) {
            return ((first || '?').charAt(0) + (last || '?').charAt(0)).toUpperCase();
        }

        function pad2(n) {
            return (n < 10 ? '0' : '') + n;
        }

        function dateValue(d) {
            return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
        }

        function dateLabel(v) {
            var p = v.split('-');
            return MONTHS[parseInt(p[1], 10) - 1] + ' ' + parseInt(p[2], 10) + ', ' + p[0];
        }

        function getMinDate(filter) {
            var now = new Date();
            if (filter === 'today') {
                return dateValue(now);
            }
            if (filter === 'week') {
                var diff = (now.getDay() + 6) % 7;
                return dateValue(new Date(now.getFullYear(), now.getMonth(), now.getDate() - diff));
            }
            if (filter === 'month') {
                return dateValue(new Date(now.getFullYear(), now.getMonth(), 1));
            }
            return '';
        }

        function setTypeBadge(el, type) {
            if (!el) return;
            el.className = 'rr-type';
            if (type === 'video') {
                el.classList.add('rr-type--video');
                el.textContent = 'Video';
            } else {
                el.classList.add('rr-type--music');
                el.textContent = 'Music';
            }
        }

        function setStatusBadge(el, status) {
            if (!el) return;
            el.className = 'rr-badge';
            if (status === 'published') {
                el.classList.add('rr-badge--published');
                el.textContent = 'Published';
            } else {
                el.classList.add('rr-badge--hidden');
                el.textContent = 'Hidden';
            }
        }

        function setStars(el, rating) {
            if (!el) return;
            var fill = el.querySelector('.rr-stars__fill');
            if (fill) {
                fill.style.width = (rating * 20) + '%';
            }
        }

        function setPicker(rating) {
            selectedRating = rating;
            document.querySelectorAll('.rr-star-btn').forEach(function (btn) {
                var star = parseInt(btn.getAttribute('data-rr-star'), 10);
                btn.classList.toggle('is-active', star <= rating);
            });
            var val = document.getElementById('rr-edit-rating-value');
            if (val) val.textContent = selectedRating.toFixed(1);
        }

        function populateEditForm() {
            if (!currentCard) return;

            var setValue = function (id, val) {
                var el = document.getElementById(id);
                if (el) el.value = val;
            };

            setValue('rr-edit-type', textValue('data-content-type', 'music'));
            setValue('rr-edit-title', textValue('data-title', ''));
            setValue('rr-edit-artist', textValue('data-artist', ''));
            setValue('rr-edit-album', textValue('data-album', ''));
            setValue('rr-edit-status', textValue('data-status', 'published'));

            var textEl = document.getElementById('rr-edit-text');
            if (textEl) textEl.value = textValue('data-text', '');

            var first = textValue('data-first', '');
            var last = textValue('data-last', '');
            var name = first + ' ' + last;
            var title = textValue('data-title', '');

            var nameEl = document.getElementById('rr-edit-user-name');
            if (nameEl) nameEl.textContent = name;

            var contentEl = document.getElementById('rr-edit-content-name');
            if (contentEl) contentEl.textContent = title;

            var avatarEl = document.getElementById('rr-edit-avatar');
            if (avatarEl) avatarEl.textContent = initials(first, last);

            var userEl = document.getElementById('rr-edit-user');
            if (userEl) userEl.textContent = name;

            var uidEl = document.getElementById('rr-edit-uid');
            if (uidEl) uidEl.textContent = 'User ID: ' + textValue('data-user-id', '');

            var rating = parseInt(textValue('data-rating', '5'), 10) || 5;
            setPicker(rating);
        }

        function getFilteredCards() {
            var query = (document.getElementById('rrSearchInput').value || '').toLowerCase().trim();
            var type = document.getElementById('rrTypeFilter').value;
            var rating = document.getElementById('rrRatingFilter').value;
            var dateFilter = document.getElementById('rrDateFilter').value;
            var minDate = getMinDate(dateFilter);

            return Array.prototype.slice.call(
                document.querySelectorAll('#rrReviewGrid .rr-review-card')
            ).filter(function (card) {
                var first = (card.getAttribute('data-first') || '').toLowerCase();
                var last = (card.getAttribute('data-last') || '').toLowerCase();
                var userId = (card.getAttribute('data-user-id') || '').toLowerCase();
                var title = (card.getAttribute('data-title') || '').toLowerCase();
                var fullName = (first + ' ' + last).trim();

                var cardType = card.getAttribute('data-content-type') || '';
                var cardRating = card.getAttribute('data-rating') || '';
                var cardDate = card.getAttribute('data-date') || '';

                var matchQuery = !query ||
                    fullName.indexOf(query) !== -1 ||
                    first.indexOf(query) !== -1 ||
                    last.indexOf(query) !== -1 ||
                    userId.indexOf(query) !== -1 ||
                    title.indexOf(query) !== -1;

                var matchType = type === 'all' || cardType === type;
                var matchRating = rating === 'all' || cardRating === rating;
                var matchDate = !minDate || (cardDate.length > 0 && cardDate >= minDate);

                return matchQuery && matchType && matchRating && matchDate;
            });
        }

        function renderPagination(total) {
            totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            var pagesEl = document.getElementById('rrPaginationPages');
            if (pagesEl) {
                pagesEl.innerHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    (function (page) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'rr-pagination__btn' +
                            (page === currentPage ? ' rr-pagination__btn--active' : '');
                        btn.textContent = page;
                        btn.addEventListener('click', function () {
                            currentPage = page;
                            renderGrid();
                        });
                        pagesEl.appendChild(btn);
                    })(i);
                }
            }

            var prevBtn = document.getElementById('rrPrevPage');
            if (prevBtn) prevBtn.disabled = currentPage <= 1;

            var nextBtn = document.getElementById('rrNextPage');
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

            document.querySelectorAll('#rrReviewGrid .rr-review-card').forEach(function (card) {
                var idx = cards.indexOf(card);
                card.style.display = (idx !== -1 && idx >= firstIndex && idx < lastIndex) ? '' : 'none';
            });

            var countEl = document.getElementById('rrCount');
            if (countEl) {
                if (total === 0) {
                    countEl.textContent = 'Showing 0 of 0 reviews';
                } else {
                    var start = (currentPage - 1) * PAGE_SIZE + 1;
                    var end = Math.min(currentPage * PAGE_SIZE, total);
                    countEl.textContent = 'Showing ' + start + '\u2013' + end + ' of ' + total + ' reviews';
                }
            }

            var emptyEl = document.getElementById('rrEmptyState');
            if (emptyEl) {
                emptyEl.hidden = total !== 0;
            }

            renderPagination(total);
        }

        // --- Close Buttons ---
        document.querySelectorAll('[data-rr-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-rr-close');
                if (target === 'view') closeModal('rrViewModal');
                else if (target === 'edit') closeModal('rrEditModal');
                else if (target === 'delete') closeModal('rrDeleteModal');
            });
        });

        // --- Close on Escape ---
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal('rrViewModal');
                closeModal('rrEditModal');
                closeModal('rrDeleteModal');
            }
        });

        // --- Close on overlay click ---
        ['rrViewModal', 'rrEditModal', 'rrDeleteModal'].forEach(function (id) {
            var modal = document.getElementById(id);
            if (!modal) return;
            var overlay = modal.querySelector('.sg-modal__overlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    closeModal(id);
                });
            }
        });

        // --- View Review ---
        document.querySelectorAll('[data-rr-action="view"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentCard = btn.closest('.rr-review-card');
                if (!currentCard) return;

                var first = textValue('data-first', '');
                var last = textValue('data-last', '');
                var name = first + ' ' + last;
                var status = textValue('data-status', 'published');
                var type = textValue('data-content-type', 'music');
                var rating = parseInt(textValue('data-rating', '5'), 10) || 5;

                var avatarEl = document.getElementById('rr-view-avatar');
                if (avatarEl) avatarEl.textContent = initials(first, last);

                var setText = function (id, val) {
                    var el = document.getElementById(id);
                    if (el) el.textContent = val;
                };

                setText('rr-view-user', name);
                setText('rr-view-uid', textValue('data-user-id', ''));
                setText('rr-view-title', textValue('data-title', ''));
                setText('rr-view-artist', textValue('data-artist', ''));
                setText('rr-view-album', textValue('data-album', ''));
                setText('rr-view-rating-value', rating.toFixed(1));
                setText('rr-view-review-id', textValue('data-review-id', ''));
                setText('rr-view-text', textValue('data-text', ''));
                setText('rr-view-status-text', status === 'published' ? 'Published' : 'Hidden');

                var dLabel = dateLabel(textValue('data-date', ''));
                setText('rr-view-date', dLabel);

                var updated = textValue('data-updated', '');
                setText('rr-view-updated', updated ? dateLabel(updated) : '\u2014');

                setTypeBadge(document.getElementById('rr-view-type-badge'), type);
                setStatusBadge(document.getElementById('rr-view-status-badge'), status);
                setStars(document.getElementById('rr-view-stars'), rating);

                openModal('rrViewModal');
            });
        });

        // --- Edit Review ---
        document.querySelectorAll('[data-rr-action="edit"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentCard = btn.closest('.rr-review-card');
                if (!currentCard) return;
                populateEditForm();
                openModal('rrEditModal');
            });
        });

        // --- View -> Edit button ---
        var viewEditBtn = document.getElementById('rrViewEditBtn');
        if (viewEditBtn) {
            viewEditBtn.addEventListener('click', function () {
                closeModal('rrViewModal');
                if (!currentCard) return;
                populateEditForm();
                openModal('rrEditModal');
            });
        }

        // --- Star picker ---
        document.querySelectorAll('.rr-star-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                setPicker(parseInt(btn.getAttribute('data-rr-star'), 10));
            });
        });

        // --- Update Review (UI Only, updates the card) ---
        var updateBtn = document.getElementById('rrUpdateReviewBtn');
        if (updateBtn) {
            updateBtn.addEventListener('click', function () {
                if (!currentCard) return;

                var type = document.getElementById('rr-edit-type').value;
                var title = document.getElementById('rr-edit-title').value.trim();
                var artist = document.getElementById('rr-edit-artist').value.trim();
                var album = document.getElementById('rr-edit-album').value.trim();
                var text = document.getElementById('rr-edit-text').value.trim();
                var status = document.getElementById('rr-edit-status').value;

                if (!title) {
                    document.getElementById('rr-edit-title').focus();
                    return;
                }
                if (!text) {
                    document.getElementById('rr-edit-text').focus();
                    return;
                }

                var now = new Date();
                var updatedValue = dateValue(now);
                var updatedLabel = dateLabel(updatedValue);

                // Update card data attributes
                currentCard.setAttribute('data-content-type', type);
                currentCard.setAttribute('data-title', title);
                currentCard.setAttribute('data-artist', artist);
                currentCard.setAttribute('data-album', album);
                currentCard.setAttribute('data-rating', String(selectedRating));
                currentCard.setAttribute('data-text', text);
                currentCard.setAttribute('data-status', status);
                currentCard.setAttribute('data-updated', updatedValue);

                // Update displayed card content
                setTypeBadge(currentCard.querySelector('.rr-type'), type);

                var titleEl = currentCard.querySelector('.rr-review-card__title');
                if (titleEl) titleEl.textContent = title;

                var strongs = currentCard.querySelectorAll('.rr-review-card__meta-item strong');
                if (strongs.length >= 2) {
                    strongs[0].textContent = artist;
                    strongs[1].textContent = album;
                }

                var fill = currentCard.querySelector('.rr-stars__fill');
                if (fill) fill.style.width = (selectedRating * 20) + '%';

                var ratingValue = currentCard.querySelector('.rr-review-card__rating-value');
                if (ratingValue) ratingValue.textContent = selectedRating.toFixed(1);

                var textEl = currentCard.querySelector('.rr-review-card__text');
                if (textEl) textEl.textContent = text;

                setStatusBadge(currentCard.querySelector('.rr-badge'), status);

                var dates = currentCard.querySelector('.rr-review-card__dates');
                if (dates) {
                    var updatedDateEl = currentCard.querySelector('.rr-review-card__date--updated');
                    if (!updatedDateEl) {
                        updatedDateEl = document.createElement('span');
                        updatedDateEl.className = 'rr-review-card__date rr-review-card__date--updated';
                        dates.appendChild(updatedDateEl);
                    }
                    updatedDateEl.textContent = 'Updated: ' + updatedLabel;
                }

                closeModal('rrEditModal');
                renderGrid();

                if (typeof showSuccess === 'function') {
                    showSuccess('Review updated successfully.', 2000);
                }
            });
        }

        // --- Delete Review ---
        document.querySelectorAll('[data-rr-action="delete"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                currentCard = btn.closest('.rr-review-card');
                if (!currentCard) return;

                var first = textValue('data-first', '');
                var last = textValue('data-last', '');
                var name = (first + ' ' + last).trim() || textValue('data-user-id', '');
                var title = textValue('data-title', '');

                var deleteUser = document.getElementById('rr-delete-user');
                if (deleteUser) deleteUser.textContent = name;

                var deleteContent = document.getElementById('rr-delete-content');
                if (deleteContent) deleteContent.textContent = title;

                openModal('rrDeleteModal');
            });
        });

        // --- Confirm Delete (UI Only, removes the card) ---
        var confirmDeleteBtn = document.getElementById('rrConfirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function () {
                if (currentCard && currentCard.parentNode) {
                    currentCard.parentNode.removeChild(currentCard);
                }
                currentCard = null;
                closeModal('rrDeleteModal');
                renderGrid();

                if (typeof showSuccess === 'function') {
                    showSuccess('Review deleted successfully.', 2000);
                }
            });
        }

        // --- Search + Filters ---
        var searchInput = document.getElementById('rrSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', renderGrid);
        }

        ['rrTypeFilter', 'rrRatingFilter', 'rrDateFilter'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', renderGrid);
            }
        });

        // --- Pagination Prev / Next ---
        var prevBtn = document.getElementById('rrPrevPage');
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    renderGrid();
                }
            });
        }

        var nextBtn = document.getElementById('rrNextPage');
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderGrid();
                }
            });
        }

        // --- Edit form submit prevention ---
        var editForm = document.getElementById('rrEditForm');
        if (editForm) {
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();
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