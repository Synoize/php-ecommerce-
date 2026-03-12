<?php

declare(strict_types=1);

// Razorpay configuration (TEST mode by default).
// Do NOT hardcode credentials anywhere else.

if (!defined('RAZORPAY_KEY_ID')) {
    define('RAZORPAY_KEY_ID', 'rzp_test_RIzm0y12l0CJSx');
}

if (!defined('RAZORPAY_KEY_SECRET')) {
    define('RAZORPAY_KEY_SECRET', 'UHJipUjdw38XWLHjZihV1IZD');
}

if (!defined('RAZORPAY_CURRENCY')) {
    define('RAZORPAY_CURRENCY', 'INR');
}

function razorpaySign(string $orderId, string $paymentId): string
{
    return hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
}

function razorpayVerifySignature(string $orderId, string $paymentId, string $signature): bool
{
    $expected = razorpaySign($orderId, $paymentId);
    return hash_equals($expected, $signature);
}
