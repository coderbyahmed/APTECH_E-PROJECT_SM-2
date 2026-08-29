<?php
$baseUrl = '/Aptech_E_Project_02/sound_management';
$websiteBase = $baseUrl . '/frontend/website';
$cssBase = $websiteBase . '/css/video_detail';
$jsBase = $websiteBase . '/js/video_detail';
$currentPage = 'videos';

require_once dirname(__DIR__, 1) . '/includes/music-data.php';
require_once dirname(__DIR__, 3) . '/backend/includes/db.php';
require_once dirname(__DIR__, 3) . '/backend/includes/user-auth.php';
require_once __DIR__ . '/../../../backend/helpers/media-duration.php';

require_once __DIR__ . '/../../../backend/includes/website-settings.php';
$ws = getWebsiteSettings();
$wsWebsiteName = htmlspecialchars($ws['website_name']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$db = getDb();

function wgGetVideoById($id, $publicOnly = false) {
    $db = getDb();
    $sql = "SELECT v.id, v.video_title, v.description, v.video_path, v.thumbnail_path, v.status, v.created_at, v.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
        FROM videos v
        LEFT JOIN artists a ON a.id = v.artist_id
        LEFT JOIN albums al ON al.id = v.album_id
        LEFT JOIN air y ON y.id = v.year_id
        LEFT JOIN genres g ON g.id = v.genre_id
        LEFT JOIN languages l ON l.id = v.language_id
        WHERE v.id = :id";
    if ($publicOnly) {
        $sql .= " AND v.status != 'draft'";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => (int)$id]);
    return $stmt->fetch() ?: null;
}

