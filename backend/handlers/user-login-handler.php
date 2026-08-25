<?php
/**
 * SOUND Group — User Login Handler (Public)
 *
 * Handles user login from the website login modal.
 * No admin authentication required.
 *
 * Actions: login
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/user-auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$action = trim($_POST['action'] ?? '');

if ($action !== 'login') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// --- Validation ---
$errors = [];

if ($email === '') {
    $errors['email'] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}

if ($password === '') {
    $errors['password'] = 'Password is required.';
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

$db = getDb();

// --- Find user by email ---
$stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid email or password.',
    ]);
    exit;
}

// --- Check account status ---
if ($user['status'] !== 'active') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error'   => 'Your account has been deactivated. Please contact support.',
    ]);
    exit;
}

// --- Login (sets session + updates last_login) ---
loginUser($user);

echo json_encode([
    'success' => true,
    'message' => 'Login successful!',
    'user'    => [
        'user_id'       => $user['user_id'],
        'full_name'     => $user['full_name'],
        'email'         => $user['email'],
        'profile_image' => $user['profile_image'],
    ],
]);
exit;
