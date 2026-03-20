<?php

declare(strict_types=1);

class CheckoutController
{
    private CartModel $cart;
    private AddressModel $addresses;
    private CouponModel $coupons;
    private OrderModel $orders;
    private PaymentModel $payments;

    public function __construct()
    {
        $this->cart = new CartModel();
        $this->addresses = new AddressModel();
        $this->coupons = new CouponModel();
        $this->orders = new OrderModel();
        $this->payments = new PaymentModel();
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

    public function placeCodOrder(int $userId, int $addressId): int
    {
        $items = $this->cart->items($userId);
        $subtotal = $this->cart->subtotal($userId);

        if ($items === []) {
            throw new RuntimeException('Cart is empty.');
        }

        $this->validateLiveStock($items);
        $orderId = $this->orders->create(
            $userId,
            $addressId,
            $items,
            $subtotal,
            $_SESSION['checkout_coupon'] ?? null,
            'cod',
            ['initial_status' => 'confirmed', 'initial_payment_status' => 'pending']
        );
        $this->cart->clear($userId);
        unset($_SESSION['checkout_coupon'], $_SESSION['pending_checkout']);

        return $orderId;
    }

    public function createRazorpayOrder(int $userId, int $addressId, string $paymentMethod = 'razorpay'): array
    {
        $items = $this->cart->items($userId);
        $subtotal = $this->cart->subtotal($userId);
        $coupon = $_SESSION['checkout_coupon'] ?? null;

        if ($items === []) {
            throw new RuntimeException('Cart is empty.');
        }

        if (!in_array($paymentMethod, ['razorpay', 'cod'], true)) {
            throw new RuntimeException('Invalid payment method.');
        }

        $this->validateLiveStock($items);

        if (RAZORPAY_KEY_ID === '' || RAZORPAY_KEY_SECRET === '') {
            throw new RuntimeException('Razorpay keys are not configured.');
        }

        $discount = $coupon ? round($subtotal * ((int) $coupon['discount_percent'] / 100), 2) : 0.0;
        $total = max(0, $subtotal - $discount);
        $collectAmount = $paymentMethod === 'cod' ? min((float) COD_BOOKING_AMOUNT, $total) : $total;

        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => (int) round($collectAmount * 100),
                'currency' => DEFAULT_CURRENCY,
                'receipt' => 'watch_' . time(),
                'notes' => [
                    'user_id' => (string) $userId,
                    'address_id' => (string) $addressId,
                    'payment_method' => $paymentMethod,
                ],
            ], JSON_THROW_ON_ERROR),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($response === false || $status < 200 || $status >= 300) {
            throw new RuntimeException('Unable to create Razorpay order.');
        }

        $orderData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        $_SESSION['pending_checkout'] = [
            'user_id' => $userId,
            'address_id' => $addressId,
            'payment_method' => $paymentMethod,
            'coupon' => $coupon,
            'cart_items' => $items,
            'subtotal' => $subtotal,
            'collect_amount' => $collectAmount,
            'full_total' => $total,
            'razorpay_order_id' => $orderData['id'],
        ];

        return [
            'razorpay_order' => $orderData,
            'collect_amount' => $collectAmount,
            'full_total' => $total,
            'payment_method' => $paymentMethod,
        ];
    }

    public function verifyRazorpayPayment(string $razorpayOrderId, string $razorpayPaymentId, string $signature): int
    {
        $generated = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, RAZORPAY_KEY_SECRET);

        if (!hash_equals($generated, $signature)) {
            throw new RuntimeException('Payment signature verification failed.');
        }

        $pending = $_SESSION['pending_checkout'] ?? null;
        if (!$pending || (string) ($pending['razorpay_order_id'] ?? '') !== $razorpayOrderId) {
            throw new RuntimeException('No pending checkout found for this payment.');
        }

        if ((int) ($pending['user_id'] ?? 0) !== (int) current_user()['id']) {
            throw new RuntimeException('Checkout session mismatch.');
        }

        $items = (array) ($pending['cart_items'] ?? []);
        if ($items === []) {
            throw new RuntimeException('Cart snapshot is empty.');
        }

        $this->validateLiveStock($items);

        $paymentMethod = (string) ($pending['payment_method'] ?? 'razorpay');
        $orderId = $this->orders->create(
            (int) $pending['user_id'],
            (int) $pending['address_id'],
            $items,
            (float) ($pending['subtotal'] ?? 0),
            $pending['coupon'] ?? null,
            $paymentMethod,
            [
                'razorpay_order_id' => $razorpayOrderId,
                'initial_status' => 'confirmed',
                'initial_payment_status' => 'pending',
            ]
        );

        if ($paymentMethod === 'cod') {
            $this->orders->markCodAdvancePaid($orderId, $razorpayPaymentId);
            $this->payments->record(
                $orderId,
                'razorpay',
                $razorpayPaymentId,
                (float) ($pending['collect_amount'] ?? COD_BOOKING_AMOUNT),
                'success'
            );
        } else {
            $this->orders->markPaid($orderId, $razorpayPaymentId);
            $this->payments->record(
                $orderId,
                'razorpay',
                $razorpayPaymentId,
                (float) ($pending['full_total'] ?? 0),
                'success'
            );
        }

        $this->cart->clear((int) current_user()['id']);
        unset($_SESSION['checkout_coupon'], $_SESSION['pending_checkout'], $_SESSION['pending_order_id']);

        return $orderId;
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
