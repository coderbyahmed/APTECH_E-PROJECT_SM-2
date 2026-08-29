/**
 * SOUND Group — Category Management
 * Handles: modal open/close, AJAX add/edit/delete, list rendering, search
 */
(function () {
    'use strict';

    var addCategories = ['year', 'artist', 'album', 'genre', 'language'];

    var categoryConfig = {
        year:     { table: 'air',       label: 'Year',   nameHeader: 'Year',     inputType: 'number', singular: 'year' },
        artist:   { table: 'artists',   label: 'Artist', nameHeader: 'Artist Name', inputType: 'text',  singular: 'artist' },
        album:    { table: 'albums',    label: 'Album',  nameHeader: 'Album Name',  inputType: 'text',  singular: 'album' },
        genre:    { table: 'genres',    label: 'Genre',  nameHeader: 'Genre Name',  inputType: 'text',  singular: 'genre' },
        language: { table: 'languages', label: 'Language', nameHeader: 'Language Name', inputType: 'text', singular: 'language' }
    };

    var addButtons = {
        year: 'cmAddYearBtn',
        artist: 'cmAddArtistBtn',
        album: 'cmAddAlbumBtn',
        genre: 'cmAddGenreBtn',
        language: 'cmAddLanguageBtn'
    };

    var ENDPOINT = (window.APP_BASE_URL || '') + '/backend/handlers/category-handler.php';

    function getCsrfToken() {
        var el = document.querySelector('input[name="csrf_token"]');
        return el ? el.value : '';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        var d = new Date(dateStr.replace(' ', 'T'));
        if (isNaN(d.getTime())) return dateStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[d.getMonth()] + ' ' + ('0' + d.getDate()).slice(-2) + ', ' + d.getFullYear();
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function getCategoryFromPage() {
        var body = document.body;
        if (body.classList.contains('cm-page--year')) return 'year';
        if (body.classList.contains('cm-page--artist')) return 'artist';
        if (body.classList.contains('cm-page--album')) return 'album';
        if (body.classList.contains('cm-page--genre')) return 'genre';
        if (body.classList.contains('cm-page--language')) return 'language';
        return null;
    }

    function postToHandler(fd, onSuccess) {
        var controller = new AbortController();
        var timeoutId = setTimeout(function () { controller.abort(); }, 10000);

        fetch(ENDPOINT, { method: 'POST', body: fd, signal: controller.signal })
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
                    return;
                }
                onSuccess(r.data);
            })
            .catch(function () {
                clearTimeout(timeoutId);
                showError('Something went wrong. Server could not be reached. Please try again.');
            });
    }

    function init() {
        // --- Modal Helpers ---
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

        function closeModalByCategory(cat) {
            closeModal('cm' + cat.charAt(0).toUpperCase() + cat.slice(1) + 'Modal');
        }

        function openModalByCategory(cat) {
            openModal('cm' + cat.charAt(0).toUpperCase() + cat.slice(1) + 'Modal');
        }

        function clearInput(cat) {
            var input = document.getElementById('cm-' + cat + '-input');
            if (input) input.value = '';
        }

        function resetButton(btn) {
            if (btn) {
                btn.disabled = false;
                btn.dataset.sgLoading = '';
            }
        }

        // --- Open Add Modal on Card Button Click (Index Page) ---
        addCategories.forEach(function (category) {
            var btn = document.querySelector('[data-cm-open="' + category + '"]');
            if (btn) {
                btn.addEventListener('click', function () {
                    clearInput(category);
                    openModalByCategory(category);
                });
            }
        });

        // --- Close Buttons ---
        var closeButtons = document.querySelectorAll('[data-cm-close]');
        closeButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-cm-close');
                if (target === 'edit') {
                    closeModal('cmEditModal');
                } else if (target === 'delete') {
                    closeModal('cmDeleteModal');
                } else {
                    closeModalByCategory(target);
                }
            });
        });

        // --- Close modals on Escape ---
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal('cmEditModal');
                closeModal('cmDeleteModal');
                addCategories.forEach(function (cat) {
                    closeModalByCategory(cat);
                });
            }
        });

        // --- Close modals on overlay click ---
        document.querySelectorAll('.sg-modal').forEach(function (modal) {
            var overlay = modal.querySelector('.sg-modal__overlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    modal.classList.remove('is-open');
                    document.body.style.overflow = '';
                });
            }
        });

        // --- Form Submit Prevention ---
        document.querySelectorAll('.cm-form').forEach(function (form) {
            form.addEventListener('submit', function (e) { e.preventDefault(); });
        });

        // =============================================
        // Index Page: Add Buttons (AJAX)
        // =============================================
        addCategories.forEach(function (category) {
            var addBtn = document.getElementById(addButtons[category]);
            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    var input = document.getElementById('cm-' + category + '-input');
                    var value = input ? input.value.trim() : '';

                    if (!value) {
                        if (input) input.focus();
                        return;
                    }

                    addBtn.disabled = true;
                    addBtn.dataset.sgLoading = 'true';

                    var fd = new FormData();
                    fd.append('action', 'add');
                    fd.append('category', category);
                    fd.append('name', value);
                    fd.append('csrf_token', getCsrfToken());

                    postToHandler(fd, function (data) {
                        showSuccess(data.message || categoryConfig[category].label + ' added successfully.', 2000);
                        closeModalByCategory(category);
                        if (input) input.value = '';
                    });

                    addBtn.disabled = false;
                    addBtn.dataset.sgLoading = '';
                });
            }
        });

        // =============================================
        // List Page: Edit & Delete
        // =============================================
        var pageCategory = getCategoryFromPage();
        if (pageCategory) {
            var cfg = categoryConfig[pageCategory];

            // --- Render table rows from data attributes (PHP-injected JSON) ---
            var tbody = document.querySelector('.cm-table__tbody');
            var recordsJson = document.getElementById('cmRecordsJson');
            if (tbody && recordsJson) {
                var records = JSON.parse(recordsJson.textContent || '[]');
                var html = '';
                records.forEach(function (r, i) {
                    var editDataAttr = 'data-cm-id="' + r.id + '" data-cm-name="' + escapeHtml(r.name) + '"';
                    html += '<tr class="cm-table__row" data-cm-row-id="' + r.id + '">'
                        + '<td class="cm-table__cell">' + (i + 1) + '</td>'
                        + '<td class="cm-table__cell cm-table__cell--bold">' + escapeHtml(r.name) + '</td>'
                        + '<td class="cm-table__cell">' + formatDate(r.created_at) + '</td>'
                        + '<td class="cm-table__cell cm-table__cell--actions">'
                        + '<div class="cm-actions">'
                        + '<button type="button" class="cm-action-btn cm-action-btn--edit" title="Edit" data-cm-action="edit" ' + editDataAttr + '>'
                        + '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">'
                        + '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>'
                        + '<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>'
                        + '</svg></button>'
                        + '<button type="button" class="cm-action-btn cm-action-btn--delete" title="Delete" data-cm-action="delete" ' + editDataAttr + '>'
                        + '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">'
                        + '<polyline points="3 6 5 6 21 6"/>'
                        + '<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>'
                        + '</svg></button>'
                        + '</div></td></tr>';
                });
                tbody.innerHTML = html;
            }

            // --- Update footer count ---
            function updateFooterCount() {
                var visible = document.querySelectorAll('.cm-table__row:not([style*="display: none"])').length;
                var countEl = document.querySelector('.cm-table-card__count');
                if (countEl) {
                    countEl.textContent = 'Showing ' + visible + ' ' + cfg.singular + (visible !== 1 ? 's' : '');
                }
            }

            // --- Rebind edit/delete handlers after render ---
            function bindListActions() {
                // Edit buttons
                document.querySelectorAll('[data-cm-action="edit"]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var id = btn.getAttribute('data-cm-id');
                        var name = btn.getAttribute('data-cm-name') || '';
                        var editInput = document.getElementById('cm-edit-input');
                        var editIdField = document.getElementById('cm-edit-id');
                        if (editInput) editInput.value = name;
                        if (editIdField) editIdField.value = id;
                        openModal('cmEditModal');
                    });
                });

                // Delete buttons
                document.querySelectorAll('[data-cm-action="delete"]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var id = btn.getAttribute('data-cm-id');
                        var name = btn.getAttribute('data-cm-name') || '';
                        var deleteName = document.getElementById('cm-delete-name');
                        var deleteIdField = document.getElementById('cm-delete-id');
                        if (deleteName) deleteName.textContent = name;
                        if (deleteIdField) deleteIdField.value = id;
                        openModal('cmDeleteModal');
                    });
                });
            }

            bindListActions();

            // --- Update Button (AJAX) ---
            var updateBtn = document.getElementById('cmUpdateBtn');
            if (updateBtn) {
                updateBtn.addEventListener('click', function () {
                    var editInput = document.getElementById('cm-edit-input');
                    var editIdField = document.getElementById('cm-edit-id');
                    var value = editInput ? editInput.value.trim() : '';
                    var id = editIdField ? editIdField.value : '';

                    if (!value) {
                        if (editInput) editInput.focus();
                        return;
                    }
                    if (!id) return;

                    updateBtn.disabled = true;

                    var fd = new FormData();
                    fd.append('action', 'edit');
                    fd.append('category', pageCategory);
                    fd.append('id', id);
                    fd.append('name', value);
                    fd.append('csrf_token', getCsrfToken());

                    postToHandler(fd, function (data) {
                        showSuccess(data.message || cfg.label + ' updated successfully.', 2000);
                        closeModal('cmEditModal');

                        // Update the row in the table
                        var row = document.querySelector('[data-cm-row-id="' + id + '"]');
                        if (row) {
                            var nameCell = row.querySelector('.cm-table__cell--bold');
                            if (nameCell) nameCell.textContent = value;
                            var editBtnInRow = row.querySelector('[data-cm-action="edit"]');
                            var deleteBtnInRow = row.querySelector('[data-cm-action="delete"]');
                            if (editBtnInRow) editBtnInRow.setAttribute('data-cm-name', value);
                            if (deleteBtnInRow) deleteBtnInRow.setAttribute('data-cm-name', value);
                        }
                    });

                    updateBtn.disabled = false;
                });
            }

            // --- Confirm Delete Button (AJAX) ---
            var confirmDeleteBtn = document.getElementById('cmConfirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function () {
                    var deleteIdField = document.getElementById('cm-delete-id');
                    var id = deleteIdField ? deleteIdField.value : '';

                    if (!id) return;

                    confirmDeleteBtn.disabled = true;

                    var fd = new FormData();
                    fd.append('action', 'delete');
                    fd.append('category', pageCategory);
                    fd.append('id', id);
                    fd.append('csrf_token', getCsrfToken());

                    postToHandler(fd, function (data) {
                        showSuccess(data.message || cfg.label + ' deleted successfully.', 2000);
                        closeModal('cmDeleteModal');

                        // Remove the row from the table
                        var row = document.querySelector('[data-cm-row-id="' + id + '"]');
                        if (row) row.remove();

                        // Re-number remaining rows
                        var rows = document.querySelectorAll('.cm-table__row');
                        rows.forEach(function (r, i) {
                            var firstCell = r.querySelector('.cm-table__cell');
                            if (firstCell) firstCell.textContent = (i + 1);
                        });

                        updateFooterCount();
                    });

                    confirmDeleteBtn.disabled = false;
                });
            }

            // =============================================
            // List Page: Search Filtering
            // =============================================
            var searchInput = document.getElementById('cmSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    var query = searchInput.value.toLowerCase().trim();
                    var rows = document.querySelectorAll('.cm-table__row');

                    rows.forEach(function (row) {
                        var cells = row.querySelectorAll('.cm-table__cell');
                        var match = false;
                        cells.forEach(function (cell) {
                            if (cell.textContent.toLowerCase().indexOf(query) !== -1) {
                                match = true;
                            }
                        });
                        row.style.display = match ? '' : 'none';
                    });

                    updateFooterCount();
                });
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
