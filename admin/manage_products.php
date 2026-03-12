<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
?>

<div class="flex items-center justify-between gap-3">
  <h1 class="text-xl font-semibold text-gray-900">Manage Products</h1>
  <a class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-hover" href="<?php echo e(BASE_URL); ?>/admin/product_form.php">Add Product</a>
</div>

<div class="mt-4 overflow-hidden rounded-2xl border bg-white shadow-soft">
  <div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
        <tr>
          <th class="px-4 py-3">Image</th>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Category</th>
          <th class="px-4 py-3">Price</th>
          <th class="px-4 py-3">Stock</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($products as $p): ?>
          <tr>
            <td class="px-4 py-3"><img class="h-14 w-[72px] rounded-xl object-cover" src="<?php echo e(uploadUrlOrLocal((string)$p['image'])); ?>" alt="img" /></td>
            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($p['name']); ?></td>
            <td class="px-4 py-3 text-gray-700"><?php echo e($p['category_name'] ?? '-'); ?></td>
            <td class="px-4 py-3 text-gray-900">₹<?php echo e(number_format((float)$p['price'],2)); ?></td>
            <td class="px-4 py-3 text-gray-900"><?php echo (int)$p['stock']; ?></td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex gap-2">
                <a class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50" href="<?php echo e(BASE_URL); ?>/admin/product_form.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
                <a class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100" href="<?php echo e(BASE_URL); ?>/admin/product_delete.php?id=<?php echo (int)$p['id']; ?>" onclick="return confirm('Delete product?')">Delete</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (count($products) === 0): ?>
          <tr><td colspan="6" class="px-4 py-6 text-sm text-gray-600">No products.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
