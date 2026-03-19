<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$items = (new WishlistModel())->adminAll();
$adminPageTitle = 'Manage Wishlists';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Wishlist Activity</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Customer</th><th class="pb-3">Product</th><th class="pb-3">Price</th><th class="pb-3">Stock</th><th class="pb-3">Saved At</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr class="border-t border-slate-100 align-top">
                    <td class="py-3">
                        <div class="font-semibold"><?= e($item['user_name']); ?></div>
                        <div class="text-xs text-slate-500"><?= e($item['email']); ?></div>
                    </td>
                    <td class="py-3"><?= e($item['product_name']); ?></td>
                    <td class="py-3"><?= e(money((float) $item['price'])); ?></td>
                    <td class="py-3"><?= (int) $item['stock']; ?></td>
                    <td class="py-3"><?= e((string) $item['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
