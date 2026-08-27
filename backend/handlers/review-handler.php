<?php
/**
 * SOUND Group — Review Handler (Admin + Public)
 *
 * Single endpoint for all review operations:
 *   Admin: list, view, edit, delete, toggle-status
 *   Public: get-for-music, add, get-stats
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$action = trim($_POST['action'] ?? '');
$db = getDb();

/**
 * Build a normalized review record from a DB row.
 */
function buildReviewRecord($row) {
    return [
        'id'           => (int) $row['id'],
        'user_id'      => (int) $row['user_id'],
        'user_public_id' => $row['user_public_id'] ?? '',
        'user_name'    => $row['user_name'] ?? '',
        'user_image'   => $row['user_image'] ?? '',
        'music_id'     => (int) $row['music_id'],
        'song_title'   => $row['song_title'] ?? '',
        'artist_name'  => $row['artist_name'] ?? '',
        'album_name'   => $row['album_name'] ?? '',
        'rating'       => (int) $row['rating'],
        'review_text'  => $row['review_text'],
        'status'       => $row['status'],
        'created_at'   => $row['created_at'],
        'updated_at'   => $row['updated_at'],
    ];
}

/**
 * Format a timestamp for display.
 */
function formatTimestamp($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return null;
    return date('M d, Y, h:i A', strtotime($ts));
}

/**
 * Compute relative time string (e.g., "3 days ago").
 */
function relativeTime($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return '';
    $diff = time() - strtotime($ts);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    return date('M j, Y', strtotime($ts));
}

/**
 * JOIN SQL base for review queries with user + music info.
 */
$reviewJoinSql = "FROM reviews r
    LEFT JOIN users u ON u.id = r.user_id
    LEFT JOIN music m ON m.id = r.music_id
    LEFT JOIN artists a ON a.id = m.artist_id
    LEFT JOIN albums al ON al.id = m.album_id";

