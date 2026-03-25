<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/payment-functions.php';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = $_POST;
}

$pay0OrderId = trim((string) ($data['order_id'] ?? ''));

if ($pay0OrderId === '') {
    json_response(['ok' => false, 'message' => 'Missing order_id.'], 422);
}

try {
    $statusResponse = pay0_check_order_status($pay0OrderId);

    if (pay0_is_success($statusResponse)) {
        $txnId = pay0_extract_transaction_id($statusResponse);
        $orderId = (new CheckoutController())->handlePay0Success($pay0OrderId, $txnId);
        json_response(['ok' => true, 'status' => 'success', 'order_id' => $orderId]);
    }

    if (pay0_is_failed($statusResponse)) {
        (new CheckoutController())->handlePay0Failure($pay0OrderId);
        json_response(['ok' => true, 'status' => 'failed']);
    }

    json_response(['ok' => true, 'status' => 'pending']);
} catch (Throwable $e) {
    json_response(['ok' => false, 'message' => $e->getMessage()], 422);
}
