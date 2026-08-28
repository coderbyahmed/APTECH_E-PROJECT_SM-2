<?php
/**
 * SOUND Group — Reviews & Ratings Management
 *
 * Real database integration.
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$pageTitle = 'Reviews & Ratings';
$activeItem = 'review-management';

include __DIR__ . '/../layout/admin-layout.php';

/* ----------------------------------------------------------
   Fetch real review data from database
   ---------------------------------------------------------- */
$db = getDb();

$reviewJoinSql = "FROM reviews r
    LEFT JOIN users u ON u.id = r.user_id
    LEFT JOIN music m ON m.id = r.music_id
    LEFT JOIN artists a ON a.id = m.artist_id
    LEFT JOIN albums al ON al.id = m.album_id
    LEFT JOIN videos v ON v.id = r.video_id";

$statsStmt = $db->prepare("SELECT COUNT(*) AS total, AVG(r.rating) AS avg_rating,
    SUM(CASE WHEN r.rating = 5 THEN 1 ELSE 0 END) AS star5,
    SUM(CASE WHEN r.rating = 4 THEN 1 ELSE 0 END) AS star4,
    SUM(CASE WHEN r.rating = 3 THEN 1 ELSE 0 END) AS star3,
    SUM(CASE WHEN r.rating = 2 THEN 1 ELSE 0 END) AS star2,
    SUM(CASE WHEN r.rating = 1 THEN 1 ELSE 0 END) AS star1,
    SUM(CASE WHEN r.status = 'published' THEN 1 ELSE 0 END) AS published_count,
    SUM(CASE WHEN r.status = 'hidden' THEN 1 ELSE 0 END) AS hidden_count
    FROM reviews r");
$statsStmt->execute();
$allStats = $statsStmt->fetch();

$totalReviews = (int) ($allStats['total'] ?? 0);
$avgRating = $totalReviews > 0 ? number_format((float) $allStats['avg_rating'], 1) : '0.0';
$publishedCount = (int) ($allStats['published_count'] ?? 0);
$hiddenCount = (int) ($allStats['hidden_count'] ?? 0);
$starCounts = [
    5 => (int) ($allStats['star5'] ?? 0),
    4 => (int) ($allStats['star4'] ?? 0),
    3 => (int) ($allStats['star3'] ?? 0),
    2 => (int) ($allStats['star2'] ?? 0),
    1 => (int) ($allStats['star1'] ?? 0),
];

$reviewsStmt = $db->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image,
    m.song_title, a.name AS artist_name, al.name AS album_name, v.video_title
    $reviewJoinSql
    ORDER BY r.created_at DESC");
$reviewsStmt->execute();
$reviews = $reviewsStmt->fetchAll();

function formatAdminTimestamp($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return null;
    return date('Y-m-d', strtotime($ts));
}

function formatAdminDateLabel($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return null;
    return date('M j, Y', strtotime($ts));
}

function adminUserInitials($name) {
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

function adminAvatarColor($id) {
    $colors = ['violet', 'blue', 'pink', 'green', 'amber', 'rose', 'teal', 'indigo', 'cyan', 'orange'];
    return $colors[$id % count($colors)];
}

$starLabel = ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'];
$starFillColors = ['rr-distribution__fill--5', 'rr-distribution__fill--4', 'rr-distribution__fill--3', 'rr-distribution__fill--2', 'rr-distribution__fill--1'];
?>

    <div class="rr-header">
        <div class="rr-header__left">
            <h1 class="rr-header__title">Reviews &amp; Ratings</h1>
            <p class="rr-header__subtitle">View and manage user reviews and ratings for music and videos.</p>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="rr-stats">
        <div class="rr-stat-card">
            <div class="rr-stat-card__icon rr-stat-card__icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="rr-stat-card__info">
                <span class="rr-stat-card__label">Total Reviews</span>
                <span class="rr-stat-card__value" id="rrTotalReviews"><?php echo $totalReviews; ?></span>
            </div>
        </div>
        <div class="rr-stat-card">
            <div class="rr-stat-card__icon rr-stat-card__icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            <div class="rr-stat-card__info">
                <span class="rr-stat-card__label">Average Rating</span>
                <span class="rr-stat-card__value" id="rrAvgRating"><?php echo $avgRating; ?></span>
            </div>
        </div>
        <div class="rr-stat-card">
            <div class="rr-stat-card__icon rr-stat-card__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="rr-stat-card__info">
                <span class="rr-stat-card__label">Published</span>
                <span class="rr-stat-card__value" id="rrPublishedCount"><?php echo $publishedCount; ?></span>
            </div>
        </div>
        <div class="rr-stat-card">
            <div class="rr-stat-card__icon rr-stat-card__icon--red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="rr-stat-card__info">
                <span class="rr-stat-card__label">Hidden</span>
                <span class="rr-stat-card__value" id="rrHiddenCount"><?php echo $hiddenCount; ?></span>
            </div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="rr-distribution">
        <div class="rr-distribution__header">
            <h2 class="rr-distribution__title">Rating Distribution</h2>
            <span class="rr-distribution__subtitle">Across all <?php echo $totalReviews; ?> reviews</span>
        </div>
        <div class="rr-distribution__rows">
            <?php for ($i = 5; $i >= 1; $i--) { ?>
                <div class="rr-distribution__row">
                    <span class="rr-distribution__label"><?php echo $starLabel[5 - $i]; ?></span>
                    <div class="rr-distribution__bar">
                        <div class="rr-distribution__fill <?php echo $starFillColors[5 - $i]; ?>" style="width: <?php echo $totalReviews > 0 ? round($starCounts[$i] / $totalReviews * 100) : 0; ?>%;"></div>
                    </div>
                    <span class="rr-distribution__count"><?php echo $starCounts[$i]; ?></span>
                    <span class="rr-distribution__pct"><?php echo $totalReviews > 0 ? round($starCounts[$i] / $totalReviews * 100) : 0; ?>%</span>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="rr-toolbar">
        <div class="rr-toolbar__search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="rr-search-input" placeholder="Search by user name, user ID or content title..." id="rrSearchInput">
        </div>
        <div class="rr-toolbar__filter">
            <label class="rr-toolbar__filter-label" for="rrTypeFilter">Content Type</label>
            <select class="rr-toolbar__filter-select" id="rrTypeFilter">
                <option value="all">All</option>
                <option value="music">Music</option>
                <option value="video">Video</option>
            </select>
        </div>
        <div class="rr-toolbar__filter">
            <label class="rr-toolbar__filter-label" for="rrRatingFilter">Rating</label>
            <select class="rr-toolbar__filter-select" id="rrRatingFilter">
                <option value="all">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>
        </div>
        <div class="rr-toolbar__filter">
            <label class="rr-toolbar__filter-label" for="rrDateFilter">Date</label>
            <select class="rr-toolbar__filter-select" id="rrDateFilter">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>
        </div>
    </div>

    <section class="rr-grid-section">
        <div class="rr-grid-section__header">
            <h2 class="rr-grid-section__title">All Reviews</h2>
        </div>

        <div class="rr-review-grid" id="rrReviewGrid">

            <?php foreach ($reviews as $r) {
                $rid = 'RV-' . str_pad($r['id'], 3, '0', STR_PAD_LEFT);
                $uid = $r['user_public_id'] ?? '';
                $fullName = $r['user_name'] ?? 'Anonymous';
                $nameParts = explode(' ', $fullName);
                $first = $nameParts[0] ?? '';
                $last = end($nameParts);
                $avatarColor = adminAvatarColor($r['id']);
                $isVideo = !empty($r['video_id']);
                $contentType = $isVideo ? 'video' : 'music';
                $title = $isVideo ? ($r['video_title'] ?? '') : ($r['song_title'] ?? '');
                $artist = $r['artist_name'] ?? 'Unknown';
                $album = $r['album_name'] ?? 'Unknown';
                $rating = (int) $r['rating'];
                $status = $r['status'];
                $text = $r['review_text'];
                $dValue = formatAdminTimestamp($r['created_at']);
                $dLabel = formatAdminDateLabel($r['created_at']);
                $uValue = formatAdminTimestamp($r['updated_at']);
                $uLabel = formatAdminDateLabel($r['updated_at']);
                $hasUpdate = $uValue && $uValue !== $dValue;
                $statusClass = $status === 'published' ? 'rr-badge--published' : 'rr-badge--hidden';
                $statusLabel = ucfirst($status);
                $starWidth = $rating * 20;
                $ratingLabel = number_format($rating, 1);
            ?>
            <article class="rr-review-card"
                     data-review-id="<?php echo (int) $r['id']; ?>"
                     data-user-id="<?php echo htmlspecialchars($uid); ?>"
                     data-first="<?php echo htmlspecialchars($first); ?>"
                     data-last="<?php echo htmlspecialchars($last); ?>"
                     data-content-type="<?php echo $contentType; ?>"
                     data-title="<?php echo htmlspecialchars($title); ?>"
                     data-artist="<?php echo htmlspecialchars($artist); ?>"
                     data-album="<?php echo htmlspecialchars($album); ?>"
                     data-rating="<?php echo $rating; ?>"
                     data-date="<?php echo $dValue; ?>"
                     data-updated="<?php echo $uValue; ?>"
                     data-status="<?php echo $status; ?>"
                     data-text="<?php echo htmlspecialchars($text); ?>">
                <div class="rr-review-card__header">
                    <div class="rr-avatar rr-avatar--card rr-avatar--<?php echo $avatarColor; ?>"><?php echo htmlspecialchars(adminUserInitials($fullName)); ?></div>
                    <div class="rr-review-card__user">
                        <h3 class="rr-review-card__user-name"><?php echo htmlspecialchars($fullName); ?></h3>
                        <span class="rr-review-card__user-id">User ID: <?php echo htmlspecialchars($uid); ?></span>
                    </div>
                    <span class="rr-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                </div>
                <div class="rr-review-card__content">
                    <div class="rr-review-card__type-row">
                        <span class="rr-type rr-type--<?php echo $contentType; ?>"><?php echo ucfirst($contentType); ?></span>
                        <h4 class="rr-review-card__title"><?php echo htmlspecialchars($title); ?></h4>
                    </div>
                    <div class="rr-review-card__meta">
                        <span class="rr-review-card__meta-item">Artist: <strong><?php echo htmlspecialchars($artist); ?></strong></span>
                        <span class="rr-review-card__meta-item">Album: <strong><?php echo htmlspecialchars($album); ?></strong></span>
                    </div>
                </div>
                <div class="rr-review-card__rating">
                    <span class="rr-stars" aria-label="<?php echo $rating; ?> out of 5 stars">
                        <span class="rr-stars__base">★★★★★</span>
                        <span class="rr-stars__fill" style="width: <?php echo $starWidth; ?>%;">★★★★★</span>
                    </span>
                    <span class="rr-review-card__rating-value"><?php echo $ratingLabel; ?></span>
                </div>
                <p class="rr-review-card__text"><?php echo htmlspecialchars($text); ?></p>
                <div class="rr-review-card__dates">
                    <span class="rr-review-card__date rr-review-card__date--review">Reviewed: <?php echo $dLabel; ?></span>
                    <?php if ($hasUpdate) { ?>
                        <span class="rr-review-card__date rr-review-card__date--updated">Updated: <?php echo $uLabel; ?></span>
                    <?php } ?>
                </div>
                <div class="rr-review-card__actions">
                    <button type="button" class="rr-action-btn rr-action-btn--view" title="View" data-rr-action="view">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button type="button" class="rr-action-btn rr-action-btn--edit" title="Edit" data-rr-action="edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button type="button" class="rr-action-btn rr-action-btn--delete" title="Delete" data-rr-action="delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </button>
                </div>
            </article>
            <?php } ?>

        </div>

        <!-- Empty State -->
        <div class="rr-empty" id="rrEmptyState" hidden>
            <div class="rr-empty__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="36" height="36">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <h3 class="rr-empty__title">No reviews found</h3>
            <p class="rr-empty__desc">Try adjusting your search or filters.</p>
        </div>

        <div class="rr-grid-section__footer">
            <span class="rr-grid-section__count" id="rrCount">Showing 6 of <?php echo $totalReviews; ?> reviews</span>
            <div class="rr-pagination">
                <button type="button" class="rr-pagination__btn" id="rrPrevPage" aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <div class="rr-pagination__pages" id="rrPaginationPages">
                    <!-- Page number buttons are built by review-management.js -->
                </div>
                <button type="button" class="rr-pagination__btn" id="rrNextPage" aria-label="Next page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- View Review Modal -->
    <div class="sg-modal" id="rrViewModal">
        <div class="sg-modal__overlay" data-rr-close="view"></div>
        <div class="sg-modal__dialog rr-modal">
            <button type="button" class="sg-modal__close" data-rr-close="view">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="rr-view-header">
                    <div class="rr-avatar rr-avatar--large rr-avatar--violet" id="rr-view-avatar">AT</div>
                    <div class="rr-view-info">
                        <h2 class="rr-view-info__title" id="rr-view-user">Ava Thompson</h2>
                        <span class="rr-view-info__id">User ID: <strong id="rr-view-uid">U1001</strong></span>
                    </div>
                </div>
                <div class="rr-view-tags">
                    <span class="rr-type rr-type--music" id="rr-view-type-badge">Music</span>
                    <span class="rr-badge" id="rr-view-status-badge">Published</span>
                </div>
                <div class="rr-view-title-block">
                    <h3 class="rr-view-title" id="rr-view-title">Midnight Echoes</h3>
                </div>
                <div class="rr-view-details">
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Artist</span>
                        <span class="rr-view-detail__value" id="rr-view-artist">The Velvet Waves</span>
                    </div>
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Album</span>
                        <span class="rr-view-detail__value" id="rr-view-album">Neon Tides</span>
                    </div>
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Rating</span>
                        <span class="rr-view-detail__value rr-view-rating">
                            <span class="rr-stars" id="rr-view-stars">
                                <span class="rr-stars__base">★★★★★</span>
                                <span class="rr-stars__fill" style="width: 100%;">★★★★★</span>
                            </span>
                            <span id="rr-view-rating-value">5.0</span>
                        </span>
                    </div>
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Review ID</span>
                        <span class="rr-view-detail__value" id="rr-view-review-id">RV-101</span>
                    </div>
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Review Date</span>
                        <span class="rr-view-detail__value" id="rr-view-date">Aug 19, 2026</span>
                    </div>
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Updated Date</span>
                        <span class="rr-view-detail__value" id="rr-view-updated">—</span>
                    </div>
                    <div class="rr-view-detail">
                        <span class="rr-view-detail__label">Status</span>
                        <span class="rr-view-detail__value" id="rr-view-status-text">Published</span>
                    </div>
                </div>
                <div class="rr-view-review">
                    <span class="rr-view-review__label">Review</span>
                    <p class="rr-view-review__text" id="rr-view-text">Absolutely incredible...</p>
                </div>
                <div class="rr-view-actions">
                    <button type="button" class="sg-btn rr-btn-cancel" data-rr-close="view">Close</button>
                    <button type="button" class="sg-btn sg-btn--primary" id="rrViewEditBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit Review
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div class="sg-modal" id="rrEditModal">
        <div class="sg-modal__overlay" data-rr-close="edit"></div>
        <div class="sg-modal__dialog rr-modal rr-modal--wide">
            <button type="button" class="sg-modal__close" data-rr-close="edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="sg-modal__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <h2 class="sg-modal__title">Edit Review</h2>
                <p class="sg-modal__subtitle">Update <strong id="rr-edit-user-name">Ava Thompson</strong>'s review for <strong id="rr-edit-content-name">Midnight Echoes</strong>.</p>

                <form id="rrEditForm" class="rr-form">
                    <div class="rr-form__profile">
                        <div class="rr-avatar rr-avatar--large rr-avatar--violet" id="rr-edit-avatar">AT</div>
                        <div class="rr-form__profile-info">
                            <span class="rr-form__profile-name" id="rr-edit-user">Ava Thompson</span>
                            <span class="rr-form__profile-id" id="rr-edit-uid">User ID: U1001</span>
                        </div>
                    </div>

                    <div class="rr-form__grid">
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="rr-edit-type">Content Type</label>
                            <select class="sg-form-input rr-form-input" id="rr-edit-type">
                                <option value="music">Music</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="rr-edit-title">Music / Video Title</label>
                            <input type="text" class="sg-form-input rr-form-input" id="rr-edit-title" value="Midnight Echoes">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="rr-edit-artist">Artist</label>
                            <input type="text" class="sg-form-input rr-form-input" id="rr-edit-artist" value="The Velvet Waves">
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="rr-edit-album">Album</label>
                            <input type="text" class="sg-form-input rr-form-input" id="rr-edit-album" value="Neon Tides">
                        </div>
                        <div class="sg-form-group rr-form__group--full">
                            <label class="sg-form-label" for="rr-edit-stars">Rating</label>
                            <div class="rr-rating-picker" id="rr-edit-stars">
                                <span class="rr-rating-picker__stars">
                                    <button type="button" class="rr-star-btn" data-rr-star="1" aria-label="1 star">★</button>
                                    <button type="button" class="rr-star-btn" data-rr-star="2" aria-label="2 stars">★</button>
                                    <button type="button" class="rr-star-btn" data-rr-star="3" aria-label="3 stars">★</button>
                                    <button type="button" class="rr-star-btn" data-rr-star="4" aria-label="4 stars">★</button>
                                    <button type="button" class="rr-star-btn" data-rr-star="5" aria-label="5 stars">★</button>
                                </span>
                                <span class="rr-rating-picker__value" id="rr-edit-rating-value">5.0</span>
                            </div>
                        </div>
                        <div class="sg-form-group rr-form__group--full">
                            <label class="sg-form-label" for="rr-edit-text">Review</label>
                            <textarea class="sg-form-input rr-form-input rr-form-textarea" id="rr-edit-text" rows="4">Absolutely incredible...</textarea>
                        </div>
                        <div class="sg-form-group">
                            <label class="sg-form-label" for="rr-edit-status">Status</label>
                            <select class="sg-form-input rr-form-input" id="rr-edit-status">
                                <option value="published">Published</option>
                                <option value="hidden">Hidden</option>
                            </select>
                        </div>
                    </div>

                    <div class="rr-form__actions">
                        <button type="button" class="sg-btn rr-btn-cancel" data-rr-close="edit">Cancel</button>
                        <button type="button" class="sg-btn sg-btn--primary" id="rrUpdateReviewBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Update Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Review Modal -->
    <div class="sg-modal" id="rrDeleteModal">
        <div class="sg-modal__overlay" data-rr-close="delete"></div>
        <div class="sg-modal__dialog rr-modal rr-modal--delete">
            <button type="button" class="sg-modal__close" data-rr-close="delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="sg-modal__body">
                <div class="rr-delete-body">
                    <div class="rr-delete-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </div>
                    <h2 class="sg-modal__title">Delete Review</h2>
                    <p class="sg-modal__subtitle">Are you sure you want to delete the review by <strong id="rr-delete-user">Ava Thompson</strong> for <strong id="rr-delete-content">Midnight Echoes</strong>? This action cannot be undone.</p>
                </div>
                <div class="rr-form__actions rr-delete-actions">
                    <button type="button" class="sg-btn rr-btn-cancel" data-rr-close="delete">Cancel</button>
                    <button type="button" class="sg-btn sg-btn--danger" id="rrConfirmDeleteBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete Review
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/admin-layout-end.php'; ?>