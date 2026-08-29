<?php
/**
 * SOUND Group — User Management AJAX Handler (Admin)
 *
 * Single endpoint for all user CRUD operations:
 *   list, view, edit, delete, toggle-status
 *
 * Always returns JSON responses.
 * Admin identity comes from the session only.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/activity-log.php';

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page and try again.']);
    exit;
}

$action = trim($_POST['action'] ?? '');
$db = getDb();

// --- Upload Config ---
$uploadDir    = dirname(__DIR__, 2) . '/uploads/profile-img/';
$uploadDirWeb = '/Aptech_E_Project_02/sound_management/uploads/profile-img/';
$allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp'];
$allowedImageExts  = ['jpg', 'jpeg', 'png', 'webp'];
$maxImageSize = 2 * 1024 * 1024;

/**
 * Build a normalized user record from a DB row.
 */
function buildUserRecord($row) {
    return [
        'id'             => (int) $row['id'],
        'user_id'        => $row['user_id'],
        'profile_image'  => $row['profile_image'],
        'full_name'      => $row['full_name'],
        'email'          => $row['email'],
        'phone'          => $row['phone'],
        'address'        => $row['address'],
        'status'         => $row['status'],
        'created_at'     => $row['created_at'],
        'updated_at'     => $row['updated_at'],
        'last_login'     => $row['last_login'],
        'last_logout'    => $row['last_logout'],
    ];
}

/**
 * Format a timestamp for display.
 */
function formatTimestamp($ts) {
    if (!$ts || $ts === '0000-00-00 00:00:00') return null;
    return date('M d, Y, h:i A', strtotime($ts));
}

