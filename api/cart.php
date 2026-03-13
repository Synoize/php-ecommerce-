<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

$cart = new CartModel();
$userId = (int) current_user()['id'];
$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$action = (string) ($_POST['action'] ?? 'add');

if ($productId <= 0) {
    set_flash('error', 'Invalid product.');
    redirect((string) ($_POST['redirect'] ?? 'cart.php'));
}

if ($action === 'remove') {
    $cart->remove($userId, $productId);
    $message = 'Item removed from cart.';
} elseif ($action === 'update') {
    $cart->update($userId, $productId, $quantity);
    $message = 'Cart updated.';
} else {
    $cart->add($userId, $productId, $quantity);
    $message = 'Item added to cart.';
}

if (wants_json()) {
    json_response(['ok' => true, 'message' => $message, 'count' => $cart->count($userId), 'subtotal' => $cart->subtotal($userId)]);
}

set_flash('success', $message);
redirect((string) ($_POST['redirect'] ?? 'cart.php'));

