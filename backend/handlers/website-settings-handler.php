<?php
/**
 * SOUND Group — Website Settings Handler
 *
 * Handles saving/updating all website settings fields.
 * Only accessible to authenticated admins.
 *
 * Supports:
 *   - JSON POST (field/value pairs)
 *   - FormData POST with file upload (for site_logo)
 *
 * Always returns JSON responses.
 */

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
    exit;
}

$db = getDb();

/* -----------------------------------------------------------
   Collect field values — works for both JSON and FormData
   ----------------------------------------------------------- */
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isMultipart = stripos($contentType, 'multipart/form-data') !== false;

$websiteName   = trim($_POST['website_name'] ?? '');
$contactEmail  = trim($_POST['contact_email'] ?? '');
$contactPhone  = trim($_POST['contact_phone'] ?? '');
$contactAddr   = trim($_POST['contact_address'] ?? '');
$facebookUrl   = trim($_POST['facebook_url'] ?? '');
$tiktokUrl     = trim($_POST['tiktok_url'] ?? '');
$linkedinUrl   = trim($_POST['linkedin_url'] ?? '');
$githubUrl     = trim($_POST['github_url'] ?? '');
$footerDesc    = trim($_POST['footer_description'] ?? '');
$copyrightText = trim($_POST['copyright_text'] ?? '');

/* -----------------------------------------------------------
   Validation
   ----------------------------------------------------------- */
if ($websiteName === '') {
    echo json_encode(['success' => false, 'error' => 'Website name is required.']);
    exit;
}

if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'A valid email address is required.']);
    exit;
}

/* -----------------------------------------------------------
   Handle logo upload (if present)
   ----------------------------------------------------------- */
$logoPath = null;
$removeLogo = isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1';
$uploadDir    = dirname(__DIR__, 2) . '/uploads/logos/';
$uploadDirWeb = '/Aptech_E_Project_02/sound_management/uploads/logos/';

if ($removeLogo && empty($_FILES['site_logo']['name'])) {
    // Remove existing logo
    $oldRow = $db->query("SELECT site_logo FROM website_settings LIMIT 1")->fetch();
    if ($oldRow && !empty($oldRow['site_logo'])) {
        $oldFile = dirname(__DIR__, 2) . '/' . ltrim($oldRow['site_logo'], '/');
        if (file_exists($oldFile)) {
            @unlink($oldFile);
        }
    }
    $logoPath = '';
} elseif (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
    $file     = $_FILES['site_logo'];
    $origName = basename($file['name']);
    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowed  = ['svg', 'png', 'jpg', 'jpeg', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        echo json_encode(['success' => false, 'error' => 'Invalid logo file type. Allowed: SVG, PNG, JPG, WebP.']);
        exit;
    }

    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'error' => 'Logo file too large. Max 5 MB.']);
        exit;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeExt     = preg_replace('/[^a-z0-9]/', '', $ext);
    $newFilename = 'logo_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $safeExt;
    $dest        = $uploadDir . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save logo file.']);
        exit;
    }

    // Remove old logo file if it exists
    $oldRow = $db->query("SELECT site_logo FROM website_settings LIMIT 1")->fetch();
    if ($oldRow && !empty($oldRow['site_logo'])) {
        $oldFile = dirname(__DIR__, 2) . '/' . ltrim($oldRow['site_logo'], '/');
        if (file_exists($oldFile)) {
            @unlink($oldFile);
        }
    }

    $logoPath = $uploadDirWeb . $newFilename;
}

/* -----------------------------------------------------------
   Save to database
   ----------------------------------------------------------- */
