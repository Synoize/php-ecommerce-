<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

try {
    (new CheckoutController())->verifyRazorpayPayment(
        (int) ($_POST['order_id'] ?? 0),
        (string) ($_POST['razorpay_order_id'] ?? ''),
        (string) ($_POST['razorpay_payment_id'] ?? ''),
        (string) ($_POST['razorpay_signature'] ?? '')
    );

    if (wants_json()) {
        json_response(['ok' => true, 'message' => 'Payment verified.']);
    }

    set_flash('success', 'Payment verified and order confirmed.');
    redirect('user/orders.php?order_id=' . (int) ($_POST['order_id'] ?? 0));
} catch (Throwable $e) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 422);
    }

    set_flash('error', $e->getMessage());
    redirect('checkout.php');
}

