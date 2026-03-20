<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$user = current_user();
$orderModel = new OrderModel();
$orderId = (int) ($_GET['order_id'] ?? 0);
$order = $orderId > 0 ? $orderModel->findForUser((int) $user['id'], $orderId) : null;
$orders = $orderModel->forUser((int) $user['id']);

$pageTitle = 'My Orders';
require __DIR__ . '/layout/header.php';
?>

<main class="mt-28 mx-auto max-w-7xl px-4 py-8">

    <!-- HEADER -->
    <h1 class="text-2xl font-bold text-black-medium md:text-3xl">
        My Orders
    </h1>

    <div class="mt-8 min-h-[calc(100vh-262px)] grid gap-6 lg:grid-cols-[320px,1fr]">

        <!-- LEFT: ORDER LIST -->
        <section class="space-y-4">

            <?php if (!empty($orders)): ?>

                <?php foreach ($orders as $item): ?>

                    <a href="<?= e(app_url('user/orders.php?order_id=' . (int) $item['id'])); ?>"
                        class="block rounded-xl border p-4 bg-white-light/10 hover:bg-white-light/20 transition">

                        <div class="flex justify-between items-center">

                            <span class="text-xs font-semibold text-black-medium">
                               #ORD-<?= strtoupper(bin2hex(random_bytes(3))); ?>-<?= (int)$item['id']; ?>
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

                <!-- EMPTY STATE -->
                <div class="flex flex-col items-center justify-center py-16 text-center">

                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-white-light/40 mb-4">
                        <i data-lucide="package" class="w-8 h-8 text-white-medium"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-black-medium">
                        No Orders Yet
                    </h3>

                    <p class="text-sm text-black-light mt-1">
                        You haven't placed any orders yet.
                    </p>

                    <a href="<?= e(app_url('shop.php')); ?>"
                        class="mt-5 inline-flex items-center gap-2 rounded-full bg-primary-medium px-6 py-3 text-sm font-semibold text-white-dark hover:bg-primary-medium/90 transition">
                        Start Shopping
                    </a>

                </div>

            <?php endif; ?>

        </section>

        <!-- RIGHT: ORDER DETAILS -->
        <section class="md:sticky md:top-28 md:h-fit rounded-xl border bg-white-light/10 p-5">

            <?php if ($order && !empty($orders)): ?>

                <?php $trackingSteps = order_tracking_steps((string) $order['status']); ?>

                <!-- HEADER -->
                <div class="flex justify-between items-center">

                    <h2 class="text-sm md:text-lg font-semibold text-black-medium">
                        Order No. #ORD-<?= strtoupper(bin2hex(random_bytes(3))); ?>-<?= (int)$order['id']; ?>
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

                <!-- COD MESSAGE -->
                <?php if ((string) $order['payment_method'] === 'cod' && (string) $order['status'] !== 'cancelled'): ?>
                    <div class="mt-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        COD booking confirmed after advance payment of <?= e(money((float) COD_BOOKING_AMOUNT)); ?>.
                    </div>
                <?php endif; ?>

                <!-- TRACKING -->
                <div class="mt-6">

                    <h3 class="text-sm font-semibold text-black-medium mb-4">
                        Order Tracking
                    </h3>

                    <?php if ($order['status'] === 'cancelled'): ?>

                        <div class="text-sm text-red-600">
                            This order has been cancelled.
                        </div>

                    <?php else: ?>

                        <?php
                        $totalSteps = count($trackingSteps);
                        $completedSteps = count(array_filter($trackingSteps, fn($s) => $s['complete']));
                        $progressPercent = ($completedSteps - 1) / max(1, ($totalSteps - 1)) * 100;
                        ?>

                        <div class="relative">

                            <!-- BASE LINE -->
                            <div class="absolute top-4 left-0 w-full h-[3px] bg-gray-200 rounded"></div>

                            <!-- PROGRESS -->
                            <div id="progress-bar"
                                class="absolute top-4 left-0 h-[3px] bg-green-500 rounded transition-all duration-700"
                                style="width: 0%">
                            </div>

                            <!-- STEPS -->
                            <div class="relative flex justify-between">

                                <?php foreach ($trackingSteps as $step): ?>

                                    <div class="flex flex-col items-center text-center w-full">

                                        <div class="w-8 h-8 flex items-center justify-center rounded-full border-2
                                            <?= $step['complete'] ? 'bg-green-500 border-green-500 text-white' : '' ?>
                                            <?= $step['current'] ? 'border-primary-medium text-primary-medium bg-white' : '' ?>
                                            <?= (!$step['complete'] && !$step['current']) ? 'border-gray-300 text-gray-400 bg-white' : '' ?>">

                                            <?= $step['complete'] ? '✔' : ($step['current'] ? '●' : '○'); ?>

                                        </div>

                                        <div class="mt-2 text-xs <?= $step['current'] ? 'text-primary-medium' : 'text-gray-500'; ?>">
                                            <?= e($step['label']); ?>
                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

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

                            <div class="flex justify-between">

                                <div>
                                    <div class="font-semibold text-black-medium">
                                        <?= e($line['name']); ?>
                                    </div>
                                    <div class="text-xs text-black-light">
                                        Qty: <?= (int) $line['quantity']; ?>
                                    </div>
                                </div>

                                <div class="font-semibold text-primary-medium">
                                    <?= e(money($productTotal)); ?>
                                </div>

                            </div>

                            <?php if (!empty($line['box_option_name']) && (int) $line['box_quantity'] > 0): ?>
                                <div class="mt-3 flex justify-between text-sm bg-white-light/30 rounded-lg px-3 py-2">
                                    <span><?= e($line['box_option_name']); ?> × <?= (int) $line['box_quantity']; ?></span>
                                    <span><?= e(money($boxTotal)); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3 flex justify-between border-t pt-2 text-sm font-medium">
                                <span>Total</span>
                                <span><?= e(money($productTotal + $boxTotal)); ?></span>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <!-- RIGHT EMPTY -->
                <div class="flex flex-col items-center justify-center py-16 text-center">

                    <i data-lucide="shopping-cart" class="w-10 h-10 text-white-medium mb-3"></i>

                    <p class="text-sm text-black-light">
                        Select an order to view details
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>

<!-- TRACKING ANIMATION -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const bar = document.getElementById("progress-bar");
        if (!bar) return;

        const percent = <?= isset($progressPercent) ? $progressPercent : 0 ?>;

        setTimeout(() => {
            bar.style.width = percent + "%";
        }, 200);
    });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>