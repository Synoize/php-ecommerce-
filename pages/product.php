<?php
require_once __DIR__ . '/../config/bootstrap.php';

$productId = (int) ($_GET['id'] ?? 0);
$store = new StoreController();
$product = $store->products->find($productId);

if (!$product) {
    http_response_code(404);
    exit('Product not found');
}

if (is_post() && isset($_POST['submit_review'])) {
    require_login();
    verify_csrf();
    $store->reviews->save(
        $productId,
        (int) current_user()['id'],
        max(1, min(5, (int) ($_POST['rating'] ?? 5))),
        trim((string) ($_POST['comment'] ?? ''))
    );
    set_flash('success', 'Review saved.');
    redirect('product/' . $productId . '/' . slugify((string) $product['name']));
}

$reviews = $store->reviews->forProduct($productId);
$related = $store->products->related((int) $product['category_id'], $productId, 4);
$wishlisted = is_logged_in() ? $store->wishlist->has((int) current_user()['id'], $productId) : false;
$pageTitle = $product['name'] . ' | Watch Ecommerce';
require __DIR__ . '/layout/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <div class="grid gap-10 lg:grid-cols-[1.1fr,0.9fr]">
        <section class="rounded-[2rem] bg-white p-6 shadow-soft">
            <img id="main-product-image" src="<?= e(upload_url((string) $product['images'][0]['image_url'])); ?>" alt="<?= e($product['name']); ?>" class="h-[420px] w-full rounded-[1.5rem] object-cover">
            <div class="mt-4 grid grid-cols-4 gap-3">
                <?php foreach ($product['images'] as $image): ?>
                    <button type="button" class="gallery-thumb overflow-hidden rounded-2xl border border-slate-200" data-image="<?= e(upload_url((string) $image['image_url'])); ?>">
                        <img src="<?= e(upload_url((string) $image['image_url'])); ?>" alt="" class="h-24 w-full object-cover" loading="lazy">
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="rounded-[2rem] bg-white p-8 shadow-soft">
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?= e($product['category_name'] ?? 'Watch'); ?></span>
                <span class="rounded-full <?= (int) $product['stock'] > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?> px-3 py-1 text-xs font-semibold"><?= (int) $product['stock'] > 0 ? 'In stock' : 'Out of stock'; ?></span>
            </div>
            <h1 class="mt-4 font-display text-4xl font-bold"><?= e($product['name']); ?></h1>
            <div class="mt-3 flex items-center gap-4 text-sm text-slate-500">
                <span><?= number_format((float) $product['avg_rating'], 1); ?> / 5 rating</span>
                <span><?= (int) $product['review_count']; ?> reviews</span>
            </div>
            <div class="mt-6 text-3xl font-semibold text-brand-600"><?= e(money((float) $product['price'])); ?></div>
            <p class="mt-6 text-slate-600"><?= nl2br(e((string) $product['description'])); ?></p>
            <div class="mt-8 flex flex-wrap gap-3">
                <?php if ((int) $product['stock'] > 0): ?>
                    <form action="<?= e(app_url('api/cart.php')); ?>" method="post" class="flex flex-wrap gap-3">
                        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                        <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">
                        <input type="number" min="1" max="<?= (int) $product['stock']; ?>" name="quantity" value="1" class="w-24 rounded-full border border-slate-200 px-4 py-3">
                        <button type="submit" class="rounded-full bg-brand-600 px-6 py-3 font-semibold text-white">Add to cart</button>
                    </form>
                <?php else: ?>
                    <button type="button" disabled class="cursor-not-allowed rounded-full bg-slate-200 px-6 py-3 font-semibold text-slate-500">Out of stock</button>
                <?php endif; ?>
                <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                    <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">
                    <button type="submit" class="rounded-full border border-slate-200 px-6 py-3 font-semibold"><?= $wishlisted ? 'Remove wishlist' : 'Save wishlist'; ?></button>
                </form>
            </div>
        </section>
    </div>

    <section class="mt-12 grid gap-8 lg:grid-cols-[1fr,0.8fr]">
        <div class="rounded-[2rem] bg-white p-8 shadow-soft">
            <h2 class="font-display text-2xl font-bold">Reviews</h2>
            <div class="mt-6 space-y-5">
                <?php foreach ($reviews as $review): ?>
                    <article class="rounded-2xl border border-slate-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="font-semibold"><?= e($review['name']); ?></div>
                            <div class="text-sm text-amber-500"><?= str_repeat('?', (int) $review['rating']); ?></div>
                        </div>
                        <p class="mt-2 text-sm text-slate-600"><?= e((string) $review['comment']); ?></p>
                    </article>
                <?php endforeach; ?>
                <?php if (!$reviews): ?>
                    <p class="text-sm text-slate-500">No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="rounded-[2rem] bg-white p-8 shadow-soft">
            <h2 class="font-display text-2xl font-bold">Write a review</h2>
            <?php if (is_logged_in()): ?>
                <form action="" method="post" class="mt-6 space-y-4">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                    <div>
                        <label class="mb-2 block text-sm font-semibold">Rating</label>
                        <select name="rating" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                            <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                                <option value="<?= $rating; ?>"><?= $rating; ?> star</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold">Comment</label>
                        <textarea name="comment" rows="5" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="rounded-full bg-brand-600 px-6 py-3 font-semibold text-white">Submit review</button>
                </form>
            <?php else: ?>
                <p class="mt-4 text-sm text-slate-600">Please <a class="font-semibold text-brand-600" href="<?= e(app_url('user/login.php')); ?>">sign in</a> to review this product.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="mt-12">
        <h2 class="font-display text-2xl font-bold">Related products</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($related as $item): ?>
                <a href="<?= e(product_link($item)); ?>" class="overflow-hidden rounded-3xl bg-white shadow-soft">
                    <img src="<?= e(upload_url((string) $item['image'])); ?>" alt="<?= e($item['name']); ?>" class="h-56 w-full object-cover" loading="lazy">
                    <div class="p-5">
                        <div class="font-semibold"><?= e($item['name']); ?></div>
                        <div class="mt-2 text-brand-600"><?= e(money((float) $item['price'])); ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
