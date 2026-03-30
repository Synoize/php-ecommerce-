<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$stats = (new AdminController())->dashboard();
$orderStats = $stats['orders'];
$inventoryStats = $stats['inventory'];
$recentOrders = array_slice((new OrderModel())->all(), 0, 7);
$registeredUsers = array_slice((new UserModel())->all(), 0, 7);
$adminPageTitle = 'Admin Dashboard';
require __DIR__ . '/partials/header.php';
?>
<div class="space-y-8">
    <section>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-black-medium">Dashboard Overview</h1>
            <p class="mt-1 text-sm text-slate-500">Store performance and inventory information at a glance.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Total Revenue</div>
                <div class="mt-2 text-3xl font-bold text-black-medium"><?= e(money((float) $orderStats['revenue'])); ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Today Revenue</div>
                <div class="mt-2 text-3xl font-bold text-black-medium"><?= e(money((float) $orderStats['today_revenue'])); ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">All Orders</div>
                <div class="mt-2 text-3xl font-bold text-black-medium"><?= (int) $orderStats['orders']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Pending Orders</div>
                <div class="mt-2 text-3xl font-bold text-amber-600"><?= (int) $orderStats['pending_orders']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Successful Orders</div>
                <div class="mt-2 text-3xl font-bold text-emerald-600"><?= (int) $orderStats['successful_orders']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Delivered Orders</div>
                <div class="mt-2 text-3xl font-bold text-sky-600"><?= (int) $orderStats['delivered_orders']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Cancelled Orders</div>
                <div class="mt-2 text-3xl font-bold text-rose-600"><?= (int) $orderStats['cancelled_orders']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Total Products</div>
                <div class="mt-2 text-3xl font-bold text-black-medium"><?= (int) $inventoryStats['total_products']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">All Stock Units</div>
                <div class="mt-2 text-3xl font-bold text-black-medium"><?= (int) $inventoryStats['total_stock']; ?></div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-2xl font-bold text-black-medium">Inventory Status</h2>
        <div class="mt-4 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Active Products</div>
                <div class="mt-2 text-3xl font-bold text-emerald-600"><?= (int) $inventoryStats['active_products']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Inactive Products</div>
                <div class="mt-2 text-3xl font-bold text-slate-600"><?= (int) $inventoryStats['inactive_products']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Low Stock Products</div>
                <div class="mt-2 text-3xl font-bold text-amber-600"><?= (int) $inventoryStats['low_stock_products']; ?></div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="text-sm text-slate-500">Out Of Stock Products</div>
                <div class="mt-2 text-3xl font-bold text-rose-600"><?= (int) $inventoryStats['out_of_stock_products']; ?></div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg h-[60vh] overflow-hidden bg-white p-6 shadow">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-2xl font-bold text-black-medium">Latest Orders</h2>
                <a class="text-sm font-semibold text-sky-600" href="<?= e(app_url('admin/manage_orders.php')); ?>">View all</a>
            </div>
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
                        <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                            <tr class="border-t border-slate-100">
                                <td class="py-3"><a class="font-semibold text-sky-600" href="<?= e(app_url('admin/order_view.php?id=' . (int) $order['id'])); ?>">#<?= (int) $order['id']; ?></a></td>
                                <td class="py-3"><?= e($order['user_name']); ?></td>
                                <td class="py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold <?= order_status_badge((string) $order['status']); ?>"><?= e($order['status']); ?></span></td>
                                <td class="py-3"><?= e(money((float) $order['total_amount'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg h-[60vh] overflow-hidden bg-white p-6 shadow">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-2xl font-bold text-black-medium">Registered Users</h2>
                <a class="text-sm font-semibold text-sky-600" href="<?= e(app_url('admin/manage_users.php')); ?>">View all</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="pb-3">Name</th>
                            <th class="pb-3">Email</th>
                            <th class="pb-3">Role</th>
                            <th class="pb-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($registeredUsers, 0, 5) as $user): ?>
                            <tr class="border-t border-slate-100">
                                <td class="py-3 font-semibold text-black-medium"><?= e($user['name']); ?></td>
                                <td class="py-3"><?= e($user['email']); ?></td>
                                <td class="py-3"><?= e(ucfirst((string) $user['role'])); ?></td>
                                <td class="py-3"><?= e(date('d M Y', strtotime((string) $user['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
