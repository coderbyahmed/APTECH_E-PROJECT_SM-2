<?php
/**
 * SOUND Group — Music Download Handler
 * Serves music files as MP3. Converts non-MP3 formats via ffmpeg.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/user-auth.php';

header('X-Content-Type-Options: nosniff');

if (!isUserLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Login required to download music.']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid music ID.']);
    exit;
}

$db = getDb();
$stmt = $db->prepare("SELECT id, song_title, music_file, status FROM music WHERE id = :id");
$stmt->execute([':id' => $id]);
$track = $stmt->fetch();

if (!$track || $track['status'] === 'inactive') {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Music not found.']);
    exit;
}

$relativePath = $track['music_file'];
if (!$relativePath) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Audio file not available.']);
    exit;
}

$projectRoot = dirname(__DIR__, 2);
$sourceFile = $projectRoot . '/' . ltrim($relativePath, '/');

if (!file_exists($sourceFile) || !is_readable($sourceFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Audio file not found on server.']);
    exit;
}

$ext = strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION));
$safeTitle = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $track['song_title']);
$safeTitle = trim(preg_replace('/\s+/', '_', $safeTitle), '_');
if (empty($safeTitle)) $safeTitle = 'music';

if ($ext === 'mp3') {
    header('Content-Type: audio/mpeg');
    header('Content-Disposition: attachment; filename="' . $safeTitle . '.mp3"');
    header('Content-Length: ' . filesize($sourceFile));
    header('Cache-Control: no-cache, must-revalidate');
    readfile($sourceFile);
    exit;
}

$ffmpegPath = $projectRoot . '/backend/tools/ffmpeg.exe';
if (!file_exists($ffmpegPath)) {
    header('Content-Type: audio/mpeg');
    header('Content-Disposition: attachment; filename="' . $safeTitle . '.mp3"');
    header('Content-Length: ' . filesize($sourceFile));
    header('Cache-Control: no-cache, must-revalidate');
    readfile($sourceFile);
    exit;
}

$tmpDir = sys_get_temp_dir() . '/sound_dl_' . bin2hex(random_bytes(8));
mkdir($tmpDir, 0700);
$tmpFile = $tmpDir . '/' . $safeTitle . '.mp3';

$cmd = '"' . $ffmpegPath . '" -y -i "' . $sourceFile . '" -vn -acodec libmp3lame -ab 192k -ar 44100 "' . $tmpFile . '" 2>&1';
$descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
$process = proc_open($cmd, $descriptors, $pipes);

if (is_resource($process)) {
    fclose($pipes[0]);
    stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);
} else {
    $exitCode = -1;
}

if ($exitCode === 0 && file_exists($tmpFile) && filesize($tmpFile) > 0) {
    header('Content-Type: audio/mpeg');
    header('Content-Disposition: attachment; filename="' . $safeTitle . '.mp3"');
    header('Content-Length: ' . filesize($tmpFile));
    header('Cache-Control: no-cache, must-revalidate');
    readfile($tmpFile);
    @unlink($tmpFile);
    @rmdir($tmpDir);
    exit;
}

@unlink($tmpFile);
@rmdir($tmpDir);

header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="' . $safeTitle . '.mp3"');
header('Content-Length: ' . filesize($sourceFile));
header('Cache-Control: no-cache, must-revalidate');
readfile($sourceFile);
exit;