try {
    // Auto-create table if missing
    $db->exec("CREATE TABLE IF NOT EXISTS `website_settings` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `website_name` VARCHAR(255) NOT NULL DEFAULT 'SOUND Group',
        `site_logo` VARCHAR(500) DEFAULT NULL,
        `contact_email` VARCHAR(255) DEFAULT NULL,
        `contact_phone` VARCHAR(50) DEFAULT NULL,
        `contact_address` TEXT DEFAULT NULL,
        `facebook_url` VARCHAR(500) DEFAULT NULL,
        `tiktok_url` VARCHAR(500) DEFAULT NULL,
        `linkedin_url` VARCHAR(500) DEFAULT NULL,
        `github_url` VARCHAR(500) DEFAULT NULL,
        `footer_description` TEXT DEFAULT NULL,
        `copyright_text` VARCHAR(500) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Seed default row if empty
    $count = $db->query("SELECT COUNT(*) FROM website_settings")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `website_settings`
            (`website_name`,`contact_email`,`contact_phone`,`contact_address`,
             `facebook_url`,`tiktok_url`,`linkedin_url`,`github_url`,
             `footer_description`,`copyright_text`,`created_at`,`updated_at`)
            VALUES
            ('SOUND Group','info@soundgroup.com','+92 317 849 7732','Pakistan',
             'https://www.facebook.com/soundgroup','https://www.tiktok.com/@soundgroup',
             'https://www.linkedin.com/company/soundgroup','https://github.com/soundgroup',
             'Discover music, videos, artists and more — all in one place. Your ultimate destination for streaming and exploring sound.',
             '&copy; 2026 SOUND Group. All rights reserved.',NOW(),NOW())");
    }

    // Fetch current row
    $current = $db->query("SELECT * FROM website_settings LIMIT 1")->fetch();

    if ($current) {
        // Determine site_logo value for DB
        $dbLogo = null;
        if ($logoPath !== null) {
            // New upload or explicit removal
            $dbLogo = $logoPath !== '' ? $logoPath : null;
        }

        if ($dbLogo !== null || $logoPath === '') {
            // Update including site_logo
            $stmt = $db->prepare("UPDATE `website_settings` SET
                `website_name`      = :website_name,
                `site_logo`         = :site_logo,
                `contact_email`     = :contact_email,
                `contact_phone`     = :contact_phone,
                `contact_address`   = :contact_address,
                `facebook_url`      = :facebook_url,
                `tiktok_url`        = :tiktok_url,
                `linkedin_url`      = :linkedin_url,
                `github_url`        = :github_url,
                `footer_description`= :footer_description,
                `copyright_text`    = :copyright_text,
                `updated_at`        = NOW()
                WHERE `id` = :id");
            $stmt->execute([
                ':website_name'      => $websiteName,
                ':site_logo'         => $dbLogo,
                ':contact_email'     => $contactEmail,
                ':contact_phone'     => $contactPhone,
                ':contact_address'   => $contactAddr,
                ':facebook_url'      => $facebookUrl,
                ':tiktok_url'        => $tiktokUrl,
                ':linkedin_url'      => $linkedinUrl,
                ':github_url'        => $githubUrl,
                ':footer_description'=> $footerDesc,
                ':copyright_text'    => $copyrightText,
                ':id'                => $current['id'],
            ]);
        } else {
            // Update without touching site_logo
            $stmt = $db->prepare("UPDATE `website_settings` SET
                `website_name`      = :website_name,
                `contact_email`     = :contact_email,
                `contact_phone`     = :contact_phone,
                `contact_address`   = :contact_address,
                `facebook_url`      = :facebook_url,
                `tiktok_url`        = :tiktok_url,
                `linkedin_url`      = :linkedin_url,
                `github_url`        = :github_url,
                `footer_description`= :footer_description,
                `copyright_text`    = :copyright_text,
                `updated_at`        = NOW()
                WHERE `id` = :id");
            $stmt->execute([
                ':website_name'      => $websiteName,
                ':contact_email'     => $contactEmail,
                ':contact_phone'     => $contactPhone,
                ':contact_address'   => $contactAddr,
                ':facebook_url'      => $facebookUrl,
                ':tiktok_url'        => $tiktokUrl,
                ':linkedin_url'      => $linkedinUrl,
                ':github_url'        => $githubUrl,
                ':footer_description'=> $footerDesc,
                ':copyright_text'    => $copyrightText,
                ':id'                => $current['id'],
            ]);
        }

        $finalLogo = $dbLogo;
    } else {
        // Insert new row (shouldn't normally happen)
        $dbLogo = $logoPath !== '' ? $logoPath : null;
        $stmt = $db->prepare("INSERT INTO `website_settings`
            (`website_name`,`site_logo`,`contact_email`,`contact_phone`,`contact_address`,
             `facebook_url`,`tiktok_url`,`linkedin_url`,`github_url`,
             `footer_description`,`copyright_text`,`created_at`,`updated_at`)
            VALUES
            (:website_name,:site_logo,:contact_email,:contact_phone,:contact_address,
             :facebook_url,:tiktok_url,:linkedin_url,:github_url,
             :footer_description,:copyright_text,NOW(),NOW())");
        $stmt->execute([
            ':website_name'      => $websiteName,
            ':site_logo'         => $dbLogo,
            ':contact_email'     => $contactEmail,
            ':contact_phone'     => $contactPhone,
            ':contact_address'   => $contactAddr,
            ':facebook_url'      => $facebookUrl,
            ':tiktok_url'        => $tiktokUrl,
            ':linkedin_url'      => $linkedinUrl,
            ':github_url'        => $githubUrl,
            ':footer_description'=> $footerDesc,
            ':copyright_text'    => $copyrightText,
        ]);
        $finalLogo = $dbLogo;
    }

    echo json_encode([
        'success'    => true,
        'message'    => 'Website settings updated successfully.',
        'settings'   => [
            'website_name'      => $websiteName,
            'site_logo'         => $finalLogo,
            'contact_email'     => $contactEmail,
            'contact_phone'     => $contactPhone,
            'contact_address'   => $contactAddr,
            'facebook_url'      => $facebookUrl,
            'tiktok_url'        => $tiktokUrl,
            'linkedin_url'      => $linkedinUrl,
            'github_url'        => $githubUrl,
            'footer_description'=> $footerDesc,
            'copyright_text'    => $copyrightText,
        ],
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save settings.']);
    exit;
}
