<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/razorpay.php';

header('Content-Type: application/json; charset=utf-8');

try {
    requireLogin();

    $totals = cartTotals($pdo);
    if (count($totals['items']) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
        exit;
    }

    $amountPaise = (int)round(((float)$totals['total']) * 100);
    if ($amountPaise <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
        exit;
    }

    $receipt = 'rcpt_' . time() . '_' . bin2hex(random_bytes(3));

    $payload = json_encode([
        'amount' => $amountPaise,
        'currency' => RAZORPAY_CURRENCY,
        'receipt' => $receipt,
        'payment_capture' => 1,
        'notes' => [
            'app' => 'project',
            'user_id' => (string)($_SESSION['user']['id'] ?? ''),
        ],
    ], JSON_UNESCAPED_SLASHES);

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 30,
    ]);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) {
        throw new RuntimeException('cURL error: ' . $err);
    }

    $data = json_decode($resp, true);
    if ($status < 200 || $status >= 300) {
        $msg = $data['error']['description'] ?? 'Failed to create Razorpay order.';
        throw new RuntimeException($msg);
    }

    // Store expected order details in session to validate during verification
    $_SESSION['razorpay'] = [
        'order_id' => (string)($data['id'] ?? ''),
        'amount_paise' => $amountPaise,
        'currency' => RAZORPAY_CURRENCY,
    ];

    echo json_encode([
        'success' => true,
        'razorpay_order_id' => $data['id'],
        'amount' => $amountPaise,
        'currency' => RAZORPAY_CURRENCY,
        'key_id' => RAZORPAY_KEY_ID,
        'name' => 'Shop',
        'prefill' => [
            'name' => $_SESSION['user']['name'] ?? '',
            'email' => $_SESSION['user']['email'] ?? '',
        ],
    ]);
} catch (Throwable $e) {
    error_log('Razorpay create order error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
