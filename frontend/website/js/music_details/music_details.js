(function () {
    'use strict';

    /* ============================================
       AUDIO PLAYER
       ============================================ */
    var audio = document.getElementById('wgAudioPlayer');
    var playerWrap = document.getElementById('wgPlayer');
    var playBtn = document.getElementById('wgPlayerPlay');
    var progressBar = document.getElementById('wgPlayerProgressBar');
    var progressFill = document.getElementById('wgPlayerProgressFill');
    var currentTimeEl = document.getElementById('wgPlayerCurrent');
    var durationEl = document.getElementById('wgPlayerDuration');
    var volumeBtn = document.getElementById('wgPlayerVolumeBtn');
    var volumeSlider = document.getElementById('wgPlayerVolume');

    function formatTime(sec) {
        if (!isFinite(sec)) return '0:00';
        var m = Math.floor(sec / 60);
        var s = Math.floor(sec % 60);
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    if (audio && playerWrap) {
        playBtn.addEventListener('click', function () {
            if (audio.paused) {
                audio.play().catch(function () {});
            } else {
                audio.pause();
            }
        });

        audio.addEventListener('play', function () {
            playerWrap.classList.add('is-playing');
        });

        audio.addEventListener('pause', function () {
            playerWrap.classList.remove('is-playing');
        });

        audio.addEventListener('ended', function () {
            playerWrap.classList.remove('is-playing');
            progressFill.style.width = '0%';
            currentTimeEl.textContent = '0:00';
        });

        audio.addEventListener('loadedmetadata', function () {
            durationEl.textContent = formatTime(audio.duration);
        });

        audio.addEventListener('timeupdate', function () {
            if (!audio.duration) return;
            var pct = (audio.currentTime / audio.duration) * 100;
            progressFill.style.width = pct + '%';
            currentTimeEl.textContent = formatTime(audio.currentTime);
        });

        progressBar.addEventListener('click', function (e) {
            if (!audio.duration) return;
            var rect = progressBar.getBoundingClientRect();
            var pct = (e.clientX - rect.left) / rect.width;
            audio.currentTime = pct * audio.duration;
        });

        if (volumeSlider) {
            audio.volume = volumeSlider.value / 100;
            volumeSlider.addEventListener('input', function () {
                audio.volume = volumeSlider.value / 100;
            });
        }

        if (volumeBtn) {
            var lastVol = 80;
            volumeBtn.addEventListener('click', function () {
                if (audio.volume > 0) {
                    lastVol = volumeSlider.value;
                    audio.volume = 0;
                    volumeSlider.value = 0;
                } else {
                    audio.volume = lastVol / 100;
                    volumeSlider.value = lastVol;
                }
            });
        }
    }

    /* ============================================
       STAR RATING — PAGE FORM
       ============================================ */
    var starSelect = document.getElementById('starSelect');
    var textarea = document.querySelector('.wg-reviews__textarea');
    var submitBtn = document.getElementById('submitReview');
    var selectedRating = 0;

    function setupStarRating(container, onRate) {
        if (!container) return;
        var picks = container.querySelectorAll('.wg-reviews__star-pick');
        var rating = 0;

        picks.forEach(function (pick) {
            pick.addEventListener('mouseenter', function () {
                var val = parseInt(pick.getAttribute('data-star'), 10);
                picks.forEach(function (p) {
                    p.classList.toggle('is-active', parseInt(p.getAttribute('data-star'), 10) <= val);
                });
            });

            pick.addEventListener('mouseleave', function () {
                picks.forEach(function (p) {
                    p.classList.toggle('is-active', parseInt(p.getAttribute('data-star'), 10) <= rating);
                });
            });

            pick.addEventListener('click', function () {
                rating = parseInt(pick.getAttribute('data-star'), 10);
                picks.forEach(function (p) {
                    p.classList.toggle('is-active', parseInt(p.getAttribute('data-star'), 10) <= rating);
                });
                if (onRate) onRate(rating);
            });
        });

        return {
            getRating: function () { return rating; },
            reset: function () {
                rating = 0;
                picks.forEach(function (p) { p.classList.remove('is-active'); });
            }
        };
    }

    var pageStars = setupStarRating(starSelect, function (r) { selectedRating = r; });

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            if (selectedRating === 0) {
                alert('Please select a star rating.');
                return;
            }
            if (!textarea || !textarea.value.trim()) {
                alert('Please write a review.');
                return;
            }
            alert('Thank you for your review! (Backend integration pending)');
            selectedRating = 0;
            textarea.value = '';
            if (pageStars) pageStars.reset();
        });
    }

    /* ============================================
       CAROUSEL — MORE FROM THIS ARTIST
       ============================================ */
    var carousel = document.getElementById('artistCarousel');
    var prevBtn = document.getElementById('carouselPrev');
    var nextBtn = document.getElementById('carouselNext');

    if (carousel && prevBtn && nextBtn) {
        var scrollAmount = 216;

        prevBtn.addEventListener('click', function () {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function () {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    /* ============================================
       ALL REVIEWS DRAWER
       ============================================ */
    var drawer = document.getElementById('reviewsDrawer');
    var overlay = document.getElementById('drawerOverlay');
    var openBtn = document.getElementById('openDrawer');
    var closeBtn = document.getElementById('closeDrawer');

    function openDrawer() {
        if (drawer) drawer.classList.add('is-open');
        if (overlay) overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (drawer) drawer.classList.remove('is-open');
        if (overlay) overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    if (openBtn) openBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    /* Drawer star rating + form */
    var drawerStarSelect = document.querySelector('.wg-drawer__star-select');
    var drawerStars = setupStarRating(drawerStarSelect, function () {});
    var drawerSubmitBtn = document.querySelector('.wg-drawer__submit-btn');
    var drawerTextarea = document.querySelector('.wg-drawer__form .wg-reviews__textarea');

    if (drawerSubmitBtn) {
        drawerSubmitBtn.addEventListener('click', function () {
            var r = drawerStars ? drawerStars.getRating() : 0;
            if (r === 0) {
                alert('Please select a star rating.');
                return;
            }
            if (!drawerTextarea || !drawerTextarea.value.trim()) {
                alert('Please write a review.');
                return;
            }
            alert('Thank you for your review! (Backend integration pending)');
            drawerTextarea.value = '';
            if (drawerStars) drawerStars.reset();
        });
    }
})();
