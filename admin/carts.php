<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$items = (new CartModel())->adminAll();
$adminPageTitle = 'Manage Carts';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Cart Activity</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Customer</th><th class="pb-3">Product</th><th class="pb-3">Quantity</th><th class="pb-3">Box Option</th><th class="pb-3">Line Total</th><th class="pb-3">Added</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr class="border-t border-slate-100 align-top">
                    <td class="py-3">
                        <div class="font-semibold"><?= e($item['user_name']); ?></div>
                        <div class="text-xs text-slate-500"><?= e($item['email']); ?></div>
                    </td>
                    <td class="py-3"><?= e($item['product_name']); ?></td>
                    <td class="py-3"><?= (int) $item['quantity']; ?></td>
                    <td class="py-3">
                        <?php if (!empty($item['box_name']) && (int) $item['box_quantity'] > 0): ?>
                            <?= e($item['box_name']); ?> x <?= (int) $item['box_quantity']; ?>
                        <?php else: ?>
                            None
                        <?php endif; ?>
                    </td>
                    <td class="py-3"><?= e(money((float) $item['line_total'])); ?></td>
                    <td class="py-3"><?= e((string) $item['added_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
