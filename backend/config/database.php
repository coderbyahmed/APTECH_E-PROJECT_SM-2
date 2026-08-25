<?php
/**
 * SOUND Group — Database Configuration
 */

require_once __DIR__ . '/../helpers/env.php';

return [
    'host'     => env('DB_HOST', '127.0.0.1'),
    'port'     => (int) env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'sound_management'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset'  => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
