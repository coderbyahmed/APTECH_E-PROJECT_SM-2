<?php
/**
 * SOUND Group — Contact Messages Handler
 *
 * Single endpoint for all contact message operations:
 *   Public:  submit
 *   Admin:   list, view, mark-read, delete
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/user-auth.php';
require_once __DIR__ . '/../includes/activity-log.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$action = trim($_POST['action'] ?? '');
$db = getDb();

/**
 * Generate a display ID like CM-1001 from a numeric DB id.
 */
function buildMessageId($id) {
    return 'CM-' . (int) $id;
}

/**
 * Build a normalized contact message record from a DB row.
 */
function buildContactRecord($row) {
    $fullNameParts = explode(' ', trim($row['full_name']), 2);
    $firstName = $fullNameParts[0] ?? '';
    $lastName = $fullNameParts[1] ?? '';
    $initials = strtoupper(substr($firstName, 0, 1));
    if ($lastName !== '') {
        $initials .= strtoupper(substr($lastName, 0, 1));
    } else {
        $initials .= strtoupper(substr($firstName, 1, 1));
    }

    $avatarColors = ['violet','blue','pink','green','amber','rose','teal','indigo','cyan','orange'];
    $colorIndex = crc32($row['full_name']) % count($avatarColors);

    return [
        'id'            => (int) $row['id'],
        'message_id'    => buildMessageId($row['id']),
        'full_name'     => $row['full_name'],
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'initials'      => $initials,
        'avatar_color'  => $avatarColors[$colorIndex],
        'profile_image' => !empty($row['profile_image']) ? $row['profile_image'] : null,
        'email'         => $row['email'],
        'phone'         => $row['phone'] ?? '',
        'inquiry_type'  => $row['inquiry_type'],
        'subject'       => $row['subject'],
        'message'       => $row['message'],
        'is_read'       => (int) $row['is_read'],
        'status'        => (int) $row['is_read'] ? 'read' : 'new',
        'created_at'    => $row['created_at'],
        'updated_at'    => $row['updated_at'] ?? '',
    ];
}

/**
 * Format a timestamp for display.
 */
function formatContactDate($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return '';
    return date('M j, Y', strtotime($ts));
}

switch ($action) {

    /* ============================================
       PUBLIC — Submit contact form
       ============================================ */
    case 'submit':
        $name        = trim($_POST['name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $inquiryType = trim($_POST['inquiry_type'] ?? '');
        $subject     = trim($_POST['subject'] ?? '');
        $message     = trim($_POST['message'] ?? '');

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Full name is required.']);
            exit;
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'A valid email address is required.']);
            exit;
        }
        if ($inquiryType === '') {
            echo json_encode(['success' => false, 'error' => 'Inquiry type is required.']);
            exit;
        }
        if ($subject === '') {
            echo json_encode(['success' => false, 'error' => 'Subject is required.']);
            exit;
        }
        if ($message === '') {
            echo json_encode(['success' => false, 'error' => 'Message is required.']);
            exit;
        }

        $allowedInquiryTypes = ['general','feedback','report','request','business','partnership','other'];
        if (!in_array($inquiryType, $allowedInquiryTypes, true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid inquiry type.']);
            exit;
        }

        $profileImage = null;
        if (isUserLoggedIn()) {
            $profileImage = getCurrentUserProfileImage();
        }

        $stmt = $db->prepare("INSERT INTO `contact_messages` (`full_name`, `email`, `phone`, `inquiry_type`, `subject`, `message`, `profile_image`, `is_read`, `created_at`, `updated_at`) VALUES (:full_name, :email, :phone, :inquiry_type, :subject, :message, :profile_image, 0, NOW(), NOW())");
        $stmt->execute([
            ':full_name'    => $name,
            ':email'        => $email,
            ':phone'        => $phone !== '' ? $phone : null,
            ':inquiry_type' => $inquiryType,
            ':subject'      => $subject,
            ':message'      => $message,
            ':profile_image'=> $profileImage,
        ]);

        $newId = (int) $db->lastInsertId();

        echo json_encode([
            'success'   => true,
            'message'   => 'Your message has been sent successfully.',
            'record_id' => $newId,
        ]);
        exit;

    /* ============================================
       ADMIN — List all contact messages
       ============================================ */
    case 'list':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
            exit;
        }

        $search     = trim($_POST['search'] ?? '');
        $status     = trim($_POST['status'] ?? 'all');
        $page       = max(1, (int) ($_POST['page'] ?? 1));
        $perPage    = max(1, min(50, (int) ($_POST['per_page'] ?? 8)));
        $offset     = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(cm.full_name LIKE :search OR cm.email LIKE :search2 OR cm.subject LIKE :search3)";
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        if ($status === 'new') {
            $where[] = "cm.is_read = 0";
        } elseif ($status === 'read') {
            $where[] = "cm.is_read = 1";
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        // Total count
        $countStmt = $db->prepare("SELECT COUNT(*) AS total FROM contact_messages cm $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];
        $totalPages = max(1, ceil($total / $perPage));

        // Records
        $sql = "SELECT cm.* FROM contact_messages cm $whereSql ORDER BY cm.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $records = [];
        foreach ($rows as $row) {
            $records[] = buildContactRecord($row);
        }

        // Stats
        $statsStmt = $db->query("SELECT COUNT(*) AS total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS new_count, SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) AS read_count FROM contact_messages");
        $stats = $statsStmt->fetch();

        echo json_encode([
            'success'    => true,
            'records'    => $records,
            'total'      => $total,
            'page'       => $page,
            'total_pages'=> $totalPages,
            'per_page'   => $perPage,
            'stats'      => [
                'total' => (int) ($stats['total'] ?? 0),
                'new'   => (int) ($stats['new_count'] ?? 0),
                'read'  => (int) ($stats['read_count'] ?? 0),
            ],
        ]);
        exit;

    /* ============================================
       ADMIN — View single contact message
       ============================================ */
    case 'view':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid message ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'Message not found.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'record'  => buildContactRecord($row),
        ]);
        exit;

    /* ============================================
       ADMIN — Mark message as read
       ============================================ */
    case 'mark-read':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid message ID.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE contact_messages SET is_read = 1, updated_at = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'error' => 'Message not found or already read.']);
            exit;
        }

        $cmSubject = '';
        $cmStmt = $db->prepare("SELECT subject FROM contact_messages WHERE id = :id");
        $cmStmt->execute([':id' => $id]);
        $cmRow = $cmStmt->fetch();
        if ($cmRow) $cmSubject = $cmRow['subject'];
        logAdminActivity($db, 'toggled_read', 'contact', $cmSubject, $id);

        echo json_encode([
            'success' => true,
            'message' => 'Message marked as read.',
        ]);
        exit;

    /* ============================================
       ADMIN — Delete contact message
       ============================================ */
    case 'delete':
        if (!isAdminLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid message ID.']);
            exit;
        }

        $delStmt = $db->prepare("SELECT subject FROM contact_messages WHERE id = :id");
        $delStmt->execute([':id' => $id]);
        $delRow = $delStmt->fetch();
        if (!$delRow) {
            echo json_encode(['success' => false, 'error' => 'Message not found.']);
            exit;
        }
        $delSubject = $delRow['subject'];

        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute([':id' => $id]);

        logAdminActivity($db, 'deleted', 'contact', $delSubject, $id);

        echo json_encode([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        exit;
}
