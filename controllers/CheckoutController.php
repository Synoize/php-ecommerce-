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

        $this->validateCartStock($items);
        $orderId = $this->orders->create($userId, $addressId, $items, $subtotal, $_SESSION['checkout_coupon'] ?? null, 'cod');
        $this->cart->clear($userId);
        unset($_SESSION['checkout_coupon']);

        return $orderId;
    }

    public function createRazorpayOrder(int $userId, int $addressId): array
    {
        $items = $this->cart->items($userId);
        $subtotal = $this->cart->subtotal($userId);
        $coupon = $_SESSION['checkout_coupon'] ?? null;

        if ($items === []) {
            throw new RuntimeException('Cart is empty.');
        }

        $this->validateCartStock($items);

        if (RAZORPAY_KEY_ID === '' || RAZORPAY_KEY_SECRET === '') {
            throw new RuntimeException('Razorpay keys are not configured.');
        }

        $discount = $coupon ? round($subtotal * ((int) $coupon['discount_percent'] / 100), 2) : 0.0;
        $total = max(0, $subtotal - $discount);

        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => (int) round($total * 100),
                'currency' => DEFAULT_CURRENCY,
                'receipt' => 'watch_' . time(),
                'notes' => ['user_id' => (string) $userId, 'address_id' => (string) $addressId],
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
        $orderId = $this->orders->create(
            $userId,
            $addressId,
            $items,
            $subtotal,
            $coupon,
            'razorpay',
            ['razorpay_order_id' => $orderData['id']]
        );

        $_SESSION['pending_order_id'] = $orderId;

        return ['order_id' => $orderId, 'razorpay_order' => $orderData];
    }

    public function verifyRazorpayPayment(int $orderId, string $razorpayOrderId, string $razorpayPaymentId, string $signature): void
    {
        $generated = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, RAZORPAY_KEY_SECRET);

        if (!hash_equals($generated, $signature)) {
            throw new RuntimeException('Payment signature verification failed.');
        }

        $this->orders->markPaid($orderId, $razorpayPaymentId);
        $order = $this->orders->findAdmin($orderId);
        $this->payments->record($orderId, 'razorpay', $razorpayPaymentId, (float) ($order['total_amount'] ?? 0), 'success');
        $this->cart->clear((int) current_user()['id']);
        unset($_SESSION['checkout_coupon'], $_SESSION['pending_order_id']);
    }

    private function validateCartStock(array $items): void
    {
        foreach ($items as $item) {
            if ((int) $item['stock'] <= 0) {
                throw new RuntimeException($item['name'] . ' is out of stock.');
            }

            if ((int) $item['quantity'] > (int) $item['stock']) {
                throw new RuntimeException('Only ' . (int) $item['stock'] . ' item(s) left for ' . $item['name'] . '.');
            }
        }
    }
}
