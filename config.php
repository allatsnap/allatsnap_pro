<?php
// InfinityFree-compatible database configuration (supports remote MySQL host)
define('DB_HOST', 'sqlXXX.epizy.com'); // Change to your MySQL host
define('DB_USER', 'epiz_xxxxxxxx');     // Change to your MySQL username
define('DB_PASS', 'your_mysql_password');
define('DB_NAME', 'epiz_xxxxxxxx_db');
define('DB_PORT', 3306);

// Site URL (without trailing slash), used for callback URLs and token signing context
define('BASE_URL', 'https://yourdomain.com');

// Cloudflare Turnstile keys
define('TURNSTILE_SITE_KEY', 'YOUR_TURNSTILE_SITE_KEY');
define('TURNSTILE_SECRET_KEY', 'YOUR_TURNSTILE_SECRET_KEY');

// Shortlink target. Use {TOKEN} placeholder.
// Example: https://short.example.com/go?token={TOKEN}
define('SHORTLINK_URL', BASE_URL . '/watch.php?token={TOKEN}');

// Token signature secret (change this in production)
define('TOKEN_SECRET', 'change_this_to_a_long_random_secret');

// Admin session settings
define('SESSION_NAME', 'allatsnap_session');

date_default_timezone_set('UTC');

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Database connection failed: ' . htmlspecialchars($mysqli->connect_error));
}
$mysqli->set_charset('utf8mb4');
