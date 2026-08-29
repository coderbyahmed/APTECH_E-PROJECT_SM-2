<?php
/**
 * SOUND Group — Application Configuration
 */

require_once __DIR__ . '/../helpers/env.php';

return [
    'app_name'  => env('APP_NAME', 'SOUND Group'),
    'app_url'   => env('APP_URL', ''),
    'app_debug' => env('APP_DEBUG', 'true') === 'true',

    // Mail / SMTP
    'mail' => [
        'host'         => env('MAIL_HOST', 'smtp.gmail.com'),
        'port'         => (int) env('MAIL_PORT', '587'),
        'username'     => env('MAIL_USERNAME', ''),
        'password'     => env('MAIL_PASSWORD', ''),
        'encryption'   => env('MAIL_ENCRYPTION', 'tls'),
        'from_address' => env('MAIL_FROM_ADDRESS', ''),
        'from_name'    => env('MAIL_FROM_NAME', 'SOUND Group'),
    ],

    // OTP Settings
    'otp' => [
        'length'     => (int) env('OTP_LENGTH', '6'),
        'expires_in' => (int) env('OTP_EXPIRES_IN', '180'),
    ],
];
