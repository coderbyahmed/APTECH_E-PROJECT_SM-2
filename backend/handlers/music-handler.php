<?php
/**
 * SOUND Group — Music Management AJAX Handler
 *
 * Single endpoint for all music CRUD operations:
 *   add, edit, delete, list
 *
 * Always returns JSON responses.
 * Handles file uploads for music files and cover images.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/activity-log.php';
require_once __DIR__ . '/../helpers/media-duration.php';

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

$action = trim($_POST['action'] ?? '');

$db = getDb();

$uploadDir     = dirname(__DIR__, 2) . '/uploads/';
$musicDir      = $uploadDir . 'music/';
$coversDir     = $uploadDir . 'covers/';
$musicDirWeb   = '/Aptech_E_Project_02/sound_management/uploads/music/';
$coversDirWeb  = '/Aptech_E_Project_02/sound_management/uploads/covers/';

$allowedAudioMimes = [
    'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/wave', 'audio/x-wav',
    'audio/flac', 'audio/x-flac', 'audio/aac', 'audio/x-aac', 'audio/mp4',
    'audio/ogg', 'audio/webm', 'audio/x-m4a', 'audio/m4a',
    'audio/x-ms-wma', 'audio/x-mpeg', 'audio/mp2',
    'audio/opus', 'audio/aiff', 'audio/x-aiff', 'audio/x-ape',
];
$allowedAudioExts = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'webm', 'm4a', 'wma', 'mpeg', 'mp2', 'opus', 'aiff', 'ape'];
$allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp'];
$allowedImageExts = ['jpg', 'jpeg', 'png', 'webp'];

$maxMusicSize = 50 * 1024 * 1024;
$maxImageSize = 5 * 1024 * 1024;

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

function buildMusicRecord($row) {
    return [
        'id'           => (int) $row['id'],
        'song_title'   => $row['song_title'],
        'artist_id'    => $row['artist_id'] !== null ? (int) $row['artist_id'] : null,
        'artist_name'  => $row['artist_name'] ?? '',
        'album_id'     => $row['album_id'] !== null ? (int) $row['album_id'] : null,
        'album_name'   => $row['album_name'] ?? '',
        'year_id'      => $row['year_id'] !== null ? (int) $row['year_id'] : null,
        'year_name'    => $row['year_name'] ?? '',
        'genre_id'     => $row['genre_id'] !== null ? (int) $row['genre_id'] : null,
        'genre_name'   => $row['genre_name'] ?? '',
        'language_id'  => $row['language_id'] !== null ? (int) $row['language_id'] : null,
        'language_name'=> $row['language_name'] ?? '',
        'description'  => $row['description'] ?? '',
        'music_file'   => $row['music_file'] ?? '',
        'cover_image'  => $row['cover_image'] ?? '',
        'duration'     => $row['duration'] ?? '',
        'status'       => $row['status'],
        'created_at'   => $row['created_at'] ?? '',
        'updated_at'   => $row['updated_at'] ?? '',
    ];
}

$joinSql = "SELECT m.*,
    a.`name` AS `artist_name`,
    al.`name` AS `album_name`,
    y.`name` AS `year_name`,
    g.`name` AS `genre_name`,
    l.`name` AS `language_name`
FROM `music` m
LEFT JOIN `artists` a ON a.`id` = m.`artist_id`
LEFT JOIN `albums` al ON al.`id` = m.`album_id`
LEFT JOIN `air` y ON y.`id` = m.`year_id`
LEFT JOIN `genres` g ON g.`id` = m.`genre_id`
LEFT JOIN `languages` l ON l.`id` = m.`language_id`";

switch ($action) {

    case 'add':
        $title       = trim($_POST['song_title'] ?? '');
        $artistId    = (int) ($_POST['artist_id'] ?? 0);
        $albumId     = (int) ($_POST['album_id'] ?? 0);
        $yearId      = (int) ($_POST['year_id'] ?? 0);
        $genreId     = (int) ($_POST['genre_id'] ?? 0);
        $languageId  = (int) ($_POST['language_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status      = trim($_POST['status'] ?? 'active');

        if ($title === '') {
            echo json_encode(['success' => false, 'error' => 'Song title is required.']);
            exit;
        }
        if (strlen($title) > 255) {
            echo json_encode(['success' => false, 'error' => 'Song title must not exceed 255 characters.']);
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

        $musicFile  = $_FILES['music_file'] ?? null;
        $coverImage = $_FILES['cover_image'] ?? null;

        if (!$musicFile || !is_array($musicFile) || $musicFile['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => 'Music file is required.']);
            exit;
        }
        $vMusic = validateFile($musicFile, $allowedAudioMimes, $allowedAudioExts, $maxMusicSize, 'music file');
        if (!$vMusic['valid']) {
            echo json_encode(['success' => false, 'error' => $vMusic['error']]);
            exit;
        }
        $vCover = validateFile($coverImage, $allowedImageMimes, $allowedImageExts, $maxImageSize, 'cover image');
        if (!$vCover['skip'] && !$vCover['valid']) {
            echo json_encode(['success' => false, 'error' => $vCover['error']]);
            exit;
        }

        $savedMusicFile  = '';
        $savedCoverImage = '';

        $newMusicName = generateUniqueFilename($musicFile['name'], 'music');
        if (!saveUploadedFile($musicFile, $musicDir, $newMusicName)) {
            echo json_encode(['success' => false, 'error' => 'Failed to save music file. Please try again.']);
            exit;
        }
        $savedMusicFile = 'uploads/music/' . $newMusicName;

        $duration = getMediaDuration($musicDir . $newMusicName);

        $postDuration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
        if ($postDuration !== '') {
            $duration = $postDuration;
        }

        if ($coverImage && $coverImage['error'] !== UPLOAD_ERR_NO_FILE) {
            $newCoverName = generateUniqueFilename($coverImage['name'], 'cover');
            if (!saveUploadedFile($coverImage, $coversDir, $newCoverName)) {
                if ($savedMusicFile) deleteFileIfExists(dirname(__DIR__, 2) . '/' . $savedMusicFile);
                echo json_encode(['success' => false, 'error' => 'Failed to save cover image. Please try again.']);
                exit;
            }
            $savedCoverImage = 'uploads/covers/' . $newCoverName;
        }

        $stmt = $db->prepare("INSERT INTO `music` (`song_title`, `artist_id`, `album_id`, `year_id`, `genre_id`, `language_id`, `description`, `music_file`, `cover_image`, `duration`, `status`, `created_at`, `updated_at`) VALUES (:song_title, :artist_id, :album_id, :year_id, :genre_id, :language_id, :description, :music_file, :cover_image, :duration, :status, NOW(), NOW())");
        $stmt->execute([
            ':song_title'   => $title,
            ':artist_id'    => $artistId,
            ':album_id'     => $albumId > 0 ? $albumId : null,
            ':year_id'      => $yearId,
            ':genre_id'     => $genreId > 0 ? $genreId : null,
            ':language_id'  => $languageId > 0 ? $languageId : null,
            ':description'  => $description,
            ':music_file'   => $savedMusicFile,
            ':cover_image'  => $savedCoverImage,
            ':duration'     => $duration,
            ':status'       => $status,
        ]);

        $newId = (int) $db->lastInsertId();

        logAdminActivity($db, 'created', 'music', $title, $newId);

        $stmt = $db->prepare($joinSql . " WHERE m.`id` = :id");
        $stmt->execute([':id' => $newId]);
        $row = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'message' => 'Music added successfully.',
            'record'  => buildMusicRecord($row),
        ]);
        exit;

    case 'edit':
        $id          = (int) ($_POST['id'] ?? 0);
        $title       = trim($_POST['song_title'] ?? '');
        $artistId    = (int) ($_POST['artist_id'] ?? 0);
        $albumId     = (int) ($_POST['album_id'] ?? 0);
        $yearId      = (int) ($_POST['year_id'] ?? 0);
        $genreId     = (int) ($_POST['genre_id'] ?? 0);
        $languageId  = (int) ($_POST['language_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status      = trim($_POST['status'] ?? 'active');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid music record ID.']);
            exit;
        }
        if ($title === '') {
            echo json_encode(['success' => false, 'error' => 'Song title is required.']);
            exit;
        }
        if (strlen($title) > 255) {
            echo json_encode(['success' => false, 'error' => 'Song title must not exceed 255 characters.']);
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

        $stmt = $db->prepare("SELECT * FROM `music` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $existing = $stmt->fetch();
        if (!$existing) {
            echo json_encode(['success' => false, 'error' => 'Music record not found.']);
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

        $musicFile  = $_FILES['music_file'] ?? null;
        $coverImage = $_FILES['cover_image'] ?? null;

        $vMusic = validateFile($musicFile, $allowedAudioMimes, $allowedAudioExts, $maxMusicSize, 'music file');
        if (!$vMusic['skip'] && !$vMusic['valid']) {
            echo json_encode(['success' => false, 'error' => $vMusic['error']]);
            exit;
        }
        $vCover = validateFile($coverImage, $allowedImageMimes, $allowedImageExts, $maxImageSize, 'cover image');
        if (!$vCover['skip'] && !$vCover['valid']) {
            echo json_encode(['success' => false, 'error' => $vCover['error']]);
            exit;
        }

        $newMusicFile  = $existing['music_file'];
        $newCoverImage = $existing['cover_image'];
        $duration = $existing['duration'];

        $postDuration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
        $hasPostDuration = ($postDuration !== '');

        if (!$vMusic['skip'] && $musicFile && $musicFile['error'] === UPLOAD_ERR_OK) {
            $newMusicName = generateUniqueFilename($musicFile['name'], 'music');
            if (!saveUploadedFile($musicFile, $musicDir, $newMusicName)) {
                echo json_encode(['success' => false, 'error' => 'Failed to save music file. Please try again.']);
                exit;
            }
            $oldMusicPath = dirname(__DIR__, 2) . '/' . $existing['music_file'];
            if ($existing['music_file']) deleteFileIfExists($oldMusicPath);
            $newMusicFile = 'uploads/music/' . $newMusicName;
            if (!$hasPostDuration) {
                $duration = getMediaDuration($musicDir . $newMusicName);
            }
        }

        if ($hasPostDuration) {
            $duration = $postDuration;
        }

        if (!$vCover['skip'] && $coverImage && $coverImage['error'] === UPLOAD_ERR_OK) {
            $newCoverName = generateUniqueFilename($coverImage['name'], 'cover');
            if (!saveUploadedFile($coverImage, $coversDir, $newCoverName)) {
                echo json_encode(['success' => false, 'error' => 'Failed to save cover image. Please try again.']);
                exit;
            }
            $oldCoverPath = dirname(__DIR__, 2) . '/' . $existing['cover_image'];
            if ($existing['cover_image']) deleteFileIfExists($oldCoverPath);
            $newCoverImage = 'uploads/covers/' . $newCoverName;
        }

        $stmt = $db->prepare("UPDATE `music` SET `song_title` = :song_title, `artist_id` = :artist_id, `album_id` = :album_id, `year_id` = :year_id, `genre_id` = :genre_id, `language_id` = :language_id, `description` = :description, `music_file` = :music_file, `cover_image` = :cover_image, `duration` = :duration, `status` = :status, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([
            ':song_title'   => $title,
            ':artist_id'    => $artistId,
            ':album_id'     => $albumId > 0 ? $albumId : null,
            ':year_id'      => $yearId,
            ':genre_id'     => $genreId > 0 ? $genreId : null,
            ':language_id'  => $languageId > 0 ? $languageId : null,
            ':description'  => $description,
            ':music_file'   => $newMusicFile,
            ':cover_image'  => $newCoverImage,
            ':duration'     => $duration,
            ':status'       => $status,
            ':id'           => $id,
        ]);

        $logAction = ($status !== $existing['status']) ? 'status_changed' : 'updated';
        logAdminActivity($db, $logAction, 'music', $title, $id);

        $stmt = $db->prepare($joinSql . " WHERE m.`id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'message' => 'Music updated successfully.',
            'record'  => buildMusicRecord($row),
        ]);
        exit;

    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid music record ID.']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM `music` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'Music record not found.']);
            exit;
        }

        // Delete associated files BEFORE database record
        if ($row['music_file']) {
            $musicFile = $row['music_file'];
            if (strpos($musicFile, 'uploads/music/') === 0) {
                deleteFileIfExists(dirname(__DIR__, 2) . '/' . $musicFile);
            }
        }
        if ($row['cover_image']) {
            $coverImg = $row['cover_image'];
            if (strpos($coverImg, 'uploads/covers/') === 0) {
                deleteFileIfExists(dirname(__DIR__, 2) . '/' . $coverImg);
            }
        }

        $stmt = $db->prepare("DELETE FROM `music` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        logAdminActivity($db, 'deleted', 'music', $row['song_title'], $id);

        echo json_encode([
            'success' => true,
            'message' => 'Music deleted successfully.',
            'record'  => ['id' => $id, 'song_title' => $row['song_title']],
        ]);
        exit;

    case 'list':
        $stmt = $db->query($joinSql . " ORDER BY m.`id` DESC");
        $rows = $stmt->fetchAll();
        $records = [];
        foreach ($rows as $row) {
            $records[] = buildMusicRecord($row);
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
