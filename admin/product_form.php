<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$productModel = new ProductModel();
$categoryModel = new CategoryModel();
$product = isset($_GET['id']) ? $productModel->find((int) $_GET['id']) : null;
$errorMessage = null;

if (is_post()) {
    verify_csrf();

    try {
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
        $existingPrimaryImage = trim((string) ($_POST['existing_image'] ?? ''));
        $primaryImage = request_uploaded_image(
            'primary_image_upload',
            'products',
            $existingPrimaryImage !== '' ? $existingPrimaryImage : null
        );

        $existingGallery = array_values(array_filter(array_map(
            static fn(string $path): string => trim($path),
            (array) ($_POST['existing_gallery'] ?? [])
        ), static fn(string $path): bool => $path !== ''));
        $uploadedGallery = request_uploaded_images('gallery_uploads', 'products');
        $gallery = array_values(array_unique(array_merge($existingGallery, $uploadedGallery)));

        if ($primaryImage !== null && $primaryImage !== '') {
            $gallery = array_values(array_filter($gallery, static fn(string $path): bool => $path !== $primaryImage));
            array_unshift($gallery, $primaryImage);
        }

        $savedId = $productModel->save([
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'price' => (float) ($_POST['price'] ?? 0),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'image' => $primaryImage,
            'is_active' => !empty($_POST['is_active']) ? 1 : 0,
            'gallery' => $gallery,
        ], $id);

        set_flash('success', 'Product saved.');
        redirect('admin/product_form.php?id=' . $savedId);
    } catch (Throwable $exception) {
        $errorMessage = $exception->getMessage();
        $fallbackImage = trim((string) ($_POST['existing_image'] ?? ''));
        $fallbackGallery = array_values(array_filter(array_map('trim', (array) ($_POST['existing_gallery'] ?? []))));

        $product = [
            'id' => (int) ($_POST['id'] ?? 0),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'price' => (string) ($_POST['price'] ?? '0'),
            'stock' => (string) ($_POST['stock'] ?? '0'),
            'image' => $fallbackImage,
            'is_active' => !empty($_POST['is_active']) ? 1 : 0,
            'images' => array_map(static fn(string $path): array => ['image_url' => $path], array_values(array_unique($fallbackGallery))),
        ];
    }
}

$categories = $categoryModel->all();
$galleryImages = [];
if (!empty($product['images']) && is_array($product['images'])) {
    foreach ($product['images'] as $image) {
        $path = trim((string) ($image['image_url'] ?? ''));
        if ($path !== '') {
            $galleryImages[] = $path;
        }
    }
}
$galleryImages = array_values(array_unique($galleryImages));

$adminPageTitle = 'Product Form';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold"><?= $product ? 'Edit product' : 'Add product'; ?></h1>
    <?php if ($errorMessage): ?>
        <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($errorMessage); ?></div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data" class="mt-6 grid gap-4 lg:grid-cols-2">
        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
        <input type="hidden" name="id" value="<?= (int) ($product['id'] ?? 0); ?>">
        <input type="hidden" name="existing_image" value="<?= e((string) ($product['image'] ?? '')); ?>">

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Product name</label>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="name" value="<?= e((string) ($product['name'] ?? '')); ?>" placeholder="Product name" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
            <select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="category_id">
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id']; ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : ''; ?>><?= e($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Price</label>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="number" step="0.01" min="0" name="price" value="<?= e((string) ($product['price'] ?? '0')); ?>" placeholder="Price" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Stock</label>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="number" min="0" name="stock" value="<?= e((string) ($product['stock'] ?? '0')); ?>" placeholder="Stock" required>
        </div>

        <div class="rounded-2xl border border-slate-200 p-4 lg:col-span-2">
            <div class="grid gap-4 lg:grid-cols-[1fr,260px]">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Upload primary image</label>
                    <input class="block w-full rounded-2xl border border-slate-200 px-4 py-3" type="file" name="primary_image_upload" accept="image/*">
                </div>
                <div>
                    <div class="mb-2 text-sm font-semibold text-slate-700">Current image</div>
                    <img src="<?= e(upload_url((string) ($product['image'] ?? ''))); ?>" alt="<?= e((string) ($product['name'] ?? 'Product')); ?>" class="h-44 w-full rounded-2xl border border-slate-200 object-contain">
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Description</label>
            <textarea class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="description" rows="5" placeholder="Description"><?= e((string) ($product['description'] ?? '')); ?></textarea>
        </div>

        <div class="rounded-2xl border border-slate-200 p-4 lg:col-span-2">
            <div class="font-semibold text-slate-900">Gallery images</div>
            <p class="mt-1 text-sm text-slate-500">Keep checked items to retain them and upload new gallery images below.</p>

            <?php if ($galleryImages !== []): ?>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <?php foreach ($galleryImages as $imagePath): ?>
                        <label class="rounded-2xl border border-slate-200 p-3">
                            <img src="<?= e(upload_url($imagePath)); ?>" alt="Gallery image" class="h-32 rounded-xl object-conatin">
                            <span class="mt-3 flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="existing_gallery[]" value="<?= e($imagePath); ?>" checked>
                                Keep this image
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Upload gallery images</label>
                <input class="block w-full rounded-2xl border border-slate-200 px-4 py-3" type="file" name="gallery_uploads[]" accept="image/*" multiple>
            </div>
        </div>

        <div class="rounded-2xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm text-sky-700 lg:col-span-2">
            Box options are now managed from the shared <a class="font-semibold underline" href="<?= e(app_url('admin/box_options.php')); ?>">Box Options</a> page.
        </div>

        <label class="flex items-center gap-2 text-sm lg:col-span-2"><input type="checkbox" name="is_active" value="1" <?= !isset($product['is_active']) || (int) $product['is_active'] === 1 ? 'checked' : ''; ?>> Active</label>
        <button class="rounded-full  bg-primary-medium hover:bg-primary-medium/90 px-5 py-3 font-semibold text-white-dark lg:col-span-2" type="submit">Save product</button>
    </form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
