<?php

declare(strict_types=1);

class OrderModel extends BaseModel
{
    public function create(int $userId, int $addressId, array $cartItems, float $subtotal, ?array $coupon, string $paymentMethod, array $paymentMeta = []): int
    {
        $discountAmount = $coupon ? round($subtotal * ((int) $coupon['discount_percent'] / 100), 2) : 0.0;
        $total = max(0, $subtotal - $discountAmount);

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (user_id, address_id, total_amount, status, payment_method, payment_status, razorpay_order_id)
                 VALUES (:user_id, :address_id, :total_amount, :status, :payment_method, :payment_status, :razorpay_order_id)'
            );
            $stmt->execute([
                'user_id' => $userId,
                'address_id' => $addressId,
                'total_amount' => $total,
                'status' => $paymentMethod === 'cod' ? 'confirmed' : 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'razorpay_order_id' => $paymentMeta['razorpay_order_id'] ?? null,
            ]);
            $orderId = (int) $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, price)
                 VALUES (:order_id, :product_id, :quantity, :price)'
            );
            $stockStmt = $this->pdo->prepare(
                'UPDATE products SET stock = stock - :quantity WHERE id = :product_id AND stock >= :quantity'
            );

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $stockStmt->execute([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
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

    public function markPaid(int $orderId, string $razorpayPaymentId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE orders
             SET payment_status = :payment_status, status = :status, razorpay_payment_id = :razorpay_payment_id
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $orderId,
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'razorpay_payment_id' => $razorpayPaymentId,
        ]);
    }

    public function dashboardStats(): array
    {
        return [
            'orders' => (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'revenue' => (float) $this->pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid' OR payment_method = 'cod'")->fetchColumn(),
            'pending_orders' => (int) $this->pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
        ];
    }
}

