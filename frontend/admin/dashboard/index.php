<?php
/**
 * SOUND Group — Admin Dashboard
 */

require_once __DIR__ . '/../../../backend/includes/session.php';
require_once __DIR__ . '/../../../backend/includes/auth.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireAuth();

$db = getDb();

function dashboardRelativeTime($datetime) {
    if (!$datetime) return '';
    $tz = new DateTimeZone('UTC');
    $now = new DateTime('now', $tz);
    $ago = new DateTime($datetime, $tz);
    $diff = $now->getTimestamp() - $ago->getTimestamp();
    if ($diff < 0) $diff = 0;
    if ($diff < 10) return 'Just now';
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) { $m = floor($diff / 60); return $m . ' minute' . ($m > 1 ? 's' : '') . ' ago'; }
    if ($diff < 86400) { $h = floor($diff / 3600); return $h . ' hour' . ($h > 1 ? 's' : '') . ' ago'; }
    if ($diff < 172800) return 'Yesterday';
    if ($diff < 604800) { $d = floor($diff / 86400); return $d . ' days ago'; }
    if ($diff < 2592000) { $w = floor($diff / 604800); return $w . ' week' . ($w > 1 ? 's' : '') . ' ago'; }
    if ($diff < 31536000) { $mo = floor($diff / 2592000); return $mo . ' month' . ($mo > 1 ? 's' : '') . ' ago'; }
    $y = floor($diff / 31536000); return $y . ' year' . ($y > 1 ? 's' : '') . ' ago';
}

