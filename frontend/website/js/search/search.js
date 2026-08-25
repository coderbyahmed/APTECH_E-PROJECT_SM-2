(function () {
    'use strict';

    var searchInput = document.getElementById('searchInput');
    var searchClear = document.getElementById('searchClear');
    var searchBtn = document.getElementById('searchBtn');
    var resultsCount = document.getElementById('resultsCount');
    var searchResults = document.getElementById('searchResults');
    var searchEmpty = document.getElementById('searchEmpty');
    var resultsInfo = document.getElementById('resultsInfo');
    var filterBtns = document.querySelectorAll('.wg-search__filter-btn');
    var emptyTitle = document.getElementById('emptyTitle');
    var emptyDesc = document.getElementById('emptyDesc');

    if (!searchInput || !searchResults) return;

    var cards = Array.prototype.slice.call(searchResults.querySelectorAll('.wg-search-card-wrap'));
    var activeFilter = 'all';

    function applyFilters() {
        var query = searchInput.value.toLowerCase().trim();
        var visible = 0;

        cards.forEach(function (wrap) {
            var type = wrap.getAttribute('data-type');
            var title = (wrap.getAttribute('data-title') || '').toLowerCase();
            var artist = (wrap.getAttribute('data-artist') || '').toLowerCase();

            var matchesType = (activeFilter === 'all') || (activeFilter === type) || (activeFilter === 'videos' && type === 'video');
            var matchesQuery = !query || title.indexOf(query) !== -1 || artist.indexOf(query) !== -1;

            if (matchesType && matchesQuery) {
                wrap.style.display = '';
                visible++;
            } else {
                wrap.style.display = 'none';
            }
        });

        resultsCount.textContent = visible + (visible === 1 ? ' result' : ' results');

        if (visible === 0) {
            updateEmptyMessage(query);
            searchEmpty.style.display = '';
            searchResults.style.display = 'none';
            resultsInfo.style.display = 'none';
        } else {
            searchEmpty.style.display = 'none';
            searchResults.style.display = '';
            resultsInfo.style.display = '';
        }

        if (query) {
            searchClear.style.display = '';
        } else {
            searchClear.style.display = 'none';
        }
    }

    function updateEmptyMessage(query) {
        if (activeFilter === 'music') {
            emptyTitle.textContent = 'No Music Found';
            emptyDesc.textContent = query ? 'No music matched your search. Try a different keyword.' : 'There is no music available right now.';
        } else if (activeFilter === 'videos') {
            emptyTitle.textContent = 'No Videos Found';
            emptyDesc.textContent = query ? 'No videos matched your search. Try a different keyword.' : 'There are no videos available right now.';
        } else {
            emptyTitle.textContent = 'No results found';
            emptyDesc.textContent = 'Try searching for another music or video.';
        }
    }

    function executeSearch() {
        var query = searchInput.value.toLowerCase().trim();
        if (!query) return;

        var firstVisible = null;
        for (var i = 0; i < cards.length; i++) {
            if (cards[i].style.display !== 'none') {
                firstVisible = cards[i];
                break;
            }
        }

        if (!firstVisible) return;

        var link = firstVisible.querySelector('a.wg-card--link');
        if (link && link.href) {
            window.location.href = link.href;
        }
    }

    searchInput.addEventListener('input', applyFilters);

    searchClear.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.focus();
        applyFilters();
    });

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            executeSearch();
        });
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) { b.classList.remove('wg-search__filter-btn--active'); });
            btn.classList.add('wg-search__filter-btn--active');
            activeFilter = btn.getAttribute('data-filter');
            applyFilters();
        });
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        executeSearch();
    });
})();
