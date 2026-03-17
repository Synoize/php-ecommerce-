<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

$cart = new CartModel();
$userId = (int) current_user()['id'];
$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$boxOptionId = isset($_POST['box_option_id']) && (int) $_POST['box_option_id'] > 0 ? (int) $_POST['box_option_id'] : null;
$boxQuantity = max(0, (int) ($_POST['box_quantity'] ?? 0));
$action = (string) ($_POST['action'] ?? 'add');
$redirect = (string) ($_POST['redirect'] ?? 'cart.php');

if ($productId <= 0) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => 'Invalid product.'], 422);
    }

    set_flash('error', 'Invalid product.');
    redirect($redirect);
}

try {
    if ($action === 'remove') {
        $cart->remove($userId, $productId);
        $message = 'Item removed from cart.';
    } elseif ($action === 'update') {
        $cart->update($userId, $productId, $quantity, $boxOptionId, $boxQuantity);
        $message = 'Cart updated.';
    } else {
        $cart->add($userId, $productId, $quantity, $boxOptionId, $boxQuantity);
        $message = 'Item added to cart.';
    }

    if (wants_json()) {
        json_response([
            'ok' => true,
            'message' => $message,
            'count' => $cart->count($userId),
            'subtotal' => $cart->subtotal($userId),
        ]);
    }

    set_flash('success', $message);
    redirect($redirect);
} catch (Throwable $e) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 422);
    }

    set_flash('error', $e->getMessage());
    redirect($redirect);
}
