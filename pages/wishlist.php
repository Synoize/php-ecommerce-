<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$items = (new WishlistModel())->items((int) current_user()['id']);
$pageTitle = 'Wishlist';
require __DIR__ . '/layout/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="font-display text-4xl font-bold">Wishlist</h1>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php foreach ($items as $item): ?>
            <article class="overflow-hidden rounded-3xl bg-white shadow-soft">
                <a href="<?= e(app_url('product/' . (int) $item['product_id'] . '/' . slugify((string) $item['name']))); ?>">
                    <img src="<?= e(upload_url((string) $item['image'])); ?>" alt="<?= e($item['name']); ?>" class="h-64 w-full object-cover" loading="lazy">
                </a>
                <div class="p-5">
                    <h2 class="text-lg font-semibold"><?= e($item['name']); ?></h2>
                    <div class="mt-2 text-brand-600"><?= e(money((float) $item['price'])); ?></div>
                    <div class="mt-4 flex gap-2">
                        <form action="<?= e(app_url('api/cart.php')); ?>" method="post" class="flex-1">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Move to cart</button>
                        </form>
                        <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id']; ?>">
                            <button type="submit" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold">Remove</button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>

