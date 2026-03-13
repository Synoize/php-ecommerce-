<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

$addressId = (int) ($_POST['address_id'] ?? 0);
$paymentMethod = (string) ($_POST['payment_method'] ?? 'cod');
$address = (new AddressModel())->findOwned((int) current_user()['id'], $addressId);

if (!$address) {
    set_flash('error', 'Please select a valid address.');
    redirect('checkout.php');
}

$checkout = new CheckoutController();

try {
    if ($paymentMethod === 'razorpay') {
        $result = $checkout->createRazorpayOrder((int) current_user()['id'], $addressId);
        if (wants_json()) {
            json_response(['ok' => true, 'gateway' => 'razorpay', 'order_id' => $result['order_id'], 'razorpay_order' => $result['razorpay_order'], 'key' => RAZORPAY_KEY_ID]);
        }

        set_flash('success', 'Razorpay order created. Complete payment from your frontend integration.');
        redirect('user/orders.php?order_id=' . (int) $result['order_id']);
    }

    $orderId = $checkout->placeCodOrder((int) current_user()['id'], $addressId);
    set_flash('success', 'Order placed successfully.');
    redirect('user/orders.php?order_id=' . $orderId);
} catch (Throwable $e) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 422);
    }

    set_flash('error', $e->getMessage());
    redirect('checkout.php');
}

