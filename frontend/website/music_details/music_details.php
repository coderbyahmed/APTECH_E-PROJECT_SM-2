<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/music_details';
$jsBase = $websiteBase . '/js/music_details';
$currentPage = 'music';

require_once dirname(__DIR__, 1) . '/includes/music-data.php';
require_once __DIR__ . '/../../../backend/includes/user-auth.php';
require_once __DIR__ . '/../../../backend/helpers/media-duration.php';

require_once __DIR__ . '/../../../backend/includes/website-settings.php';
$ws = getWebsiteSettings();
$wsWebsiteName = htmlspecialchars($ws['website_name']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$track = $id > 0 ? wgGetMusicById($id, true) : null;

if (!$track) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Music Not Found - <?php echo $wsWebsiteName; ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
        <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
        <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    </head>
    <body>
    <?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>
    <main style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:4rem 1.5rem;">
        <div>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64" style="color:var(--wg-text-muted);margin-bottom:1rem;"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
            <h1 style="font-size:1.5rem;color:var(--wg-text-primary);margin-bottom:0.5rem;">Music Not Found</h1>
            <p style="color:var(--wg-text-secondary);margin-bottom:1.5rem;">The track you're looking for doesn't exist or has been removed.</p>
            <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-btn wg-btn--primary">Browse Music</a>
        </div>
    </main>
    <?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$trackCoverUrl = wgResolveCoverUrl($track['cover_image'], $baseUrl);
$isInactive = ($track['status'] === 'inactive');
$trackAudioUrl = $isInactive ? '' : wgResolveMusicUrl($track['music_file'], $baseUrl);
$trackStatus = ucfirst($track['status']);
$trackPlaceholder = ($track['id'] % 5) + 1;

$artistName = $track['artist_name'] ?: '';
$artistCards = $artistName ? wgGetMusicByArtist($artistName, $track['id'], 6) : [];
if (count($artistCards) < 6) {
    $extraCards = wgGetAllMusic(6 - count($artistCards));
    $existingIds = array_column($artistCards, 'id');
    $existingIds[] = $track['id'];
    foreach ($extraCards as $extra) {
        if (!in_array($extra['id'], $existingIds) && count($artistCards) < 6) {
            $artistCards[] = $extra;
        }
    }
}

/* ---- Review data for this music ---- */
require_once dirname(__DIR__, 3) . '/backend/includes/db.php';
$reviewDb = getDb();

// Rating stats
$rStmt = $reviewDb->prepare("SELECT COUNT(*) AS total, AVG(rating) AS avg_rating,
    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star5,
    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star4,
    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star3,
    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star2,
    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star1
    FROM reviews WHERE music_id = :music_id AND status = 'published'");
$rStmt->execute([':music_id' => $track['id']]);
$rStats = $rStmt->fetch();
$reviewTotal = (int) ($rStats['total'] ?? 0);
$reviewAvg = $reviewTotal > 0 ? round((float) $rStats['avg_rating'], 1) : '0.0';
$reviewDist = [
    5 => (int) ($rStats['star5'] ?? 0),
    4 => (int) ($rStats['star4'] ?? 0),
    3 => (int) ($rStats['star3'] ?? 0),
    2 => (int) ($rStats['star2'] ?? 0),
    1 => (int) ($rStats['star1'] ?? 0),
];

// Published reviews (max 6 for grid)
$rStmt2 = $reviewDb->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image
    FROM reviews r LEFT JOIN users u ON u.id = r.user_id
    WHERE r.music_id = :music_id AND r.status = 'published'
    ORDER BY r.created_at DESC LIMIT 6");
$rStmt2->execute([':music_id' => $track['id']]);
$reviewCards = $rStmt2->fetchAll();

// All published reviews (for drawer)
$rStmt3 = $reviewDb->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image
    FROM reviews r LEFT JOIN users u ON u.id = r.user_id
    WHERE r.music_id = :music_id AND r.status = 'published'
    ORDER BY r.created_at DESC");
$rStmt3->execute([':music_id' => $track['id']]);
$allReviews = $rStmt3->fetchAll();

function wgRelativeTime($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return '';
    $tsUtc = (new DateTime($ts, new DateTimeZone('UTC')))->getTimestamp();
    $diff = time() - $tsUtc;
    if ($diff < 0) $diff = 0;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) { $m = floor($diff / 60); return $m . ' minute' . ($m > 1 ? 's' : '') . ' ago'; }
    if ($diff < 86400) { $h = floor($diff / 3600); return $h . ' hour' . ($h > 1 ? 's' : '') . ' ago'; }
    if ($diff < 604800) { $d = floor($diff / 86400); return $d . ' day' . ($d > 1 ? 's' : '') . ' ago'; }
    if ($diff < 2592000) { $w = floor($diff / 604800); return $w . ' week' . ($w > 1 ? 's' : '') . ' ago'; }
    if ($diff < 31536000) { $mo = floor($diff / 2592000); return $mo . ' month' . ($mo > 1 ? 's' : '') . ' ago'; }
    $y = floor($diff / 31536000);
    return $y . ' year' . ($y > 1 ? 's' : '') . ' ago';
}

function wgUserInitials($name) {
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

function wgStarHtml($rating) {
    $filled = str_repeat('&#9733;', $rating);
    $empty = str_repeat('&#9734;', 5 - $rating);
    return $filled . $empty;
}

function wgReviewAvatarHtml($userImage, $userName, $idx) {
    $initials = htmlspecialchars(wgUserInitials($userName));
    if ($userImage) {
        $baseUrl = '/Aptech_E_Project_02/sound_management';
        $src = $baseUrl . '/' . ltrim($userImage, '/');
        return '<div class="wg-review-card__avatar"><img src="' . htmlspecialchars($src) . '" alt="" class="wg-review-card__avatar-img" loading="lazy" onerror="this.style.display=\'none\';this.parentNode.textContent=\'' . $initials . '\'"></div>';
    }
    return '<div class="wg-review-card__avatar">' . $initials . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($track['song_title']); ?> - <?php echo $wsWebsiteName; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/music_details.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/profile_modal/profile_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/loaders/button-spinner.css">
</head>
<body class="wg-page--details" data-music-id="<?php echo (int)$track['id']; ?>" data-handler-url="/Aptech_E_Project_02/sound_management/backend/handlers/review-handler.php" data-user-logged-in="<?php echo isUserLoggedIn() ? '1' : '0'; ?>">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- BREADCRUMB -->
<div class="wg-details-breadcrumb">
    <div class="wg-details-breadcrumb__inner">
        <a href="<?php echo $websiteBase; ?>/index.php" class="wg-details-breadcrumb__link">Home</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-details-breadcrumb__link">Music</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <span class="wg-details-breadcrumb__current"><?php echo htmlspecialchars($track['song_title']); ?></span>
    </div>
</div>

<!-- MAIN CONTENT -->
<main class="wg-details">
    <div class="wg-details__inner">

        <!-- TOP SECTION: Cover + Info -->
        <div class="wg-details__top">
            <div class="wg-details__cover">
                <?php if ($trackCoverUrl): ?>
                    <img src="<?php echo htmlspecialchars($trackCoverUrl); ?>" alt="<?php echo htmlspecialchars($track['song_title']); ?>" class="wg-details__cover-img">
                <?php else: ?>
                    <div class="wg-details__cover-placeholder wg-card__cover-placeholder--<?php echo $trackPlaceholder; ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="56" height="56">
                            <path d="M9 18V5l12-2v13"/>
                            <circle cx="6" cy="18" r="3"/>
                            <circle cx="18" cy="16" r="3"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <div class="wg-details__info">
                <div class="wg-details__status">
                    <span class="wg-details__status-badge wg-details__status-badge--<?php echo strtolower($track['status']); ?>"><?php echo htmlspecialchars($trackStatus); ?></span>
                </div>
                <h1 class="wg-details__title"><?php echo htmlspecialchars($track['song_title']); ?></h1>
                <p class="wg-details__artist"><?php echo htmlspecialchars($artistName ?: 'Unknown Artist'); ?></p>

                <!-- META PILLS -->
                <div class="wg-details__meta">
                    <?php if ($track['album_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Album</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['album_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <?php if ($track['year_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Year</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['year_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <?php if ($track['genre_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Genre</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['genre_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <?php if ($track['language_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Language</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($track['language_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <span class="wg-details__meta-pill wg-details__meta-pill--rating">
                        <span class="wg-details__meta-pill-value">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    </span>
                </div>

                <!-- DESCRIPTION -->
                <?php if ($track['description']): ?>
                <div class="wg-details__description">
                    <h3 class="wg-details__desc-heading">About this song</h3>
                    <p class="wg-details__desc-text"><?php echo htmlspecialchars($track['description']); ?></p>
                </div>
                <?php endif; ?>

                <!-- CUSTOM AUDIO PLAYER -->
                <div class="wg-player<?php echo $isInactive ? ' wg-player--disabled' : ''; ?>" id="wgPlayer">
                    <audio class="wg-player__audio" id="wgAudioPlayer" preload="metadata">
                        <?php if ($trackAudioUrl): ?>
                            <source src="<?php echo htmlspecialchars($trackAudioUrl); ?>" type="audio/mpeg">
                        <?php endif; ?>
                    </audio>
                    <button class="wg-player__play" id="wgPlayerPlay" type="button" aria-label="Play" <?php echo $isInactive ? 'disabled' : ''; ?>>
                        <svg class="svg--play" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                        <svg class="svg--pause" viewBox="0 0 24 24" fill="currentColor"><rect x="5" y="3" width="4" height="18"/><rect x="15" y="3" width="4" height="18"/></svg>
                    </button>
                    <div class="wg-player__progress-wrap">
                        <span class="wg-player__time" id="wgPlayerCurrent">0:00</span>
                        <div class="wg-player__progress-bar" id="wgPlayerProgressBar">
                            <div class="wg-player__progress-fill" id="wgPlayerProgressFill"></div>
                        </div>
                        <span class="wg-player__duration" id="wgPlayerDuration">0:00</span>
                    </div>
                    <div class="wg-player__volume">
                        <button class="wg-player__volume-btn" id="wgPlayerVolumeBtn" type="button" aria-label="Volume">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                        </button>
                        <input type="range" class="wg-player__volume-slider" id="wgPlayerVolume" min="0" max="100" value="80">
                    </div>
                    <?php if ($trackAudioUrl): ?>
                    <a class="wg-player__download" id="wgPlayerDownload" href="<?php echo $baseUrl; ?>/backend/handlers/music-download-handler.php?id=<?php echo (int)$track['id']; ?>" aria-label="Download">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($isInactive): ?>
                    <div class="wg-player__unavailable" style="color:var(--wg-text-muted);font-size:0.8125rem;padding:0 0.5rem;">Audio not available for this track</div>
                    <?php elseif (!$trackAudioUrl): ?>
                    <div class="wg-player__unavailable" style="color:var(--wg-text-muted);font-size:0.8125rem;padding:0 0.5rem;">Audio unavailable</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RATINGS & REVIEWS -->
        <section class="wg-details__reviews">

            <!-- ROW 1: Summary + Form -->
            <div class="wg-reviews__top-row">
                <div class="wg-reviews__summary">
                    <h3 class="wg-reviews__summary-title">Ratings &amp; Reviews</h3>
                    <div class="wg-reviews__score">
                        <span class="wg-reviews__score-number"><?php echo $reviewAvg; ?></span>
                        <span class="wg-reviews__score-max">/ 5</span>
                    </div>
                    <div class="wg-reviews__stars">
                        <?php
                        $avgInt = (int) floor($reviewAvg);
                        $hasHalf = ($reviewAvg - $avgInt) >= 0.5;
                        for ($s = 1; $s <= 5; $s++):
                            if ($s <= $avgInt): ?>
                                <span class="wg-reviews__star wg-reviews__star--filled">&#9733;</span>
                            <?php elseif ($s === $avgInt + 1 && $hasHalf): ?>
                                <span class="wg-reviews__star wg-reviews__star--half">&#9733;</span>
                            <?php else: ?>
                                <span class="wg-reviews__star">&#9733;</span>
                            <?php endif;
                        endfor; ?>
                    </div>
                    <p class="wg-reviews__based">Based on <?php echo $reviewTotal; ?> review<?php echo $reviewTotal !== 1 ? 's' : ''; ?></p>
                    <div class="wg-reviews__dist">
                        <?php for ($i = 5; $i >= 1; $i--):
                            $pct = $reviewTotal > 0 ? round($reviewDist[$i] / $reviewTotal * 100) : 0; ?>
                            <div class="wg-reviews__dist-row"><span class="wg-reviews__dist-label"><?php echo $i; ?> &#9733;</span><div class="wg-reviews__dist-bar"><div class="wg-reviews__dist-fill" style="width:<?php echo $pct; ?>%"></div></div><span class="wg-reviews__dist-pct"><?php echo $pct; ?>%</span></div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="wg-reviews__form-wrap">
                    <h3 class="wg-reviews__form-title">Add Your Review</h3>
                    <span class="wg-reviews__form-rating-label">Your Rating</span>
                    <div class="wg-reviews__star-select" id="starSelect">
                        <span class="wg-reviews__star-pick" data-star="1">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="2">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="3">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="4">&#9733;</span>
                        <span class="wg-reviews__star-pick" data-star="5">&#9733;</span>
                    </div>
                    <div class="wg-reviews__form-error" id="ratingError" style="display:none;color:#f87171;font-size:0.8125rem;margin-bottom:0.5rem;"></div>
                    <textarea class="wg-reviews__textarea" id="reviewText" placeholder="Write your review..." rows="3"></textarea>
                    <div class="wg-reviews__form-error" id="reviewError" style="display:none;color:#f87171;font-size:0.8125rem;margin-bottom:0.5rem;"></div>
                    <button class="wg-reviews__submit-btn" id="submitReview">Submit Review</button>
                </div>
            </div>

            <!-- ROW 2: Reviews Grid -->
            <div class="wg-reviews__cards-section">
                <h3 class="wg-details__section-title" style="margin-bottom:1rem;">Reviews</h3>
                <div class="wg-reviews__cards-grid" id="reviewsGrid">
                    <?php foreach ($reviewCards as $idx => $rc): ?>
                    <div class="wg-review-card">
                        <div class="wg-review-card__row">
                            <?php echo wgReviewAvatarHtml($rc['user_image'], $rc['user_name'], $idx); ?>
                            <div class="wg-review-card__info">
                                <span class="wg-review-card__name"><?php echo htmlspecialchars($rc['user_name'] ?: 'Anonymous'); ?></span>
                                <span class="wg-review-card__stars"><?php echo wgStarHtml((int)$rc['rating']); ?></span>
                            </div>
                            <span class="wg-review-card__date" data-ts="<?php echo htmlspecialchars($rc['created_at']); ?>"><?php echo wgRelativeTime($rc['created_at']); ?></span>
                        </div>
                        <p class="wg-review-card__text">"<?php echo htmlspecialchars($rc['review_text']); ?>"</p>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($reviewCards)): ?>
                    <div class="wg-reviews__empty" style="grid-column:1/-1;text-align:center;padding:2rem;color:var(--wg-text-muted);">No reviews yet. Be the first to review!</div>
                    <?php endif; ?>
                </div>
                <?php if ($reviewTotal > 6 || count($allReviews) > 6): ?>
                <button class="wg-reviews__all-btn" id="openDrawer">All Reviews</button>
                <?php endif; ?>
            </div>
        </section>

        <!-- ALL REVIEWS DRAWER -->
        <div class="wg-drawer-overlay" id="drawerOverlay"></div>
        <aside class="wg-drawer" id="reviewsDrawer">
            <div class="wg-drawer__header">
                <div>
                    <h3 class="wg-drawer__title">All Reviews</h3>
                    <span class="wg-drawer__count"><?php echo $reviewTotal; ?> Review<?php echo $reviewTotal !== 1 ? 's' : ''; ?></span>
                </div>
                <button class="wg-drawer__close" id="closeDrawer" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="wg-drawer__list" id="drawerList">
                <?php foreach ($allReviews as $idx => $rc): ?>
                <div class="wg-drawer__review">
                    <div class="wg-drawer__review-row">
                        <?php if ($rc['user_image']): ?>
                            <div class="wg-drawer__review-avatar"><img src="<?php echo htmlspecialchars('/Aptech_E_Project_02/sound_management/' . ltrim($rc['user_image'], '/')); ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.parentElement.textContent='<?php echo htmlspecialchars(wgUserInitials($rc['user_name'])); ?>'"></div>
                        <?php else: ?>
                            <div class="wg-drawer__review-avatar"><?php echo htmlspecialchars(wgUserInitials($rc['user_name'])); ?></div>
                        <?php endif; ?>
                        <div class="wg-drawer__review-info">
                            <span class="wg-drawer__review-name"><?php echo htmlspecialchars($rc['user_name'] ?: 'Anonymous'); ?></span>
                            <span class="wg-drawer__review-stars"><?php echo wgStarHtml((int)$rc['rating']); ?></span>
                        </div>
                        <span class="wg-drawer__review-date" data-ts="<?php echo htmlspecialchars($rc['created_at']); ?>"><?php echo wgRelativeTime($rc['created_at']); ?></span>
                    </div>
                    <p class="wg-drawer__review-text">"<?php echo htmlspecialchars($rc['review_text']); ?>"</p>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="wg-drawer__form">
                <h4 class="wg-drawer__form-title">Add Your Review</h4>
                <div class="wg-reviews__star-select wg-drawer__star-select">
                    <span class="wg-reviews__star-pick" data-star="1">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="2">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="3">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="4">&#9733;</span>
                    <span class="wg-reviews__star-pick" data-star="5">&#9733;</span>
                </div>
                <div class="wg-reviews__form-error" id="drawerRatingError" style="display:none;color:#f87171;font-size:0.8125rem;margin-bottom:0.5rem;"></div>
                <textarea class="wg-reviews__textarea" id="drawerReviewText" placeholder="Write your review..." rows="3"></textarea>
                <div class="wg-reviews__form-error" id="drawerReviewError" style="display:none;color:#f87171;font-size:0.8125rem;margin-bottom:0.5rem;"></div>
                <button class="wg-reviews__submit-btn wg-drawer__submit-btn" id="drawerSubmitReview">Submit Review</button>
            </div>
        </aside>

        <!-- MORE FROM THIS ARTIST -->
        <?php if (!empty($artistCards)): ?>
        <section class="wg-details__artist-section">
            <div class="wg-details__artist-header">
                <h2 class="wg-details__section-title">More From This Artist</h2>
                <a href="<?php echo $websiteBase; ?>/music/music.php" class="wg-details__viewall">View All <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </div>
            <div class="wg-carousel">
                <div class="wg-carousel__track" id="artistCarousel">
                    <?php
                    $artistPlaceholder = 1;
                    foreach ($artistCards as $ac):
                        $mc_id = (int)$ac['id'];
                        $mc_title = $ac['song_title'];
                        $mc_artist = $ac['artist_name'] ?: 'Unknown Artist';
                        $mc_album = $ac['album_name'] ?: '';
                        $mc_year = $ac['year_name'] ?: '';
                        $mc_genre = $ac['genre_name'] ?: '';
                        $mc_language = $ac['language_name'] ?: '';
                        $mc_placeholder = $artistPlaceholder;
                        $mc_cover_image = $ac['cover_image'] ?: '';
                        $mc_duration = $ac['duration'] ?? '';
                        $artistPlaceholder = ($artistPlaceholder % 5) + 1;
                    ?>
                    <div class="wg-carousel__item">
                        <?php include __DIR__ . '/../components/music_card/music_card.php'; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="wg-carousel__nav wg-carousel__nav--prev" id="carouselPrev" aria-label="Previous">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button class="wg-carousel__nav wg-carousel__nav--next" id="carouselNext" aria-label="Next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </section>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>

<script src="<?php echo $websiteBase; ?>/js/components/notifications/notification.js"></script>
<script src="<?php echo $websiteBase; ?>/js/components/loaders/button-spinner.js"></script>
<script src="<?php echo $jsBase; ?>/music_details.js"></script>
</body>
</html>
