<?php
/**
 * SOUND Group — Website Settings Helper
 *
 * Centralized loader for site-wide configuration.
 * Fetches settings from the `website_settings` table (single-row).
 * Uses static cache so the DB is queried only once per request.
 *
 * Usage:
 *   $settings = getWebsiteSettings();
 *   echo $settings['website_name'];
 *
 * Convenience accessors:
 *   echo ws('website_name');
 *   echo ws('contact_email');
 */

require_once __DIR__ . '/db.php';

/**
 * Return all website settings as an associative array.
 * Static cache prevents multiple DB hits on the same request.
 */
function getWebsiteSettings() {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $defaults = [
        'website_name'      => 'SOUND Group',
        'site_logo'         => null,
        'contact_email'     => 'info@soundgroup.com',
        'contact_phone'     => '',
        'contact_address'   => '',
        'facebook_url'      => '',
        'tiktok_url'        => '',
        'linkedin_url'      => '',
        'github_url'        => '',
        'footer_description'=> '',
        'copyright_text'    => '',
    ];

    try {
        $db   = getDb();
        $stmt = $db->query("SELECT * FROM `website_settings` LIMIT 1");
        $row  = $stmt->fetch();

        if (!$row) {
            $cache = $defaults;
            return $cache;
        }

        $cache = [];
        foreach ($defaults as $key => $fallback) {
            $cache[$key] = isset($row[$key]) && $row[$key] !== null ? $row[$key] : $fallback;
        }
    } catch (Exception $e) {
        $cache = $defaults;
    }

    return $cache;
}

/**
 * Ensure a URL has a protocol prefix.
 */
function ensureProtocol($url) {
    $url = trim($url);
    if ($url !== '' && $url !== '#' && !preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    return $url;
}

/**
 * Shorthand accessor — returns a single setting by key.
 * Falls back to $default if the key is missing.
 */
function ws($key, $default = null) {
    $all = getWebsiteSettings();
    return isset($all[$key]) && $all[$key] !== null ? $all[$key] : $default;
}
