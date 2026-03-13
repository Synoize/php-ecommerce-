<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$productModel = new ProductModel();
$categoryModel = new CategoryModel();
$product = isset($_GET['id']) ? $productModel->find((int) $_GET['id']) : null;

if (is_post()) {
    verify_csrf();
    $gallery = preg_split('/\r\n|\r|\n/', trim((string) ($_POST['gallery'] ?? ''))) ?: [];
    $id = $productModel->save([
        'name' => trim((string) $_POST['name']),
        'description' => trim((string) $_POST['description']),
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'price' => (float) ($_POST['price'] ?? 0),
        'stock' => (int) ($_POST['stock'] ?? 0),
        'image' => trim((string) ($_POST['image'] ?? '')),
        'is_active' => !empty($_POST['is_active']) ? 1 : 0,
        'gallery' => $gallery,
    ], isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null);

    set_flash('success', 'Product saved.');
    redirect('admin/product_form.php?id=' . $id);
}

$categories = $categoryModel->all();
$adminPageTitle = 'Product Form';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold"><?= $product ? 'Edit product' : 'Add product'; ?></h1>
    <form action="" method="post" class="mt-6 grid gap-4 lg:grid-cols-2">
        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
        <input type="hidden" name="id" value="<?= (int) ($product['id'] ?? 0); ?>">
        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="name" value="<?= e((string) ($product['name'] ?? '')); ?>" placeholder="Product name" required>
        <select class="rounded-2xl border border-slate-200 px-4 py-3" name="category_id">
            <option value="">Select category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id']; ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : ''; ?>><?= e($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="number" step="0.01" name="price" value="<?= e((string) ($product['price'] ?? '0')); ?>" placeholder="Price" required>
        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="number" name="stock" value="<?= e((string) ($product['stock'] ?? '0')); ?>" placeholder="Stock" required>
        <input class="rounded-2xl border border-slate-200 px-4 py-3 lg:col-span-2" type="text" name="image" value="<?= e((string) ($product['image'] ?? '')); ?>" placeholder="Primary image URL or local asset path">
        <textarea class="rounded-2xl border border-slate-200 px-4 py-3 lg:col-span-2" name="description" rows="5" placeholder="Description"><?= e((string) ($product['description'] ?? '')); ?></textarea>
        <textarea class="rounded-2xl border border-slate-200 px-4 py-3 lg:col-span-2" name="gallery" rows="5" placeholder="One gallery image URL per line"><?php
            if (!empty($product['images'])) {
                echo e(implode(PHP_EOL, array_map(static fn(array $image): string => (string) $image['image_url'], $product['images'])));
            }
        ?></textarea>
        <label class="flex items-center gap-2 text-sm lg:col-span-2"><input type="checkbox" name="is_active" value="1" <?= !isset($product['is_active']) || (int) $product['is_active'] === 1 ? 'checked' : ''; ?>> Active</label>
        <button class="rounded-full bg-slate-900 px-5 py-3 font-semibold text-white lg:col-span-2" type="submit">Save product</button>
    </form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

