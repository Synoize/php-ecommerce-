<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$orderModel = new OrderModel();
$orderId = (int) ($_GET['order_id'] ?? 0);
$order = $orderId > 0 ? $orderModel->findForUser((int) current_user()['id'], $orderId) : null;
$orders = $orderModel->forUser((int) current_user()['id']);
$pageTitle = 'My Orders';
require __DIR__ . '/layout/header.php';
?>

<main class="mx-auto max-w-7xl px-4 py-10 mt-20">

    <!-- HEADER -->
    <h1 class="text-2xl md:text-3xl font-bold text-black-medium">
        My Orders
    </h1>

    <div class="mt-8 grid gap-6 lg:grid-cols-[320px,1fr]">

        <!-- LEFT: ORDER LIST -->
        <section class="space-y-3">

            <?php if ($orders): ?>
                <?php foreach ($orders as $item): ?>

                    <a href="<?= e(app_url('user/orders.php?order_id=' . (int) $item['id'])); ?>"
                        class="block rounded-xl border p-4 bg-white-light/10 hover:bg-white-light/20 transition">

                        <div class="flex justify-between items-center">

                            <span class="font-semibold text-black-medium">
                                #<?= (int) $item['id']; ?>
                            </span>

                            <span class="text-xs px-3 py-1 rounded-full font-medium <?= order_status_badge((string) $item['status']); ?>">
                                <?= e($item['status']); ?>
                            </span>

                        </div>

                        <div class="mt-2 text-xs text-black-light">
                            <?= date('d M Y', strtotime((string) $item['created_at'])); ?>
                        </div>

                        <div class="mt-2 font-semibold text-primary-medium">
                            <?= e(money((float) $item['total_amount'])); ?>
                        </div>

                    </a>

                <?php endforeach; ?>
            <?php else: ?>

                <div class="text-sm text-black-light">
                    No orders found.
                </div>

            <?php endif; ?>

        </section>

        <!-- RIGHT: ORDER DETAILS -->
        <section class="sticky top-28 h-fit rounded-xl border bg-white-light/10 p-5">

            <?php if ($order): ?>

                <?php $trackingSteps = order_tracking_steps((string) $order['status']); ?>

                <!-- HEADER -->
                <div class="flex justify-between items-center">

                    <h2 class="text-lg md:text-xl font-semibold text-black-medium">
                        Order #<?= (int) $order['id']; ?>
                    </h2>

                    <span class="text-xs px-3 py-1 rounded-full font-medium <?= order_status_badge((string) $order['status']); ?>">
                        <?= e($order['status']); ?>
                    </span>

                </div>

                <!-- META -->
                <div class="mt-4 text-sm text-black-light space-y-1">
                    <div>Payment: <?= e($order['payment_method']); ?> / <?= e($order['payment_status']); ?></div>
                    <div>Address: <?= e($order['address_line']); ?>, <?= e($order['city']); ?>, <?= e($order['state']); ?></div>
                </div>

                <!-- TRACKING -->
                <div class="mt-6">

                    <h3 class="text-sm font-semibold text-black-medium mb-3">
                        Order Tracking
                    </h3>

                    <?php if ($order['status'] === 'cancelled'): ?>

                        <div class="text-sm text-red-600">
                            This order has been cancelled.
                        </div>

                    <?php else: ?>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                            <?php foreach ($trackingSteps as $step): ?>
                                <div class="rounded-lg border px-3 py-3 text-center
                                    <?= $step['complete'] ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200'; ?>">

                                    <div class="text-[10px] uppercase tracking-wider
                                        <?= $step['current'] ? 'text-primary-medium' : 'text-gray-400'; ?>">
                                        <?= $step['key']; ?>
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-black-medium">
                                        <?= e($step['label']); ?>
                                    </div>

                                </div>
                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

                <!-- ITEMS -->
                <div class="mt-6 space-y-4">

                    <?php foreach ($order['items'] as $line): ?>

                        <?php
                        $productTotal = (float) $line['price'] * (int) $line['quantity'];
                        $boxTotal = (float) ($line['box_option_price'] ?? 0) * (int) ($line['box_quantity'] ?? 0);
                        ?>

                        <div class="rounded-xl border p-4 bg-white-light/20">

                            <div class="flex justify-between items-start gap-3">

                                <div>
                                    <div class="font-semibold text-black-medium">
                                        <?= e($line['name']); ?>
                                    </div>

                                    <div class="text-xs text-black-light mt-1">
                                        Qty: <?= (int) $line['quantity']; ?>
                                    </div>
                                </div>

                                <div class="font-semibold text-primary-medium">
                                    <?= e(money($productTotal)); ?>
                                </div>

                            </div>

                            <!-- BOX -->
                            <?php if (!empty($line['box_option_name']) && (int) $line['box_quantity'] > 0): ?>
                                <div class="mt-3 flex justify-between text-sm bg-white-light/30 rounded-lg px-3 py-2">

                                    <span>
                                        <?= e($line['box_option_name']); ?> × <?= (int) $line['box_quantity']; ?>
                                    </span>

                                    <span>
                                        <?= e(money($boxTotal)); ?>
                                    </span>

                                </div>
                            <?php endif; ?>

                            <!-- TOTAL -->
                            <div class="mt-3 flex justify-between border-t pt-2 text-sm font-medium">

                                <span>Line Total</span>
                                <span><?= e(money($productTotal + $boxTotal)); ?></span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

                <!-- COUPON -->
                <?php if ($order['coupon']): ?>
                    <div class="mt-5 text-sm text-green-600">
                        Coupon <?= e($order['coupon']['code']); ?> saved <?= e(money($order['coupon']['discount_amount'])); ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <div class="text-center text-sm text-black-light py-10">
                    Select an order to view details
                </div>

            <?php endif; ?>

        </section>

    </div>

</main>

<?php require __DIR__ . '/layout/footer.php'; ?>