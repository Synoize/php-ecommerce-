<?php

declare(strict_types=1);

class WishlistModel extends BaseModel
{
    public function items(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT w.*, p.name, p.price, p.best_price, p.stock, p.image
             FROM wishlist w
             INNER JOIN products p ON p.id = w.product_id
             WHERE w.user_id = :user_id
             ORDER BY w.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function adminAll(): array
    {
        return $this->pdo->query(
            'SELECT w.*, u.name AS user_name, u.email, p.name AS product_name, p.price, p.best_price, p.stock
             FROM wishlist w
             INNER JOIN users u ON u.id = w.user_id
             INNER JOIN products p ON p.id = w.product_id
             ORDER BY w.created_at DESC'
        )->fetchAll();
    }

    public function has(int $userId, int $productId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM wishlist WHERE user_id = :user_id AND product_id = :product_id LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return (bool) $stmt->fetchColumn();
    }

    public function toggle(int $userId, int $productId): bool
    {
        if ($this->has($userId, $productId)) {
            $stmt = $this->pdo->prepare(
                'DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id'
            );
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            return false;
        }

        $stmt = $this->pdo->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)');
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return true;
    }

    public function count(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
