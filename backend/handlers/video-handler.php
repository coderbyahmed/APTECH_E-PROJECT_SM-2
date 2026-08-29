<?php
/**
 * SOUND Group — Video Management AJAX Handler
 *
 * Single endpoint for all video CRUD operations:
 *   add, edit, delete, list
 *
 * Always returns JSON responses.
 * Handles file uploads for video files and thumbnail images.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/activity-log.php';
require_once __DIR__ . '/../helpers/media-duration.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success'  => false,
        'error'    => 'Your session has expired. Please sign in again.',
        'redirect' => baseUrl() . '/frontend/admin/authentication/login.php',
    ]);
    exit;
}

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';
if (!verifyCsrfToken($submittedToken)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Security token mismatch. Please reload the page and try again.']);
    exit;
}

$action = trim($_POST['action'] ?? '');

$db = getDb();

$uploadDir      = dirname(__DIR__, 2) . '/uploads/';
$videosDir      = $uploadDir . 'videos/';
$thumbnailsDir  = $uploadDir . 'thumbnails/';

$allowedVideoMimes = [
    'video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo',
    'video/x-matroska', 'video/x-m4v', 'video/avi',
];
$allowedVideoExts = ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v'];
$allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp'];
$allowedImageExts = ['jpg', 'jpeg', 'png', 'webp'];

$maxVideoSize   = 500 * 1024 * 1024;
$maxImageSize   = 5 * 1024 * 1024;

function generateUniqueFilename($originalName, $prefix) {
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeExt = preg_replace('/[^a-z0-9]/', '', $ext);
    return $prefix . '_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $safeExt;
}

function validateFile($file, $allowedMimes, $allowedExts, $maxSize, $label) {
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['valid' => false, 'error' => '', 'skip' => true];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server size limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form size limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        ];
        return ['valid' => false, 'error' => $errors[$file['error']] ?? 'Upload error occurred.', 'skip' => false];
    }
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024);
        return ['valid' => false, 'error' => $label . ' must not exceed ' . $maxMB . 'MB.', 'skip' => false];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($mimeType, $allowedMimes, true) || !in_array($ext, $allowedExts, true)) {
        return ['valid' => false, 'error' => 'Invalid ' . $label . ' type. Allowed: ' . implode(', ', $allowedExts) . '.', 'skip' => false];
    }
    return ['valid' => true, 'skip' => false];
}

function saveUploadedFile($file, $destDir, $newFilename) {
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $destDir . $newFilename)) {
        return false;
    }
    return true;
}

function deleteFileIfExists($path) {
    if ($path && file_exists($path)) {
        @unlink($path);
    }
}

function buildVideoRecord($row) {
    return [
        'id'             => (int) $row['id'],
        'video_title'    => $row['video_title'],
        'artist_id'      => $row['artist_id'] !== null ? (int) $row['artist_id'] : null,
        'artist_name'    => $row['artist_name'] ?? '',
        'album_id'       => $row['album_id'] !== null ? (int) $row['album_id'] : null,
        'album_name'     => $row['album_name'] ?? '',
        'year_id'        => $row['year_id'] !== null ? (int) $row['year_id'] : null,
        'year_name'      => $row['year_name'] ?? '',
        'genre_id'       => $row['genre_id'] !== null ? (int) $row['genre_id'] : null,
        'genre_name'     => $row['genre_name'] ?? '',
        'language_id'    => $row['language_id'] !== null ? (int) $row['language_id'] : null,
        'language_name'  => $row['language_name'] ?? '',
        'description'    => $row['description'] ?? '',
        'video_path'     => $row['video_path'] ?? '',
        'thumbnail_path' => $row['thumbnail_path'] ?? '',
        'duration'       => $row['duration'] ?? '',
        'status'         => $row['status'],
        'created_at'     => $row['created_at'] ?? '',
        'updated_at'     => $row['updated_at'] ?? '',
    ];
}

$joinSql = "SELECT v.*,
    a.`name` AS `artist_name`,
    al.`name` AS `album_name`,
    y.`name` AS `year_name`,
    g.`name` AS `genre_name`,
    l.`name` AS `language_name`
FROM `videos` v
LEFT JOIN `artists` a ON a.`id` = v.`artist_id`
LEFT JOIN `albums` al ON al.`id` = v.`album_id`
LEFT JOIN `air` y ON y.`id` = v.`year_id`
LEFT JOIN `genres` g ON g.`id` = v.`genre_id`
LEFT JOIN `languages` l ON l.`id` = v.`language_id`";

switch ($action) {

    case 'add':
        $title       = trim($_POST['video_title'] ?? '');
        $artistId    = (int) ($_POST['artist_id'] ?? 0);
        $albumId     = (int) ($_POST['album_id'] ?? 0);
        $yearId      = (int) ($_POST['year_id'] ?? 0);
        $genreId     = (int) ($_POST['genre_id'] ?? 0);
        $languageId  = (int) ($_POST['language_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status      = trim($_POST['status'] ?? 'active');

        if ($title === '') {
            echo json_encode(['success' => false, 'error' => 'Video title is required.']);
            exit;
        }
        if (strlen($title) > 255) {
            echo json_encode(['success' => false, 'error' => 'Video title must not exceed 255 characters.']);
            exit;
        }
        if (!in_array($status, ['active', 'draft', 'inactive'], true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid status value.']);
            exit;
        }
        if ($artistId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Please select an artist.']);
            exit;
        }
        if ($yearId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Please select a year.']);
            exit;
        }

        $stmt = $db->prepare("SELECT `id` FROM `artists` WHERE `id` = :id");
        $stmt->execute([':id' => $artistId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Selected artist not found.']);
            exit;
        }
        if ($albumId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `albums` WHERE `id` = :id");
            $stmt->execute([':id' => $albumId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected album not found.']);
                exit;
            }
        }
        if ($yearId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `air` WHERE `id` = :id");
            $stmt->execute([':id' => $yearId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected year not found.']);
                exit;
            }
        }
        if ($genreId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `genres` WHERE `id` = :id");
            $stmt->execute([':id' => $genreId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected genre not found.']);
                exit;
            }
        }
        if ($languageId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `languages` WHERE `id` = :id");
            $stmt->execute([':id' => $languageId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected language not found.']);
                exit;
            }
        }

        $videoFile   = $_FILES['video_file'] ?? null;
        $thumbnail   = $_FILES['thumbnail'] ?? null;

        if (!$videoFile || !is_array($videoFile) || $videoFile['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => 'Video file is required.']);
            exit;
        }
        $vVideo = validateFile($videoFile, $allowedVideoMimes, $allowedVideoExts, $maxVideoSize, 'video file');
        if (!$vVideo['valid']) {
            echo json_encode(['success' => false, 'error' => $vVideo['error']]);
            exit;
        }
        $vThumb = validateFile($thumbnail, $allowedImageMimes, $allowedImageExts, $maxImageSize, 'thumbnail');
        if (!$vThumb['skip'] && !$vThumb['valid']) {
            echo json_encode(['success' => false, 'error' => $vThumb['error']]);
            exit;
        }

        $savedVideoPath   = '';
        $savedThumbPath   = '';

        $newVideoName = generateUniqueFilename($videoFile['name'], 'video');
        if (!saveUploadedFile($videoFile, $videosDir, $newVideoName)) {
            echo json_encode(['success' => false, 'error' => 'Failed to save video file. Please try again.']);
            exit;
        }
        $savedVideoPath = 'uploads/videos/' . $newVideoName;

        $duration = getMediaDuration($videosDir . $newVideoName);

        $postDuration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
        if ($postDuration !== '') {
            $duration = $postDuration;
        }

        if ($thumbnail && $thumbnail['error'] !== UPLOAD_ERR_NO_FILE) {
            $newThumbName = generateUniqueFilename($thumbnail['name'], 'thumb');
            if (!saveUploadedFile($thumbnail, $thumbnailsDir, $newThumbName)) {
                if ($savedVideoPath) deleteFileIfExists(dirname(__DIR__, 2) . '/' . $savedVideoPath);
                echo json_encode(['success' => false, 'error' => 'Failed to save thumbnail. Please try again.']);
                exit;
            }
            $savedThumbPath = 'uploads/thumbnails/' . $newThumbName;
        }

        $stmt = $db->prepare("INSERT INTO `videos` (`video_title`, `artist_id`, `album_id`, `year_id`, `genre_id`, `language_id`, `description`, `video_path`, `thumbnail_path`, `duration`, `status`, `created_at`, `updated_at`) VALUES (:video_title, :artist_id, :album_id, :year_id, :genre_id, :language_id, :description, :video_path, :thumbnail_path, :duration, :status, NOW(), NOW())");
        $stmt->execute([
            ':video_title'    => $title,
            ':artist_id'      => $artistId,
            ':album_id'       => $albumId > 0 ? $albumId : null,
            ':year_id'        => $yearId,
            ':genre_id'       => $genreId > 0 ? $genreId : null,
            ':language_id'    => $languageId > 0 ? $languageId : null,
            ':description'    => $description,
            ':video_path'     => $savedVideoPath,
            ':thumbnail_path' => $savedThumbPath,
            ':duration'       => $duration,
            ':status'         => $status,
        ]);

        $newId = (int) $db->lastInsertId();

        logAdminActivity($db, 'created', 'video', $title, $newId);

        $stmt = $db->prepare($joinSql . " WHERE v.`id` = :id");
        $stmt->execute([':id' => $newId]);
        $row = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'message' => 'Video added successfully.',
            'record'  => buildVideoRecord($row),
        ]);
        exit;

    case 'edit':
        $id          = (int) ($_POST['id'] ?? 0);
        $title       = trim($_POST['video_title'] ?? '');
        $artistId    = (int) ($_POST['artist_id'] ?? 0);
        $albumId     = (int) ($_POST['album_id'] ?? 0);
        $yearId      = (int) ($_POST['year_id'] ?? 0);
        $genreId     = (int) ($_POST['genre_id'] ?? 0);
        $languageId  = (int) ($_POST['language_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status      = trim($_POST['status'] ?? 'active');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid video record ID.']);
            exit;
        }
        if ($title === '') {
            echo json_encode(['success' => false, 'error' => 'Video title is required.']);
            exit;
        }
        if (strlen($title) > 255) {
            echo json_encode(['success' => false, 'error' => 'Video title must not exceed 255 characters.']);
            exit;
        }
        if (!in_array($status, ['active', 'draft', 'inactive'], true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid status value.']);
            exit;
        }
        if ($artistId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Please select an artist.']);
            exit;
        }
        if ($yearId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Please select a year.']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM `videos` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $existing = $stmt->fetch();
        if (!$existing) {
            echo json_encode(['success' => false, 'error' => 'Video record not found.']);
            exit;
        }

        $stmt = $db->prepare("SELECT `id` FROM `artists` WHERE `id` = :id");
        $stmt->execute([':id' => $artistId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Selected artist not found.']);
            exit;
        }
        if ($albumId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `albums` WHERE `id` = :id");
            $stmt->execute([':id' => $albumId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected album not found.']);
                exit;
            }
        }
        if ($yearId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `air` WHERE `id` = :id");
            $stmt->execute([':id' => $yearId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected year not found.']);
                exit;
            }
        }
        if ($genreId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `genres` WHERE `id` = :id");
            $stmt->execute([':id' => $genreId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected genre not found.']);
                exit;
            }
        }
        if ($languageId > 0) {
            $stmt = $db->prepare("SELECT `id` FROM `languages` WHERE `id` = :id");
            $stmt->execute([':id' => $languageId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Selected language not found.']);
                exit;
            }
        }

        $videoFile = $_FILES['video_file'] ?? null;
        $thumbnail = $_FILES['thumbnail'] ?? null;

        $vVideo = validateFile($videoFile, $allowedVideoMimes, $allowedVideoExts, $maxVideoSize, 'video file');
        if (!$vVideo['skip'] && !$vVideo['valid']) {
            echo json_encode(['success' => false, 'error' => $vVideo['error']]);
            exit;
        }
        $vThumb = validateFile($thumbnail, $allowedImageMimes, $allowedImageExts, $maxImageSize, 'thumbnail');
        if (!$vThumb['skip'] && !$vThumb['valid']) {
            echo json_encode(['success' => false, 'error' => $vThumb['error']]);
            exit;
        }

        $newVideoPath = $existing['video_path'];
        $newThumbPath = $existing['thumbnail_path'];
        $duration = $existing['duration'];

        $postDuration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
        $hasPostDuration = ($postDuration !== '');

        if (!$vVideo['skip'] && $videoFile && $videoFile['error'] === UPLOAD_ERR_OK) {
            $newVideoName = generateUniqueFilename($videoFile['name'], 'video');
            if (!saveUploadedFile($videoFile, $videosDir, $newVideoName)) {
                echo json_encode(['success' => false, 'error' => 'Failed to save video file. Please try again.']);
                exit;
            }
            $oldVideoPath = dirname(__DIR__, 2) . '/' . $existing['video_path'];
            if ($existing['video_path']) deleteFileIfExists($oldVideoPath);
            $newVideoPath = 'uploads/videos/' . $newVideoName;
            if (!$hasPostDuration) {
                $duration = getMediaDuration($videosDir . $newVideoName);
            }
        }

        if ($hasPostDuration) {
            $duration = $postDuration;
        }

        if (!$vThumb['skip'] && $thumbnail && $thumbnail['error'] === UPLOAD_ERR_OK) {
            $newThumbName = generateUniqueFilename($thumbnail['name'], 'thumb');
            if (!saveUploadedFile($thumbnail, $thumbnailsDir, $newThumbName)) {
                echo json_encode(['success' => false, 'error' => 'Failed to save thumbnail. Please try again.']);
                exit;
            }
            $oldThumbPath = dirname(__DIR__, 2) . '/' . $existing['thumbnail_path'];
            if ($existing['thumbnail_path']) deleteFileIfExists($oldThumbPath);
            $newThumbPath = 'uploads/thumbnails/' . $newThumbName;
        }

        $stmt = $db->prepare("UPDATE `videos` SET `video_title` = :video_title, `artist_id` = :artist_id, `album_id` = :album_id, `year_id` = :year_id, `genre_id` = :genre_id, `language_id` = :language_id, `description` = :description, `video_path` = :video_path, `thumbnail_path` = :thumbnail_path, `duration` = :duration, `status` = :status, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([
            ':video_title'    => $title,
            ':artist_id'      => $artistId,
            ':album_id'       => $albumId > 0 ? $albumId : null,
            ':year_id'        => $yearId,
            ':genre_id'       => $genreId > 0 ? $genreId : null,
            ':language_id'    => $languageId > 0 ? $languageId : null,
            ':description'    => $description,
            ':video_path'     => $newVideoPath,
            ':thumbnail_path' => $newThumbPath,
            ':duration'       => $duration,
            ':status'         => $status,
            ':id'             => $id,
        ]);

        $logAction = ($status !== $existing['status']) ? 'status_changed' : 'updated';
        logAdminActivity($db, $logAction, 'video', $title, $id);

        $stmt = $db->prepare($joinSql . " WHERE v.`id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'message' => 'Video updated successfully.',
            'record'  => buildVideoRecord($row),
        ]);
        exit;

    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid video record ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM `videos` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'Video record not found.']);
            exit;
        }

        // Delete associated files BEFORE database record
        if ($row['video_path']) {
            $videoPath = $row['video_path'];
            if (strpos($videoPath, 'uploads/videos/') === 0) {
                deleteFileIfExists(dirname(__DIR__, 2) . '/' . $videoPath);
            }
        }
        if ($row['thumbnail_path']) {
            $thumbPath = $row['thumbnail_path'];
            if (strpos($thumbPath, 'uploads/thumbnails/') === 0) {
                deleteFileIfExists(dirname(__DIR__, 2) . '/' . $thumbPath);
            }
        }

        $stmt = $db->prepare("DELETE FROM `videos` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        logAdminActivity($db, 'deleted', 'video', $row['video_title'], $id);

        echo json_encode([
            'success' => true,
            'message' => 'Video deleted successfully.',
            'record'  => ['id' => $id, 'video_title' => $row['video_title']],
        ]);
        exit;

    case 'list':
        $stmt = $db->query($joinSql . " ORDER BY v.`id` DESC");
        $rows = $stmt->fetchAll();
        $records = [];
        foreach ($rows as $row) {
            $records[] = buildVideoRecord($row);
        }

        echo json_encode([
            'success' => true,
            'records' => $records,
            'count'   => count($records),
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request.']);
        exit;
}
