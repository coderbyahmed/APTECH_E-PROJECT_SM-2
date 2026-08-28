<?php
/**
 * SOUND Group — Admin Profile AJAX Endpoint
 *
 * Handles:
 *   get_profile  — Fetch current admin's profile data
 *   update_profile — Update name, address, profile_image
 *
 * Always returns JSON responses. Admin identity comes from the session only.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';

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

$adminId = (int) $_SESSION['admin_id'];
$action  = trim($_POST['action'] ?? '');

switch ($action) {

    /* ============================================
       Get current admin profile
       ============================================ */
    case 'get_profile':
        $admin = getAdminProfile($adminId);

        if (!$admin) {
            echo json_encode(['success' => false, 'error' => 'Admin not found.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'record'  => [
                'id'            => (int) $admin['id'],
                'name'          => $admin['name'],
                'email'         => $admin['email'],
                'profile_image' => $admin['profile_image'] ?? null,
                'address'       => $admin['address'] ?? '',
                'created_at'    => $admin['created_at'],
                'updated_at'    => $admin['updated_at'],
            ],
        ]);
        exit;

    /* ============================================
       Update admin profile (name, address, image)
       ============================================ */
    case 'update_profile':
        $name    = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // --- Validation ---
        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Name is required.']);
            exit;
        }

        if (strlen($name) > 255) {
            echo json_encode(['success' => false, 'error' => 'Name must not exceed 255 characters.']);
            exit;
        }

        if (strlen($address) > 500) {
            echo json_encode(['success' => false, 'error' => 'Address must not exceed 500 characters.']);
            exit;
        }

        // --- Fetch current admin data ---
        $admin = getAdminProfile($adminId);
        if (!$admin) {
            echo json_encode(['success' => false, 'error' => 'Admin not found.']);
            exit;
        }

        $profileImage = $admin['profile_image'];
        $uploadDir    = dirname(__DIR__, 2) . '/uploads/admin-profile-image/';
        $uploadDirWeb = '/Aptech_E_Project_02/sound_management/uploads/admin-profile-image/';

        // --- Handle profile image upload ---
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_image'];

            // Size check: max 2MB
            if ($file['size'] > 2 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'Profile image must not exceed 2MB.']);
                exit;
            }

            // MIME type check
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!isset($allowedMimes[$mimeType])) {
                echo json_encode(['success' => false, 'error' => 'Invalid image type. Allowed: JPG, PNG, WebP.']);
                exit;
            }

            // Extension check
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowedExts, true)) {
                echo json_encode(['success' => false, 'error' => 'Invalid image extension. Allowed: JPG, PNG, WebP.']);
                exit;
            }

            // Create upload directory if needed
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $newFilename = 'admin_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . ($allowedMimes[$mimeType] === 'jpg' ? 'jpg' : $allowedMimes[$mimeType]);
            $dest = $uploadDir . $newFilename;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                echo json_encode(['success' => false, 'error' => 'Failed to upload profile image. Please try again.']);
                exit;
            }

            // Delete old image if it exists
            if ($admin['profile_image']) {
                $oldPath = dirname(__DIR__, 2) . '/' . ltrim($admin['profile_image'], '/');
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $profileImage = $uploadDirWeb . $newFilename;
        }

        // --- Update database ---
        updateAdminProfile($adminId, $name, $address, $profileImage);

        // --- Update session ---
        setSession('admin_name', $name);
        setSession('admin_profile_image', $profileImage);
        setSession('admin_address', $address);

        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'record'  => [
                'id'            => $adminId,
                'name'          => $name,
                'email'         => $admin['email'],
                'profile_image' => $profileImage,
                'address'       => $address,
            ],
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
