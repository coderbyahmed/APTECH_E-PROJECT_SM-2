(function () {
    'use strict';

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

    /* ============================================
       VIDEO PLAY BUTTON (placeholder)
       ============================================ */
    var coverPlay = document.querySelector('.wg-details__cover-play');
    if (coverPlay) {
        coverPlay.addEventListener('click', function () {
            alert('Video playback will be available once integrated with the media server.');
        });
    }
})();
