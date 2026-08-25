(function () {
    'use strict';

    var searchInput = document.getElementById('musicSearch');
    var filterArtist = document.getElementById('filterArtist');
    var filterAlbum = document.getElementById('filterAlbum');
    var filterYear = document.getElementById('filterYear');
    var filterGenre = document.getElementById('filterGenre');
    var filterLanguage = document.getElementById('filterLanguage');
    var clearBtn = document.getElementById('clearFilters');
    var resultCount = document.getElementById('resultCount');
    var musicGrid = document.getElementById('musicGrid');
    var musicEmpty = document.getElementById('musicEmpty');
    var loadMoreWrap = document.getElementById('loadMoreWrap');

    if (!musicGrid) return;

    var cards = Array.prototype.slice.call(musicGrid.querySelectorAll('.wg-music-card-wrap'));

    function applyFilters() {
        var query = (searchInput.value || '').toLowerCase().trim();
        var artist = filterArtist.value;
        var album = filterAlbum.value;
        var year = filterYear.value;
        var genre = filterGenre.value;
        var language = filterLanguage.value;

        var visible = 0;

        cards.forEach(function (wrap) {
            var title = (wrap.getAttribute('data-title') || '').toLowerCase();
            var cardArtist = wrap.getAttribute('data-artist') || '';
            var cardAlbum = wrap.getAttribute('data-album') || '';
            var cardYear = wrap.getAttribute('data-year') || '';
            var cardGenre = wrap.getAttribute('data-genre') || '';
            var cardLanguage = wrap.getAttribute('data-language') || '';

            var match = true;

            if (query && title.indexOf(query) === -1 && cardArtist.toLowerCase().indexOf(query) === -1 && cardAlbum.toLowerCase().indexOf(query) === -1) {
                match = false;
            }
            if (artist && cardArtist !== artist) match = false;
            if (album && cardAlbum !== album) match = false;
            if (year && cardYear !== year) match = false;
            if (genre && cardGenre !== genre) match = false;
            if (language && cardLanguage !== language) match = false;

            wrap.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        resultCount.textContent = visible + (visible === 1 ? ' song' : ' songs');

        if (visible === 0) {
            musicEmpty.style.display = '';
            loadMoreWrap.style.display = 'none';
        } else {
            musicEmpty.style.display = 'none';
            loadMoreWrap.style.display = '';
        }
    }

    searchInput.addEventListener('input', applyFilters);
    filterArtist.addEventListener('change', applyFilters);
    filterAlbum.addEventListener('change', applyFilters);
    filterYear.addEventListener('change', applyFilters);
    filterGenre.addEventListener('change', applyFilters);
    filterLanguage.addEventListener('change', applyFilters);

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        filterArtist.value = '';
        filterAlbum.value = '';
        filterYear.value = '';
        filterGenre.value = '';
        filterLanguage.value = '';
        applyFilters();
    });

    var loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            loadMoreBtn.textContent = 'No more songs';
            loadMoreBtn.disabled = true;
            loadMoreBtn.style.opacity = '0.5';
            loadMoreBtn.style.cursor = 'default';
        });
    }
})();
