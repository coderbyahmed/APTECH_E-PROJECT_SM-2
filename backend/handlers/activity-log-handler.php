<?php
/**
 * SOUND Group — Activity Log AJAX Handler
 *
 * Handles listing and deletion of admin activity logs:
 *   - list_all: Return all activity logs as JSON
 *   - dashboard_refresh: Return rendered HTML for dashboard (LIMIT 8)
 *   - delete_individual: Delete a single activity log entry
 *   - delete_all: Delete all activity log entries
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success'  => false,
        'error'    => 'Your session has expired. Please sign in again.',
        'redirect' => baseUrl() . '/frontend/admin/authentication/login.php',
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? $_POST['action'] ?? '';

$db = getDb();

function actLogRelativeTime($datetime) {
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

switch ($action) {
    case 'list_all':
        $activities = $db->query("SELECT * FROM `admin_activity_logs` ORDER BY `created_at` DESC")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'activities' => $activities]);
        break;

    case 'dashboard_refresh':
        $activities = $db->query("SELECT * FROM `admin_activity_logs` ORDER BY `created_at` DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

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
        $moduleNames = ['music' => 'Music', 'video' => 'Video', 'user' => 'User', 'review' => 'Review', 'contact' => 'Contact Message', 'year' => 'Year', 'artist' => 'Artist', 'album' => 'Album', 'genre' => 'Genre', 'language' => 'Language'];

        if (empty($activities)) {
            $html = '<div class="db-activity__item"><div class="db-activity__info"><p class="db-activity__text">No recent activity yet.</p></div></div>';
        } else {
            $html = '';
            foreach ($activities as $act) {
                $icon = $actIcons[$act['module']] ?? $actIcons['music'];
                $actionLabel = $actActionLabels[$act['action']] ?? ['performed', 'performed'];
                $moduleLabel = $moduleNames[$act['module']] ?? ucfirst($act['module']);
                $html .= '<div class="db-activity__item">';
                $html .= '<div class="db-activity__icon db-activity__icon--' . $icon['color'] . '">';
                $html .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">' . $icon['svg'] . '</svg>';
                $html .= '</div>';
                $html .= '<div class="db-activity__info">';
                $html .= '<p class="db-activity__text"><strong>' . htmlspecialchars($moduleLabel . ' ' . ucfirst($actionLabel[0])) . '</strong> &mdash; ' . htmlspecialchars($act['item_name']) . '</p>';
                $html .= '<span class="db-activity__time" data-datetime="' . htmlspecialchars($act['created_at']) . '">' . actLogRelativeTime($act['created_at']) . '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        echo json_encode(['success' => true, 'html' => $html]);
        break;

    case 'delete_individual':
        $csrfToken = $input['csrf_token'] ?? $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid security token. Please reload the page and try again.']);
            exit;
        }

        $activityId = (int) ($input['id'] ?? $_POST['id'] ?? 0);
        if ($activityId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid activity ID.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM `admin_activity_logs` WHERE `id` = :id");
        $stmt->execute([':id' => $activityId]);

        echo json_encode(['success' => true, 'message' => 'Activity deleted successfully.']);
        break;

    case 'delete_all':
        $csrfToken = $input['csrf_token'] ?? $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid security token. Please reload the page and try again.']);
            exit;
        }

        $db->exec("DELETE FROM `admin_activity_logs`");
        echo json_encode(['success' => true, 'message' => 'All activities deleted successfully.']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        break;
}
