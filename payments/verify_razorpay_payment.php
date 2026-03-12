<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/razorpay.php';

requireLogin();

if (!isPost()) {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

try {
    $rpOrderId = trim((string)($_POST['razorpay_order_id'] ?? ''));
    $rpPaymentId = trim((string)($_POST['razorpay_payment_id'] ?? ''));
    $rpSignature = trim((string)($_POST['razorpay_signature'] ?? ''));

    if ($rpOrderId === '' || $rpPaymentId === '' || $rpSignature === '') {
        throw new RuntimeException('Payment verification data missing.');
    }

    $expected = $_SESSION['razorpay']['order_id'] ?? '';
    $expectedAmount = (int)($_SESSION['razorpay']['amount_paise'] ?? 0);

    if ($expected === '' || $expected !== $rpOrderId) {
        throw new RuntimeException('Invalid Razorpay order reference.');
    }

    if (!razorpayVerifySignature($rpOrderId, $rpPaymentId, $rpSignature)) {
        throw new RuntimeException('Razorpay signature verification failed.');
    }

    $totals = cartTotals($pdo);
    $amountNow = (int)round(((float)$totals['total']) * 100);
    if ($amountNow <= 0 || $amountNow !== $expectedAmount) {
        throw new RuntimeException('Cart amount changed. Please try again.');
    }

    if (count($totals['items']) === 0) {
        throw new RuntimeException('Cart is empty.');
    }

    $pdo->beginTransaction();

    $userId = (int)($_SESSION['user']['id'] ?? 0);
    if ($userId <= 0) {
        throw new RuntimeException('User session missing.');
    }

    $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, created_at, payment_method, payment_status, razorpay_payment_id, razorpay_order_id) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)");
    $orderStmt->execute([$userId, $totals['total'], 'pending', 'razorpay', 'paid', $rpPaymentId, $rpOrderId]);
    $orderId = (int)$pdo->lastInsertId();

    $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
    $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');

    foreach ($totals['items'] as $it) {
        $p = $it['product'];
        $pid = (int)$p['id'];
        $qty = (int)$it['quantity'];
        $price = (float)$p['price'];

        $itemStmt->execute([$orderId, $pid, $qty, $price]);

        $stockStmt->execute([$qty, $pid, $qty]);
        if ($stockStmt->rowCount() !== 1) {
            throw new RuntimeException('Insufficient stock for: ' . (string)$p['name']);
        }
    }

    $pdo->commit();

    cartClear();
    unset($_SESSION['razorpay']);

    setFlash('success', 'Payment successful. Order placed.');
    redirect('/user/orders.php');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Razorpay verify error: ' . $e->getMessage());
    setFlash('danger', 'Payment failed: ' . $e->getMessage());
    redirect('/checkout.php');
}
