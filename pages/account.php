<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$addresses = (new AddressModel())->forUser((int) current_user()['id']);
$orders = (new OrderModel())->forUser((int) current_user()['id']);
$pageTitle = 'My Account';
require __DIR__ . '/layout/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <div class="grid gap-8 lg:grid-cols-[320px,1fr]">
        <aside class="rounded-[2rem] bg-white p-6 shadow-soft">
            <div class="font-display text-2xl font-bold"><?= e((string) current_user()['name']); ?></div>
            <div class="mt-2 text-sm text-slate-500"><?= e((string) current_user()['email']); ?></div>
            <div class="mt-6 space-y-2 text-sm">
                <a class="block rounded-2xl bg-slate-100 px-4 py-3" href="<?= e(app_url('user/orders.php')); ?>">Order history</a>
                <a class="block rounded-2xl bg-slate-100 px-4 py-3" href="<?= e(app_url('checkout.php')); ?>">Checkout</a>
                <a class="block rounded-2xl bg-slate-100 px-4 py-3" href="<?= e(app_url('wishlist.php')); ?>">Wishlist</a>
            </div>
        </aside>
        <section class="space-y-8">
            <div class="rounded-[2rem] bg-white p-6 shadow-soft">
                <h2 class="font-display text-2xl font-bold">Saved addresses</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <?php foreach ($addresses as $address): ?>
                        <div class="rounded-3xl border border-slate-100 p-4">
                            <div class="font-semibold"><?= e($address['full_name']); ?></div>
                            <p class="mt-2 text-sm text-slate-500"><?= e($address['address_line']); ?>, <?= e($address['city']); ?>, <?= e($address['state']); ?> - <?= e($address['pincode']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-soft">
                <h2 class="font-display text-2xl font-bold">Recent orders</h2>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500"><th class="pb-3">Order</th><th class="pb-3">Date</th><th class="pb-3">Status</th><th class="pb-3">Total</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                            <tr class="border-t border-slate-100">
                                <td class="py-3"><a class="font-semibold text-brand-600" href="<?= e(app_url('user/orders.php?order_id=' . (int) $order['id'])); ?>">#<?= (int) $order['id']; ?></a></td>
                                <td class="py-3"><?= e((string) $order['created_at']); ?></td>
                                <td class="py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold <?= order_status_badge((string) $order['status']); ?>"><?= e($order['status']); ?></span></td>
                                <td class="py-3"><?= e(money((float) $order['total_amount'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>

