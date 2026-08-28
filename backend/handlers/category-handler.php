<?php
/**
 * SOUND Group — Category Management AJAX Handler
 *
 * Single endpoint for all category CRUD operations:
 *   add, edit, delete, list
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

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page and try again.']);
    exit;
}

$action   = trim($_POST['action'] ?? '');
$category = trim($_POST['category'] ?? '');

$categories = [
    'year'     => ['table' => 'air',     'label' => 'Year'],
    'artist'   => ['table' => 'artists', 'label' => 'Artist'],
    'album'    => ['table' => 'albums',  'label' => 'Album'],
    'genre'    => ['table' => 'genres',  'label' => 'Genre'],
    'language' => ['table' => 'languages', 'label' => 'Language'],
];

if (!isset($categories[$category])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid category.']);
    exit;
}

$table = $categories[$category]['table'];
$label = $categories[$category]['label'];
$db    = getDb();

switch ($action) {

    // =========================================================
    // ADD
    // =========================================================
    case 'add':
        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'The ' . $label . ' name field is required.']);
            exit;
        }

        if ($category === 'year') {
            if (!preg_match('/^\d{4}$/', $name) || (int) $name < 1900 || (int) $name > 2099) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid year between 1900 and 2099.']);
                exit;
            }
        } else {
            if (strlen($name) > 255) {
                echo json_encode(['success' => false, 'error' => 'The ' . $label . ' name must not exceed 255 characters.']);
                exit;
            }
            if (strlen($name) < 1) {
                echo json_encode(['success' => false, 'error' => 'The ' . $label . ' name field is required.']);
                exit;
            }
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM `" . $table . "` WHERE `name` = :name");
        $stmt->execute([':name' => $name]);
        if ((int) $stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'error' => 'This ' . $label . ' already exists. Please use a different value.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO `" . $table . "` (`name`, `created_at`, `updated_at`) VALUES (:name, NOW(), NOW())");
        $stmt->execute([':name' => $name]);

        $newId = (int) $db->lastInsertId();

        logAdminActivity($db, 'created', $category, $name, $newId);

        echo json_encode([
            'success' => true,
            'message' => $label . ' added successfully.',
            'record'  => ['id' => $newId, 'name' => $name],
        ]);
        exit;

    // =========================================================
    // EDIT
    // =========================================================
    case 'edit':
        $id   = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid record ID.']);
            exit;
        }

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'The ' . $label . ' name field is required.']);
            exit;
        }

        if ($category === 'year') {
            if (!preg_match('/^\d{4}$/', $name) || (int) $name < 1900 || (int) $name > 2099) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid year between 1900 and 2099.']);
                exit;
            }
        } else {
            if (strlen($name) > 255) {
                echo json_encode(['success' => false, 'error' => 'The ' . $label . ' name must not exceed 255 characters.']);
                exit;
            }
        }

        $stmt = $db->prepare("SELECT `id` FROM `" . $table . "` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Record not found.']);
            exit;
        }

        $stmt = $db->prepare("SELECT `id` FROM `" . $table . "` WHERE `name` = :name AND `id` != :id");
        $stmt->execute([':name' => $name, ':id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'error' => 'This ' . $label . ' already exists. Please use a different value.']);
            exit;
        }

        $stmt = $db->prepare("UPDATE `" . $table . "` SET `name` = :name, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([':name' => $name, ':id' => $id]);

        logAdminActivity($db, 'updated', $category, $name, $id);

        echo json_encode([
            'success' => true,
            'message' => $label . ' updated successfully.',
            'record'  => ['id' => $id, 'name' => $name],
        ]);
        exit;

    // =========================================================
    // DELETE
    // =========================================================
    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid record ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT `name` FROM `" . $table . "` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'Record not found.']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM `" . $table . "` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        logAdminActivity($db, 'deleted', $category, $row['name'], $id);

        echo json_encode([
            'success' => true,
            'message' => $label . ' deleted successfully.',
            'record'  => ['id' => $id, 'name' => $row['name']],
        ]);
        exit;

    // =========================================================
    // LIST
    // =========================================================
    case 'list':
        $stmt = $db->prepare("SELECT `id`, `name`, `created_at`, `updated_at` FROM `" . $table . "` ORDER BY `id` DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'records' => $rows,
            'count'   => count($rows),
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
