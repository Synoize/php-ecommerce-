<?php

declare(strict_types=1);

class CheckoutController
{
    private CartModel $cart;
    private AddressModel $addresses;
    private CouponModel $coupons;
    private OrderModel $orders;
    private PaymentModel $payments;
    private UserModel $users;

    public function __construct()
    {
        $this->cart = new CartModel();
        $this->addresses = new AddressModel();
        $this->coupons = new CouponModel();
        $this->orders = new OrderModel();
        $this->payments = new PaymentModel();
        $this->users = new UserModel();
    }

    public function checkoutData(int $userId): array
    {
        $items = $this->cart->items($userId);
        $subtotal = $this->cart->subtotal($userId);
        $coupon = $_SESSION['checkout_coupon'] ?? null;
        $discount = $coupon ? round($subtotal * ((int) $coupon['discount_percent'] / 100), 2) : 0.0;

        return [
            'items' => $items,
            'addresses' => $this->addresses->forUser($userId),
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'cod_booking_amount' => (float) COD_BOOKING_AMOUNT,
        ];
    }

    public function applyCoupon(string $code): array
    {
        $coupon = $this->coupons->validateCode($code);

        if (!$coupon) {
            unset($_SESSION['checkout_coupon']);
            return ['ok' => false, 'message' => 'Coupon is invalid or expired.'];
        }

        $_SESSION['checkout_coupon'] = $coupon;
        return ['ok' => true, 'message' => 'Coupon applied.', 'coupon' => $coupon];
    }

    public function beginPay0Order(int $userId, int $addressId, string $paymentMethod): array
    {
        $items = $this->cart->items($userId);
        $subtotal = $this->cart->subtotal($userId);
        $coupon = $_SESSION['checkout_coupon'] ?? null;
        $user = $this->users->find($userId);

        if ($items === []) {
            throw new RuntimeException('Cart is empty.');
        }

        if (!$user) {
            throw new RuntimeException('User not found.');
        }

        if (!in_array($paymentMethod, ['cod', 'pay0'], true)) {
            throw new RuntimeException('Invalid payment method selected.');
        }

        $this->validateLiveStock($items);

        if (empty($user['phone'])) {
            throw new RuntimeException('Please add a mobile number to your account before payment.');
        }

        if (!empty($_SESSION['pending_pay0_order_id'])) {
            unset($_SESSION['pending_pay0_orders'][$_SESSION['pending_pay0_order_id']]);
        }

        $discount = $coupon ? round($subtotal * ((int) $coupon['discount_percent'] / 100), 2) : 0.0;
        $total = max(0, $subtotal - $discount);
        $collectAmount = $paymentMethod === 'cod' ? min((float) COD_BOOKING_AMOUNT, $total) : $total;
        $pay0OrderId = 'ORD_' . time() . random_int(1000, 9999);

        $_SESSION['pending_pay0_orders'][$pay0OrderId] = [
            'user_id' => $userId,
            'address_id' => $addressId,
            'items' => $items,
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'payment_method' => $paymentMethod,
        ];

        $_SESSION['pending_pay0_order_id'] = $pay0OrderId;

        return [
            'pay0_order_id' => $pay0OrderId,
            'customer_name' => (string) $user['name'],
            'customer_mobile' => (string) $user['phone'],
            'collect_amount' => $collectAmount,
            'full_total' => $total,
            'payment_method' => $paymentMethod,
        ];
    }

    public function handlePay0Success(string $pay0OrderId, string $txnId): int
    {
        $order = $this->orders->findByPay0OrderId($pay0OrderId);

        if (!$order) {
            $pending = $_SESSION['pending_pay0_orders'][$pay0OrderId] ?? null;
            if (!$pending) {
                throw new RuntimeException('Payment cannot be finalized because the pending order data was lost.');
            }

            $this->orders->createOrder(
                (int) $pending['user_id'],
                (int) $pending['address_id'],
                $pending['items'],
                (float) $pending['subtotal'],
                $pending['coupon'] ?? null,
                (string) $pending['payment_method'],
                $pay0OrderId,
                'confirmed',
                (string) $pending['payment_method'] === 'cod' ? 'pending' : 'paid'
            );

            unset($_SESSION['pending_pay0_orders'][$pay0OrderId], $_SESSION['pending_pay0_order_id']);
        }

        $result = $this->orders->finalizePay0Success($pay0OrderId, $txnId);
        $amount = $result['payment_method'] === 'cod'
            ? min((float) COD_BOOKING_AMOUNT, (float) $result['total_amount'])
            : (float) $result['total_amount'];

        $transactionId = $txnId !== '' ? $txnId : $pay0OrderId;
        $this->payments->record($result['order_id'], 'pay0', $transactionId, $amount, 'success');

        if (is_logged_in()) {
            $this->cart->clear((int) current_user()['id']);
        }

        unset($_SESSION['checkout_coupon'], $_SESSION['pending_pay0_order_id']);

        return (int) $result['order_id'];
    }

    public function handlePay0Failure(string $pay0OrderId): void
    {
        $this->orders->markPaymentFailedByPay0OrderId($pay0OrderId);
        unset($_SESSION['pending_pay0_orders'][$pay0OrderId], $_SESSION['pending_pay0_order_id']);
    }

    private function validateLiveStock(array $items): void
    {
        $stmt = db()->prepare('SELECT name, stock, is_active FROM products WHERE id = :id LIMIT 1');

        foreach ($items as $item) {
            $stmt->execute(['id' => (int) $item['product_id']]);
            $product = $stmt->fetch();

            if (!$product || (int) $product['is_active'] !== 1) {
                throw new RuntimeException($item['name'] . ' is unavailable.');
            }

            if ((int) $product['stock'] <= 0) {
                throw new RuntimeException($item['name'] . ' is out of stock.');
            }

            if ((int) $item['quantity'] > (int) $product['stock']) {
                throw new RuntimeException('Only ' . (int) $product['stock'] . ' item(s) left for ' . $item['name'] . '.');
            }
        }
    }
}
