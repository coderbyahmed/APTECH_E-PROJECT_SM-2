<?php
/**
 * SOUND Group — User Profile Handler (Public)
 *
 * Handles profile view and update for the logged-in website user.
 * Security: uses session user_db_id — never trusts client-supplied user ID.
 *
 * Actions: get, update
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/user-auth.php';
require_once __DIR__ . '/../helpers/cloudinary.php';

header('Content-Type: application/json');

if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Please log in first.', 'redirect' => baseUrl() . '/frontend/website/index.php']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$action = trim($_POST['action'] ?? '');
$userId = getCurrentUserId();
$db = getDb();

switch ($action) {

    case 'get':
        $stmt = $db->prepare("SELECT `user_id`, `full_name`, `email`, `phone`, `address`, `profile_image` FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'user' => [
                'user_id' => $row['user_id'],
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'address' => $row['address'] ?? '',
                'profile_image' => $row['profile_image'],
            ],
        ]);
        exit;

    case 'update':
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $errors = [];

        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        } elseif (strlen($fullName) > 255) {
            $errors['full_name'] = 'Full name must not exceed 255 characters.';
        }

        $phoneDigits = preg_replace('/\D/', '', $phone);
        if ($phone === '') {
            $errors['phone'] = 'Phone number is required.';
        } elseif (!preg_match('/^\d{11}$/', $phoneDigits)) {
            $errors['phone'] = 'Phone number must be exactly 11 digits.';
        }

        if ($address !== '' && strlen($address) > 500) {
            $errors['address'] = 'Address must not exceed 500 characters.';
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Validation failed.', 'errors' => $errors]);
            exit;
        }

        $stmt = $db->prepare("SELECT `phone`, `profile_image` FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $userId]);
        $existing = $stmt->fetch();

        if (!$existing) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        if (!isset($errors['phone'])) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `phone` = :phone AND `id` != :id");
            $stmt->execute([':phone' => $phoneDigits, ':id' => $userId]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors['phone'] = 'An account already exists with this phone number.';
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Validation failed.', 'errors' => $errors]);
                exit;
            }
        }

        $tmpDir = sys_get_temp_dir();
        $allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedImageExts = ['jpg', 'jpeg', 'png', 'webp'];
        $maxImageSize = 2 * 1024 * 1024;

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
                echo json_encode(['success' => false, 'error' => 'Invalid image type. Allowed: JPG, PNG, WebP.', 'errors' => ['profile_image' => 'Invalid image type. Allowed: JPG, PNG, WebP.']]);
                exit;
            }

            $safeExt = preg_replace('/[^a-z0-9]/', '', $ext);
            $filename = 'profile_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $safeExt;

            $cloudinary = getCloudinary();
            if ($cloudinary->isConfigured()) {
                try {
                    $tmpPath = $tmpDir . '/' . $filename;
                    move_uploaded_file($file['tmp_name'], $tmpPath);
                    $result = $cloudinary->upload($tmpPath, 'sound_management/userProfile', $filename, [
                        'resource_type' => 'image',
                        'transformation' => 'c_fill,w_400,h_400',
                    ]);
                    @unlink($tmpPath);
                    $profileImage = $result['url'];
                } catch (Exception $e) {
                    if (file_exists($tmpPath)) @unlink($tmpPath);
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to upload profile image to cloud storage. Please try again.']);
                    exit;
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Cloud storage is not configured. Please contact administrator.']);
                exit;
            }

            // Delete old image
            if ($existing['profile_image']) {
                if (CloudinaryHelper::isCloudinaryUrl($existing['profile_image'])) {
                    $cloudinary->deleteByUrl($existing['profile_image']);
                } else {
                    $oldPath = dirname(__DIR__, 2) . '/' . $existing['profile_image'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }
        }

        $stmt = $db->prepare("
            UPDATE `users`
            SET `full_name` = :full_name, `phone` = :phone, `address` = :address,
                `profile_image` = :profile_image, `updated_at` = NOW()
            WHERE `id` = :id
        ");
        $stmt->execute([
            ':full_name' => $fullName,
            ':phone' => $phoneDigits,
            ':address' => $address !== '' ? $address : null,
            ':profile_image' => $profileImage,
            ':id' => $userId,
        ]);

        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_phone'] = $phoneDigits;
        $_SESSION['user_address'] = $address;
        $_SESSION['user_profile_image'] = $profileImage;

        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => [
                'full_name' => $fullName,
                'phone' => $phoneDigits,
                'address' => $address,
                'profile_image' => $profileImage,
            ],
        ]);
        exit;

    case 'sync':
        $stmt = $db->prepare("SELECT `user_id`, `full_name`, `email`, `phone`, `address`, `profile_image` FROM `users` WHERE `id` = :id");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }

        $_SESSION['user_name'] = $row['full_name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_phone'] = $row['phone'];
        $_SESSION['user_address'] = $row['address'] ?? '';
        $_SESSION['user_profile_image'] = $row['profile_image'];

        echo json_encode([
            'success' => true,
            'user' => [
                'user_id' => $row['user_id'],
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'address' => $row['address'] ?? '',
                'profile_image' => $row['profile_image'],
            ],
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
