<?php
require_once __DIR__ . '/../config/bootstrap.php';

$categories = (new CategoryModel())->all();
$pageTitle = 'Watch Categories';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-7xl px-4 py-12">
    <h1 class="text-2xl md:text-3xl font-bold">Watch categories</h1>
    <p class="mt-3 max-w-2xl text-slate-600">Browse category-based collections and jump straight into filtered product listings.</p>
    <div class="mt-10 grid gap-6 md:grid-cols-3 xl:grid-cols-4">
        <?php foreach ($categories as $category): ?>
            <a href="<?= e(app_url('shop.php?category=' . (int) $category['id'])); ?>" class="overflow-hidden rounded-3xl bg-white shadow-soft">
                <img src="<?= e(upload_url((string) $category['image'])); ?>" alt="<?= e($category['name']); ?>" class="h-56 w-full object-cover" loading="lazy">
                <div class="p-5">
                    <div class="font-semibold"><?= e($category['name']); ?></div>
                    <div class="mt-2 text-sm text-slate-500"><?= (int) $category['product_count']; ?> products</div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
