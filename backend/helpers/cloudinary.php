<?php
/**
 * SOUND Group — Cloudinary Helper
 * Uses the official Cloudinary PHP SDK for uploads, deletions, and URL management.
 */

require_once __DIR__ . '/env.php';

// Load Composer autoload if available
$autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryHelper {
    private $cloudinary;
    private $cloudName;
    private $apiKey;
    private $apiSecret;
    private $configured;

    public function __construct() {
        $this->cloudName = env('CLOUDINARY_CLOUD_NAME', '');
        $this->apiKey    = env('CLOUDINARY_API_KEY', '');
        $this->apiSecret = env('CLOUDINARY_API_SECRET', '');
        $this->configured = $this->cloudName !== '' && $this->apiKey !== '' && $this->apiSecret !== '';

        if ($this->configured) {
            $config = new Configuration([
                'cloud' => [
                    'cloud_name' => $this->cloudName,
                    'api_key'    => $this->apiKey,
                    'api_secret' => $this->apiSecret,
                ],
            ]);
            $this->cloudinary = new Cloudinary($config);
        }
    }

    public function isConfigured() {
        return $this->configured;
    }

    public function getSdk() {
        return $this->cloudinary;
    }

    /**
     * Upload a file to Cloudinary using the official SDK.
     */
    public function upload($filePath, $folder = '', $publicId = null, $options = []) {
        if (!$this->configured) {
            throw new RuntimeException('Cloudinary is not configured.');
        }
        if (!file_exists($filePath)) {
            throw new RuntimeException('File not found: ' . $filePath);
        }

        $resourceType = $options['resource_type'] ?? $this->guessResourceType($filePath);

        $uploadOptions = [
            'folder'        => $folder,
            'resource_type' => $resourceType,
        ];
        if ($publicId) {
            $uploadOptions['public_id'] = $publicId;
        }
        if (isset($options['transformation'])) {
            $t = $options['transformation'];
            if (is_string($t)) {
                $parts = array_map('trim', explode(',', $t));
                $transformed = [];
                $crop = null;
                foreach ($parts as $p) {
                    if (preg_match('/^c_(\w+)$/', $p, $m)) { $crop = $m[1]; continue; }
                    if (preg_match('/^w_(\d+)$/', $p, $m)) { $transformed['width'] = (int)$m[1]; continue; }
                    if (preg_match('/^h_(\d+)$/', $p, $m)) { $transformed['height'] = (int)$m[1]; continue; }
                    if (preg_match('/^q_(\d+)$/', $p, $m)) { $transformed['quality'] = $m[1]; continue; }
                    if (preg_match('/^f_(\w+)$/', $p, $m)) { $transformed['fetch_format'] = $m[1]; continue; }
                }
                if ($crop) $transformed['crop'] = $crop;
                $t = $transformed;
            }
            $uploadOptions['transformation'] = $t;
        }

        $uploadApi = $this->cloudinary->uploadApi();
        $result = $uploadApi->upload($filePath, $uploadOptions);

        if (isset($result['secure_url'])) {
            return [
                'url'       => $result['secure_url'],
                'public_id' => $result['public_id'],
            ];
        }

        throw new RuntimeException('Cloudinary upload failed: No URL returned.');
    }

    /**
     * Delete a file from Cloudinary by public_id.
     */
    public function delete($publicId, $resourceType = 'video') {
        if (!$this->configured || !$publicId) {
            return false;
        }
        try {
            $uploadApi = $this->cloudinary->uploadApi();
            $uploadApi->destroy($publicId, ['resource_type' => $resourceType]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete a resource by extracting public_id from a Cloudinary URL.
     */
    public function deleteByUrl($url) {
        $publicId = $this->extractPublicId($url);
        if (!$publicId) {
            return false;
        }
        $resourceType = $this->guessResourceTypeFromUrl($url);
        return $this->delete($publicId, $resourceType);
    }

    /**
     * Extract the public_id from a Cloudinary URL.
     */
    public function extractPublicId($url) {
        if (!$url || strpos($url, 'cloudinary.com') === false) {
            return null;
        }
        if (preg_match('#/upload/(?:v\d+/)?(.+?)(?:\.\w+)?$#', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Check if a given path/URL is a Cloudinary URL.
     */
    public static function isCloudinaryUrl($path) {
        return $path && (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0)
            && strpos($path, 'cloudinary.com') !== false;
    }

    private function guessResourceType($filePath) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $videoExts = ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v', 'flv', 'wmv'];
        $audioExts = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'webm', 'm4a', 'wma', 'opus', 'aiff', 'ape'];
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff'];

        if (in_array($ext, $videoExts)) return 'video';
        if (in_array($ext, $audioExts)) return 'video';
        if (in_array($ext, $imageExts)) return 'image';
        return 'raw';
    }

    private function guessResourceTypeFromUrl($url) {
        if (preg_match('#/video/upload/#', $url)) return 'video';
        if (preg_match('#/image/upload/#', $url)) return 'image';
        if (preg_match('#/raw/upload/#', $url)) return 'raw';
        return 'video';
    }
}

function getCloudinary() {
    static $instance = null;
    if ($instance === null) {
        $instance = new CloudinaryHelper();
    }
    return $instance;
}
