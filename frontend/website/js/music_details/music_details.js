(function () {
    'use strict';

    var body = document.body;
    var musicId = parseInt(body.getAttribute('data-music-id'), 10) || 0;
    var handlerUrl = body.getAttribute('data-handler-url') || ((window.APP_BASE_URL || '') + '/backend/handlers/review-handler.php');
    var isLoggedIn = body.getAttribute('data-user-logged-in') === '1';

    /* ============================================
       AUTH GUARD — Show signup modal for guests
       ============================================ */
    function requireAuth(actionLabel) {
        if (isLoggedIn) return true;
        if (typeof showWarning === 'function') {
            showWarning('Please sign up to ' + actionLabel + '.', 3000);
        }
        if (typeof window.openSignupModal === 'function') {
            window.openSignupModal();
        }
        return false;
    }

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
            if (!requireAuth('play music')) return;
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

    /* Download button auth guard */
    var downloadBtn = document.getElementById('wgPlayerDownload');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function (e) {
            if (!requireAuth('download music')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    /* ============================================
       STAR RATING — REUSABLE
       ============================================ */
    function setupStarRating(container, onRate) {
        if (!container) return null;
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

    /* ============================================
       REVIEW SUBMISSION — SHARED LOGIC
       ============================================ */
    function showFieldError(el, msg) {
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
    }

    function clearFieldError(el) {
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    }

    function relativeTime(ts) {
        if (!ts) return '';
        var norm = String(ts).trim().replace(' ', 'T');
        var ms = new Date(norm + 'Z').getTime();
        if (isNaN(ms)) return '';
        var diff = Math.max(0, Math.floor(Date.now() / 1000) - Math.floor(ms / 1000));
        if (diff < 60) return 'Just now';
        var m = Math.floor(diff / 60);
        if (diff < 3600) return m + ' minute' + (m > 1 ? 's' : '') + ' ago';
        var h = Math.floor(diff / 3600);
        if (diff < 86400) return h + ' hour' + (h > 1 ? 's' : '') + ' ago';
        var d = Math.floor(diff / 86400);
        if (diff < 604800) return d + ' day' + (d > 1 ? 's' : '') + ' ago';
        var w = Math.floor(diff / 604800);
        if (diff < 2592000) return w + ' week' + (w > 1 ? 's' : '') + ' ago';
        var mo = Math.floor(diff / 2592000);
        if (diff < 31536000) return mo + ' month' + (mo > 1 ? 's' : '') + ' ago';
        var y = Math.floor(diff / 31536000);
        return y + ' year' + (y > 1 ? 's' : '') + ' ago';
    }

    function userInitials(name) {
        var parts = (name || '').trim().split(/\s+/);
        if (parts.length >= 2) {
            return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
        }
        return (name || 'A').substring(0, 2).toUpperCase();
    }

    function buildReviewCardHtml(review) {
        var avatarHtml = '';
        var initials = userInitials(review.user_name);
        if (review.user_image) {
            var src = (window.APP_BASE_URL || '') + '/' + review.user_image.replace(/^\//, '');
            avatarHtml = '<div class="wg-review-card__avatar"><img src="' + src + '" alt="" class="wg-review-card__avatar-img" loading="lazy" onerror="this.style.display=\'none\';this.parentNode.textContent=\'' + initials + '\'"></div>';
        } else {
            avatarHtml = '<div class="wg-review-card__avatar">' + initials + '</div>';
        }

        var stars = '';
        for (var i = 1; i <= 5; i++) {
            stars += i <= review.rating ? '&#9733;' : '&#9734;';
        }

        return '<div class="wg-review-card">' +
            '<div class="wg-review-card__row">' +
            avatarHtml +
            '<div class="wg-review-card__info">' +
            '<span class="wg-review-card__name">' + escapeHtml(review.user_name || 'Anonymous') + '</span>' +
            '<span class="wg-review-card__stars">' + stars + '</span>' +
            '</div>' +
            '<span class="wg-review-card__date" data-ts="' + (review.created_at || '') + '">' + relativeTime(review.created_at) + '</span>' +
            '</div>' +
            '<p class="wg-review-card__text">"' + escapeHtml(review.review_text) + '"</p>' +
            '</div>';
    }

    function buildDrawerReviewHtml(review) {
        var avatarHtml = '';
        if (review.user_image) {
            var src = (window.APP_BASE_URL || '') + '/' + review.user_image.replace(/^\//, '');
            avatarHtml = '<div class="wg-drawer__review-avatar"><img src="' + src + '" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.parentElement.textContent=\'' + userInitials(review.user_name) + '\'"></div>';
        } else {
            avatarHtml = '<div class="wg-drawer__review-avatar">' + userInitials(review.user_name) + '</div>';
        }

        var stars = '';
        for (var i = 1; i <= 5; i++) {
            stars += i <= review.rating ? '&#9733;' : '&#9734;';
        }

        return '<div class="wg-drawer__review">' +
            '<div class="wg-drawer__review-row">' +
            avatarHtml +
            '<div class="wg-drawer__review-info">' +
            '<span class="wg-drawer__review-name">' + escapeHtml(review.user_name || 'Anonymous') + '</span>' +
            '<span class="wg-drawer__review-stars">' + stars + '</span>' +
            '</div>' +
            '<span class="wg-drawer__review-date" data-ts="' + (review.created_at || '') + '">' + relativeTime(review.created_at) + '</span>' +
            '</div>' +
            '<p class="wg-drawer__review-text">"' + escapeHtml(review.review_text) + '"</p>' +
            '</div>';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function submitReview(rating, reviewText, ratingErrorEl, reviewErrorEl, submitBtn, callback) {
        clearFieldError(ratingErrorEl);
        clearFieldError(reviewErrorEl);

        var hasError = false;
        if (rating === 0) {
            showFieldError(ratingErrorEl, 'Please select a rating.');
            hasError = true;
        }
        if (!reviewText) {
            showFieldError(reviewErrorEl, 'Please write your review.');
            hasError = true;
        }
        if (hasError) return;

        var fd = new FormData();
        fd.append('action', 'add');
        fd.append('music_id', musicId);
        fd.append('rating', rating);
        fd.append('review_text', reviewText);

        if (submitBtn) submitBtn.disabled = true;

        fetch(handlerUrl, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (submitBtn) submitBtn.disabled = false;
                if (data.success) {
                    if (typeof showSuccess === 'function') {
                        showSuccess(data.message || 'Review submitted successfully.', 3000);
                    }
                    if (callback) callback();
                    refreshReviews();
                } else if (data.login_required) {
                    if (typeof showWarning === 'function') {
                        showWarning('Please sign up to submit a review.', 3000);
                    }
                    if (typeof window.openSignupModal === 'function') {
                        window.openSignupModal();
                    }
                } else {
                    var errs = data.errors || {};
                    if (errs.rating) showFieldError(ratingErrorEl, errs.rating);
                    if (errs.review_text) showFieldError(reviewErrorEl, errs.review_text);
                    if (!errs.rating && !errs.review_text && data.error) {
                        showFieldError(reviewErrorEl, data.error);
                    }
                }
            })
            .catch(function () {
                if (submitBtn) submitBtn.disabled = false;
                showFieldError(reviewErrorEl, 'Something went wrong. Please try again.');
            });
    }

    function refreshReviews() {
        var fd = new FormData();
        fd.append('action', 'get-for-music');
        fd.append('music_id', musicId);

        fetch(handlerUrl, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) return;
                var reviews = data.reviews || [];

                // Update grid (max 6)
                var grid = document.getElementById('reviewsGrid');
                if (grid) {
                    var gridHtml = '';
                    var count = Math.min(reviews.length, 6);
                    for (var i = 0; i < count; i++) {
                        gridHtml += buildReviewCardHtml(reviews[i]);
                    }
                    if (reviews.length === 0) {
                        gridHtml = '<div class="wg-reviews__empty" style="grid-column:1/-1;text-align:center;padding:2rem;color:var(--wg-text-muted);">No reviews yet. Be the first to review!</div>';
                    }
                    grid.innerHTML = gridHtml;
                }

                // Update drawer list
                var drawerList = document.getElementById('drawerList');
                if (drawerList) {
                    var drawerHtml = '';
                    for (var j = 0; j < reviews.length; j++) {
                        drawerHtml += buildDrawerReviewHtml(reviews[j]);
                    }
                    drawerList.innerHTML = drawerHtml;
                }

                // Update drawer count
                var countEl = document.querySelector('.wg-drawer__count');
                if (countEl) {
                    countEl.textContent = reviews.length + ' Review' + (reviews.length !== 1 ? 's' : '');
                }

                // Update "All Reviews" button visibility
                var allBtn = document.getElementById('openDrawer');
                if (allBtn) {
                    allBtn.style.display = reviews.length > 6 ? '' : 'none';
                }

                // Refresh stats
                refreshStats();
            })
            .catch(function () {});
    }

    function refreshStats() {
        var fd = new FormData();
        fd.append('action', 'get-stats');
        fd.append('music_id', musicId);

        fetch(handlerUrl, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) return;
                var scoreEl = document.querySelector('.wg-reviews__score-number');
                if (scoreEl) scoreEl.textContent = data.avg_rating;

                var basedEl = document.querySelector('.wg-reviews__based');
                if (basedEl) basedEl.textContent = 'Based on ' + data.total + ' review' + (data.total !== 1 ? 's' : '');

                // Update distribution bars
                var distRows = document.querySelectorAll('.wg-reviews__dist-row');
                var keys = [5, 4, 3, 2, 1];
                distRows.forEach(function (row, idx) {
                    var star = keys[idx];
                    var count = data.distribution[star] || 0;
                    var pct = data.total > 0 ? Math.round(count / data.total * 100) : 0;
                    var fill = row.querySelector('.wg-reviews__dist-fill');
                    var pctLabel = row.querySelector('.wg-reviews__dist-pct');
                    if (fill) fill.style.width = pct + '%';
                    if (pctLabel) pctLabel.textContent = pct + '%';
                });
            })
            .catch(function () {});
    }

    /* ============================================
       PAGE FORM — Star Rating + Submit
       ============================================ */
    var starSelect = document.getElementById('starSelect');
    var reviewTextEl = document.getElementById('reviewText');
    var submitBtn = document.getElementById('submitReview');
    var ratingErrorEl = document.getElementById('ratingError');
    var reviewErrorEl = document.getElementById('reviewError');

    var pageStars = setupStarRating(starSelect, function () {});

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var rating = pageStars ? pageStars.getRating() : 0;
            var text = reviewTextEl ? reviewTextEl.value.trim() : '';
            submitReview(rating, text, ratingErrorEl, reviewErrorEl, submitBtn, function () {
                if (pageStars) pageStars.reset();
                if (reviewTextEl) reviewTextEl.value = '';
            });
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
    var drawerSubmitBtn = document.getElementById('drawerSubmitReview');
    var drawerReviewText = document.getElementById('drawerReviewText');
    var drawerRatingError = document.getElementById('drawerRatingError');
    var drawerReviewError = document.getElementById('drawerReviewError');

    if (drawerSubmitBtn) {
        drawerSubmitBtn.addEventListener('click', function () {
            var r = drawerStars ? drawerStars.getRating() : 0;
            var text = drawerReviewText ? drawerReviewText.value.trim() : '';
            submitReview(r, text, drawerRatingError, drawerReviewError, drawerSubmitBtn, function () {
                if (drawerStars) drawerStars.reset();
                if (drawerReviewText) drawerReviewText.value = '';
            });
        });
    }

    /* ============================================
       LIVE TIMESTAMP UPDATER
       ============================================ */
    function updateAllTimestamps() {
        document.querySelectorAll('[data-ts]').forEach(function (el) {
            var ts = el.getAttribute('data-ts');
            if (ts) el.textContent = relativeTime(ts);
        });
    }

    setInterval(updateAllTimestamps, 30000);
})();
