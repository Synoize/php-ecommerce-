<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

$result = (new CheckoutController())->applyCoupon((string) ($_POST['code'] ?? ''));

if (wants_json()) {
    json_response($result, $result['ok'] ? 200 : 422);
}

set_flash($result['ok'] ? 'success' : 'error', $result['message']);
redirect('checkout.php');