/* --- Statistics Counts --- */
$totalMusic = (int) $db->query("SELECT COUNT(*) FROM music")->fetchColumn();
$totalVideos = (int) $db->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$totalUsers = (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalReviews = (int) $db->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

/* --- Content Overview Chart Data (music & videos per month this year) --- */
$chartMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$contentChartMusic = array_fill(0, 12, 0);
$contentChartVideos = array_fill(0, 12, 0);

$musicByMonth = $db->query("SELECT MONTH(created_at) AS m, COUNT(*) AS c FROM music WHERE YEAR(created_at) = YEAR(CURDATE()) GROUP BY MONTH(created_at)")->fetchAll(PDO::FETCH_KEY_PAIR);
foreach ($musicByMonth as $m => $c) { $contentChartMusic[$m - 1] = (int) $c; }
$videosByMonth = $db->query("SELECT MONTH(created_at) AS m, COUNT(*) AS c FROM videos WHERE YEAR(created_at) = YEAR(CURDATE()) GROUP BY MONTH(created_at)")->fetchAll(PDO::FETCH_KEY_PAIR);
foreach ($videosByMonth as $m => $c) { $contentChartVideos[$m - 1] = (int) $c; }

/* --- User Growth Chart Data (cumulative users per month, last 6 months) --- */
$userGrowthLabels = [];
$userGrowthData = [];
$monthsBack = 6;
for ($i = $monthsBack - 1; $i >= 0; $i--) {
    $dt = new DateTime("-{$i} months");
    $userGrowthLabels[] = $dt->format('M');
    $yr = (int) $dt->format('Y');
    $mn = (int) $dt->format('m');
    $stmtUg = $db->prepare("SELECT COUNT(*) FROM users WHERE YEAR(created_at) <= ? AND (YEAR(created_at) < ? OR MONTH(created_at) <= ?)");
    $nextMonth = $mn + 1;
    $nextYear = $yr;
    if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
    $stmtUg->execute([$yr, $nextYear, $mn]);
    $userGrowthData[] = (int) $stmtUg->fetchColumn();
}

/* --- Reviews & Ratings --- */
$reviewStats = $db->query("SELECT COALESCE(AVG(rating),0) AS avg_rating, SUM(status='published') AS published_count, SUM(status='hidden') AS hidden_count FROM reviews")->fetch(PDO::FETCH_ASSOC);
$avgRating = round((float) $reviewStats['avg_rating'], 1);
$publishedReviews = (int) $reviewStats['published_count'];
$hiddenReviews = (int) $reviewStats['hidden_count'];
$starCounts = [];
for ($s = 1; $s <= 5; $s++) {
    $stmtStar = $db->prepare("SELECT COUNT(*) FROM reviews WHERE rating = ?");
    $stmtStar->execute([$s]);
    $starCounts[$s] = (int) $stmtStar->fetchColumn();
}
$maxStar = max($starCounts);

/* --- Recent Activity (admin actions only from admin_activity_logs) --- */
$actIcons = [
    'music'    => ['color' => 'purple',  'svg' => '<path d="M9 18V5l12-2v13" /><circle cx="6" cy="18" r="3" /><circle cx="18" cy="16" r="3" />'],
    'video'    => ['color' => 'pink',    'svg' => '<polygon points="23 7 16 12 23 17 23 7" /><rect x="1" y="5" width="15" height="14" rx="2" ry="2" />'],
    'user'     => ['color' => 'blue',    'svg' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="8.5" cy="7" r="4" /><line x1="20" y1="8" x2="20" y2="14" /><line x1="23" y1="11" x2="17" y2="11" />'],
    'review'   => ['color' => 'amber',   'svg' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />'],
    'contact'  => ['color' => 'green',   'svg' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />'],
    'year'     => ['color' => 'teal',    'svg' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />'],
    'artist'   => ['color' => 'teal',    'svg' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />'],
    'album'    => ['color' => 'teal',    'svg' => '<path d="M9 18V5l12-2v13" /><circle cx="6" cy="18" r="3" /><circle cx="18" cy="16" r="3" />'],
    'genre'    => ['color' => 'teal',    'svg' => '<line x1="8" y1="6" x2="21" y2="6" /><line x1="8" y1="12" x2="21" y2="12" /><line x1="8" y1="18" x2="21" y2="18" /><line x1="3" y1="6" x2="3.01" y2="6" /><line x1="3" y1="12" x2="3.01" y2="12" /><line x1="3" y1="18" x2="3.01" y2="18" />'],
    'language' => ['color' => 'teal',    'svg' => '<circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" /><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />'],
];

$actActionLabels = [
    'created'        => ['created',   'added'],
    'updated'        => ['updated',   'updated'],
    'deleted'        => ['deleted',   'deleted'],
    'status_changed' => ['status changed for', 'status changed'],
    'published'      => ['published', 'published'],
    'hidden'         => ['hid',       'hid'],
    'toggled_read'   => ['marked as read', 'marked as read'],
];

$activities = $db->query("SELECT * FROM `admin_activity_logs` ORDER BY `created_at` DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$allActivities = $db->query("SELECT * FROM `admin_activity_logs` ORDER BY `created_at` DESC")->fetchAll(PDO::FETCH_ASSOC);

/* --- Latest Music --- */
$latestMusic = $db->query("SELECT m.id, m.song_title, m.cover_image, m.status, m.created_at, a.name AS artist_name, al.name AS album_name FROM music m LEFT JOIN artists a ON m.artist_id = a.id LEFT JOIN albums al ON m.album_id = al.id ORDER BY m.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

/* --- Latest Videos --- */
$latestVideos = $db->query("SELECT v.id, v.video_title, v.thumbnail_path, v.status, v.created_at, a.name AS artist_name FROM videos v LEFT JOIN artists a ON v.artist_id = a.id ORDER BY v.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

/* --- Recent Users --- */
$recentUsers = $db->query("SELECT id, full_name, email, profile_image, status, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

/* --- Contact Messages --- */
$contactMessages = $db->query("SELECT id, full_name, email, subject, message, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Dashboard';
$activeItem = 'dashboard';

include __DIR__ . '/../layout/admin-layout.php';
?>

<!-- Welcome Header -->
<div class="db-welcome">
    <div class="db-welcome__text">
        <h2 class="db-welcome__title">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
            👋</h2>
        <p class="db-welcome__subtitle">Here's what's happening with your platform today. Monitor your content, users,
            and engagement at a glance.</p>
    </div>
    <div class="db-welcome__date">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" width="16" height="16">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
        </svg>
        <span id="dbCurrentDate"></span>
    </div>
</div>

<!-- Stats Cards -->
<div class="db-stats">
    <div class="db-stat-card">
        <div class="db-stat-card__icon db-stat-card__icon--purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="22" height="22">
                <path d="M9 18V5l12-2v13" />
                <circle cx="6" cy="18" r="3" />
                <circle cx="18" cy="16" r="3" />
            </svg>
        </div>
        <div class="db-stat-card__info">
            <span class="db-stat-card__label">Total Music</span>
            <span class="db-stat-card__value"><?php echo number_format($totalMusic); ?></span>
        </div>
        <div class="db-stat-card__trend db-stat-card__trend--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="14" height="14">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            </svg>
            &nbsp;
        </div>
    </div>
    <div class="db-stat-card">
        <div class="db-stat-card__icon db-stat-card__icon--pink">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="22" height="22">
                <polygon points="23 7 16 12 23 17 23 7" />
                <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
            </svg>
        </div>
        <div class="db-stat-card__info">
            <span class="db-stat-card__label">Total Videos</span>
            <span class="db-stat-card__value"><?php echo number_format($totalVideos); ?></span>
        </div>
        <div class="db-stat-card__trend db-stat-card__trend--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="14" height="14">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            </svg>
            &nbsp;
        </div>
    </div>
    <div class="db-stat-card">
        <div class="db-stat-card__icon db-stat-card__icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="22" height="22">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
        </div>
        <div class="db-stat-card__info">
            <span class="db-stat-card__label">Total Users</span>
            <span class="db-stat-card__value"><?php echo number_format($totalUsers); ?></span>
        </div>
        <div class="db-stat-card__trend db-stat-card__trend--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="14" height="14">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            </svg>
            &nbsp;
        </div>
    </div>
    <div class="db-stat-card">
        <div class="db-stat-card__icon db-stat-card__icon--amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="22" height="22">
                <polygon
                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
            </svg>
        </div>
        <div class="db-stat-card__info">
            <span class="db-stat-card__label">Total Reviews</span>
            <span class="db-stat-card__value"><?php echo number_format($totalReviews); ?></span>
        </div>
        <div class="db-stat-card__trend db-stat-card__trend--up">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" width="14" height="14">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            </svg>
            &nbsp;
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="db-charts">
    <div class="db-card db-chart-card">
        <div class="db-card__header">
            <h3 class="db-card__title">Content Overview</h3>
            <span class="db-card__badge">This Year</span>
        </div>
        <div class="db-chart-wrap">
            <canvas id="dbContentChart"></canvas>
        </div>
    </div>
    <div class="db-card db-chart-card">
        <div class="db-card__header">
            <h3 class="db-card__title">User Growth</h3>
            <span class="db-card__badge">Last 6 Months</span>
        </div>
        <div class="db-chart-wrap">
            <canvas id="dbUserChart"></canvas>
        </div>
    </div>
</div>

<!-- Reviews & Ratings + Recent Activity -->
<div class="db-row">
    <!-- Reviews & Ratings Overview -->
    <div class="db-card db-reviews-overview">
        <div class="db-card__header">
            <h3 class="db-card__title">Reviews &amp; Ratings</h3>
        </div>
        <div class="db-rating-summary">
            <div class="db-rating-big">
                <span class="db-rating-big__number"><?php echo $avgRating; ?></span>
                <div class="db-rating-big__stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg viewBox="0 0 24 24" fill="<?php echo $i <= round($avgRating) ? '#f59e0b' : '#e5e7eb'; ?>" width="16" height="16">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    <?php endfor; ?>
                </div>
                <span class="db-rating-big__count"><?php echo number_format($totalReviews); ?> reviews</span>
            </div>
            <div class="db-rating-stats">
                <div class="db-rating-stat">
                    <span class="db-rating-stat__label">Published</span>
                    <span class="db-rating-stat__value db-rating-stat__value--green"><?php echo number_format($publishedReviews); ?></span>
                </div>
                <div class="db-rating-stat">
                    <span class="db-rating-stat__label">Hidden</span>
                    <span class="db-rating-stat__value db-rating-stat__value--gray"><?php echo number_format($hiddenReviews); ?></span>
                </div>
            </div>
        </div>
        <div class="db-rating-bars">
            <?php for ($s = 5; $s >= 1; $s--):
                $pct = $maxStar > 0 ? round(($starCounts[$s] / $maxStar) * 100) : 0;
            ?>
            <div class="db-rating-bar">
                <span class="db-rating-bar__label"><?php echo $s; ?> ★</span>
                <div class="db-rating-bar__track">
                    <div class="db-rating-bar__fill" style="width:<?php echo $pct; ?>%"></div>
                </div>
                <span class="db-rating-bar__count"><?php echo $starCounts[$s]; ?></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="db-card db-activity">
        <div class="db-card__header">
            <h3 class="db-card__title">Recent Activity</h3>
            <button type="button" class="db-card__link" id="sgActivityViewAll" style="background:none;border:none;cursor:pointer;font:inherit;padding:0;">View All</button>
        </div>
        <div class="db-activity__list">
            <?php if (empty($activities)): ?>
            <div class="db-activity__item">
                <div class="db-activity__info">
                    <p class="db-activity__text">No recent activity yet.</p>
                </div>
            </div>
            <?php else: foreach ($activities as $act):
                $icon = $actIcons[$act['module']] ?? $actIcons['music'];
                $actionLabel = $actActionLabels[$act['action']] ?? ['performed', 'performed'];
                $moduleNames = ['music' => 'Music', 'video' => 'Video', 'user' => 'User', 'review' => 'Review', 'contact' => 'Contact Message', 'year' => 'Year', 'artist' => 'Artist', 'album' => 'Album', 'genre' => 'Genre', 'language' => 'Language'];
                $moduleLabel = $moduleNames[$act['module']] ?? ucfirst($act['module']);
            ?>
            <div class="db-activity__item">
                <div class="db-activity__icon db-activity__icon--<?php echo $icon['color']; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" width="14" height="14">
                        <?php echo $icon['svg']; ?>
                    </svg>
                </div>
                <div class="db-activity__info">
                    <p class="db-activity__text"><strong><?php echo $moduleLabel . ' ' . ucfirst($actionLabel[0]); ?></strong> — <?php echo htmlspecialchars($act['item_name']); ?></p>
                    <span class="db-activity__time" data-datetime="<?php echo htmlspecialchars($act['created_at']); ?>"><?php echo dashboardRelativeTime($act['created_at']); ?></span>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<!-- Latest Music Table -->
<div class="db-card db-table-card">
    <div class="db-card__header">
        <h3 class="db-card__title">Latest Music</h3>
        <a href="<?php echo $baseUrl; ?>/frontend/admin/music-management/index.php" class="db-card__link">View All →</a>
    </div>
    <div class="db-table-wrap">
        <table class="db-table">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Album</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($latestMusic)): ?>
                <tr><td colspan="6" style="text-align:center;padding:2rem;color:#9ca3af;">No music found.</td></tr>
                <?php else: foreach ($latestMusic as $music): ?>
                <tr>
                    <td>
                        <?php if ($music['cover_image']): ?>
                        <img src="<?php echo htmlspecialchars(strpos($music['cover_image'], 'http') === 0 ? $music['cover_image'] : $baseUrl . '/' . $music['cover_image']); ?>" alt="Cover" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                        <?php else: ?>
                        <div class="db-table-cover db-table-cover--purple">🎵</div>
                        <?php endif; ?>
                    </td>
                    <td class="db-table-title"><?php echo htmlspecialchars($music['song_title']); ?></td>
                    <td><?php echo htmlspecialchars($music['artist_name'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($music['album_name'] ?? '—'); ?></td>
                    <td>
                        <?php if ($music['status'] === 'active'): ?>
                            <span class="db-badge db-badge--active">Active</span>
                        <?php elseif ($music['status'] === 'draft'): ?>
                            <span class="db-badge db-badge--draft">Draft</span>
                        <?php else: ?>
                            <span class="db-badge db-badge--inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="db-table-date"><?php echo date('M d, Y', strtotime($music['created_at'])); ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Latest Videos Table -->
<div class="db-card db-table-card">
    <div class="db-card__header">
        <h3 class="db-card__title">Latest Videos</h3>
        <a href="<?php echo $baseUrl; ?>/frontend/admin/video-management/index.php" class="db-card__link">View All →</a>
    </div>
    <div class="db-table-wrap">
        <table class="db-table">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($latestVideos)): ?>
                <tr><td colspan="5" style="text-align:center;padding:2rem;color:#9ca3af;">No videos found.</td></tr>
                <?php else: foreach ($latestVideos as $video): ?>
                <tr>
                    <td>
                        <?php if ($video['thumbnail_path']): ?>
                        <img src="<?php echo htmlspecialchars(strpos($video['thumbnail_path'], 'http') === 0 ? $video['thumbnail_path'] : $baseUrl . '/' . $video['thumbnail_path']); ?>" alt="Thumbnail" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                        <?php else: ?>
                        <div class="db-table-cover db-table-cover--pink">🎬</div>
                        <?php endif; ?>
                    </td>
                    <td class="db-table-title"><?php echo htmlspecialchars($video['video_title']); ?></td>
                    <td><?php echo htmlspecialchars($video['artist_name'] ?? '—'); ?></td>
                    <td>
                        <?php if ($video['status'] === 'active'): ?>
                            <span class="db-badge db-badge--active">Active</span>
                        <?php elseif ($video['status'] === 'draft'): ?>
                            <span class="db-badge db-badge--draft">Draft</span>
                        <?php else: ?>
                            <span class="db-badge db-badge--inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="db-table-date"><?php echo date('M d, Y', strtotime($video['created_at'])); ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Users + Contact Messages -->
<div class="db-row">
    <!-- Recent Users -->
    <div class="db-card db-users-card">
        <div class="db-card__header">
            <h3 class="db-card__title">Recent Users</h3>
            <a href="<?php echo $baseUrl; ?>/frontend/admin/user-management/index.php" class="db-card__link">View All
                →</a>
        </div>
        <div class="db-user-list">
            <?php if (empty($recentUsers)): ?>
            <div class="db-user-item">
                <div class="db-user-item__info">
                    <span class="db-user-item__name" style="color:#9ca3af;">No users found.</span>
                </div>
            </div>
            <?php else: foreach ($recentUsers as $user):
                $initials = '';
                $parts = explode(' ', $user['full_name']);
                foreach ($parts as $p) { $initials .= strtoupper(mb_substr($p, 0, 1)); }
                $initials = mb_substr($initials, 0, 2);
                $colors = ['#8b5cf6,#7c3aed','#60a5fa,#3b82f6','#f472b6,#ec4899','#34d399,#10b981','#fbbf24,#f59e0b','#f87171,#ef4444'];
                $colorIdx = $user['id'] % count($colors);
            ?>
            <div class="db-user-item">
                <?php if ($user['profile_image']): ?>
                <div class="db-user-item__avatar" style="padding:0;">
                    <img src="<?php echo htmlspecialchars(strpos($user['profile_image'], 'http') === 0 ? $user['profile_image'] : $baseUrl . '/' . ltrim($user['profile_image'], '/')); ?>" alt="Profile" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="db-user-item__avatar" style="background:linear-gradient(135deg,<?php echo $colors[$colorIdx]; ?>)"><?php echo $initials; ?></div>
                <?php endif; ?>
                <div class="db-user-item__info">
                    <span class="db-user-item__name"><?php echo htmlspecialchars($user['full_name']); ?></span>
                    <span class="db-user-item__email"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="db-user-item__meta">
                    <span class="db-user-item__date"><?php echo dashboardRelativeTime($user['created_at']); ?></span>
                    <?php if ($user['status'] === 'active'): ?>
                        <span class="db-badge db-badge--active">Active</span>
                    <?php else: ?>
                        <span class="db-badge db-badge--inactive">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Contact Messages -->
    <div class="db-card db-messages-card">
        <div class="db-card__header">
            <h3 class="db-card__title">Contact Messages</h3>
            <a href="<?php echo $baseUrl; ?>/frontend/admin/contact-messages/index.php" class="db-card__link">View All
                →</a>
        </div>
        <div class="db-message-list">
            <?php if (empty($contactMessages)): ?>
            <div class="db-message-item">
                <div class="db-message-item__info">
                    <p class="db-message-item__preview" style="color:#9ca3af;">No messages yet.</p>
                </div>
            </div>
            <?php else: foreach ($contactMessages as $msg): ?>
            <div class="db-message-item <?php echo !$msg['is_read'] ? 'db-message-item--unread' : ''; ?>">
                <div class="db-message-item__dot"></div>
                <div class="db-message-item__info">
                    <div class="db-message-item__top">
                        <span class="db-message-item__sender"><?php echo htmlspecialchars($msg['full_name']); ?></span>
                        <span class="db-message-item__time"><?php echo dashboardRelativeTime($msg['created_at']); ?></span>
                    </div>
                    <span class="db-message-item__subject"><?php echo htmlspecialchars($msg['subject']); ?></span>
                    <p class="db-message-item__preview"><?php echo htmlspecialchars(mb_substr($msg['message'], 0, 100)); ?><?php echo mb_strlen($msg['message']) > 100 ? '...' : ''; ?></p>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<!-- Activity View All Modal -->
<div class="sg-act-overlay" id="sgActivityModal" style="display:none;">
    <div class="sg-act-modal">
        <div class="sg-act-modal__header">
            <h3 class="sg-act-modal__title">All Recent Activity</h3>
            <div class="sg-act-modal__header-actions">
                <button type="button" class="sg-act-btn sg-act-btn--danger" id="sgActivityDeleteAll">Delete All</button>
                <button type="button" class="sg-act-modal__close" id="sgActivityModalClose">&times;</button>
            </div>
        </div>
        <div class="sg-act-modal__body" id="sgActivityModalBody">
            <!-- Activities loaded dynamically -->
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="sg-act-overlay" id="sgConfirmModal" style="display:none;">
    <div class="sg-act-modal sg-act-modal--confirm">
        <div class="sg-act-modal__header">
            <h3 class="sg-act-modal__title" id="sgConfirmTitle">Confirm</h3>
            <button type="button" class="sg-act-modal__close" id="sgConfirmClose">&times;</button>
        </div>
        <div class="sg-act-modal__body">
            <p id="sgConfirmMessage"></p>
        </div>
        <div class="sg-act-modal__footer">
            <button type="button" class="sg-act-btn sg-act-btn--cancel" id="sgConfirmCancel">Cancel</button>
            <button type="button" class="sg-act-btn sg-act-btn--danger" id="sgConfirmOk">Delete</button>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        /* Current Date */
        var dateEl = document.getElementById('dbCurrentDate');
        if (dateEl) {
            var d = new Date();
            dateEl.textContent = d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        /* Content Overview — Bar Chart */
        var contentCtx = document.getElementById('dbContentChart');
        if (contentCtx) {
            new Chart(contentCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartMonths); ?>,
                    datasets: [
                        {
                            label: 'Music',
                            data: <?php echo json_encode($contentChartMusic); ?>,
                            backgroundColor: 'rgba(124, 58, 237, 0.8)',
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Videos',
                            data: <?php echo json_encode($contentChartVideos); ?>,
                            backgroundColor: 'rgba(236, 72, 153, 0.8)',
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 12, borderRadius: 3, useBorderRadius: true, padding: 16, font: { size: 12, family: "'Instrument Sans',sans-serif" } } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, family: "'Instrument Sans',sans-serif" }, color: '#9ca3af' } },
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 11, family: "'Instrument Sans',sans-serif" }, color: '#9ca3af' }, border: { display: false } }
                    }
                }
            });
        }

        /* User Growth — Line Chart */
        var userCtx = document.getElementById('dbUserChart');
        if (userCtx) {
            new Chart(userCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($userGrowthLabels); ?>,
                    datasets: [{
                        label: 'Users',
                        data: <?php echo json_encode($userGrowthData); ?>,
                        fill: true,
                        backgroundColor: 'rgba(124, 58, 237, 0.08)',
                        borderColor: '#7c3aed',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#7c3aed',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, family: "'Instrument Sans',sans-serif" }, color: '#9ca3af' } },
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 11, family: "'Instrument Sans',sans-serif" }, color: '#9ca3af' }, border: { display: false } }
                    }
                }
            });
        }
    })();

    /* --- Auto-update activity timestamps every 30 seconds --- */
    function sgRelativeTime(ts) {
        if (!ts) return '';
        var now = Math.floor(Date.now() / 1000);
        var then = Math.floor(new Date(ts + 'Z').getTime() / 1000);
        var diff = now - then;
        if (diff < 0) diff = 0;
        if (diff < 10) return 'Just now';
        if (diff < 60) return diff + ' seconds ago';
        var m = Math.floor(diff / 60);
        if (diff < 3600) return m + ' minute' + (m > 1 ? 's' : '') + ' ago';
        var h = Math.floor(diff / 3600);
        if (diff < 86400) return h + ' hour' + (h > 1 ? 's' : '') + ' ago';
        if (diff < 172800) return 'Yesterday';
        var d = Math.floor(diff / 86400);
        if (diff < 604800) return d + ' days ago';
        var w = Math.floor(diff / 604800);
        if (diff < 2592000) return w + ' week' + (w > 1 ? 's' : '') + ' ago';
        var mo = Math.floor(diff / 2592000);
        if (diff < 31536000) return mo + ' month' + (mo > 1 ? 's' : '') + ' ago';
        var y = Math.floor(diff / 31536000);
        return y + ' year' + (y > 1 ? 's' : '') + ' ago';
    }

    function sgUpdateActivityTimestamps() {
        var els = document.querySelectorAll('.db-activity__time[data-datetime]');
        for (var i = 0; i < els.length; i++) {
            var dtAttr = els[i].getAttribute('data-datetime');
            if (dtAttr) {
                els[i].textContent = sgRelativeTime(dtAttr);
            }
        }
    }

    setInterval(sgUpdateActivityTimestamps, 30000);

    /* --- Activity Modal & Delete Functionality --- */
    var sgCSRFToken = '<?php echo csrfToken(); ?>';
    var sgActivityModal = document.getElementById('sgActivityModal');
    var sgActivityModalBody = document.getElementById('sgActivityModalBody');
    var sgConfirmModal = document.getElementById('sgConfirmModal');
    var sgConfirmTitle = document.getElementById('sgConfirmTitle');
    var sgConfirmMessage = document.getElementById('sgConfirmMessage');
    var sgConfirmOk = document.getElementById('sgConfirmOk');
    var sgConfirmCancel = document.getElementById('sgConfirmCancel');
    var sgConfirmClose = document.getElementById('sgConfirmClose');
    var sgConfirmCallback = null;

    var sgActIcons = <?php echo json_encode($actIcons); ?>;
    var sgActActionLabels = <?php echo json_encode($actActionLabels); ?>;
    var sgModuleNames = <?php echo json_encode(['music' => 'Music', 'video' => 'Video', 'user' => 'User', 'review' => 'Review', 'contact' => 'Contact Message', 'year' => 'Year', 'artist' => 'Artist', 'album' => 'Album', 'genre' => 'Genre', 'language' => 'Language']); ?>;

    function sgShowConfirm(title, message, callback) {
        sgConfirmTitle.textContent = title;
        sgConfirmMessage.textContent = message;
        sgConfirmCallback = callback;
        sgConfirmModal.style.display = 'flex';
    }

    function sgHideConfirm() {
        sgConfirmModal.style.display = 'none';
        sgConfirmCallback = null;
    }

    sgConfirmOk.addEventListener('click', function() {
        if (sgConfirmCallback) sgConfirmCallback();
        sgHideConfirm();
    });
    sgConfirmCancel.addEventListener('click', sgHideConfirm);
    sgConfirmClose.addEventListener('click', sgHideConfirm);
    sgConfirmModal.addEventListener('click', function(e) {
        if (e.target === sgConfirmModal) sgHideConfirm();
    });

    function sgRenderActivityItem(act, showDelete) {
        var icon = sgActIcons[act.module] || sgActIcons['music'];
        var actionLabel = sgActActionLabels[act.action] || ['performed', 'performed'];
        var moduleLabel = sgModuleNames[act.module] || act.module.charAt(0).toUpperCase() + act.module.slice(1);
        var deleteBtn = showDelete ? '<button type="button" class="sg-activity-delete" data-id="' + act.id + '" title="Delete activity"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>' : '';
        return '<div class="db-activity__item" data-activity-id="' + act.id + '">' +
            '<div class="db-activity__icon db-activity__icon--' + icon.color + '">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">' + icon.svg + '</svg>' +
            '</div>' +
            '<div class="db-activity__info">' +
                '<p class="db-activity__text"><strong>' + moduleLabel + ' ' + actionLabel[0].charAt(0).toUpperCase() + actionLabel[0].slice(1) + '</strong> &mdash; ' + sgEscapeHtml(act.item_name) + '</p>' +
                '<span class="db-activity__time" data-datetime="' + sgEscapeHtml(act.created_at) + '">' + sgRelativeTime(act.created_at) + '</span>' +
            '</div>' +
            deleteBtn +
        '</div>';
    }

    function sgEscapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function sgLoadAllActivities() {
        sgActivityModalBody.innerHTML = '<div class="sg-act-modal-loading">Loading activities...</div>';
        sgActivityModal.style.display = 'flex';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo baseUrl(); ?>/backend/handlers/activity-log-handler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.success && resp.activities) {
                        sgRenderModalActivities(resp.activities);
                    } else {
                        sgActivityModalBody.innerHTML = '<div class="sg-act-modal-empty">Failed to load activities.</div>';
                    }
                } else {
                    sgActivityModalBody.innerHTML = '<div class="sg-act-modal-empty">Failed to load activities.</div>';
                }
            }
        };
        xhr.send(JSON.stringify({ action: 'list_all', csrf_token: sgCSRFToken }));
    }

    function sgRenderModalActivities(activities) {
        if (!activities || activities.length === 0) {
                    sgActivityModalBody.innerHTML = '<div class="sg-act-modal-empty"><p>No recent activity yet.</p></div>';
            return;
        }
        var html = '';
        for (var i = 0; i < activities.length; i++) {
            html += sgRenderActivityItem(activities[i], true);
        }
        sgActivityModalBody.innerHTML = html;
        sgAttachDeleteListeners();
    }

    function sgAttachDeleteListeners() {
        var btns = sgActivityModalBody.querySelectorAll('.sg-activity-delete');
        for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener('click', function() {
                var id = parseInt(this.getAttribute('data-id'));
                sgShowConfirm('Delete Activity', 'Are you sure you want to delete this activity?', function() {
                    sgDeleteActivity(id);
                });
            });
        }
    }

    function sgDeleteActivity(id) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo baseUrl(); ?>/backend/handlers/activity-log-handler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.success) {
                    var item = sgActivityModalBody.querySelector('[data-activity-id="' + id + '"]');
                    if (item) item.remove();
                    sgRefreshDashboard();
                }
            }
        };
        xhr.send(JSON.stringify({ action: 'delete_individual', id: id, csrf_token: sgCSRFToken }));
    }

    function sgDeleteAllActivities() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo baseUrl(); ?>/backend/handlers/activity-log-handler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.success) {
            sgActivityModalBody.innerHTML = '<div class="sg-act-modal-empty"><p>No recent activity yet.</p></div>';
                    sgRefreshDashboard();
                }
            }
        };
        xhr.send(JSON.stringify({ action: 'delete_all', csrf_token: sgCSRFToken }));
    }

    function sgRefreshDashboard() {
        var activityList = document.querySelector('.db-activity__list');
        if (!activityList) return;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo baseUrl(); ?>/backend/handlers/activity-log-handler.php?action=dashboard_refresh', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var resp = JSON.parse(xhr.responseText);
                if (resp.success) {
                    activityList.innerHTML = resp.html;
                }
            }
        };
        xhr.send();
    }

    document.getElementById('sgActivityViewAll').addEventListener('click', function() {
        sgLoadAllActivities();
    });
    document.getElementById('sgActivityModalClose').addEventListener('click', function() {
        sgActivityModal.style.display = 'none';
    });
    sgActivityModal.addEventListener('click', function(e) {
        if (e.target === sgActivityModal) sgActivityModal.style.display = 'none';
    });
    document.getElementById('sgActivityDeleteAll').addEventListener('click', function() {
        sgShowConfirm('Delete All Activities', 'Are you sure you want to delete all recent activities?', function() {
            sgDeleteAllActivities();
        });
    });
</script>

<?php
include __DIR__ . '/../layout/admin-layout-end.php';
?>
