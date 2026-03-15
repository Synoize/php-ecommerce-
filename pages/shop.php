<?php
require_once __DIR__ . '/../config/bootstrap.php';

$store = new StoreController();
$filters = [
    'query' => trim((string) ($_GET['q'] ?? '')),
    'category_id' => (int) ($_GET['category'] ?? 0),
    'sort' => (string) ($_GET['sort'] ?? ''),
    'page' => (int) ($_GET['page'] ?? 1),
    'per_page' => 8,
];
$result = $store->products->search($filters);
$categories = $store->categories->all();
$pageTitle = 'Shop Watches';
require __DIR__ . '/layout/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <div class="rounded-[2rem] bg-white p-6 shadow-soft">
        <div class="grid gap-4 lg:grid-cols-[1.2fr,1fr,1fr,auto]">
            <form action="<?= e(app_url('shop.php')); ?>" method="get" class="lg:col-span-2">
                <input type="search" name="q" value="<?= e($filters['query']); ?>" placeholder="Search watches..." class="w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3">
            </form>
            <select onchange="window.location=this.value" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-3">
                <option value="<?= e(app_url('shop.php?q=' . urlencode($filters['query']) . '&sort=' . urlencode($filters['sort']))); ?>" <?= $filters['category_id'] === 0 ? 'selected' : ''; ?>>All categories</option>
                <?php foreach ($categories as $category): ?>
                    <?php $link = app_url('shop.php?category=' . (int) $category['id'] . '&q=' . urlencode($filters['query']) . '&sort=' . urlencode($filters['sort'])); ?>
                    <option value="<?= e($link); ?>" <?= $filters['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>><?= e($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select onchange="window.location=this.value" class="rounded-full border border-slate-200 bg-slate-50 px-4 py-3">
                <?php foreach (['' => 'Newest', 'price_asc' => 'Price low to high', 'price_desc' => 'Price high to low', 'rating' => 'Top rated'] as $sortValue => $sortLabel): ?>
                    <?php $link = app_url('shop.php?category=' . (int) $filters['category_id'] . '&q=' . urlencode($filters['query']) . '&sort=' . urlencode($sortValue)); ?>
                    <option value="<?= e($link); ?>" <?= $filters['sort'] === $sortValue ? 'selected' : ''; ?>><?= e($sortLabel); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($result['items'] as $product): ?>
            <article class="overflow-hidden rounded-3xl bg-white shadow-soft">
                <a href="<?= e(product_link($product)); ?>">
                    <img src="<?= e(upload_url((string) $product['image'])); ?>" alt="<?= e($product['name']); ?>" class="h-72 w-full object-cover" loading="lazy">
                </a>
                <div class="p-5">
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?= e($product['category_name'] ?? 'Watch'); ?></div>
                    <h2 class="mt-2 text-lg font-semibold"><?= e($product['name']); ?></h2>
                    <p class="mt-2 text-sm text-slate-500"><?= number_format((float) $product['avg_rating'], 1); ?> rating • <?= (int) $product['review_count']; ?> reviews</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="font-semibold text-brand-600"><?= e(money((float) $product['price'])); ?></span>
                        <span class="rounded-full <?= (int) $product['stock'] > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?> px-3 py-1 text-xs font-semibold"><?= (int) $product['stock'] > 0 ? 'In stock' : 'Out of stock'; ?></span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <?php if ((int) $product['stock'] > 0): ?>
                            <form action="<?= e(app_url('api/cart.php')); ?>" method="post" class="flex-1">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="redirect" value="shop.php">
                                <button type="submit" class="w-full rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Add to cart</button>
                            </form>
                        <?php else: ?>
                            <button type="button" disabled class="w-full flex-1 cursor-not-allowed rounded-full bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-500">Out of stock</button>
                        <?php endif; ?>
                        <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                            <input type="hidden" name="redirect" value="shop.php">
                            <button type="submit" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold">Save</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($result['pages'] > 1): ?>
        <div class="mt-10 flex flex-wrap gap-2">
            <?php for ($i = 1; $i <= $result['pages']; $i++): ?>
                <a href="<?= e(app_url('shop.php?category=' . (int) $filters['category_id'] . '&q=' . urlencode($filters['query']) . '&sort=' . urlencode($filters['sort']) . '&page=' . $i)); ?>" class="rounded-full px-4 py-2 text-sm font-semibold <?= $i === $result['page'] ? 'bg-brand-600 text-white' : 'bg-white shadow-soft'; ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
