<?php

declare(strict_types=1);

class CartModel extends BaseModel
{
    public function items(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, p.name, p.price, p.stock, p.image
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
        $stmt = $this->pdo->prepare(
            'INSERT INTO cart (user_id, product_id, quantity)
             VALUES (:user_id, :product_id, :quantity)
             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => max(1, $quantity),
        ]);
    }

    public function update(int $userId, int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($userId, $productId);
            return;
        }

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
}