switch ($action) {

    // =========================================================
    // PUBLIC: Get reviews for a specific music (published only)
    // =========================================================
    case 'get-for-music':
        $musicId = isset($_POST['music_id']) ? (int) $_POST['music_id'] : 0;
        if ($musicId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid music ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image,
                   m.song_title, a.name AS artist_name, al.name AS album_name
            $reviewJoinSql
            WHERE r.music_id = :music_id AND r.status = 'published'
            ORDER BY r.created_at DESC");
        $stmt->execute([':music_id' => $musicId]);
        $rows = $stmt->fetchAll();

        $reviews = [];
        foreach ($rows as $row) {
            $rec = buildReviewRecord($row);
            $rec['created_at_formatted'] = formatTimestamp($row['created_at']);
            $rec['relative_time'] = relativeTime($row['created_at']);
            $reviews[] = $rec;
        }

        echo json_encode(['success' => true, 'reviews' => $reviews]);
        exit;

    // =========================================================
    // PUBLIC: Get rating stats for a specific music
    // =========================================================
    case 'get-stats':
        $musicId = isset($_POST['music_id']) ? (int) $_POST['music_id'] : 0;
        if ($musicId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid music ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT COUNT(*) AS total, AVG(rating) AS avg_rating,
                   SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star5,
                   SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star4,
                   SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star3,
                   SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star2,
                   SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star1
            FROM reviews WHERE music_id = :music_id AND status = 'published'");
        $stmt->execute([':music_id' => $musicId]);
        $stats = $stmt->fetch();

        $total = (int) ($stats['total'] ?? 0);
        $avgRating = $total > 0 ? round((float) $stats['avg_rating'], 1) : 0;
        $distribution = [
            5 => (int) ($stats['star5'] ?? 0),
            4 => (int) ($stats['star4'] ?? 0),
            3 => (int) ($stats['star3'] ?? 0),
            2 => (int) ($stats['star2'] ?? 0),
            1 => (int) ($stats['star1'] ?? 0),
        ];

        echo json_encode([
            'success' => true,
            'total' => $total,
            'avg_rating' => $avgRating,
            'distribution' => $distribution,
        ]);
        exit;

    // =========================================================
    // PUBLIC: Submit a new review (authenticated user required)
    // =========================================================
    case 'add':
        require_once __DIR__ . '/../includes/user-auth.php';

        if (!isUserLoggedIn()) {
            echo json_encode(['success' => false, 'error' => 'Please log in first.', 'login_required' => true]);
            exit;
        }

        if (!isCurrentUserActive()) {
            echo json_encode(['success' => false, 'error' => 'Your account is inactive.']);
            exit;
        }

        $userId = getCurrentUserId();
        $musicId = isset($_POST['music_id']) ? (int) $_POST['music_id'] : 0;
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $reviewText = trim($_POST['review_text'] ?? '');

        // Validation
        $errors = [];
        if ($musicId <= 0) {
            $errors['music_id'] = 'Invalid music ID.';
        }
        if ($rating < 1 || $rating > 5) {
            $errors['rating'] = 'Please select a rating.';
        }
        if ($reviewText === '') {
            $errors['review_text'] = 'Please write your review.';
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'error' => 'Validation failed.', 'errors' => $errors]);
            exit;
        }

        // Check music exists and is published (active)
        $stmt = $db->prepare("SELECT id, status FROM music WHERE id = :id");
        $stmt->execute([':id' => $musicId]);
        $music = $stmt->fetch();
        if (!$music || $music['status'] !== 'active') {
            echo json_encode(['success' => false, 'error' => 'Music not found.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO reviews (user_id, music_id, rating, review_text, status, created_at, updated_at)
            VALUES (:user_id, :music_id, :rating, :review_text, 'published', NOW(), NOW())");
        $stmt->execute([
            ':user_id' => $userId,
            ':music_id' => $musicId,
            ':rating' => $rating,
            ':review_text' => $reviewText,
        ]);

        $newId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'review_id' => (int) $newId,
        ]);
        exit;

    // =========================================================
    // ADMIN: List all reviews (published + hidden)
    // =========================================================
    case 'list':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Your session has expired. Please sign in again.']);
            exit;
        }

        $search = trim($_POST['search'] ?? '');
        $typeFilter = trim($_POST['type'] ?? 'all');
        $ratingFilter = trim($_POST['rating'] ?? 'all');
        $dateFilter = trim($_POST['date'] ?? 'all');

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(u.full_name LIKE :search OR u.user_id LIKE :search2 OR m.song_title LIKE :search3 OR a.name LIKE :search4)";
            $params[':search'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
            $params[':search4'] = "%$search%";
        }

        if ($ratingFilter !== 'all' && in_array($ratingFilter, ['1','2','3','4','5'])) {
            $where[] = "r.rating = :rating";
            $params[':rating'] = (int) $ratingFilter;
        }

        if ($dateFilter === 'today') {
            $where[] = "DATE(r.created_at) = CURDATE()";
        } elseif ($dateFilter === 'week') {
            $where[] = "r.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } elseif ($dateFilter === 'month') {
            $where[] = "r.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $db->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image,
                   m.song_title, a.name AS artist_name, al.name AS album_name
            $reviewJoinSql
            $whereSql
            ORDER BY r.created_at DESC");
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $reviews = [];
        foreach ($rows as $row) {
            $rec = buildReviewRecord($row);
            $rec['created_at_formatted'] = formatTimestamp($row['created_at']);
            $rec['updated_at_formatted'] = formatTimestamp($row['updated_at']);
            $rec['relative_time'] = relativeTime($row['created_at']);
            $reviews[] = $rec;
        }

        // Compute stats from all reviews (no filter)
        $statsStmt = $db->prepare("SELECT COUNT(*) AS total, AVG(rating) AS avg_rating,
                   SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star5,
                   SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star4,
                   SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star3,
                   SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star2,
                   SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star1
            FROM reviews");
        $statsStmt->execute();
        $allStats = $statsStmt->fetch();

        $totalAll = (int) ($allStats['total'] ?? 0);
        $avgAll = $totalAll > 0 ? round((float) $allStats['avg_rating'], 1) : '0.0';

        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'stats' => [
                'total' => $totalAll,
                'avg_rating' => $avgAll,
                'star5' => (int) ($allStats['star5'] ?? 0),
                'star4' => (int) ($allStats['star4'] ?? 0),
                'star3' => (int) ($allStats['star3'] ?? 0),
                'star2' => (int) ($allStats['star2'] ?? 0),
                'star1' => (int) ($allStats['star1'] ?? 0),
            ],
        ]);
        exit;

    // =========================================================
    // ADMIN: View a single review
    // =========================================================
    case 'view':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Your session has expired. Please sign in again.']);
            exit;
        }

        $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        if ($reviewId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid review ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT r.*, u.user_id AS user_public_id, u.full_name AS user_name, u.profile_image AS user_image,
                   m.song_title, a.name AS artist_name, al.name AS album_name
            $reviewJoinSql
            WHERE r.id = :id");
        $stmt->execute([':id' => $reviewId]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'Review not found.']);
            exit;
        }

        $rec = buildReviewRecord($row);
        $rec['created_at_formatted'] = formatTimestamp($row['created_at']);
        $rec['updated_at_formatted'] = formatTimestamp($row['updated_at']);

        echo json_encode(['success' => true, 'review' => $rec]);
        exit;

    // =========================================================
    // ADMIN: Edit a review
    // =========================================================
    case 'edit':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Your session has expired. Please sign in again.']);
            exit;
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page and try again.']);
            exit;
        }

        $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        $reviewText = trim($_POST['review_text'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if ($reviewId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid review ID.']);
            exit;
        }

        $errors = [];
        if ($rating < 1 || $rating > 5) {
            $errors['rating'] = 'Rating must be between 1 and 5.';
        }
        if ($reviewText === '') {
            $errors['review_text'] = 'Review text is required.';
        }
        if (!in_array($status, ['published', 'hidden'])) {
            $errors['status'] = 'Invalid status value.';
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'error' => 'Validation failed.', 'errors' => $errors]);
            exit;
        }

        $stmt = $db->prepare("UPDATE reviews SET rating = :rating, review_text = :review_text, status = :status, updated_at = NOW()
            WHERE id = :id");
        $stmt->execute([
            ':rating' => $rating,
            ':review_text' => $reviewText,
            ':status' => $status,
            ':id' => $reviewId,
        ]);

        echo json_encode(['success' => true, 'message' => 'Review updated successfully.']);
        exit;

    // =========================================================
    // ADMIN: Delete a review
    // =========================================================
    case 'delete':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Your session has expired. Please sign in again.']);
            exit;
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page and try again.']);
            exit;
        }

        $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        if ($reviewId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid review ID.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $reviewId]);

        echo json_encode(['success' => true, 'message' => 'Review deleted successfully.']);
        exit;

    // =========================================================
    // ADMIN: Toggle status (published ↔ hidden)
    // =========================================================
    case 'toggle-status':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Your session has expired. Please sign in again.']);
            exit;
        }

        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page and try again.']);
            exit;
        }

        $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        $newStatus = trim($_POST['new_status'] ?? '');

        if ($reviewId <= 0 || !in_array($newStatus, ['published', 'hidden'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE reviews SET status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $reviewId]);

        $label = $newStatus === 'published' ? 'Published' : 'Hidden';
        echo json_encode(['success' => true, 'message' => "Review status changed to $label.", 'new_status' => $newStatus]);
        exit;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        exit;
}
