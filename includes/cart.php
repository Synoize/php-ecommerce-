<?php

declare(strict_types=1);

function cartInit(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cartAdd(int $productId, int $qty = 1): void
{
    cartInit();
    if ($qty < 1) {
        $qty = 1;
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cartUpdate(int $productId, int $qty): void
{
    cartInit();
    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
        return;
    }
    $_SESSION['cart'][$productId] = $qty;
}

function cartRemove(int $productId): void
{
    cartInit();
    unset($_SESSION['cart'][$productId]);
}

function cartCount(): int
{
    cartInit();
    return array_sum($_SESSION['cart']);
}

function cartItems(PDO $pdo): array
{
    cartInit();
    if (count($_SESSION['cart']) === 0) {
        return [];
    }

    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    foreach ($products as $p) {
        $pid = (int)$p['id'];
        $qty = (int)($_SESSION['cart'][$pid] ?? 0);
        if ($qty <= 0) continue;
        $price = (float)$p['price'];
        $items[] = [
            'product' => $p,
            'quantity' => $qty,
            'line_total' => $price * $qty,
        ];
    }
    return $items;
}

function cartTotals(PDO $pdo): array
{
    $items = cartItems($pdo);
    $subtotal = 0.0;
    foreach ($items as $it) {
        $subtotal += (float)$it['line_total'];
    }

    $discount = 0.0;
    $coupon = $_SESSION['coupon'] ?? null;
    if (is_array($coupon) && isset($coupon['discount_amount'])) {
        $discount = (float)$coupon['discount_amount'];
    }

    $total = max(0.0, $subtotal - $discount);

    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total' => $total,
    ];
}
