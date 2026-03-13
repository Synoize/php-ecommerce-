<?php
require_once __DIR__ . '/../config/bootstrap.php';

$store = new StoreController();
$featuredCategories = $store->categories->featured(4);
$featuredProducts = $store->products->featured(8);
$pageTitle = 'Watch Ecommerce | Home';
$pageDescription = 'Shop premium watches with category filtering, reviews, wishlist, cart, and secure checkout.';
require __DIR__ . '/layout/header.php';
?>
<main>
    
    <section class="overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(198,146,20,0.22),_transparent_32%),linear-gradient(120deg,_#071b2b,_#0f4c81_60%,_#12324d)]">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 md:grid-cols-2 md:items-center">
            <div>
                <span class="inline-flex rounded-full bg-white/10 px-4 py-1 text-sm text-white/90">Spring 2026 collection</span>
                <h1 class="mt-6 font-display text-5xl font-bold leading-tight text-white">Watches built for daily wear and collector shelves.</h1>
                <p class="mt-5 max-w-xl text-base text-slate-200">Browse featured classics, sports watches, and luxury styles with real-time stock, reviews, coupons, and Razorpay checkout.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="<?= e(app_url('shop.php')); ?>" class="rounded-full bg-accent px-6 py-3 font-semibold text-slate-900">Shop now</a>
                    <a href="<?= e(app_url('categories.php')); ?>" class="rounded-full border border-white/20 px-6 py-3 font-semibold text-white">Browse categories</a>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <?php foreach (array_slice($featuredProducts, 0, 4) as $heroProduct): ?>
                    <a href="<?= e(product_link($heroProduct)); ?>" class="rounded-3xl border border-white/10 bg-white/10 p-4 text-white shadow-soft transition hover:-translate-y-1">
                        <img src="<?= e(upload_url((string) $heroProduct['image'])); ?>" alt="<?= e($heroProduct['name']); ?>" class="h-48 w-full rounded-2xl object-cover" loading="lazy">
                        <div class="mt-4 text-sm text-slate-200"><?= e($heroProduct['category_name'] ?? 'Watch'); ?></div>
                        <div class="mt-1 font-semibold"><?= e($heroProduct['name']); ?></div>
                        <div class="mt-2 text-accent"><?= e(money((float) $heroProduct['price'])); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Categories</div>
                <h2 class="mt-2 font-display text-3xl font-bold">Shop by style</h2>
            </div>
            <a href="<?= e(app_url('categories.php')); ?>" class="text-sm font-semibold text-brand-600">View all</a>
        </div>
        <div class="mt-8 grid gap-6 md:grid-cols-4">
            <?php foreach ($featuredCategories as $category): ?>
                <a href="<?= e(app_url('shop.php?category=' . (int) $category['id'])); ?>" class="group overflow-hidden rounded-3xl bg-white shadow-soft">
                    <img src="<?= e(upload_url((string) '/images/uploads/' . $category['image'])); ?>" alt="<?= e($category['name']); ?>" class="h-56 w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                    <div class="p-5">
                        <div class="font-semibold"><?= e($category['name']); ?></div>
                        <div class="mt-2 text-sm text-slate-500">Explore curated products</div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-sm font-semibold uppercase tracking-[0.25em] text-brand-600">Trending</div>
                <h2 class="mt-2 font-display text-3xl font-bold">Featured watches</h2>
            </div>
            <a href="<?= e(app_url('shop.php')); ?>" class="text-sm font-semibold text-brand-600">See all products</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($featuredProducts as $product): ?>
                <article class="overflow-hidden rounded-3xl bg-white shadow-soft">
                    <a href="<?= e(product_link($product)); ?>">
                        <img src="<?= e(upload_url((string) $product['image'])); ?>" alt="<?= e($product['name']); ?>" class="h-64 w-full object-cover" loading="lazy">
                    </a>
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?= e($product['category_name'] ?? 'Watch'); ?></span>
                            <span class="text-xs text-amber-500"><?= number_format((float) $product['avg_rating'], 1); ?> / 5</span>
                        </div>
                        <h3 class="mt-2 text-lg font-semibold"><?= e($product['name']); ?></h3>
                        <p class="mt-2 text-sm text-slate-500"><?= (int) $product['review_count']; ?> reviews • Stock <?= (int) $product['stock']; ?></p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="font-semibold text-brand-600"><?= e(money((float) $product['price'])); ?></span>
                            <a href="<?= e(product_link($product)); ?>" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold">View</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
