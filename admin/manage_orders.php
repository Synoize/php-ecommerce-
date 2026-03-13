<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$orderModel = new OrderModel();

if (is_post()) {
    verify_csrf();
    $orderModel->updateStatus((int) $_POST['order_id'], (string) $_POST['status'], (string) $_POST['payment_status']);
    set_flash('success', 'Order updated.');
    redirect('admin/manage_orders.php');
}

$orders = $orderModel->all();
$adminPageTitle = 'Manage Orders';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Orders</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Order</th><th class="pb-3">Customer</th><th class="pb-3">Status</th><th class="pb-3">Payment</th><th class="pb-3">Total</th><th class="pb-3">Action</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><a class="text-sky-600" href="<?= e(app_url('admin/order_view.php?id=' . (int) $order['id'])); ?>">#<?= (int) $order['id']; ?></a></td>
                    <td class="py-3"><?= e($order['user_name']); ?></td>
                    <td class="py-3"><?= e($order['status']); ?></td>
                    <td class="py-3"><?= e($order['payment_status']); ?></td>
                    <td class="py-3"><?= e(money((float) $order['total_amount'])); ?></td>
                    <td class="py-3">
                        <form action="" method="post" class="flex flex-wrap gap-2">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="order_id" value="<?= (int) $order['id']; ?>">
                            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2">
                                <?php foreach (['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                                    <option value="<?= e($status); ?>" <?= $order['status'] === $status ? 'selected' : ''; ?>><?= e(ucfirst($status)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="payment_status" class="rounded-xl border border-slate-200 px-3 py-2">
                                <?php foreach (['pending', 'paid', 'failed'] as $paymentStatus): ?>
                                    <option value="<?= e($paymentStatus); ?>" <?= $order['payment_status'] === $paymentStatus ? 'selected' : ''; ?>><?= e(ucfirst($paymentStatus)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="rounded-xl bg-slate-900 px-4 py-2 text-white" type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

