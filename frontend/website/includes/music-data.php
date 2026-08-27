<?php
/**
 * SOUND Group — Website Music Data Helper
 * Fetches music records from database with JOINs
 */

require_once dirname(__DIR__, 3) . '/backend/includes/db.php';

$musicBaseUrl = '/Aptech_E_Project_02/sound_management';

function wgGetAllMusic($limit = 0, $status = 'active') {
    $db = getDb();
    $sql = "SELECT m.id, m.song_title, m.description, m.music_file, m.cover_image, m.status, m.created_at, m.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
            FROM music m
            LEFT JOIN artists a ON a.id = m.artist_id
            LEFT JOIN albums al ON al.id = m.album_id
            LEFT JOIN air y ON y.id = m.year_id
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN languages l ON l.id = m.language_id";

    $params = [];
    if ($status === 'published') {
        $sql .= " WHERE m.status != 'draft'";
    } elseif ($status !== 'all') {
        $sql .= " WHERE m.status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY m.created_at DESC";

    if ($limit > 0) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function wgGetMusicById($id, $publicOnly = false) {
    $db = getDb();
    $sql = "SELECT m.id, m.song_title, m.description, m.music_file, m.cover_image, m.status, m.created_at, m.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
        FROM music m
        LEFT JOIN artists a ON a.id = m.artist_id
        LEFT JOIN albums al ON al.id = m.album_id
        LEFT JOIN air y ON y.id = m.year_id
        LEFT JOIN genres g ON g.id = m.genre_id
        LEFT JOIN languages l ON l.id = m.language_id
        WHERE m.id = :id";
    if ($publicOnly) {
        $sql .= " AND m.status != 'draft'";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => (int)$id]);
    return $stmt->fetch() ?: null;
}

function wgGetMusicByArtist($artistName, $excludeId = 0, $limit = 6) {
    $db = getDb();
    $sql = "SELECT m.id, m.song_title, m.description, m.music_file, m.cover_image, m.status, m.created_at, m.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
            FROM music m
            LEFT JOIN artists a ON a.id = m.artist_id
            LEFT JOIN albums al ON al.id = m.album_id
            LEFT JOIN air y ON y.id = m.year_id
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN languages l ON l.id = m.language_id
            WHERE m.status = 'active'";
    $params = [];

    if ($artistName !== '') {
        $sql .= " AND a.name = :artist";
        $params[':artist'] = $artistName;
    }
    if ($excludeId > 0) {
        $sql .= " AND m.id != :exclude_id";
        $params[':exclude_id'] = (int)$excludeId;
    }

    $sql .= " ORDER BY m.created_at DESC LIMIT " . (int)$limit;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function wgResolveCoverUrl($coverImage, $baseUrl) {
    if (!$coverImage) return '';
    $path = ltrim($coverImage, '/');
    return $baseUrl . '/' . $path;
}

function wgResolveMusicUrl($musicFile, $baseUrl) {
    if (!$musicFile) return '';
    $path = ltrim($musicFile, '/');
    return $baseUrl . '/' . $path;
}