function wgGetVideosByArtist($artistName, $excludeId = 0, $limit = 6) {
    $db = getDb();
    $sql = "SELECT v.id, v.video_title, v.description, v.video_path, v.thumbnail_path, v.status, v.created_at, v.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON a.id = v.artist_id
            LEFT JOIN albums al ON al.id = v.album_id
            LEFT JOIN air y ON y.id = v.year_id
            LEFT JOIN genres g ON g.id = v.genre_id
            LEFT JOIN languages l ON l.id = v.language_id
            WHERE v.status = 'active'";
    $params = [];

    if ($artistName !== '') {
        $sql .= " AND a.name = :artist";
        $params[':artist'] = $artistName;
    }
    if ($excludeId > 0) {
        $sql .= " AND v.id != :exclude_id";
        $params[':exclude_id'] = (int)$excludeId;
    }

    $sql .= " ORDER BY v.created_at DESC LIMIT " . (int)$limit;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function wgGetAllVideos($limit = 0, $status = 'active') {
    $db = getDb();
    $sql = "SELECT v.id, v.video_title, v.description, v.video_path, v.thumbnail_path, v.status, v.created_at, v.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON a.id = v.artist_id
            LEFT JOIN albums al ON al.id = v.album_id
            LEFT JOIN air y ON y.id = v.year_id
            LEFT JOIN genres g ON g.id = v.genre_id
            LEFT JOIN languages l ON l.id = v.language_id";

    $params = [];
    if ($status === 'published') {
        $sql .= " WHERE v.status != 'draft'";
    } elseif ($status !== 'all') {
        $sql .= " WHERE v.status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY v.created_at DESC";

    if ($limit > 0) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

$video = $id > 0 ? wgGetVideoById($id, true) : null;

if (!$video) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Video Not Found - <?php echo $wsWebsiteName; ?></title>
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64" style="color:var(--wg-text-muted);margin-bottom:1rem;"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
            <h1 style="font-size:1.5rem;color:var(--wg-text-primary);margin-bottom:0.5rem;">Video Not Found</h1>
            <p style="color:var(--wg-text-secondary);margin-bottom:1.5rem;">The video you're looking for doesn't exist or has been removed.</p>
            <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-btn wg-btn--primary">Browse Videos</a>
        </div>
    </main>
    <?php include __DIR__ . '/../components/layout/footer/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$videoStatus = ucfirst($video['status']);
$videoPlaceholder = ($video['id'] % 5) + 1;
$artistName = $video['artist_name'] ?: '';

$videoSrcUrl = '';
if (!empty($video['video_path'])) {
    $videoSrcUrl = $baseUrl . '/' . ltrim($video['video_path'], '/');
}
$videoThumbUrl = '';
if (!empty($video['thumbnail_path'])) {
    $videoThumbUrl = $baseUrl . '/' . ltrim($video['thumbnail_path'], '/');
}
$videoIsPlayable = ($videoSrcUrl !== '' && $video['status'] === 'active');

$artistCards = $artistName ? wgGetVideosByArtist($artistName, $video['id'], 6) : [];
if (count($artistCards) < 6) {
    $extraCards = wgGetAllVideos(6 - count($artistCards));
    $existingIds = array_column($artistCards, 'id');
    $existingIds[] = $video['id'];
    foreach ($extraCards as $extra) {
        if (!in_array($extra['id'], $existingIds) && count($artistCards) < 6) {
            $artistCards[] = $extra;
        }
    }
}

/* ---- Review data for this video ---- */
$reviewDb = getDb();

// Rating stats
$rStmt = $reviewDb->prepare("SELECT COUNT(*) AS total, AVG(rating) AS avg_rating,
    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star5,
    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star4,
    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star3,
    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star2,
    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star1
    FROM reviews WHERE video_id = :video_id AND status = 'published'");
$rStmt->execute([':video_id' => $video['id']]);
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
    WHERE r.video_id = :video_id AND r.status = 'published'
    ORDER BY r.created_at DESC LIMIT 6");
$rStmt2->execute([':video_id' => $video['id']]);
$reviewCards = $rStmt2->fetchAll();

// All published reviews (for drawer)
$rStmt3 = $reviewDb->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image
    FROM reviews r LEFT JOIN users u ON u.id = r.user_id
    WHERE r.video_id = :video_id AND r.status = 'published'
    ORDER BY r.created_at DESC");
$rStmt3->execute([':video_id' => $video['id']]);
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
    <title><?php echo htmlspecialchars($video['video_title']); ?> - <?php echo $wsWebsiteName; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/home/website.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/navbar/navbar.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/layout/footer/footer.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/video_card/video_card.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/music_card/music_card.css">
    <link rel="stylesheet" href="<?php echo $cssBase; ?>/video_detail.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/signup_modal/signup_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/login_modal/login_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/components/profile_modal/profile_modal.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/notifications/notification.css">
    <link rel="stylesheet" href="<?php echo $websiteBase; ?>/css/components/loaders/button-spinner.css">
</head>
<body class="wg-page--details wg-page--video-details" data-video-id="<?php echo (int)$video['id']; ?>" data-handler-url="/Aptech_E_Project_02/sound_management/backend/handlers/review-handler.php" data-user-logged-in="<?php echo isUserLoggedIn() ? '1' : '0'; ?>">

<?php include __DIR__ . '/../components/layout/navbar/navbar.php'; ?>

<!-- BREADCRUMB -->
<div class="wg-details-breadcrumb">
    <div class="wg-details-breadcrumb__inner">
        <a href="<?php echo $websiteBase; ?>/index.php" class="wg-details-breadcrumb__link">Home</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-details-breadcrumb__link">Videos</a>
        <span class="wg-details-breadcrumb__sep">/</span>
        <span class="wg-details-breadcrumb__current"><?php echo htmlspecialchars($video['video_title']); ?></span>
    </div>
</div>

<!-- MAIN CONTENT -->
<main class="wg-details">
    <div class="wg-details__inner">

        <!-- TOP SECTION: Video Player + Info -->
        <div class="wg-details__top">
            <div class="wg-details__cover wg-details__cover--video">
                <?php if ($videoSrcUrl): ?>
                    <video class="wg-details__video" id="videoPlayer" playsinline controls preload="metadata"
                        <?php if ($videoThumbUrl): ?>poster="<?php echo htmlspecialchars($videoThumbUrl); ?>"<?php endif; ?>
                        src="<?php echo htmlspecialchars($videoSrcUrl); ?>"></video>
                    <?php if ($videoIsPlayable): ?>
                    <div class="wg-details__cover-play" id="videoPlayToggle">
                        <svg class="wg-video-icon-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                        <svg class="wg-video-icon-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" style="display:none;"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </div>
                    <?php else: ?>
                    <div class="wg-details__unavailable-overlay" id="videoUnavailable">
                        <span class="wg-details__unavailable-text">Playback is unavailable for this video.</span>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="wg-details__cover-placeholder wg-card__cover-placeholder--<?php echo (int) $videoPlaceholder; ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="56" height="56">
                            <polygon points="23 7 16 12 23 17 23 7"/>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                        </svg>
                    </div>
                    <div class="wg-details__cover-play" id="videoPlayToggle">
                        <svg class="wg-video-icon-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                        <svg class="wg-video-icon-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" style="display:none;"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </div>
                <?php endif; ?>
            </div>

            <div class="wg-details__info">
                <div class="wg-details__status">
                    <span class="wg-details__status-badge wg-details__status-badge--<?php echo strtolower($video['status']); ?>"><?php echo htmlspecialchars($videoStatus); ?></span>
                </div>
                <h1 class="wg-details__title"><?php echo htmlspecialchars($video['video_title']); ?></h1>
                <p class="wg-details__artist"><?php echo htmlspecialchars($artistName ?: 'Unknown Artist'); ?></p>

                <!-- META PILLS -->
                <div class="wg-details__meta">
                    <?php if ($video['album_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Album</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['album_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <?php if ($video['year_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Year</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['year_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <?php if ($video['genre_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Genre</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['genre_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <?php if ($video['language_name']): ?>
                    <span class="wg-details__meta-pill">
                        <span class="wg-details__meta-pill-label">Language</span>
                        <span class="wg-details__meta-pill-value"><?php echo htmlspecialchars($video['language_name']); ?></span>
                    </span>
                    <?php endif; ?>
                    <span class="wg-details__meta-pill wg-details__meta-pill--rating">
                        <span class="wg-details__meta-pill-value">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    </span>
                </div>

                <!-- DESCRIPTION -->
                <?php if ($video['description']): ?>
                <div class="wg-details__description">
                    <h3 class="wg-details__desc-heading">About this video</h3>
                    <p class="wg-details__desc-text"><?php echo htmlspecialchars($video['description']); ?></p>
                </div>
                <?php endif; ?>
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
                <a href="<?php echo $websiteBase; ?>/video/video.php" class="wg-details__viewall">View All <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
            </div>
            <div class="wg-carousel">
                <div class="wg-carousel__track" id="artistCarousel">
                    <?php foreach ($artistCards as $card): ?>
                    <div class="wg-carousel__item">
                        <?php
                        $vc_id = $card['id'];
                        $vc_title = $card['video_title'];
                        $vc_artist = $card['artist_name'] ?: 'Unknown Artist';
                        $vc_album = $card['album_name'] ?: '';
                        $vc_year = $card['year_name'] ?: '';
                        $vc_genre = $card['genre_name'] ?: '';
                        $vc_language = $card['language_name'] ?: '';
                        $vc_duration = $card['duration'] ?? '';
                        $vc_placeholder = ($card['id'] % 5) + 1;
                        $vc_thumbnail = $card['thumbnail_path'] ?: '';
                        include __DIR__ . '/../components/video_card/video_card.php';
                        ?>
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
<script src="<?php echo $jsBase; ?>/video_detail.js"></script>
</body>
</html>
