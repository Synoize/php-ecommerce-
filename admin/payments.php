<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$payments = (new PaymentModel())->all();
$adminPageTitle = 'Manage Payments';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Payments</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Order</th><th class="pb-3">Customer</th><th class="pb-3">Gateway</th><th class="pb-3">Transaction</th><th class="pb-3">Amount</th><th class="pb-3">Status</th><th class="pb-3">Created</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><a class="text-sky-600" href="<?= e(app_url('admin/order_view.php?id=' . (int) $payment['order_id'])); ?>">#<?= (int) $payment['order_id']; ?></a></td>
                    <td class="py-3">
                        <div><?= e($payment['user_name']); ?></div>
                        <div class="text-xs text-slate-500"><?= e($payment['email']); ?></div>
                    </td>
                    <td class="py-3"><?= e($payment['payment_gateway']); ?></td>
                    <td class="py-3"><?= e($payment['transaction_id']); ?></td>
                    <td class="py-3"><?= e(money((float) $payment['amount'])); ?></td>
                    <td class="py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold <?= order_status_badge((string) $payment['status']); ?>"><?= e($payment['status']); ?></span></td>
                    <td class="py-3"><?= e((string) $payment['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
