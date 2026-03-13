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
<main class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="font-display text-4xl font-bold">Order history</h1>
    <div class="mt-8 grid gap-8 lg:grid-cols-[380px,1fr]">
        <section class="rounded-[2rem] bg-white p-6 shadow-soft">
            <div class="space-y-4">
                <?php foreach ($orders as $item): ?>
                    <a href="<?= e(app_url('user/orders.php?order_id=' . (int) $item['id'])); ?>" class="block rounded-3xl border border-slate-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold">#<?= (int) $item['id']; ?></div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= order_status_badge((string) $item['status']); ?>"><?= e($item['status']); ?></span>
                        </div>
                        <div class="mt-2 text-sm text-slate-500"><?= e((string) $item['created_at']); ?></div>
                        <div class="mt-2 font-semibold text-brand-600"><?= e(money((float) $item['total_amount'])); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="rounded-[2rem] bg-white p-6 shadow-soft">
            <?php if ($order): ?>
                <?php $trackingSteps = order_tracking_steps((string) $order['status']); ?>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-display text-2xl font-bold">Order #<?= (int) $order['id']; ?></h2>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?= order_status_badge((string) $order['status']); ?>"><?= e($order['status']); ?></span>
                </div>
                <div class="mt-6 grid gap-3 text-sm text-slate-600">
                    <div>Payment: <?= e((string) $order['payment_method']); ?> / <?= e((string) $order['payment_status']); ?></div>
                    <div>Address: <?= e((string) $order['address_line']); ?>, <?= e((string) $order['city']); ?>, <?= e((string) $order['state']); ?></div>
                </div>
                <div class="mt-6 rounded-3xl bg-slate-50 p-5">
                    <div class="text-sm font-semibold text-slate-700">Order tracking</div>
                    <?php if ((string) $order['status'] === 'cancelled'): ?>
                        <p class="mt-3 text-sm text-rose-600">This order has been cancelled by the store.</p>
                    <?php else: ?>
                        <div class="mt-4 grid gap-4 md:grid-cols-4">
                            <?php foreach ($trackingSteps as $step): ?>
                                <div class="rounded-2xl border px-4 py-3 <?= $step['complete'] ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white'; ?>">
                                    <div class="text-xs uppercase tracking-[0.2em] <?= $step['current'] ? 'text-brand-600' : 'text-slate-400'; ?>"><?= $step['key']; ?></div>
                                    <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($step['label']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mt-6 space-y-4">
                    <?php foreach ($order['items'] as $line): ?>
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-4">
                            <div>
                                <div class="font-semibold"><?= e($line['name']); ?></div>
                                <div class="mt-1 text-sm text-slate-500">Qty <?= (int) $line['quantity']; ?></div>
                            </div>
                            <div class="font-semibold text-brand-600"><?= e(money((float) $line['price'] * (int) $line['quantity'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($order['coupon']): ?>
                    <div class="mt-6 text-sm text-slate-600">Coupon <?= e((string) $order['coupon']['code']); ?> saved <?= e(money((float) $order['coupon']['discount_amount'])); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-slate-500">Select an order to view its details.</p>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
