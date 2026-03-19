<?php

declare(strict_types=1);

class CartModel extends BaseModel
{
    public function items(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, p.name, p.price, p.stock, p.image, p.is_active,
                    b.name AS box_name, b.image AS box_image, b.price AS box_price, b.is_active AS box_is_active
             FROM cart c
             INNER JOIN products p ON p.id = c.product_id
             LEFT JOIN product_box_options b ON b.id = c.box_option_id
             WHERE c.user_id = :user_id
             ORDER BY c.added_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            $productTotal = (float) $item['price'] * (int) $item['quantity'];
            $boxTotal = ((float) ($item['box_price'] ?? 0)) * (int) ($item['box_quantity'] ?? 0);
            $item['line_total'] = $productTotal + $boxTotal;
        }

        return $items;
    }

    public function adminAll(): array
    {
        $rows = $this->pdo->query(
            'SELECT c.*, u.name AS user_name, u.email, p.name AS product_name, p.price,
                    b.name AS box_name, b.price AS box_price
             FROM cart c
             INNER JOIN users u ON u.id = c.user_id
             INNER JOIN products p ON p.id = c.product_id
             LEFT JOIN product_box_options b ON b.id = c.box_option_id
             ORDER BY c.added_at DESC'
        )->fetchAll();

        foreach ($rows as &$row) {
            $row['line_total'] = ((float) $row['price'] * (int) $row['quantity'])
                + ((float) ($row['box_price'] ?? 0) * (int) ($row['box_quantity'] ?? 0));
        }

        return $rows;
    }

    public function add(int $userId, int $productId, int $quantity, ?int $boxOptionId = null, int $boxQuantity = 0): void
    {
        $quantity = max(1, $quantity);
        $product = $this->ensureAvailableQuantity($productId, $quantity);
        $existing = $this->cartItem($userId, $productId);
        $newQuantity = ((int) ($existing['quantity'] ?? 0)) + $quantity;

        if ($newQuantity > (int) $product['stock']) {
            throw new RuntimeException('Only ' . (int) $product['stock'] . ' item(s) available in stock.');
        }

        $box = $this->resolveBoxOption($productId, $boxOptionId, $boxQuantity, $newQuantity, $existing);

        $stmt = $this->pdo->prepare(
            'INSERT INTO cart (user_id, product_id, quantity, box_option_id, box_quantity)
             VALUES (:user_id, :product_id, :quantity, :box_option_id, :box_quantity)
             ON DUPLICATE KEY UPDATE
                quantity = VALUES(quantity),
                box_option_id = VALUES(box_option_id),
                box_quantity = VALUES(box_quantity)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $newQuantity,
            'box_option_id' => $box['id'],
            'box_quantity' => $box['quantity'],
        ]);
    }

    public function update(int $userId, int $productId, int $quantity, ?int $boxOptionId = null, int $boxQuantity = 0): void
    {
        if ($quantity <= 0) {
            $this->remove($userId, $productId);
            return;
        }

        $this->ensureAvailableQuantity($productId, $quantity);
        $box = $this->resolveBoxOption($productId, $boxOptionId, $boxQuantity, $quantity);

        $stmt = $this->pdo->prepare(
            'UPDATE cart
             SET quantity = :quantity, box_option_id = :box_option_id, box_quantity = :box_quantity
             WHERE user_id = :user_id AND product_id = :product_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'box_option_id' => $box['id'],
            'box_quantity' => $box['quantity'],
        ]);
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
            'SELECT COALESCE(SUM((c.quantity * p.price) + (c.box_quantity * COALESCE(b.price, 0))), 0)
             FROM cart c
             INNER JOIN products p ON p.id = c.product_id
             LEFT JOIN product_box_options b ON b.id = c.box_option_id
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

    private function cartItem(int $userId, int $productId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT quantity, box_option_id, box_quantity
             FROM cart
             WHERE user_id = :user_id AND product_id = :product_id
             LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return $stmt->fetch() ?: null;
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

    private function resolveBoxOption(int $productId, ?int $boxOptionId, int $boxQuantity, int $productQuantity, ?array $existing = null): array
    {
        if ($boxOptionId === null || $boxOptionId <= 0) {
            return ['id' => null, 'quantity' => 0];
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, product_id, is_active FROM product_box_options WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $boxOptionId]);
        $box = $stmt->fetch();

        if (!$box || (int) $box['product_id'] !== $productId || (int) $box['is_active'] !== 1) {
            throw new RuntimeException('Selected box option is unavailable.');
        }

        $quantity = max(1, $boxQuantity);
        if ($existing && (int) ($existing['box_option_id'] ?? 0) === $boxOptionId) {
            $quantity += (int) ($existing['box_quantity'] ?? 0);
        }

        if ($quantity > $productQuantity) {
            $quantity = $productQuantity;
        }

        return ['id' => $boxOptionId, 'quantity' => $quantity];
    }
}
