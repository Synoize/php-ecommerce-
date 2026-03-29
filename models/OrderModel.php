<?php

declare(strict_types=1);

class OrderModel extends BaseModel
{
    public function createPendingPaymentOrder(
        int $userId,
        int $addressId,
        array $cartItems,
        float $subtotal,
        ?array $coupon,
        string $paymentMethod,
        string $pay0OrderId
    ): int {
        $discountAmount = $coupon ? round($subtotal * ((int) $coupon['discount_percent'] / 100), 2) : 0.0;
        $total = max(0, $subtotal - $discountAmount);

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (user_id, address_id, total_amount, status, payment_method, payment_status, pay0_order_id)
                 VALUES (:user_id, :address_id, :total_amount, :status, :payment_method, :payment_status, :pay0_order_id)'
            );
            $stmt->execute([
                'user_id' => $userId,
                'address_id' => $addressId,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'pay0_order_id' => $pay0OrderId,
            ]);
            $orderId = (int) $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                'INSERT INTO order_items (
                    order_id, product_id, quantity, price,
                    box_option_id, box_option_name, box_option_price, box_quantity
                 ) VALUES (
                    :order_id, :product_id, :quantity, :price,
                    :box_option_id, :box_option_name, :box_option_price, :box_quantity
                 )'
            );

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'box_option_id' => $item['box_option_id'] ?: null,
                    'box_option_name' => $item['box_name'] ?: null,
                    'box_option_price' => $item['box_price'] ?: null,
                    'box_quantity' => $item['box_quantity'] ?? 0,
                ]);
            }

            if ($coupon) {
                $couponStmt = $this->pdo->prepare(
                    'INSERT INTO order_coupons (order_id, coupon_id, discount_amount)
                     VALUES (:order_id, :coupon_id, :discount_amount)'
                );
                $couponStmt->execute([
                    'order_id' => $orderId,
                    'coupon_id' => $coupon['id'],
                    'discount_amount' => $discountAmount,
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deletePendingForUser(int $orderId, int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM orders
             WHERE id = :id AND user_id = :user_id AND status = :status AND payment_status = :payment_status'
        );
        $stmt->execute([
            'id' => $orderId,
            'user_id' => $userId,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function findByPay0OrderId(string $pay0OrderId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM orders WHERE pay0_order_id = :pay0_order_id LIMIT 1'
        );
        $stmt->execute(['pay0_order_id' => $pay0OrderId]);
        return $stmt->fetch() ?: null;
    }

    public function finalizePay0Success(string $pay0OrderId, string $txnId): array
    {
        $this->pdo->beginTransaction();

        try {
            $orderStmt = $this->pdo->prepare(
                'SELECT * FROM orders WHERE pay0_order_id = :pay0_order_id LIMIT 1 FOR UPDATE'
            );
            $orderStmt->execute(['pay0_order_id' => $pay0OrderId]);
            $order = $orderStmt->fetch();

            if (!$order) {
                throw new RuntimeException('Order not found.');
            }

            $alreadyFinalized = (string) ($order['pay0_txn_id'] ?? '') !== '';
            if (!$alreadyFinalized) {
                $items = $this->items((int) $order['id']);
                $stockStmt = $this->pdo->prepare(
                    'UPDATE products SET stock = stock - :quantity WHERE id = :product_id AND stock >= :quantity'
                );

                foreach ($items as $item) {
                    $stockStmt->execute([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                    ]);

                    if ($stockStmt->rowCount() !== 1) {
                        throw new RuntimeException($item['name'] . ' is no longer available in the requested quantity.');
                    }
                }

                $paymentStatus = (string) $order['payment_method'] === 'cod' ? 'pending' : 'paid';
                $updateStmt = $this->pdo->prepare(
                    'UPDATE orders
                     SET payment_status = :payment_status, status = :status, pay0_txn_id = :pay0_txn_id
                     WHERE id = :id'
                );
                $updateStmt->execute([
                    'id' => $order['id'],
                    'payment_status' => $paymentStatus,
                    'status' => 'confirmed',
                    'pay0_txn_id' => $txnId,
                ]);
                $order['payment_status'] = $paymentStatus;
                $order['status'] = 'confirmed';
                $order['pay0_txn_id'] = $txnId;
            }

            $this->pdo->commit();

            return [
                'order_id' => (int) $order['id'],
                'payment_method' => (string) $order['payment_method'],
                'total_amount' => (float) $order['total_amount'],
                'already_finalized' => $alreadyFinalized,
            ];
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function markPaymentFailedByPay0OrderId(string $pay0OrderId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE orders SET payment_status = :payment_status WHERE pay0_order_id = :pay0_order_id AND payment_status = :current_status'
        );
        $stmt->execute([
            'payment_status' => 'failed',
            'pay0_order_id' => $pay0OrderId,
            'current_status' => 'pending',
        ]);
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT o.*, a.city, a.state
             FROM orders o
             LEFT JOIN addresses a ON a.id = o.address_id
             WHERE o.user_id = :user_id
             ORDER BY o.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findForUser(int $userId, int $orderId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT o.*, a.full_name, a.phone, a.address_line, a.city, a.state, a.pincode, a.country
             FROM orders o
             LEFT JOIN addresses a ON a.id = o.address_id
             WHERE o.id = :order_id AND o.user_id = :user_id
             LIMIT 1'
        );
        $stmt->execute(['order_id' => $orderId, 'user_id' => $userId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = $this->items($orderId);
        $order['coupon'] = $this->coupon($orderId);
        return $order;
    }

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT o.*, u.name AS user_name, u.email
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC'
        )->fetchAll();
    }

    public function findAdmin(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT o.*, u.name AS user_name, u.email, a.full_name, a.phone, a.address_line, a.city, a.state, a.pincode, a.country
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             LEFT JOIN addresses a ON a.id = o.address_id
             WHERE o.id = :order_id
             LIMIT 1'
        );
        $stmt->execute(['order_id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = $this->items($orderId);
        $order['coupon'] = $this->coupon($orderId);
        return $order;
    }

    public function items(int $orderId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT oi.*, p.name, p.image
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :order_id'
        );
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function coupon(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT oc.*, c.code, c.discount_percent
             FROM order_coupons oc
             INNER JOIN coupons c ON c.id = oc.coupon_id
             WHERE oc.order_id = :order_id
             LIMIT 1'
        );
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetch() ?: null;
    }

    public function updateStatus(int $orderId, string $status, string $paymentStatus): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE orders SET status = :status, payment_status = :payment_status WHERE id = :id'
        );
        $stmt->execute(['id' => $orderId, 'status' => $status, 'payment_status' => $paymentStatus]);
    }

    public function dashboardStats(): array
    {
        $summary = $this->pdo->query(
            "SELECT
                COUNT(*) AS total_orders,
                COALESCE(SUM(CASE WHEN payment_status = 'paid' OR payment_method = 'cod' THEN total_amount ELSE 0 END), 0) AS total_revenue,
                COALESCE(SUM(CASE WHEN DATE(created_at) = CURDATE() AND (payment_status = 'paid' OR payment_method = 'cod') THEN total_amount ELSE 0 END), 0) AS today_revenue,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) AS pending_orders,
                COALESCE(SUM(CASE WHEN status IN ('confirmed', 'shipped', 'delivered') THEN 1 ELSE 0 END), 0) AS successful_orders,
                COALESCE(SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END), 0) AS delivered_orders,
                COALESCE(SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END), 0) AS cancelled_orders
             FROM orders"
        )->fetch();

        return [
            'orders' => (int) ($summary['total_orders'] ?? 0),
            'revenue' => (float) ($summary['total_revenue'] ?? 0),
            'today_revenue' => (float) ($summary['today_revenue'] ?? 0),
            'pending_orders' => (int) ($summary['pending_orders'] ?? 0),
            'successful_orders' => (int) ($summary['successful_orders'] ?? 0),
            'delivered_orders' => (int) ($summary['delivered_orders'] ?? 0),
            'cancelled_orders' => (int) ($summary['cancelled_orders'] ?? 0),
        ];
    }
}
