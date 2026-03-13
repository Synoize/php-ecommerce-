<?php

declare(strict_types=1);

class ReviewModel extends BaseModel
{
    public function forProduct(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, u.name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.product_id = :product_id
             ORDER BY r.created_at DESC'
        );
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function save(int $productId, int $userId, int $rating, string $comment): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reviews (product_id, user_id, rating, comment)
             VALUES (:product_id, :user_id, :rating, :comment)
             ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), created_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $rating,
            'comment' => $comment ?: null,
        ]);
    }

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT r.*, p.name AS product_name, u.name AS user_name
             FROM reviews r
             INNER JOIN products p ON p.id = r.product_id
             INNER JOIN users u ON u.id = r.user_id
             ORDER BY r.created_at DESC'
        )->fetchAll();
    }
}