switch ($action) {

    // =========================================================
    // LIST — Fetch all users
    // =========================================================
    case 'list':
        $search = trim($_POST['search'] ?? '');
        $status = trim($_POST['status'] ?? 'all');

        $sql = "SELECT * FROM `users` WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (`full_name` LIKE :search OR `user_id` LIKE :search OR `email` LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($status !== 'all' && in_array($status, ['active', 'inactive'])) {
            $sql .= " AND `status` = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY `id` DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $records = [];
        foreach ($rows as $row) {
            $record = buildUserRecord($row);
            $record['created_at_formatted'] = formatTimestamp($row['created_at']);
            $record['last_login_formatted']  = formatTimestamp($row['last_login']);
            $record['last_logout_formatted'] = formatTimestamp($row['last_logout']);
            $records[] = $record;
        }

        echo json_encode([
            'success'  => true,
            'records'  => $records,
            'count'    => count($records),
        ]);
        exit;

    // =========================================================
    // VIEW — Fetch single user
    // =========================================================
    case 'view':
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        $record = buildUserRecord($row);
        $record['created_at_formatted'] = formatTimestamp($row['created_at']);
        $record['last_login_formatted']  = formatTimestamp($row['last_login']);
        $record['last_logout_formatted'] = formatTimestamp($row['last_logout']);

        echo json_encode([
            'success' => true,
            'record'  => $record,
        ]);
        exit;

    // =========================================================
    // EDIT — Update user information
    // =========================================================
    case 'edit':
        $id       = (int) ($_POST['id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $status   = trim($_POST['status'] ?? 'active');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
            exit;
        }

        // Validation
        $errors = [];

        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        $phoneDigits = preg_replace('/\D/', '', $phone);
        if ($phone === '') {
            $errors['phone'] = 'Phone number is required.';
        } elseif (!preg_match('/^\d{11}$/', $phoneDigits)) {
            $errors['phone'] = 'Phone number must be exactly 11 digits.';
        }

        if (!in_array($status, ['active', 'inactive'])) {
            $errors['status'] = 'Invalid status value.';
        }

        // Check user exists
        $stmt = $db->prepare("SELECT `id`, `profile_image` FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $existing = $stmt->fetch();
        if (!$existing) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        // Duplicate email (excluding self)
        if (!isset($errors['email'])) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email AND `id` != :id");
            $stmt->execute([':email' => $email, ':id' => $id]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors['email'] = 'An account already exists with this email address.';
            }
        }

        // Duplicate phone (excluding self)
        if (!isset($errors['phone'])) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `phone` = :phone AND `id` != :id");
            $stmt->execute([':phone' => $phoneDigits, ':id' => $id]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors['phone'] = 'An account already exists with this phone number.';
            }
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error'   => 'Validation failed.',
                'errors'  => $errors,
            ]);
            exit;
        }

        // Handle new profile image upload
        $profileImage = $existing['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_image'];
            if ($file['size'] > $maxImageSize) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Profile image must not exceed 2MB.', 'errors' => ['profile_image' => 'Profile image must not exceed 2MB.']]);
                exit;
            }
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($mimeType, $allowedImageMimes, true) || !in_array($ext, $allowedImageExts, true)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Invalid image type.', 'errors' => ['profile_image' => 'Invalid image type. Allowed: JPG, PNG, WebP.']]);
                exit;
            }

            $safeExt = preg_replace('/[^a-z0-9]/', '', $ext);
            $filename = 'profile_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $safeExt;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                // Delete old image
                if ($existing['profile_image']) {
                    $oldPath = dirname(__DIR__, 2) . '/' . $existing['profile_image'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $profileImage = 'uploads/profile-img/' . $filename;
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to save profile image.']);
                exit;
            }
        }

        $stmt = $db->prepare("
            UPDATE `users`
            SET `full_name` = :full_name, `email` = :email, `phone` = :phone,
                `address` = :address, `status` = :status, `profile_image` = :profile_image,
                `updated_at` = NOW()
            WHERE `id` = :id
        ");
        $stmt->execute([
            ':full_name'     => $fullName,
            ':email'         => $email,
            ':phone'         => $phoneDigits,
            ':address'       => $address !== '' ? $address : null,
            ':status'        => $status,
            ':profile_image' => $profileImage,
            ':id'            => $id,
        ]);

        logAdminActivity($db, 'updated', 'user', $fullName, $id);

        // Fetch updated record
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $record = buildUserRecord($row);
        $record['created_at_formatted'] = formatTimestamp($row['created_at']);
        $record['last_login_formatted']  = formatTimestamp($row['last_login']);
        $record['last_logout_formatted'] = formatTimestamp($row['last_logout']);

        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully.',
            'record'  => $record,
        ]);
        exit;

    // =========================================================
    // DELETE — Delete a user
    // =========================================================
    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT `id`, `full_name`, `profile_image` FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        // Delete profile image (only from expected directory)
        if ($row['profile_image']) {
            $profileImg = $row['profile_image'];
            if (strpos($profileImg, 'uploads/profile-img/') === 0) {
                $imgPath = dirname(__DIR__, 2) . '/' . $profileImg;
                if (file_exists($imgPath)) {
                    @unlink($imgPath);
                }
            }
        }

        $stmt = $db->prepare("DELETE FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        logAdminActivity($db, 'deleted', 'user', $row['full_name'], $id);

        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully.',
            'record'  => ['id' => $id, 'full_name' => $row['full_name']],
        ]);
        exit;

    // =========================================================
    // TOGGLE-STATUS — Switch active/inactive
    // =========================================================
    case 'toggle-status':
        $id     = (int) ($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
            exit;
        }

        if (!in_array($status, ['active', 'inactive'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid status value.']);
            exit;
        }

        $stmt = $db->prepare("SELECT `id`, `full_name` FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE `users` SET `status` = :status, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);

        logAdminActivity($db, 'status_changed', 'user', $row['full_name'], $id);

        echo json_encode([
            'success' => true,
            'message' => 'User status updated to ' . ucfirst($status) . '.',
            'record'  => ['id' => $id, 'status' => $status],
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
