<?php

declare(strict_types=1);

if (!defined('APP_OB_STARTED')) {
    define('APP_OB_STARTED', true);
    if (!headers_sent()) {
        ob_start();
    }
}

// Base URL (adjust if you change folder name or use a virtual host)
// Example: http://localhost/watch-ecommerce
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/watch-ecommerce');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cart.php';

