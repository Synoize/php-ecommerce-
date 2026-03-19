<?php

declare(strict_types=1);

class PaymentModel extends BaseModel
{
    public function record(int $orderId, string $gateway, string $transactionId, float $amount, string $status): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO payments (order_id, payment_gateway, transaction_id, amount, status)
             VALUES (:order_id, :payment_gateway, :transaction_id, :amount, :status)
             ON DUPLICATE KEY UPDATE status = VALUES(status), amount = VALUES(amount)'
        );
        $stmt->execute([
            'order_id' => $orderId,
            'payment_gateway' => $gateway,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'status' => $status,
        ]);
    }

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT p.*, o.user_id, o.payment_method, o.payment_status, u.name AS user_name, u.email
             FROM payments p
             INNER JOIN orders o ON o.id = p.order_id
             INNER JOIN users u ON u.id = o.user_id
             ORDER BY p.created_at DESC'
        )->fetchAll();
    }
}
