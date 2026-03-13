<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();
verify_csrf();

$active = (new WishlistModel())->toggle((int) current_user()['id'], (int) ($_POST['product_id'] ?? 0));
$message = $active ? 'Added to wishlist.' : 'Removed from wishlist.';

if (wants_json()) {
    json_response(['ok' => true, 'active' => $active, 'message' => $message]);
}

set_flash('success', $message);
redirect((string) ($_POST['redirect'] ?? 'wishlist.php'));

