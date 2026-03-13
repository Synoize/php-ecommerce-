<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$products = (new ProductModel())->adminAll();
$adminPageTitle = 'Manage Products';
require __DIR__ . '/partials/header.php';
?>
<div class="mb-6 flex items-center justify-between gap-4">
    <h1 class="text-3xl font-bold">Products</h1>
    <a href="<?= e(app_url('admin/product_form.php')); ?>" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Add product</a>
</div>
<div class="rounded-3xl bg-white p-6 shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Product</th><th class="pb-3">Category</th><th class="pb-3">Price</th><th class="pb-3">Stock</th><th class="pb-3">Action</th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><?= e($product['name']); ?></td>
                    <td class="py-3"><?= e((string) $product['category_name']); ?></td>
                    <td class="py-3"><?= e(money((float) $product['price'])); ?></td>
                    <td class="py-3"><?= (int) $product['stock']; ?></td>
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

