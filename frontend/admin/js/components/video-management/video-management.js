/**
 * SOUND Group -- Video Management
 * AJAX CRUD with file upload, dynamic dropdowns, thumbnail/video preview
 */
(function () {
    'use strict';
    var ENDPOINT = (window.APP_BASE_URL || '') + '/backend/handlers/video-handler.php';
    var BASE_URL = window.APP_BASE_URL || '';
    function resolvePath(p) { return p ? BASE_URL + '/' + p.replace(/^\//, '') : ''; }
    function getCsrfToken() { var el = document.querySelector('input[name="csrf_token"]'); return el ? el.value : ''; }
    function escapeHtml(str) { if (!str) return ''; var d = document.createElement('div'); d.appendChild(document.createTextNode(str)); return d.innerHTML; }
    function openModal(id) { var m = document.getElementById(id); if (m) { m.classList.add('is-open'); document.body.style.overflow = 'hidden'; } }
    function closeModal(id) { var m = document.getElementById(id); if (m) { m.classList.remove('is-open'); document.body.style.overflow = ''; } }
    function postToHandler(fd, onSuccess) {
        var ctrl = new AbortController(); var tid = setTimeout(function () { ctrl.abort(); }, 60000);
        fetch(ENDPOINT, { method: 'POST', body: fd, signal: ctrl.signal })
            .then(function (res) { clearTimeout(tid); return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (r) { if (!r.ok || !r.data.success) { if (r.data.redirect) { window.location.href = r.data.redirect; return; } showError(r.data.error || 'Something went wrong.'); return; } onSuccess(r.data); })
            .catch(function () { clearTimeout(tid); showError('Server could not be reached.'); });
    }
    function populateSelect(el, items, ph) {
        if (!el) return; el.innerHTML = '<option value="">' + (ph || 'Select') + '</option>';
        items.forEach(function (i) { var o = document.createElement('option'); o.value = i.id; o.textContent = i.name; el.appendChild(o); });
    }
    function selectById(el, id) { if (!el || !id) return; el.value = String(id); }
    var videoData = [], cmData = {};
    function getVideoById(id) { for (var i = 0; i < videoData.length; i++) { if (videoData[i].id === id) return videoData[i]; } return null; }

    /* --- Preview Modal (Thumbnail → Video Player) --- */
    function openPreviewModal(id) {
        var v = getVideoById(id); if (!v) return;
        var container = document.getElementById('vm-preview-container');
        var thumb = document.getElementById('vm-preview-thumb');
        var fallback = document.getElementById('vm-preview-fallback');
        var playBtn = document.getElementById('vm-preview-play-btn');
        var video = document.getElementById('vm-preview-video');
        var title = document.getElementById('vm-preview-title');
        if (title) title.textContent = v.video_title || '';
        if (thumb) { thumb.style.display = 'none'; thumb.src = ''; }
        if (fallback) fallback.style.display = 'none';
        if (playBtn) { playBtn.style.display = ''; playBtn.classList.remove('vm-preview-play-btn--hidden'); }
        if (video) { video.style.display = 'none'; video.src = ''; video.pause(); }
        if (v.thumbnail_path) {
            var imgSrc = resolvePath(v.thumbnail_path);
            if (thumb) {
                thumb.onload = function () { if (fallback) fallback.style.display = 'none'; };
                thumb.onerror = function () { thumb.style.display = 'none'; if (fallback) fallback.style.display = ''; };
                thumb.src = imgSrc;
                thumb.alt = escapeHtml(v.video_title);
                thumb.style.display = '';
            }
        } else {
            if (fallback) fallback.style.display = '';
        }
        if (!v.video_path) {
            if (playBtn) playBtn.style.display = 'none';
        }
        if (container) container.setAttribute('data-vm-preview-id', id);
        openModal('vmPreviewModal');
    }
    function closePreviewModal() {
        var video = document.getElementById('vm-preview-video');
        var thumb = document.getElementById('vm-preview-thumb');
        var fallback = document.getElementById('vm-preview-fallback');
        var playBtn = document.getElementById('vm-preview-play-btn');
        if (video) { video.pause(); video.src = ''; video.style.display = 'none'; }
        if (thumb) { thumb.style.display = 'none'; thumb.src = ''; }
        if (fallback) fallback.style.display = 'none';
        if (playBtn) { playBtn.style.display = ''; playBtn.classList.remove('vm-preview-play-btn--hidden'); }
        closeModal('vmPreviewModal');
    }
    function bindPreviewModal() {
        var playBtn = document.getElementById('vm-preview-play-btn');
        if (playBtn) {
            playBtn.addEventListener('click', function () {
                var container = document.getElementById('vm-preview-container');
                var id = parseInt(container.getAttribute('data-vm-preview-id'));
                var v = getVideoById(id);
                if (!v || !v.video_path) { showError('No video file available.'); return; }
                var thumb = document.getElementById('vm-preview-thumb');
                var fallback = document.getElementById('vm-preview-fallback');
                var video = document.getElementById('vm-preview-video');
                if (thumb) thumb.style.display = 'none';
                if (fallback) fallback.style.display = 'none';
                if (playBtn) { playBtn.classList.add('vm-preview-play-btn--hidden'); }
                if (video) {
                    video.style.display = '';
                    video.src = resolvePath(v.video_path);
                    video.load();
                    video.play().catch(function () { });
                    video.onerror = function () { showError('Failed to load the video file.'); closePreviewModal(); };
                }
            });
        }
        var thumbEl = document.getElementById('vm-preview-thumb');
        if (thumbEl) {
            thumbEl.addEventListener('click', function () {
                if (playBtn) playBtn.click();
            });
        }
    }
    function init() {
        var cj = document.getElementById('vmCategoriesJson'); if (cj) { try { cmData = JSON.parse(cj.textContent || '{}'); } catch (e) { cmData = {}; } }
        var vj = document.getElementById('vmVideosJson'); if (vj) { try { videoData = JSON.parse(vj.textContent || '[]'); } catch (e) { videoData = []; } }
        var artists = cmData.artists || [], albums = cmData.albums || [], years = cmData.air || [], genres = cmData.genres || [], languages = cmData.languages || [];
        populateSelect(document.getElementById('vm-add-artist'), artists, 'Select artist');
        populateSelect(document.getElementById('vm-add-album'), albums, 'Select album');
        populateSelect(document.getElementById('vm-add-year'), years, 'Select year');
        populateSelect(document.getElementById('vm-add-genre'), genres, 'Select genre');
        populateSelect(document.getElementById('vm-add-language'), languages, 'Select language');
        populateSelect(document.getElementById('vm-edit-artist'), artists, 'Select artist');
        populateSelect(document.getElementById('vm-edit-album'), albums, 'Select album');
        populateSelect(document.getElementById('vm-edit-year'), years, 'Select year');
        populateSelect(document.getElementById('vm-edit-genre'), genres, 'Select genre');
        populateSelect(document.getElementById('vm-edit-language'), languages, 'Select language');
        ['vmAddForm', 'vmEditForm'].forEach(function (fid) { var f = document.getElementById(fid); if (f) f.addEventListener('submit', function (e) { e.preventDefault(); }); });
        renderTable(videoData); bindTableActions(); bindAddModal(); bindEditModal(); bindDeleteModal(); bindViewModal(); bindSearch(); bindModalClose(); bindPreviewModal();
    }
    function renderTable(records) {
        var tbody = document.getElementById('vmTableBody'), countEl = document.getElementById('vmCount');
        var cardsEl = document.getElementById('vmMobileCards');
        if (!tbody) return;
        if (!records || records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:#999;">No videos found.</td></tr>';
            if (cardsEl) cardsEl.innerHTML = '<p style="text-align:center;padding:40px;color:#999;">No videos found.</p>';
            if (countEl) countEl.textContent = 'Showing 0 videos';
            return;
        }
        var html = '', cardHtml = '';
        records.forEach(function (v) {
            var sc = 'vm-badge--active', sl = 'Active';
            if (v.status === 'draft') { sc = 'vm-badge--draft'; sl = 'Draft'; }
            else if (v.status === 'inactive') { sc = 'vm-badge--inactive'; sl = 'Inactive'; }
            var hasVid = !!v.video_path;
            var thumbH = v.thumbnail_path
                ? '<img src="' + escapeHtml(resolvePath(v.thumbnail_path)) + '" alt="Thumb" class="vm-video-thumb-img">'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
            var thumbWrap = '<div class="vm-video-thumb"' + (hasVid ? ' data-vm-has-video="true" data-vm-preview="' + v.id + '"' : '') + '>' + thumbH + '</div>';
            html += '<tr class="vm-table__row" data-vm-row-id="' + v.id + '">'
                + '<td class="vm-table__cell">' + thumbWrap + '</td>'
                + '<td class="vm-table__cell"><span class="vm-table__cell--bold">' + escapeHtml(v.video_title) + '</span></td>'
                + '<td class="vm-table__cell">' + escapeHtml(v.artist_name || '-') + '</td>'
                + '<td class="vm-table__cell">' + escapeHtml(v.album_name || '-') + '</td>'
                + '<td class="vm-table__cell">' + escapeHtml(v.year_name || '-') + '</td>'
                + '<td class="vm-table__cell"><span class="vm-badge vm-badge--genre">' + escapeHtml(v.genre_name || '-') + '</span></td>'
                + '<td class="vm-table__cell">' + escapeHtml(v.language_name || '-') + '</td>'
                + '<td class="vm-table__cell"><span class="vm-badge ' + sc + '">' + sl + '</span></td>'
                + '<td class="vm-table__cell vm-table__cell--actions"><div class="vm-actions">'
                + '<button type="button" class="vm-action-btn vm-action-btn--view" title="View" data-vm-action="view" data-vm-id="' + v.id + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
                + '<button type="button" class="vm-action-btn vm-action-btn--edit" title="Edit" data-vm-action="edit" data-vm-id="' + v.id + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>'
                + '<button type="button" class="vm-action-btn vm-action-btn--delete" title="Delete" data-vm-action="delete" data-vm-id="' + v.id + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>'
                + '</div></td></tr>';
            var thumbCard = v.thumbnail_path
                ? '<img src="' + escapeHtml(resolvePath(v.thumbnail_path)) + '" alt="Thumbnail" class="vm-mobile-card__thumb-img">'
                : '<div class="vm-mobile-card__thumb-placeholder"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>';
            cardHtml += '<div class="vm-mobile-card" data-vm-row-id="' + v.id + '">'
                + '<div class="vm-mobile-card__thumb-wrap"' + (hasVid ? ' data-vm-has-video="true" data-vm-preview="' + v.id + '"' : '') + '>' + thumbCard + '</div>'
                + '<div class="vm-mobile-card__body">'
                + '<div class="vm-mobile-card__title" title="' + escapeHtml(v.video_title) + '">' + escapeHtml(v.video_title) + '</div>'
                + '<div class="vm-mobile-card__meta">'
                + '<div class="vm-mobile-card__meta-row"><span class="vm-mobile-card__meta-label">Artist</span><span class="vm-mobile-card__meta-value">' + escapeHtml(v.artist_name || '-') + '</span></div>'
                + '<div class="vm-mobile-card__meta-row"><span class="vm-mobile-card__meta-label">Album</span><span class="vm-mobile-card__meta-value">' + escapeHtml(v.album_name || '-') + '</span></div>'
                + '<div class="vm-mobile-card__meta-row"><span class="vm-mobile-card__meta-label">Year</span><span class="vm-mobile-card__meta-value">' + escapeHtml(v.year_name || '-') + '</span></div>'
                + '<div class="vm-mobile-card__meta-row"><span class="vm-mobile-card__meta-label">Genre</span><span class="vm-mobile-card__meta-value">' + escapeHtml(v.genre_name || '-') + '</span></div>'
                + '<div class="vm-mobile-card__meta-row"><span class="vm-mobile-card__meta-label">Language</span><span class="vm-mobile-card__meta-value">' + escapeHtml(v.language_name || '-') + '</span></div>'
                + '</div></div>'
                + '<div class="vm-mobile-card__footer">'
                + '<span class="vm-badge ' + sc + ' vm-mobile-card__badge">' + sl + '</span>'
                + '<div class="vm-mobile-card__actions">'
                + '<button type="button" class="vm-action-btn vm-action-btn--view" title="View" data-vm-action="view" data-vm-id="' + v.id + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
                + '<button type="button" class="vm-action-btn vm-action-btn--edit" title="Edit" data-vm-action="edit" data-vm-id="' + v.id + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>'
                + '<button type="button" class="vm-action-btn vm-action-btn--delete" title="Delete" data-vm-action="delete" data-vm-id="' + v.id + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>'
                + '</div></div></div>';
        });
        tbody.innerHTML = html;
        if (cardsEl) cardsEl.innerHTML = cardHtml;
        if (countEl) countEl.textContent = 'Showing ' + records.length + ' video' + (records.length !== 1 ? 's' : '');
    }
    function bindTableActions() {
        var tbody = document.getElementById('vmTableBody');
        var cardsEl = document.getElementById('vmMobileCards');
        function handleAction(e) {
            var t = e.target.closest('[data-vm-preview]');
            if (t) { e.preventDefault(); e.stopPropagation(); openPreviewModal(parseInt(t.getAttribute('data-vm-preview'))); return; }
            var a = e.target.closest('[data-vm-action]');
            if (!a) return;
            var action = a.getAttribute('data-vm-action'), vid = parseInt(a.getAttribute('data-vm-id'));
            if (action === 'view') openViewModal(vid); else if (action === 'edit') openEditModal(vid); else if (action === 'delete') openDeleteModal(vid);
        }
        if (tbody) tbody.addEventListener('click', handleAction);
        if (cardsEl) cardsEl.addEventListener('click', handleAction);
    }

    function bindModalClose() {
        document.querySelectorAll('[data-vm-close]').forEach(function (b) { b.addEventListener('click', function () { var t = b.getAttribute('data-vm-close'); if (t === 'preview') closePreviewModal(); else if (t === 'add') closeModal('vmAddModal'); else if (t === 'edit') closeModal('vmEditModal'); else if (t === 'view') closeModal('vmViewModal'); else if (t === 'delete') closeModal('vmDeleteModal'); }); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closePreviewModal(); closeModal('vmAddModal'); closeModal('vmEditModal'); closeModal('vmViewModal'); closeModal('vmDeleteModal'); } });
        document.querySelectorAll('.sg-modal').forEach(function (m) { var o = m.querySelector('.sg-modal__overlay'); if (o) { o.addEventListener('click', function () { if (m.id === 'vmPreviewModal') closePreviewModal(); else { m.classList.remove('is-open'); document.body.style.overflow = ''; } }); } });
    }
    function setupFilePreview(inputId, uploadId, previewId, nameId, removeId, imgId) {
        var inp = document.getElementById(inputId), up = document.getElementById(uploadId), pv = document.getElementById(previewId);
        var nm = document.getElementById(nameId), rm = document.getElementById(removeId), im = imgId ? document.getElementById(imgId) : null;
        if (inp) { inp.addEventListener('change', function () { if (inp.files && inp.files.length > 0) { var f = inp.files[0]; if (nm) nm.textContent = f.name; if (im && f.type && f.type.startsWith('image/')) { var r = new FileReader(); r.onload = function (e) { im.src = e.target.result; im.style.display = ''; }; r.readAsDataURL(f); } if (up) up.style.display = 'none'; if (pv) pv.style.display = ''; } }); }
        if (rm) { rm.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); if (inp) inp.value = ''; if (im) { im.src = ''; im.style.display = 'none'; } if (up) up.style.display = ''; if (pv) pv.style.display = 'none'; }); }
    }
    function resetAddForm() {
        var f = document.getElementById('vmAddForm'); if (f) f.reset();
        var vu = document.getElementById('vm-video-upload'), vp = document.getElementById('vm-video-preview'), tu = document.getElementById('vm-thumb-upload'), tp = document.getElementById('vm-thumb-preview');
        if (vu) vu.style.display = ''; if (vp) vp.style.display = 'none'; if (tu) tu.style.display = ''; if (tp) tp.style.display = 'none';
    }
    function bindAddModal() {
        var ab = document.getElementById('vmAddVideoBtn');
        if (ab) { ab.addEventListener('click', function () { resetAddForm(); openModal('vmAddModal'); }); }
        setupFilePreview('vm-add-video-file', 'vm-video-upload', 'vm-video-preview', 'vm-video-file-name', 'vm-video-file-remove', null);
        setupFilePreview('vm-add-thumb-image', 'vm-thumb-upload', 'vm-thumb-preview', 'vm-thumb-file-name', 'vm-thumb-file-remove', 'vm-thumb-preview-img');
        var sb = document.getElementById('vmSaveVideoBtn');
        if (sb) {
            sb.addEventListener('click', function () {
                var t = document.getElementById('vm-add-title').value.trim(), a = document.getElementById('vm-add-artist').value, y = document.getElementById('vm-add-year').value;
                if (!t) { showError('Video title is required.'); return; } if (!a) { showError('Please select an artist.'); return; } if (!y) { showError('Please select a year.'); return; }
                var vf = document.getElementById('vm-add-video-file').files[0]; if (!vf) { showError('Please upload a video file.'); return; }
                var fd = new FormData(); fd.append('action', 'add'); fd.append('csrf_token', getCsrfToken()); fd.append('video_title', t);
                fd.append('artist_id', a); fd.append('album_id', document.getElementById('vm-add-album').value || '0');
                fd.append('year_id', y); fd.append('genre_id', document.getElementById('vm-add-genre').value || '0');
                fd.append('language_id', document.getElementById('vm-add-language').value || '0');
                fd.append('description', document.getElementById('vm-add-description').value.trim());
                fd.append('duration', document.getElementById('vm-add-duration').value.trim());
                fd.append('status', document.getElementById('vm-add-status').value); fd.append('video_file', vf);
                var tf = document.getElementById('vm-add-thumb-image').files[0]; if (tf) fd.append('thumbnail', tf);
                sb.disabled = true;
                postToHandler(fd, function (data) { videoData.unshift(data.record); renderTable(videoData); closeModal('vmAddModal'); resetAddForm(); showSuccess(data.message || 'Video added successfully.'); });
                sb.disabled = false;
            });
        }
    }
    function bindEditModal() {
        var ub = document.getElementById('vmUpdateVideoBtn');
        if (ub) {
            ub.addEventListener('click', function () {
                var id = document.getElementById('vm-edit-id').value, t = document.getElementById('vm-edit-title').value.trim();
                var a = document.getElementById('vm-edit-artist').value, y = document.getElementById('vm-edit-year').value;
                if (!id) return; if (!t) { showError('Video title is required.'); return; } if (!a) { showError('Please select an artist.'); return; } if (!y) { showError('Please select a year.'); return; }
                var fd = new FormData(); fd.append('action', 'edit'); fd.append('csrf_token', getCsrfToken()); fd.append('id', id);
                fd.append('video_title', t); fd.append('artist_id', a); fd.append('album_id', document.getElementById('vm-edit-album').value || '0');
                fd.append('year_id', y); fd.append('genre_id', document.getElementById('vm-edit-genre').value || '0');
                fd.append('language_id', document.getElementById('vm-edit-language').value || '0');
                fd.append('description', document.getElementById('vm-edit-description').value.trim());
                fd.append('duration', document.getElementById('vm-edit-duration').value.trim());
                fd.append('status', document.getElementById('vm-edit-status').value);
                var vf = document.getElementById('vm-edit-video-file').files[0]; if (vf) fd.append('video_file', vf);
                var tf = document.getElementById('vm-edit-thumb-image').files[0]; if (tf) fd.append('thumbnail', tf);
                ub.disabled = true;
                postToHandler(fd, function (data) { for (var i = 0; i < videoData.length; i++) { if (videoData[i].id === data.record.id) { videoData[i] = data.record; break; } } renderTable(videoData); closeModal('vmEditModal'); showSuccess(data.message || 'Video updated successfully.'); });
                ub.disabled = false;
            });
        }
        setupFilePreview('vm-edit-video-file', 'vm-edit-video-upload', 'vm-edit-video-preview', 'vm-edit-video-name-file', 'vm-edit-video-replace', null);
        setupFilePreview('vm-edit-thumb-image', 'vm-edit-thumb-upload', 'vm-edit-thumb-preview', 'vm-edit-thumb-name', 'vm-edit-thumb-replace', 'vm-edit-thumb-img');
    }
    function openEditModal(id) {
        var v = getVideoById(id); if (!v) return;
        document.getElementById('vm-edit-id').value = v.id;
        document.getElementById('vm-edit-video-name').textContent = v.video_title;
        document.getElementById('vm-edit-title').value = v.video_title;
        selectById(document.getElementById('vm-edit-artist'), v.artist_id);
        selectById(document.getElementById('vm-edit-album'), v.album_id);
        selectById(document.getElementById('vm-edit-year'), v.year_id);
        selectById(document.getElementById('vm-edit-genre'), v.genre_id);
        selectById(document.getElementById('vm-edit-language'), v.language_id);
        document.getElementById('vm-edit-description').value = v.description || '';
        document.getElementById('vm-edit-duration').value = v.duration || '';
        document.getElementById('vm-edit-status').value = v.status;
        var vu = document.getElementById('vm-edit-video-upload'), vp = document.getElementById('vm-edit-video-preview'), vn = document.getElementById('vm-edit-video-name-file'), vfi = document.getElementById('vm-edit-video-file');
        if (v.video_path) { if (vu) vu.style.display = 'none'; if (vp) vp.style.display = ''; if (vn) vn.textContent = v.video_path.split('/').pop(); } else { if (vu) vu.style.display = ''; if (vp) vp.style.display = 'none'; }
        if (vfi) vfi.value = '';
        var tu = document.getElementById('vm-edit-thumb-upload'), tp = document.getElementById('vm-edit-thumb-preview'), tn = document.getElementById('vm-edit-thumb-name');
        var ti = document.getElementById('vm-edit-thumb-img'), tph = document.getElementById('vm-edit-thumb-placeholder'), tfi = document.getElementById('vm-edit-thumb-image');
        if (v.thumbnail_path) { if (tu) tu.style.display = 'none'; if (tp) tp.style.display = ''; if (ti) { ti.src = resolvePath(v.thumbnail_path); ti.style.display = ''; } if (tph) tph.style.display = 'none'; if (tn) tn.textContent = v.thumbnail_path.split('/').pop(); } else { if (tu) tu.style.display = ''; if (tp) tp.style.display = 'none'; }
        if (tfi) tfi.value = '';
        openModal('vmEditModal');
    }
    function bindDeleteModal() {
        var cb = document.getElementById('vmConfirmDeleteBtn');
        if (cb) {
            cb.addEventListener('click', function () {
                var id = document.getElementById('vm-delete-id').value; if (!id) return;
                cb.disabled = true;
                var fd = new FormData(); fd.append('action', 'delete'); fd.append('csrf_token', getCsrfToken()); fd.append('id', id);
                postToHandler(fd, function (data) { videoData = videoData.filter(function (v) { return v.id !== parseInt(id); }); renderTable(videoData); closeModal('vmDeleteModal'); showSuccess(data.message || 'Video deleted successfully.'); });
                cb.disabled = false;
            });
        }
    }
    function openDeleteModal(id) {
        var v = getVideoById(id); if (!v) return;
        document.getElementById('vm-delete-id').value = id;
        document.getElementById('vm-delete-video-name').textContent = v.video_title;
        openModal('vmDeleteModal');
    }
    function bindViewModal() {
        var eb = document.getElementById('vmViewEditBtn');
        if (eb) {
            eb.addEventListener('click', function () {
                var id = parseInt(eb.getAttribute('data-vm-id'));
                closeModal('vmViewModal'); if (id) openEditModal(id);
            });
        }
    }
    function openViewModal(id) {
        var v = getVideoById(id); if (!v) return;
        document.getElementById('vm-view-title').textContent = v.video_title;
        document.getElementById('vm-view-artist').textContent = v.artist_name;
        document.getElementById('vm-view-album').textContent = v.album_name || '-';
        document.getElementById('vm-view-year').textContent = v.year_name || '-';
        document.getElementById('vm-view-genre').textContent = v.genre_name || '-';
        document.getElementById('vm-view-language').textContent = v.language_name || '-';
        document.getElementById('vm-view-description').textContent = v.description || 'No description available.';
        var sf = document.getElementById('vm-view-video-file');
        sf.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg> ' + (v.video_path ? v.video_path.split('/').pop() : 'No file');
        var st = document.getElementById('vm-view-status');
        st.textContent = v.status.charAt(0).toUpperCase() + v.status.slice(1);
        st.className = 'vm-badge'; if (v.status === 'active') st.classList.add('vm-badge--active'); else if (v.status === 'draft') st.classList.add('vm-badge--draft'); else st.classList.add('vm-badge--inactive');
        var ti = document.getElementById('vm-view-thumb-img'), ic = document.getElementById('vm-view-thumb-icon');
        if (v.thumbnail_path) { ti.src = resolvePath(v.thumbnail_path); ti.style.display = ''; ic.style.display = 'none'; } else { ti.style.display = 'none'; ic.style.display = ''; }
        var pp = document.getElementById('vm-view-player'), vd = document.getElementById('vm-view-video'), eb = document.getElementById('vmViewEditBtn');
        if (v.video_path) { pp.style.display = ''; vd.src = resolvePath(v.video_path); } else { pp.style.display = 'none'; vd.src = ''; }
        if (eb) eb.setAttribute('data-vm-id', v.id);
        openModal('vmViewModal');
    }
    function bindSearch() {
        var si = document.getElementById('vmSearchInput');
        if (!si) return;
        si.addEventListener('input', function () {
            var q = si.value.toLowerCase().trim();
            var filtered = q ? videoData.filter(function (v) { return (v.video_title + ' ' + v.artist_name + ' ' + v.album_name + ' ' + v.year_name + ' ' + v.genre_name + ' ' + v.language_name).toLowerCase().indexOf(q) !== -1; }) : videoData;
            renderTable(filtered);
        });
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
