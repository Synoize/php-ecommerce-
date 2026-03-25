<?php

declare(strict_types=1);

$appEnv = strtolower((string) ($_SERVER['APP_ENV'] ?? 'production'));
$httpHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
$isLocalHost = in_array($httpHost, ['localhost', '127.0.0.1'], true);
$baseUrl = $isLocalHost
    ? 'http://localhost/watch-ecommerce'
    : 'https://your-production-domain.com';

const APP_NAME = 'BIG BRANDS';
const APP_TIMEZONE = 'Asia/Kolkata';

const DB_HOST = 'localhost';
const DB_NAME = 'watch_ecommerce';
const DB_USER = 'root';
const DB_PASS = '';

const PAY0_CREATE_ORDER_URL = 'https://pay0.shop/api/create-order';
const PAY0_CHECK_ORDER_STATUS_URL = 'https://pay0.shop/api/check-order-status';
const PAY0_API_KEY = 'PAY0_API_KEY';
const PAY0_SECRET = 'PAY0_SECRET';
const DEFAULT_CURRENCY = 'INR';
const COD_BOOKING_AMOUNT = 00.00;

define('APP_ENV', $appEnv);
define('BASE_URL', $baseUrl);
define('PAY0_WEBHOOK_URL', BASE_URL . '/payment/webhook.php');
