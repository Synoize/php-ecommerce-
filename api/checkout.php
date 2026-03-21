<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

$addressId = (int) ($_POST['address_id'] ?? 0);
$paymentMethod = (string) ($_POST['payment_method'] ?? 'cod');
$address = (new AddressModel())->findOwned((int) current_user()['id'], $addressId);

if (!$address) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => 'Please select a valid address.'], 422);
    }

    set_flash('error', 'Please select a valid address.');
    redirect('checkout.php');
}

$checkout = new CheckoutController();

try {
    if (!in_array($paymentMethod, ['cod', 'razorpay'], true)) {
        throw new RuntimeException('Invalid payment method selected.');
    }

    $result = $checkout->createRazorpayOrder((int) current_user()['id'], $addressId, $paymentMethod);

    if (wants_json()) {
        json_response([
            'ok' => true,
            'gateway' => 'razorpay',
            'razorpay_order' => $result['razorpay_order'],
            'key' => RAZORPAY_KEY_ID,
            'payment_method' => $result['payment_method'],
            'collect_amount' => $result['collect_amount'],
            'full_total' => $result['full_total'],
            'message' => $paymentMethod === 'cod'
                ? 'Pay the booking amount to confirm your COD order.'
                : 'Complete the full payment to place your order.',
        ]);
    }

    set_flash('error', 'Complete the online payment from the checkout page to place this order.');
    redirect('checkout.php');
} catch (Throwable $e) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 422);
    }

    set_flash('error', $e->getMessage());
    redirect('checkout.php');
}
