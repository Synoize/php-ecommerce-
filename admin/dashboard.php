<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$stats = (new AdminController())->dashboard();
$recentOrders = (new OrderModel())->all();
$adminPageTitle = 'Admin Dashboard';
require __DIR__ . '/partials/header.php';
?>
<div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-lg shadow bg-white-light/20 p-6">
        <div class="text-sm text-black-light">Total Revenue</div>
        <div class="mt-2 text-3xl font-bold"><?= e(money((float) $stats['orders']['revenue'])); ?></div>
    </div>
    <div class="rounded-lg shadow bg-white-light/20 p-6">
        <div class="text-sm text-black-light">Orders</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['orders']['orders']; ?></div>
    </div>
    <div class="rounded-lg shadow bg-white-light/20 p-6">
        <div class="text-sm text-black-light">Pending Orders</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['orders']['pending_orders']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Products</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['products']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Users</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['users']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Categories</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['categories']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Slides</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['slides']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Reviews</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['reviews']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Payments</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['payments']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Addresses</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['addresses']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Cart Rows</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['carts']; ?></div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="text-sm text-slate-500">Wishlist Rows</div>
        <div class="mt-2 text-3xl font-bold"><?= (int) $stats['wishlists']; ?></div>
    </div>
</div>
<div class="mt-8 grid gap-6 xl:grid-cols-[1.4fr,1fr]">
    <div class="rounded-3xl bg-white p-6 shadow">
        <h2 class="text-xl font-bold">Recent orders</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="pb-3">Order</th>
                        <th class="pb-3">Customer</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($recentOrders, 0, 10) as $order): ?>
                        <tr class="border-t border-slate-100">
                            <td class="py-3"><a class="font-semibold text-sky-600" href="<?= e(app_url('admin/order_view.php?id=' . (int) $order['id'])); ?>">#<?= (int) $order['id']; ?></a></td>
                            <td class="py-3"><?= e($order['user_name']); ?></td>
                            <td class="py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold <?= order_status_badge((string) $order['status']); ?>">
                                    <?= e($order['status']); ?>
                                </span></td>
                            <td class="py-3"><?= e(money((float) $order['total_amount'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <h2 class="text-xl font-bold">Quick management</h2>
        <div class="mt-4 grid gap-3 text-sm">
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/product_form.php')); ?>">Add product</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/categories_page.php')); ?>">Manage categories</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/slides.php')); ?>">Manage homepage slides</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/payments.php')); ?>">View payments</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/addresses.php')); ?>">View addresses</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/carts.php')); ?>">View cart activity</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/wishlists.php')); ?>">View wishlist activity</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/reviews_page.php')); ?>">Moderate reviews</a>
            <a class="rounded-2xl border border-slate-200 px-4 py-3 hover:bg-slate-50" href="<?= e(app_url('admin/manage_users.php')); ?>">Manage user roles</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>