<?php

declare(strict_types=1);

class CartModel extends BaseModel
{
    public function items(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, p.name, p.price, p.stock, p.image, p.is_active
             FROM cart c
             INNER JOIN products p ON p.id = c.product_id
             WHERE c.user_id = :user_id
             ORDER BY c.added_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            $item['line_total'] = (float) $item['price'] * (int) $item['quantity'];
        }

        return $items;
    }

    public function add(int $userId, int $productId, int $quantity): void
    {
        $quantity = max(1, $quantity);
        $product = $this->ensureAvailableQuantity($productId, $quantity);
        $existingQuantity = $this->cartQuantity($userId, $productId);
        $newQuantity = $existingQuantity + $quantity;

        if ($newQuantity > (int) $product['stock']) {
            throw new RuntimeException('Only ' . (int) $product['stock'] . ' item(s) available in stock.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO cart (user_id, product_id, quantity)
             VALUES (:user_id, :product_id, :quantity)
             ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $newQuantity,
        ]);
    }

    public function update(int $userId, int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($userId, $productId);
            return;
        }

        $this->ensureAvailableQuantity($productId, $quantity);

        $stmt = $this->pdo->prepare(
            'UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id'
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId, 'quantity' => $quantity]);
    }

    public function remove(int $userId, int $productId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id');
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    }

    public function clear(int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM cart WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }

    public function subtotal(int $userId): float
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(c.quantity * p.price), 0)
             FROM cart c
             INNER JOIN products p ON p.id = c.product_id
             WHERE c.user_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
        return (float) $stmt->fetchColumn();
    }

    public function count(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    private function cartQuantity(int $userId, int $productId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(quantity, 0) FROM cart WHERE user_id = :user_id AND product_id = :product_id LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return (int) $stmt->fetchColumn();
    }

    private function ensureAvailableQuantity(int $productId, int $quantity): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, stock, is_active FROM products WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();

        if (!$product || (int) $product['is_active'] !== 1) {
            throw new RuntimeException('This product is unavailable.');
        }

        if ((int) $product['stock'] <= 0) {
            throw new RuntimeException('This product is out of stock.');
        }

        if ($quantity > (int) $product['stock']) {
            throw new RuntimeException('Only ' . (int) $product['stock'] . ' item(s) available in stock.');
        }

        return $product;
    }
}
