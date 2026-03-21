<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$items = (new WishlistModel())->items((int) current_user()['id']);
$pageTitle = 'Wishlist';

require __DIR__ . '/layout/header.php';
?>

<main class="mt-28 mx-auto max-w-7xl px-4 py-8">

    <!-- Heading -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-black-medium">
            Your Wishlist
        </h1>
        <span class="text-sm text-black-light">
            <?= count($items); ?> items
        </span>
    </div>

    <?php if (empty($items)): ?>

        <!-- Empty State -->
        <div class="mt-12 min-h-[calc(100vh-262px)] flex flex-col items-center justify-center text-center">
            <div class="rounded-full bg-white-light/40 p-6 text-2xl">
                <i data-lucide="heart" class="fill-current text-red-500"></i>
            </div>

            <h2 class="mt-4 text-lg font-semibold text-black-medium">
                Your wishlist is empty
            </h2>

            <p class="mt-2 text-sm text-black-light">
                Save products you like to see them here later.
            </p>

            <a href="<?= e(app_url('shop.php')); ?>"
                class="mt-6 bg-primary-medium px-6 py-3 text-sm font-semibold text-white-dark transition hover:bg-primary-medium/80">
                Browse Products
            </a>
        </div>

    <?php else: ?>

        <!-- Wishlist Grid -->
        <div class="mt-6 min-h-[calc(100vh-238px)] grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">

            <?php foreach ($items as $item): ?>
                <div class="group overflow-hidden">

                    <!-- Image Box -->
                    <div class="relative overflow-hidden rounded-lg bg-white-light/40">

                        <!-- Product Image -->
                        <a href="<?= e(app_url('product/' . (int) $item['product_id'] . '/' . slugify((string) $item['name']))); ?>">
                            <img
                                src="<?= e(upload_url((string) $item['image'])); ?>"
                                alt="<?= e($item['name']); ?>"
                                class="h-36 md:h-44 w-full object-contain p-2 transition duration-500 group-hover:scale-105"
                                loading="lazy" />
                        </a>

                        <!-- Stock Badge -->
                        <?php if (isset($item['stock']) && (int)$item['stock'] == 0): ?>

                            <span class="absolute top-2 left-2 text-[10px] bg-red-100 text-red-600 font-semibold px-2 py-1 rounded-md">
                                Out of Stock
                            </span>

                        <?php elseif (isset($item['stock']) && (int)$item['stock'] < 20): ?>

                            <span class="absolute top-2 left-2 text-[10px] bg-orange-100 text-orange-600 font-semibold px-2 py-1 rounded-md">
                                Only <?= (int)$item['stock']; ?> left
                            </span>

                        <?php else: ?>

                            <span class="absolute top-2 left-2 text-[10px] bg-green-100 text-green-600 font-semibold px-2 py-1 rounded-md">
                                In Stock
                            </span>

                        <?php endif; ?>

                        <!-- Remove Button -->
                        <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post"
                            class="absolute top-2 right-2">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id']; ?>">

                            <button type="submit"
                                class="rounded-full bg-white-dark/80 p-2 transition hover:bg-white-dark hover:scale-105 transition">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </form>

                    </div>

                    <!-- Content -->
                    <div class="py-4 px-2">

                        <!-- Title -->
                        <h3 class="line-clamp-2 text-xs md:text-sm font-medium text-black-medium">
                            <?= e($item['name']); ?>
                        </h3>

                        <!-- Price -->
                        <p class="mt-2 text-sm font-semibold text-green-600">
                            <?= e(money((float) $item['price'])); ?>
                        </p>

                        <!-- Action -->
                        <?php if (!isset($item['stock']) || (int)$item['stock'] > 0): ?>

                            <form action="<?= e(app_url('api/cart.php')); ?>" method="post" class="mt-4">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $item['product_id']; ?>">
                                <input type="hidden" name="quantity" value="1">

                                <button
                                    type="submit"
                                    class="w-full bg-primary-medium py-2.5 text-center text-sm font-semibold text-white-dark transition hover:bg-primary-medium/80">
                                    MOVE TO CART
                                </button>
                            </form>

                        <?php else: ?>

                            <button
                                disabled
                                class="mt-4 w-full bg-red-100 py-2.5 text-center text-sm font-bold text-rose-500 cursor-not-allowed">
                                Out of Stock
                            </button>

                        <?php endif; ?>

                    </div>

                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

<?php require __DIR__ . '/layout/footer.php'; ?>