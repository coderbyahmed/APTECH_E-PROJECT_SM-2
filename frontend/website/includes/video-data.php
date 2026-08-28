<?php
/**
 * SOUND Group — Website Video Data Helper
 * Fetches video records from database with JOINs
 */

require_once dirname(__DIR__, 3) . '/backend/includes/db.php';

function wgGetAllVideos($limit = 0, $status = 'active') {
    $db = getDb();
    $sql = "SELECT v.id, v.video_title, v.description, v.video_path, v.thumbnail_path, v.status, v.created_at, v.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON a.id = v.artist_id
            LEFT JOIN albums al ON al.id = v.album_id
            LEFT JOIN air y ON y.id = v.year_id
            LEFT JOIN genres g ON g.id = v.genre_id
            LEFT JOIN languages l ON l.id = v.language_id";

    $params = [];
    if ($status === 'published') {
        $sql .= " WHERE v.status != 'draft'";
    } elseif ($status !== 'all') {
        $sql .= " WHERE v.status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY v.created_at DESC, v.id DESC";

    if ($limit > 0) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function wgGetVideoById($id, $publicOnly = false) {
    $db = getDb();
    $sql = "SELECT v.id, v.video_title, v.description, v.video_path, v.thumbnail_path, v.status, v.created_at, v.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
        FROM videos v
        LEFT JOIN artists a ON a.id = v.artist_id
        LEFT JOIN albums al ON al.id = v.album_id
        LEFT JOIN air y ON y.id = v.year_id
        LEFT JOIN genres g ON g.id = v.genre_id
        LEFT JOIN languages l ON l.id = v.language_id
        WHERE v.id = :id";
    if ($publicOnly) {
        $sql .= " AND v.status != 'draft'";
    }
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => (int)$id]);
    return $stmt->fetch() ?: null;
}

function wgGetVideosByArtist($artistName, $excludeId = 0, $limit = 6) {
    $db = getDb();
    $sql = "SELECT v.id, v.video_title, v.description, v.video_path, v.thumbnail_path, v.status, v.created_at, v.updated_at,
                   a.name AS artist_name,
                   al.name AS album_name,
                   y.name AS year_name,
                   g.name AS genre_name,
                   l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON a.id = v.artist_id
            LEFT JOIN albums al ON al.id = v.album_id
            LEFT JOIN air y ON y.id = v.year_id
            LEFT JOIN genres g ON g.id = v.genre_id
            LEFT JOIN languages l ON l.id = v.language_id
            WHERE v.status = 'active'";
    $params = [];

    if ($artistName !== '') {
        $sql .= " AND a.name = :artist";
        $params[':artist'] = $artistName;
    }
    if ($excludeId > 0) {
        $sql .= " AND v.id != :exclude_id";
        $params[':exclude_id'] = (int)$excludeId;
    }

    $sql .= " ORDER BY v.created_at DESC, v.id DESC LIMIT " . (int)$limit;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function wgResolveVideoUrl($videoPath, $baseUrl) {
    if (!$videoPath) return '';
    $path = ltrim($videoPath, '/');
    return $baseUrl . '/' . $path;
}

function wgResolveThumbnailUrl($thumbnailPath, $baseUrl) {
    if (!$thumbnailPath) return '';
    $path = ltrim($thumbnailPath, '/');
    return $baseUrl . '/' . $path;
}
