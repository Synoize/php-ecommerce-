<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$addresses = (new AddressModel())->forUser((int) current_user()['id']);
$orders = (new OrderModel())->forUser((int) current_user()['id']);
$pageTitle = 'My Account';
require __DIR__ . '/layout/header.php';
?>

<main class="mt-28 mx-auto max-w-7xl px-4 py-8">

    <!-- HEADER -->
    <h1 class="text-2xl md:text-3xl font-bold text-black-medium mb-6">
        My Account
    </h1>
    

    <div class="min-h-[calc(100vh-238px)] grid gap-6 lg:grid-cols-[280px,1fr]">

        <!-- SIDEBAR -->
        <aside class="md:sticky top-28 md:h-fit rounded-xl border bg-white-light/10 p-5 h-fit">

            <div class="text-lg font-semibold text-black-medium">
                <?= e((string) current_user()['name']); ?>
            </div>

            <div class="text-sm text-black-light mt-1">
                <?= e((string) current_user()['email']); ?>
            </div>

            <div class="mt-5 space-y-2 text-sm text-black-light">

                <a href="<?= e(app_url('user/orders.php')); ?>"
                    class="flex items-center gap-2 rounded-lg px-4 py-2 bg-white-light/20 hover:bg-white-light/40">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Order History
                </a>

                <a href="<?= e(app_url('checkout.php')); ?>"
                    class="flex items-center gap-2 rounded-lg px-4 py-2 bg-white-light/20 hover:bg-white-light/40">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Checkout
                </a>

                <a href="<?= e(app_url('wishlist.php')); ?>"
                    class="flex items-center gap-2 rounded-lg px-4 py-2 bg-white-light/20 hover:bg-white-light/40">
                    <i data-lucide="heart" class="w-4 h-4"></i> Wishlist
                </a>

            </div>

        </aside>

        <!-- MAIN CONTENT -->
        <section class="space-y-6">

            <!-- ADDRESSES -->
            <div class="rounded-xl border bg-white-light/10 p-5">

                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-black-medium">
                        Saved Addresses
                    </h2>
                </div>

                <?php if ($addresses): ?>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">

                        <?php foreach ($addresses as $address): ?>
                            <div class="rounded-lg border p-4 bg-white-dark">

                                <div class="font-semibold text-black-medium text-sm">
                                    <?= e($address['full_name']); ?>
                                </div>

                                <p class="mt-2 text-xs text-black-light leading-5">
                                    <?= e($address['address_line']); ?>,
                                    <?= e($address['city']); ?>,
                                    <?= e($address['state']); ?> -
                                    <?= e($address['pincode']); ?>,
                                    <?= e($address['country']); ?>
                                </p>
                                <p class="mt-2 text-xs text-black-light leading-5">Phone: <?= e($address['phone']); ?></p>

                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php else: ?>

                    <div class="mt-4 text-sm text-black-light">
                        No addresses saved yet.
                    </div>

                <?php endif; ?>

            </div>

            <!-- ORDERS -->
            <div class="rounded-xl border bg-white-light/10 p-5">

                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-black-medium">
                        Recent Orders
                    </h2>

                    <a href="<?= e(app_url('user/orders.php')); ?>"
                        class="text-sm text-primary-medium hover:underline">
                        View All
                    </a>
                </div>

                <?php if ($orders): ?>

                    <div class="mt-4 overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead>
                                <tr class="text-left text-black-light border-b">
                                    <th class="pb-2">Order</th>
                                    <th class="pb-2">Date</th>
                                    <th class="pb-2">Status</th>
                                    <th class="pb-2">Total</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                    <tr class="border-b">

                                        <td class="py-3">
                                            <a href="<?= e(app_url('user/orders.php?order_id=' . (int) $order['id'])); ?>"
                                                class="font-semibold text-primary-medium hover:underline">
                                                #<?= (int) $order['id']; ?>
                                            </a>
                                        </td>

                                        <td class="py-3 text-black-light">
                                            <?= e(date('d M Y', strtotime((string) $order['created_at']))); ?>
                                        </td>

                                        <td class="py-3">
                                            <span class="px-3 py-1 text-xs rounded-full font-medium <?= order_status_badge((string) $order['status']); ?>">
                                                <?= e($order['status']); ?>
                                            </span>
                                        </td>

                                        <td class="py-3 font-semibold text-black-medium">
                                            <?= e(money((float) $order['total_amount'])); ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="mt-4 text-sm text-black-light">
                        No orders yet.
                    </div>

                <?php endif; ?>

            </div>

        </section>

    </div>

</main>

<?php require __DIR__ . '/layout/footer.php'; ?>