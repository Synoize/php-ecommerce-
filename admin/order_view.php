<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$orderModel = new OrderModel();

if (is_post()) {
    verify_csrf();
    $orderModel->updateStatus((int) $_POST['order_id'], (string) $_POST['status'], (string) $_POST['payment_status']);
    set_flash('success', 'Order status updated.');
    redirect('admin/order_view.php?id=' . (int) $_POST['order_id']);
}

$order = $orderModel->findAdmin((int) ($_GET['id'] ?? 0));
if (!$order) {
    exit('Order not found');
}

$adminPageTitle = 'Order View';
require __DIR__ . '/partials/header.php';
?>
<div class="grid gap-6 lg:grid-cols-[1fr,360px]">
    <div class="rounded-3xl bg-white p-6 shadow">
        <h1 class="text-2xl font-bold">Order #<?= (int) $order['id']; ?></h1>
        <?php $trackingSteps = order_tracking_steps((string) $order['status']); ?>
        <div class="mt-6 rounded-3xl bg-slate-50 p-5">
            <div class="text-sm font-semibold text-slate-700">Customer-facing status</div>
            <?php if ((string) $order['status'] === 'cancelled'): ?>
                <p class="mt-3 text-sm text-rose-600">The order is marked as cancelled.</p>
            <?php else: ?>
                <div class="mt-4 grid gap-4 md:grid-cols-4">
                    <?php foreach ($trackingSteps as $step): ?>
                        <div class="rounded-2xl border px-4 py-3 <?= $step['complete'] ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white'; ?>">
                            <div class="text-xs uppercase tracking-[0.2em] <?= $step['current'] ? 'text-sky-600' : 'text-slate-400'; ?>"><?= $step['key']; ?></div>
                            <div class="mt-2 text-sm font-semibold text-slate-800"><?= e($step['label']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-6 space-y-4">
            <?php foreach ($order['items'] as $item): ?>
                <?php $productTotal = (float) $item['price'] * (int) $item['quantity']; ?>
                <?php $boxTotal = (float) ($item['box_option_price'] ?? 0) * (int) ($item['box_quantity'] ?? 0); ?>
                <div class="rounded-2xl border border-slate-100 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="font-semibold"><?= e($item['name']); ?></div>
                            <div class="text-sm text-slate-500">Qty <?= (int) $item['quantity']; ?></div>
                        </div>
                        <div class="font-semibold text-sky-600"><?= e(money($productTotal)); ?></div>
                    </div>
                    <?php if (!empty($item['box_option_name']) && (int) $item['box_quantity'] > 0): ?>
                        <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <div><?= e((string) $item['box_option_name']); ?> x <?= (int) $item['box_quantity']; ?></div>
                            <div><?= e(money($boxTotal)); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-sm font-semibold text-slate-700">
                        <span>Line total</span>
                        <span><?= e(money($productTotal + $boxTotal)); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <aside class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Customer</div>
        <div class="mt-2 font-semibold"><?= e($order['user_name']); ?></div>
        <div class="mt-1 text-sm text-slate-500"><?= e($order['email']); ?></div>
        <form action="" method="post" class="mt-6 space-y-3">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <input type="hidden" name="order_id" value="<?= (int) $order['id']; ?>">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Order status</label>
                <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                    <?php foreach (['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                        <option value="<?= e($status); ?>" <?= $order['status'] === $status ? 'selected' : ''; ?>><?= e(ucfirst($status)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Payment status</label>
                <select name="payment_status" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                    <?php foreach (['pending', 'paid', 'failed'] as $paymentStatus): ?>
                        <option value="<?= e($paymentStatus); ?>" <?= $order['payment_status'] === $paymentStatus ? 'selected' : ''; ?>><?= e(ucfirst($paymentStatus)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="w-full rounded-full bg-primary-medium hover:bg-primary-medium/90 px-5 py-3 font-semibold text-white-dark" type="submit">Update order</button>
        </form>
        <div class="mt-6 text-sm text-slate-500">Shipping</div>
        <div class="mt-2 text-sm"><?= e((string) $order['address_line']); ?>, <?= e((string) $order['city']); ?>, <?= e((string) $order['state']); ?> - <?= e((string) $order['pincode']); ?></div>
        <div class="mt-6 text-sm text-slate-500">Payment</div>
        <div class="mt-2 text-sm"><?= e((string) $order['payment_method']); ?> / <?= e((string) $order['payment_status']); ?></div>
        <div class="mt-6 text-sm text-slate-500">Phone</div>
        <div class="mt-2 text-sm"><?= e((string) $order['phone']); ?></div>
        <?php if (!empty($order['coupon'])): ?>
            <div class="mt-6 text-sm text-slate-500">Coupon</div>
            <div class="mt-2 text-sm"><?= e((string) $order['coupon']['code']); ?> saved <?= e(money((float) $order['coupon']['discount_amount'])); ?></div>
        <?php endif; ?>
        <div class="mt-6 text-sm text-slate-500">Order total</div>
        <div class="mt-2 text-lg font-semibold"><?= e(money((float) $order['total_amount'])); ?></div>
    </aside>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
