/**
 * SOUND Group — Music Management
 * AJAX CRUD with file upload, dynamic dropdowns, audio preview
 * Persistent background audio player with localStorage persistence
 */
(function () {
    'use strict';
    var ENDPOINT = '/Aptech_E_Project_02/sound_management/backend/handlers/music-handler.php';
    var BASE_URL = '/Aptech_E_Project_02/sound_management';
    var STORAGE_KEY = 'mm_audio_state';
    function resolvePath(p) { return p ? BASE_URL + '/' + p.replace(/^\//, '') : ''; }
    function getCsrfToken() { var el = document.querySelector('input[name="csrf_token"]'); return el ? el.value : ''; }
    function escapeHtml(str) { if (!str) return ''; var d = document.createElement('div'); d.appendChild(document.createTextNode(str)); return d.innerHTML; }
    function openModal(id) { var m = document.getElementById(id); if (m) { m.classList.add('is-open'); document.body.style.overflow = 'hidden'; } }
    function closeModal(id) { var m = document.getElementById(id); if (m) { m.classList.remove('is-open'); document.body.style.overflow = ''; } }
    function postToHandler(fd, onSuccess) {
        var ctrl = new AbortController(); var tid = setTimeout(function () { ctrl.abort(); }, 30000);
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
    var musicData = [], cmData = {}, inlinePlayingId = null, mmInlineAudio = null;
    function getMusicById(id) { for (var i = 0; i < musicData.length; i++) { if (musicData[i].id === id) return musicData[i]; } return null; }

    /* --- localStorage persistence helpers --- */
    function saveAudioState(state) { try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch (e) {} }
    function loadAudioState() { try { var s = localStorage.getItem(STORAGE_KEY); return s ? JSON.parse(s) : null; } catch (e) { return null; } }
    function clearAudioState() { try { localStorage.removeItem(STORAGE_KEY); } catch (e) {} }

    /* --- Create persistent audio element (IIFE top-level, survives navigation) --- */
    mmInlineAudio = document.createElement('audio');
    mmInlineAudio.id = 'mmInlineAudio';
    mmInlineAudio.preload = 'auto';
    document.body.appendChild(mmInlineAudio);

    /* --- Restore state from localStorage on page load --- */
    var _saved = loadAudioState();
    if (_saved && _saved.src) {
        mmInlineAudio.src = _saved.src;
        if (_saved.currentTime && _saved.currentTime > 0) mmInlineAudio.currentTime = _saved.currentTime;
        inlinePlayingId = _saved.songId || null;
        if (_saved.playing) { mmInlineAudio.play().catch(function () {}); }
    }

    /* --- Periodic state save on timeupdate --- */
    var _lastSave = 0;
    mmInlineAudio.addEventListener('timeupdate', function () {
        var now = Date.now();
        if (now - _lastSave > 2000 && !mmInlineAudio.paused && inlinePlayingId) {
            _lastSave = now;
            saveAudioState({ songId: inlinePlayingId, src: mmInlineAudio.src, currentTime: mmInlineAudio.currentTime, playing: true });
        }
    });
    mmInlineAudio.addEventListener('ended', function () { inlinePlayingId = null; clearAudioState(); updateInlineIcons(); });
    mmInlineAudio.addEventListener('pause', function () {
        updateInlineIcons();
        if (inlinePlayingId) {
            saveAudioState({ songId: inlinePlayingId, src: mmInlineAudio.src, currentTime: mmInlineAudio.currentTime, playing: false });
        }
    });
    mmInlineAudio.addEventListener('play', function () {
        updateInlineIcons();
        if (inlinePlayingId) {
            saveAudioState({ songId: inlinePlayingId, src: mmInlineAudio.src, currentTime: mmInlineAudio.currentTime, playing: true });
        }
    });
    mmInlineAudio.addEventListener('error', function () {
        if (inlinePlayingId) {
            inlinePlayingId = null;
            clearAudioState();
            updateInlineIcons();
        }
    });

    /* --- Save state on page unload (navigation between modules) --- */
    window.addEventListener('beforeunload', function () {
        if (inlinePlayingId && !mmInlineAudio.paused) {
            saveAudioState({ songId: inlinePlayingId, src: mmInlineAudio.src, currentTime: mmInlineAudio.currentTime, playing: true });
        }
    });

    /* --- Logout cleanup: stop audio, clear state --- */
    document.querySelectorAll('form[action*="logout"]').forEach(function (f) {
        f.addEventListener('submit', function () {
            mmInlineAudio.pause();
            mmInlineAudio.src = '';
            clearAudioState();
        });
    });

    function updateInlineIcons() {
        document.querySelectorAll('.mm-cover-playable').forEach(function (el) {
            if (String(inlinePlayingId) === el.getAttribute('data-mm-inline-play')) { el.classList.add('mm-cover-is-playing'); } else { el.classList.remove('mm-cover-is-playing'); }
        });
    }
    function toggleInlinePlay(id) {
        var m = getMusicById(id);
        if (!m || !m.music_file) return;
        if (mmViewAudioOpen()) { mmViewAudioStop(); }
        if (inlinePlayingId === id) {
            if (mmInlineAudio.paused) { mmInlineAudio.play(); } else { mmInlineAudio.pause(); }
        } else {
            mmInlineAudio.src = resolvePath(m.music_file);
            mmInlineAudio.currentTime = 0;
            mmInlineAudio.play();
            inlinePlayingId = id;
            saveAudioState({ songId: id, src: resolvePath(m.music_file), currentTime: 0, playing: true });
        }
        updateInlineIcons();
    }
    function mmViewAudioOpen() { var a = document.getElementById('mm-view-audio'); return a && !a.paused && !a.ended; }
    function mmViewAudioStop() { var a = document.getElementById('mm-view-audio'); if (a) { a.pause(); a.currentTime = 0; } }
    function init() {
        var cj = document.getElementById('mmCategoriesJson'); if (cj) { try { cmData = JSON.parse(cj.textContent || '{}'); } catch(e) { cmData = {}; } }
        var mj = document.getElementById('mmMusicJson'); if (mj) { try { musicData = JSON.parse(mj.textContent || '[]'); } catch(e) { musicData = []; } }
        var artists = cmData.artists || [], albums = cmData.albums || [], years = cmData.air || [], genres = cmData.genres || [], languages = cmData.languages || [];
        populateSelect(document.getElementById('mm-add-artist'), artists, 'Select artist');
        populateSelect(document.getElementById('mm-add-album'), albums, 'Select album');
        populateSelect(document.getElementById('mm-add-year'), years, 'Select year');
        populateSelect(document.getElementById('mm-add-genre'), genres, 'Select genre');
        populateSelect(document.getElementById('mm-add-language'), languages, 'Select language');
        populateSelect(document.getElementById('mm-edit-artist'), artists, 'Select artist');
        populateSelect(document.getElementById('mm-edit-album'), albums, 'Select album');
        populateSelect(document.getElementById('mm-edit-year'), years, 'Select year');
        populateSelect(document.getElementById('mm-edit-genre'), genres, 'Select genre');
        populateSelect(document.getElementById('mm-edit-language'), languages, 'Select language');
        renderTable(musicData); bindAddModal(); bindEditModal(); bindDeleteModal(); bindViewModal(); bindSearch(); bindModalClose();
        updateInlineIcons();
    }
    function renderTable(records) {
        var tbody = document.getElementById('mmTableBody'), countEl = document.getElementById('mmCount');
        var cardsEl = document.getElementById('mmMobileCards');
        if (!tbody) return;
        if (!records || records.length === 0) {
            var emptyHtml = '<tr><td colspan="8" style="text-align:center;padding:40px;color:#999;">No music records found.</td></tr>';
            tbody.innerHTML = emptyHtml;
            if (cardsEl) cardsEl.innerHTML = '<p style="text-align:center;padding:40px;color:#999;">No music records found.</p>';
            if (countEl) countEl.textContent = 'Showing 0 music';
            return;
        }
        var html = '', cardHtml = '';
        records.forEach(function (m) {
            var sc = 'mm-badge--active', sl = 'Active';
            if (m.status === 'draft') { sc = 'mm-badge--draft'; sl = 'Draft'; }
            else if (m.status === 'inactive') { sc = 'mm-badge--inactive'; sl = 'Inactive'; }

            /* --- Table Row --- */
            var ch = m.cover_image ? '<img src="'+escapeHtml(resolvePath(m.cover_image))+'" alt="Cover" class="mm-music-info__cover-img">' : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>';
            var coverCls = m.music_file ? 'mm-music-info__cover mm-cover-playable' : 'mm-music-info__cover';
            var playAttr = m.music_file ? ' data-mm-inline-play="'+m.id+'"' : '';
            var playOvl = m.music_file ? '<span class="mm-cover-play-overlay"><svg class="mm-icon-play" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><polygon points="6,3 20,12 6,21"/></svg><svg class="mm-icon-pause" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><rect x="5" y="3" width="4" height="18"/><rect x="15" y="3" width="4" height="18"/></svg></span>' : '';
            html += '<tr class="mm-table__row" data-mm-row-id="'+m.id+'">'
                +'<td class="mm-table__cell"><div class="mm-music-info"><div class="'+coverCls+'"'+playAttr+'>'+ch+playOvl+'</div><span class="mm-music-info__title">'+escapeHtml(m.song_title)+'</span></div></td>'
                +'<td class="mm-table__cell">'+escapeHtml(m.artist_name)+'</td>'
                +'<td class="mm-table__cell">'+escapeHtml(m.album_name||'-')+'</td>'
                +'<td class="mm-table__cell">'+escapeHtml(m.year_name)+'</td>'
                +'<td class="mm-table__cell"><span class="mm-badge mm-badge--genre">'+escapeHtml(m.genre_name||'-')+'</span></td>'
                +'<td class="mm-table__cell">'+escapeHtml(m.language_name||'-')+'</td>'
                +'<td class="mm-table__cell"><span class="mm-badge '+sc+'">'+sl+'</span></td>'
                +'<td class="mm-table__cell mm-table__cell--actions"><div class="mm-actions">'
                +'<button type="button" class="mm-action-btn mm-action-btn--view" title="View" data-mm-action="view" data-mm-id="'+m.id+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
                +'<button type="button" class="mm-action-btn mm-action-btn--edit" title="Edit" data-mm-action="edit" data-mm-id="'+m.id+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>'
                +'<button type="button" class="mm-action-btn mm-action-btn--delete" title="Delete" data-mm-action="delete" data-mm-id="'+m.id+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>'
                +'</div></td></tr>';

            /* --- Mobile Card --- */
            var coverImg = m.cover_image
                ? '<img src="'+escapeHtml(resolvePath(m.cover_image))+'" alt="Cover" class="mm-mobile-card__cover-img">'
                : '<div class="mm-mobile-card__cover-placeholder"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="40" height="40"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>';
            var coverWrapCls = m.music_file ? 'mm-mobile-card__cover-wrap mm-cover-playable' : 'mm-mobile-card__cover-wrap';
            var coverPlayAttr = m.music_file ? ' data-mm-inline-play="'+m.id+'"' : '';
            var coverPlayOvl = m.music_file ? '<span class="mm-mobile-card__play-overlay"><svg class="mm-icon-play" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><polygon points="6,3 20,12 6,21"/></svg><svg class="mm-icon-pause" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><rect x="5" y="3" width="4" height="18"/><rect x="15" y="3" width="4" height="18"/></svg></span>' : '';

            cardHtml += '<div class="mm-mobile-card" data-mm-row-id="'+m.id+'">'
                +'<div class="'+coverWrapCls+'"'+coverPlayAttr+'>'+coverImg+coverPlayOvl+'</div>'
                +'<div class="mm-mobile-card__body">'
                +'<div class="mm-mobile-card__title" title="'+escapeHtml(m.song_title)+'">'+escapeHtml(m.song_title)+'</div>'
                +'<div class="mm-mobile-card__meta">'
                +'<div class="mm-mobile-card__meta-row"><span class="mm-mobile-card__meta-label">Artist</span><span class="mm-mobile-card__meta-value">'+escapeHtml(m.artist_name||'-')+'</span></div>'
                +'<div class="mm-mobile-card__meta-row"><span class="mm-mobile-card__meta-label">Album</span><span class="mm-mobile-card__meta-value">'+escapeHtml(m.album_name||'-')+'</span></div>'
                +'<div class="mm-mobile-card__meta-row"><span class="mm-mobile-card__meta-label">Year</span><span class="mm-mobile-card__meta-value">'+escapeHtml(m.year_name||'-')+'</span></div>'
                +'<div class="mm-mobile-card__meta-row"><span class="mm-mobile-card__meta-label">Genre</span><span class="mm-mobile-card__meta-value">'+escapeHtml(m.genre_name||'-')+'</span></div>'
                +'<div class="mm-mobile-card__meta-row"><span class="mm-mobile-card__meta-label">Language</span><span class="mm-mobile-card__meta-value">'+escapeHtml(m.language_name||'-')+'</span></div>'
                +'</div>'
                +'</div>'
                +'<div class="mm-mobile-card__footer">'
                +'<span class="mm-badge '+sc+' mm-mobile-card__badge">'+sl+'</span>'
                +'<div class="mm-mobile-card__actions">'
                +'<button type="button" class="mm-action-btn mm-action-btn--view" title="View" data-mm-action="view" data-mm-id="'+m.id+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
                +'<button type="button" class="mm-action-btn mm-action-btn--edit" title="Edit" data-mm-action="edit" data-mm-id="'+m.id+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>'
                +'<button type="button" class="mm-action-btn mm-action-btn--delete" title="Delete" data-mm-action="delete" data-mm-id="'+m.id+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>'
                +'</div></div></div>';
        });
        tbody.innerHTML = html;
        if (cardsEl) cardsEl.innerHTML = cardHtml;
        if (countEl) countEl.textContent = 'Showing ' + records.length + ' music';

        /* --- Bind table events --- */
        tbody.querySelectorAll('[data-mm-inline-play]').forEach(function(el){el.addEventListener('click',function(){toggleInlinePlay(parseInt(el.getAttribute('data-mm-inline-play')));});});
        tbody.querySelectorAll('[data-mm-action="view"]').forEach(function(b){b.addEventListener('click',function(){openViewModal(parseInt(b.getAttribute('data-mm-id')));});});
        tbody.querySelectorAll('[data-mm-action="edit"]').forEach(function(b){b.addEventListener('click',function(){openEditModal(parseInt(b.getAttribute('data-mm-id')));});});
        tbody.querySelectorAll('[data-mm-action="delete"]').forEach(function(b){b.addEventListener('click',function(){openDeleteModal(parseInt(b.getAttribute('data-mm-id')));});});

        /* --- Bind mobile card events --- */
        if (cardsEl) {
            cardsEl.querySelectorAll('[data-mm-inline-play]').forEach(function(el){el.addEventListener('click',function(){toggleInlinePlay(parseInt(el.getAttribute('data-mm-inline-play')));});});
            cardsEl.querySelectorAll('[data-mm-action="view"]').forEach(function(b){b.addEventListener('click',function(){openViewModal(parseInt(b.getAttribute('data-mm-id')));});});
            cardsEl.querySelectorAll('[data-mm-action="edit"]').forEach(function(b){b.addEventListener('click',function(){openEditModal(parseInt(b.getAttribute('data-mm-id')));});});
            cardsEl.querySelectorAll('[data-mm-action="delete"]').forEach(function(b){b.addEventListener('click',function(){openDeleteModal(parseInt(b.getAttribute('data-mm-id')));});});
        }

        updateInlineIcons();
    }
    function bindModalClose() {
        document.querySelectorAll('[data-mm-close]').forEach(function(b){b.addEventListener('click',function(){var t=b.getAttribute('data-mm-close');if(t==='add')closeModal('mmAddModal');else if(t==='edit')closeModal('mmEditModal');else if(t==='view')closeModal('mmViewModal');else if(t==='delete')closeModal('mmDeleteModal');});});
        document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeModal('mmAddModal');closeModal('mmEditModal');closeModal('mmViewModal');closeModal('mmDeleteModal');}});
        document.querySelectorAll('.sg-modal').forEach(function(m){var o=m.querySelector('.sg-modal__overlay');if(o){o.addEventListener('click',function(){m.classList.remove('is-open');document.body.style.overflow='';});}});
    }
    function setupFilePreview(inputId, uploadId, previewId, nameId, removeId, imgId) {
        var inp = document.getElementById(inputId), up = document.getElementById(uploadId), pv = document.getElementById(previewId);
        var nm = document.getElementById(nameId), rm = document.getElementById(removeId), im = imgId ? document.getElementById(imgId) : null;
        if (inp) { inp.addEventListener('change', function () { if (inp.files && inp.files.length > 0) { var f = inp.files[0]; if (nm) nm.textContent = f.name; if (im) { var r = new FileReader(); r.onload = function(e){im.src=e.target.result;}; r.readAsDataURL(f); } if (up) up.style.display='none'; if (pv) pv.style.display=''; } }); }
        if (rm) { rm.addEventListener('click', function(e){e.preventDefault();e.stopPropagation();if(inp)inp.value='';if(im)im.src='';if(up)up.style.display='';if(pv)pv.style.display='none';}); }
    }
    function resetAddForm() {
        var f = document.getElementById('mmAddForm'); if(f)f.reset();
        var mu=document.getElementById('mm-music-upload'),mp=document.getElementById('mm-music-preview'),cu=document.getElementById('mm-cover-upload'),cp=document.getElementById('mm-cover-preview');
        if(mu)mu.style.display='';if(mp)mp.style.display='none';if(cu)cu.style.display='';if(cp)cp.style.display='none';
    }
    function bindAddModal() {
        var ab = document.getElementById('mmAddMusicBtn');
        if (ab) { ab.addEventListener('click', function(){resetAddForm();openModal('mmAddModal');}); }
        setupFilePreview('mm-add-music-file','mm-music-upload','mm-music-preview','mm-music-file-name','mm-music-file-remove',null);
        setupFilePreview('mm-add-cover-image','mm-cover-upload','mm-cover-preview','mm-cover-file-name','mm-cover-file-remove','mm-cover-preview-img');
        var sb = document.getElementById('mmSaveMusicBtn');
        if (sb) { sb.addEventListener('click', function () {
            var t=document.getElementById('mm-add-title').value.trim(), a=document.getElementById('mm-add-artist').value, y=document.getElementById('mm-add-year').value;
            if(!t){showError('Song title is required.');return;} if(!a){showError('Please select an artist.');return;} if(!y){showError('Please select a year.');return;}
            var mf=document.getElementById('mm-add-music-file').files[0]; if(!mf){showError('Please upload a music file.');return;}
            var fd=new FormData(); fd.append('action','add'); fd.append('csrf_token',getCsrfToken()); fd.append('song_title',t);
            fd.append('artist_id',a); fd.append('album_id',document.getElementById('mm-add-album').value||'0');
            fd.append('year_id',y); fd.append('genre_id',document.getElementById('mm-add-genre').value||'0');
            fd.append('language_id',document.getElementById('mm-add-language').value||'0');
            fd.append('description',document.getElementById('mm-add-description').value.trim());
            fd.append('status',document.getElementById('mm-add-status').value); fd.append('music_file',mf);
            var cf=document.getElementById('mm-add-cover-image').files[0]; if(cf)fd.append('cover_image',cf);
            sb.disabled=true;
            postToHandler(fd, function(data){musicData.unshift(data.record);renderTable(musicData);closeModal('mmAddModal');resetAddForm();showSuccess(data.message||'Music added successfully.');});
            sb.disabled=false;
        }); }
    }
    function bindEditModal() {
        var ub = document.getElementById('mmUpdateMusicBtn');
        if (ub) { ub.addEventListener('click', function () {
            var id=document.getElementById('mm-edit-id').value, t=document.getElementById('mm-edit-title').value.trim();
            var a=document.getElementById('mm-edit-artist').value, y=document.getElementById('mm-edit-year').value;
            if(!id)return; if(!t){showError('Song title is required.');return;} if(!a){showError('Please select an artist.');return;} if(!y){showError('Please select a year.');return;}
            var fd=new FormData(); fd.append('action','edit'); fd.append('csrf_token',getCsrfToken()); fd.append('id',id);
            fd.append('song_title',t); fd.append('artist_id',a); fd.append('album_id',document.getElementById('mm-edit-album').value||'0');
            fd.append('year_id',y); fd.append('genre_id',document.getElementById('mm-edit-genre').value||'0');
            fd.append('language_id',document.getElementById('mm-edit-language').value||'0');
            fd.append('description',document.getElementById('mm-edit-description').value.trim());
            fd.append('status',document.getElementById('mm-edit-status').value);
            var mf=document.getElementById('mm-edit-music-file').files[0]; if(mf)fd.append('music_file',mf);
            var cf=document.getElementById('mm-edit-cover-image').files[0]; if(cf)fd.append('cover_image',cf);
            ub.disabled=true;
            postToHandler(fd, function(data){for(var i=0;i<musicData.length;i++){if(musicData[i].id===data.record.id){musicData[i]=data.record;break;}} renderTable(musicData);closeModal('mmEditModal');showSuccess(data.message||'Music updated successfully.');});
            ub.disabled=false;
        }); }
        setupFilePreview('mm-edit-music-file','mm-edit-music-upload','mm-edit-music-preview','mm-edit-music-name','mm-edit-music-replace',null);
        setupFilePreview('mm-edit-cover-image','mm-edit-cover-upload','mm-edit-cover-preview','mm-edit-cover-name','mm-edit-cover-replace','mm-edit-cover-img');
    }
    function openEditModal(id) {
        var m=getMusicById(id); if(!m)return;
        document.getElementById('mm-edit-id').value=m.id;
        document.getElementById('mm-edit-song-name').textContent=m.song_title;
        document.getElementById('mm-edit-title').value=m.song_title;
        selectById(document.getElementById('mm-edit-artist'),m.artist_id);
        selectById(document.getElementById('mm-edit-album'),m.album_id);
        selectById(document.getElementById('mm-edit-year'),m.year_id);
        selectById(document.getElementById('mm-edit-genre'),m.genre_id);
        selectById(document.getElementById('mm-edit-language'),m.language_id);
        document.getElementById('mm-edit-description').value=m.description||'';
        document.getElementById('mm-edit-status').value=m.status;
        var mu=document.getElementById('mm-edit-music-upload'),mp=document.getElementById('mm-edit-music-preview'),mn=document.getElementById('mm-edit-music-name'),mfi=document.getElementById('mm-edit-music-file');
        if(m.music_file){if(mu)mu.style.display='none';if(mp)mp.style.display='';if(mn)mn.textContent=m.music_file.split('/').pop();}else{if(mu)mu.style.display='';if(mp)mp.style.display='none';}
        if(mfi)mfi.value='';
        var cu=document.getElementById('mm-edit-cover-upload'),cp=document.getElementById('mm-edit-cover-preview'),cn=document.getElementById('mm-edit-cover-name');
        var ci=document.getElementById('mm-edit-cover-img'),cph=document.getElementById('mm-edit-cover-placeholder'),cfi=document.getElementById('mm-edit-cover-image');
        if(m.cover_image){if(cu)cu.style.display='none';if(cp)cp.style.display='';if(ci){ci.src=resolvePath(m.cover_image);ci.style.display='';}if(cph)cph.style.display='none';if(cn)cn.textContent=m.cover_image.split('/').pop();}else{if(cu)cu.style.display='';if(cp)cp.style.display='none';}
        if(cfi)cfi.value='';
        openModal('mmEditModal');
    }
    function bindDeleteModal() {
        var cb = document.getElementById('mmConfirmDeleteBtn');
        if (cb) { cb.addEventListener('click', function () {
            var id=document.getElementById('mm-delete-id').value; if(!id)return;
            cb.disabled=true;
            var fd=new FormData(); fd.append('action','delete'); fd.append('csrf_token',getCsrfToken()); fd.append('id',id);
            postToHandler(fd, function(data){musicData=musicData.filter(function(m){return m.id!==parseInt(id);});renderTable(musicData);closeModal('mmDeleteModal');showSuccess(data.message||'Music deleted successfully.');});
            cb.disabled=false;
        }); }
    }
    function openDeleteModal(id) {
        var m=getMusicById(id); if(!m)return;
        document.getElementById('mm-delete-id').value=id;
        document.getElementById('mm-delete-song-name').textContent=m.song_title;
        openModal('mmDeleteModal');
    }
    function bindViewModal() {
        var eb = document.getElementById('mmViewEditBtn');
        if (eb) { eb.addEventListener('click', function () {
            var id = parseInt(eb.getAttribute('data-mm-id'));
            closeModal('mmViewModal'); if (id) openEditModal(id);
        }); }
    }
    function openViewModal(id) {
        var m=getMusicById(id); if(!m)return;
        document.getElementById('mm-view-title').textContent=m.song_title;
        document.getElementById('mm-view-artist').textContent=m.artist_name;
        document.getElementById('mm-view-album').textContent=m.album_name||'-';
        document.getElementById('mm-view-year').textContent=m.year_name;
        document.getElementById('mm-view-genre').textContent=m.genre_name||'-';
        document.getElementById('mm-view-language').textContent=m.language_name||'-';
        document.getElementById('mm-view-description').textContent=m.description||'No description available.';
        var sf = document.getElementById('mm-view-music-file');
        sf.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg> '+(m.music_file?m.music_file.split('/').pop():'No file');
        var st=document.getElementById('mm-view-status');
        st.textContent=m.status.charAt(0).toUpperCase()+m.status.slice(1);
        st.className='mm-badge';if(m.status==='active')st.classList.add('mm-badge--active');else if(m.status==='draft')st.classList.add('mm-badge--draft');else st.classList.add('mm-badge--inactive');
        var ci=document.getElementById('mm-view-cover-img'),ic=document.getElementById('mm-view-cover-icon');
        if(m.cover_image){ci.src=resolvePath(m.cover_image);ci.style.display='';ic.style.display='none';}else{ci.style.display='none';ic.style.display='';}
        var ap=document.getElementById('mm-view-player'),au=document.getElementById('mm-view-audio'),eb=document.getElementById('mmViewEditBtn');
        if(m.music_file){ap.style.display='';au.src=resolvePath(m.music_file);}else{ap.style.display='none';au.src='';}
        if(eb)eb.setAttribute('data-mm-id',m.id);
        openModal('mmViewModal');
    }
    function bindSearch() {
        var si=document.getElementById('mmSearchInput');
        if(!si)return;
        si.addEventListener('input',function(){
            var q=si.value.toLowerCase().trim();
            var filtered=q?musicData.filter(function(m){return(m.song_title+' '+m.artist_name+' '+m.album_name+' '+m.year_name+' '+m.genre_name+' '+m.language_name).toLowerCase().indexOf(q)!==-1;}):musicData;
            renderTable(filtered);
        });
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init);}else{init();}
})();
