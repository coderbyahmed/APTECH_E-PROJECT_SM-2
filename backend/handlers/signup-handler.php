<?php
/**
 * SOUND Group — Signup Handler (Public)
 *
 * Handles new user registration from the website signup modal.
 * No admin authentication required.
 *
 * Actions: register
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../helpers/cloudinary.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$action = trim($_POST['action'] ?? '');

if ($action !== 'register') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$db = getDb();

// --- Upload Config ---
$tmpDir = sys_get_temp_dir();
$allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp'];
$allowedImageExts  = ['jpg', 'jpeg', 'png', 'webp'];
$maxImageSize = 2 * 1024 * 1024; // 2MB

// --- Input ---
$fullName       = trim($_POST['full_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$address        = trim($_POST['address'] ?? '');
$password       = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// --- Validation ---
$errors = [];

// Profile Image (required)
if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors['profile_image'] = 'Profile image is required.';
} else {
    $file = $_FILES['profile_image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server size limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form size limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        ];
        $errors['profile_image'] = $uploadErrors[$file['error']] ?? 'Upload error occurred.';
    } elseif ($file['size'] > $maxImageSize) {
        $errors['profile_image'] = 'Profile image must not exceed 2MB.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($mimeType, $allowedImageMimes, true) || !in_array($ext, $allowedImageExts, true)) {
            $errors['profile_image'] = 'Invalid image type. Allowed: JPG, PNG, WebP.';
        }
    }
}

// Full Name
if ($fullName === '') {
    $errors['full_name'] = 'Full name is required.';
} elseif (strlen($fullName) > 255) {
    $errors['full_name'] = 'Full name must not exceed 255 characters.';
}

// Email
if ($email === '') {
    $errors['email'] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
} elseif (strlen($email) > 255) {
    $errors['email'] = 'Email must not exceed 255 characters.';
}

// Phone (exactly 11 digits)
$phoneDigits = preg_replace('/\D/', '', $phone);
if ($phone === '') {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^\d{11}$/', $phoneDigits)) {
    $errors['phone'] = 'Phone number must be exactly 11 digits.';
}

// Address (optional but max 500 chars)
if ($address !== '' && strlen($address) > 500) {
    $errors['address'] = 'Address must not exceed 500 characters.';
}

// Password
if ($password === '') {
    $errors['password'] = 'Password is required.';
} elseif (strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters.';
}

// Confirm Password
if ($confirmPassword === '') {
    $errors['confirm_password'] = 'Please confirm your password.';
} elseif ($password !== $confirmPassword) {
    $errors['confirm_password'] = 'Passwords do not match.';
}

// Duplicate email check
if (!isset($errors['email'])) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email");
    $stmt->execute([':email' => $email]);
    if ((int) $stmt->fetchColumn() > 0) {
        $errors['email'] = 'An account already exists with this email address.';
    }
}

// Duplicate phone check
if (!isset($errors['phone'])) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `phone` = :phone");
    $stmt->execute([':phone' => $phoneDigits]);
    if ((int) $stmt->fetchColumn() > 0) {
        $errors['phone'] = 'An account already exists with this phone number.';
    }
}

// Return validation errors
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'error'   => 'Validation failed.',
        'errors'  => $errors,
    ]);
    exit;
}

// --- Generate User ID (U0001, U0002, ...) ---
$stmt = $db->prepare("SELECT `user_id` FROM `users` ORDER BY `id` DESC LIMIT 1");
$stmt->execute();
$lastUserId = $stmt->fetchColumn();

if ($lastUserId) {
    $lastNum = (int) substr($lastUserId, 1);
    $newNum = $lastNum + 1;
} else {
    $newNum = 1;
}
$newUserId = 'U' . str_pad($newNum, 4, '0', STR_PAD_LEFT);

// Double-check uniqueness (race condition safety)
$stmt = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `user_id` = :uid");
$stmt->execute([':uid' => $newUserId]);
if ((int) $stmt->fetchColumn() > 0) {
    // Find next available
    $stmt = $db->prepare("SELECT `user_id` FROM `users` ORDER BY `id` DESC LIMIT 1");
    $stmt->execute();
    $latest = $stmt->fetchColumn();
    $num = (int) substr($latest, 1) + 1;
    $newUserId = 'U' . str_pad($num, 4, '0', STR_PAD_LEFT);
}

// --- Upload Profile Image ---
$profileImage = null;
if (!empty($_FILES['profile_image']['tmp_name'])) {
    $file = $_FILES['profile_image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
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
}

// --- Hash Password ---
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// --- Insert User ---
$stmt = $db->prepare("
    INSERT INTO `users` (`user_id`, `profile_image`, `full_name`, `email`, `phone`, `address`, `password`, `created_at`, `updated_at`)
    VALUES (:user_id, :profile_image, :full_name, :email, :phone, :address, :password, NOW(), NOW())
");

$stmt->execute([
    ':user_id'       => $newUserId,
    ':profile_image' => $profileImage,
    ':full_name'     => $fullName,
    ':email'         => $email,
    ':phone'         => $phoneDigits,
    ':address'       => $address !== '' ? $address : null,
    ':password'      => $hashedPassword,
]);

echo json_encode([
    'success' => true,
    'message' => 'Account created successfully.',
    'user'    => [
        'user_id'       => $newUserId,
        'full_name'     => $fullName,
        'email'         => $email,
        'profile_image' => $profileImage,
    ],
]);
exit;
