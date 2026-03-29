<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$products = (new ProductModel())->adminAll();
$adminPageTitle = 'Manage Products';
require __DIR__ . '/partials/header.php';
?>
<div class="mb-6 flex items-center justify-between gap-4">
    <h1 class="text-3xl font-bold">Products</h1>
    <a href="<?= e(app_url('admin/product_form.php')); ?>" class="rounded-full  bg-primary-medium hover:bg-primary-medium/90 px-5 py-3 text-sm font-semibold text-white-dark">Add product</a>
</div>
<div class="rounded-3xl bg-white p-6 shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Product</th><th class="pb-3">Category</th><th class="pb-3">Price</th><th class="pb-3">Stock</th><th class="pb-3">Media</th><th class="pb-3">Status</th><th class="pb-3">Action</th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3">
                        <div class="font-semibold"><?= e($product['name']); ?></div>
                        <div class="text-xs text-slate-500"><?= (int) $product['box_count']; ?> box option(s)</div>
                    </td>
                    <td class="py-3"><?= e((string) $product['category_name']); ?></td>
                    <td class="py-3"><?= e(money((float) $product['price'])); ?></td>
                    <td class="py-3"><?= (int) $product['stock']; ?></td>
                    <td class="py-3 text-slate-500"><?= (int) $product['image_count']; ?> gallery image(s)</td>
                    <td class="py-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold <?= (int) $product['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'; ?>">
                            <?= (int) $product['is_active'] === 1 ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td class="py-3">
                        <a class="text-sky-600" href="<?= e(app_url('admin/product_form.php?id=' . (int) $product['id'])); ?>">Edit</a>
                        <a class="ml-3 text-rose-600" href="<?= e(app_url('admin/product_delete.php?id=' . (int) $product['id'])); ?>" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
