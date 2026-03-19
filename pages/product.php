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
$roundedRating = (int) round((float) $product['avg_rating']);
$ratingStars = str_repeat('⭐', $roundedRating) . str_repeat('&#9733', max(0, 5 - $roundedRating));
$boxOptions = $product['box_options'] ?? [];
$defaultBox = $boxOptions[0] ?? null;
$pageTitle = $product['name'] . ' | Watch Ecommerce';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-7xl px-4 py-10">
    <div class="grid gap-10 lg:grid-cols-2 lg:items-start">
        <section>
            <div class="flex gap-4">
                <div class="flex flex-col gap-4">
                    <?php foreach ($product['images'] as $image): ?>
                        <button type="button" class="gallery-thumb overflow-hidden p-2 border bg-white-light/20 hover:bg-white-light/40" data-image="<?= e(upload_url((string) $image['image_url'])); ?>">
                            <img src="<?= e(upload_url((string) $image['image_url'])); ?>" alt="" class="h-12 w-28 object-contain md:h-20" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="relative overflow-hidden md:p-4 border bg-white-light/20">
                    <img id="main-product-image" src="<?= e(upload_url((string) $product['images'][0]['image_url'])); ?>" alt="<?= e($product['name']); ?>" class="h-[300px] w-full object-contain md:h-[400px]">

                    <!-- Wishlist -->
                    <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post" class="absolute top-3 right-3 z-10">
                        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                        <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">
                        <button
                            type="submit"
                            class="rounded-full border p-2 transition bg-white-dark hover:scale-105 <?= $wishlisted ? 'text-red-500' : 'text-black-light'; ?>"
                            aria-label="<?= $wishlisted ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                            <i data-lucide="heart" class="h-4 w-4 <?= $wishlisted ? 'fill-current' : ''; ?>"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="md:p-2">
                <span class="text-[10px] uppercase tracking-wider text-black-light bg-primary-light px-2 py-1 rounded-md">
                    <?= e($product['category_name'] ?? 'Watch'); ?>
                </span>

                <h3 class="mt-3 line-clamp-2 text-2xl md:text-4xl font-semibold text-black-medium py-2">
                    <?= e($product['name']); ?>
                </h3>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm ">

                    <!-- Stars + Rating Badge -->
                    <div class="flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1">
                        <span class="flex items-center text-sm gap-4 text-white-dark">
                            <?= $ratingStars; ?>
                        </span>
                        <span class="text-sm font-semibold text-amber-600">
                            <?= number_format((float) $product['avg_rating'], 1); ?>
                        </span>
                    </div>

                    <!-- Divider -->
                    <span class="hidden sm:block h-4 w-px bg-white-medium"></span>

                    <!-- Reviews Count -->
                    <span class="font-medium text-black-light">
                        <?= (int) $product['review_count']; ?> Reviews
                    </span>

                </div>

                <!-- Price Section -->
                <div class="mt-5 flex flex-col gap-1">

                    <!-- Price -->
                    <div class="flex items-center gap-3">
                        <span class="text-2xl md:text-3xl font-bold text-black-medium">
                            <?= e(money((float) $product['price'])); ?>
                        </span>

                        <!-- Optional badge -->
                        <span class="rounded-full bg-white-light px-2 py-1 text-xs font-medium text-primary-medium">
                            Best Price
                        </span>
                    </div>

                    <!-- Tax Info -->
                    <span class="text-xs md:text-sm text-black-light">
                        Inclusive of all taxes
                    </span>

                </div>

                <!-- Stock Section -->
                <div class="mt-6 flex flex-wrap items-center gap-3">

                    <!-- Stock Badge -->
                    <?php if ((int)$product['stock'] === 0): ?>

                        <span class="text-xs md:text-sm flex items-center gap-1 bg-red-100 text-red-600 font-semibold px-3 py-1 rounded-md">
                            <i data-lucide="frown" class="w-3.5 h-3.5 hidden md:block"></i>
                            Out of Stock
                        </span>

                    <?php elseif ((int)$product['stock'] < 20): ?>

                        <span class="text-xs md:text-sm flex items-center gap-1 bg-orange-100 text-orange-600 font-semibold px-3 py-1 rounded-md">
                            Only <?= (int)$product['stock']; ?> left
                        </span>

                    <?php else: ?>

                        <span class="text-xs md:text-sm flex items-center gap-1 bg-green-100 text-green-600 font-semibold px-3 py-1 rounded-md">
                            In Stock
                        </span>

                    <?php endif; ?>

                    <!-- Stock Count -->
                    <span class="text-sm text-slate-600">
                        <?= (int) $product['stock']; ?> units available
                    </span>

                </div>

                <p class="mt-6 text-sm leading-7 text-black-light"><?= nl2br(e((string) $product['description'])); ?></p>

                <?php if ($boxOptions !== []): ?>
                    <div class="mt-8 border-t pt-6">
                        <div class="text-2xl font-semibold text-black-medium">Buy with Box <span class="text-rose-500">*</span></div>
                        <div class="mt-4 grid gap-3 grid-cols-2">
                            <label class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium text-black-medium">
                                <input type="radio" name="buy_with_box" value="1" class="box-choice" <?= $defaultBox ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium text-black-medium">
                                <input type="radio" name="buy_with_box" value="0" class="box-choice" <?= $defaultBox ? '' : 'checked'; ?>>
                                <span>No</span>
                            </label>
                        </div>

                        <div id="box-option-panel" class="mt-5 <?= $defaultBox ? '' : 'hidden'; ?> rounded-2xl border bg-white-light/10 p-4">
                            <div class="grid gap-5 lg:grid-cols-[200px,1fr] lg:items-start">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-black-light">Choose box</label>
                                    <div class="relative w-full">
                                        <select id="box-option-select"
                                            class="w-full appearance-none rounded-xl border px-4 py-3 pr-12 text-sm text-black-light outline-none transition focus:border-white-medium focus:rounded-b-none">

                                            <?php foreach ($boxOptions as $option): ?>
                                                <option value="<?= (int) $option['id']; ?>"
                                                    data-name="<?= e((string) $option['name']); ?>"
                                                    data-price="<?= e((string) $option['price']); ?>"
                                                    data-image="<?= e(upload_url((string) $option['image'])); ?>"
                                                    <?= $defaultBox && (int) $defaultBox['id'] === (int) $option['id'] ? 'selected' : ''; ?>>
                                                    <?= e($option['name']); ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>

                                        <!-- Custom Arrow -->
                                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                                <div class="grid gap-5 sm:grid-cols-[120px,1fr] sm:items-start">

                                    <!-- Product Image -->
                                    <div class="overflow-hidden bg-white-dark border">
                                        <img
                                            id="box-option-image"
                                            src="<?= $defaultBox ? e(upload_url((string) $defaultBox['image'])) : ''; ?>"
                                            alt="Box Image"
                                            class="h-40 w-full object-contain transition duration-300 hover:scale-105">
                                    </div>

                                    <div class="flex flex-col justify-between h-full">
                                        <!-- Product Details -->
                                        <div class="flex flex-row justify-between h-full">

                                            <div class="h-full flex flex-col justify-between">
                                                <div>
                                                    <!-- Name -->
                                                    <h3 id="box-option-name" class="text-lg md:text-lg font-semibold text-black-medium">
                                                        <?= $defaultBox ? e((string) $defaultBox['name']) : 'Select Box'; ?>
                                                    </h3>

                                                    <!-- Price -->
                                                    <p id="box-option-price" class="mt-2 text-base font-medium text-black-medium">
                                                        <?= $defaultBox ? e(money((float) $defaultBox['price'])) : e(money(0)); ?>
                                                    </p>
                                                </div>

                                                <!-- Pricing Breakdown -->

                                                <div class="flex justify-between text-xs uppercase tracking-wider text-black-light my-4">
                                                    <span>Options amount</span>
                                                    <span id="box-option-total">
                                                        <?= $defaultBox ? e(money((float) $defaultBox['price'])) : e(money(0)); ?>
                                                    </span>
                                                </div>

                                            </div>



                                            <!-- Quantity Section -->
                                            <div class="flex flex-col justify-start">
                                                <label class="mb-2 text-sm font-semibold text-black-light">Box Quantity</label>

                                                <input
                                                    id="box-qty"
                                                    type="number"
                                                    min="1"
                                                    max="<?= max(1, (int) $product['stock']); ?>"
                                                    value="1"
                                                    class="w-full rounded-xl border px-3 py-2 text-center text-base text-black-medium font-medium outline-none focus:border-white-medium transition">

                                                <!-- Stock Info -->
                                                <span class="mt-2 text-xs text-white-medium text-center">
                                                    Max: <?= (int) $product['stock']; ?>
                                                </span>
                                            </div>
                                        </div>



                                        <!-- Total Price -->
                                        <div class="flex justify-between items-center border-t pt-2">
                                            <span class="text-sm font-medium text-black-light">Final total</span>
                                            <span id="final-total" class="text-xl md:text-2xl font-bold text-black-medium">
                                                <?= e(money((float) $product['price'] + (float) ($defaultBox['price'] ?? 0))); ?>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-8 flex items-center justify-start gap-3 rounded-2xl border border-dashed bg-white-light/20 px-5 py-4">

                        <!-- Icon -->
                        <i data-lucide="circle-off" class="w-12 h-12 md:w-8 md:h-8 text-white-medium"></i>

                        <!-- Content -->
                        <div>
                            <p class="text-sm font-medium text-black-light">
                                No Box Options Available
                            </p>
                            <p class="mt-1 text-xs md:text-sm text-white-medium">
                                This watch does not include any additional box packaging options.
                            </p>
                        </div>

                    </div>
                <?php endif; ?>

                <div class="mt-6 grid gap-5 md:grid-cols-[1fr,auto] md:items-end">

                    <!-- Quantity -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-black-light">Quantity</label>

                        <div class="flex w-fit items-center rounded-lg border overflow-hidden">

                            <button
                                type="button"
                                class="qty-btn p-3.5 text-lg text-black-light bg-white-light/80 hover:bg-white-light/40 transition"
                                data-step="-1">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                            <div class="flex items-center justify-center pl-3 w-fit">
                                <input
                                    id="product-qty"
                                    type="number"
                                    min="1"
                                    max="<?= max(1, (int) $product['stock']); ?>"
                                    value="1"
                                    class="w-10 text-center text-base font-semibold outline-none text-black-medium bg-whi"
                                    <?= (int) $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            </div>


                            <button
                                type="button"
                                class="qty-btn p-3.5 text-lg text-black-light bg-white-light/80 hover:bg-white-light/40 transition"
                                data-step="1">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>

                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-3">

                        <?php if ((int) $product['stock'] > 0): ?>

                            <!-- Add to Cart -->
                            <form id="add-to-cart-form" action="<?= e(app_url('api/cart.php')); ?>" method="post">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="box_option_id" value="<?= $defaultBox ? (int) $defaultBox['id'] : ''; ?>">
                                <input type="hidden" name="box_quantity" value="<?= $defaultBox ? '1' : '0'; ?>">
                                <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">

                                <button
                                    type="submit"
                                    class="w-full bg-white-light/80 px-6 py-3 text-sm font-semibold text-black-medium hover:bg-white-light/40 transition">
                                    ADD TO CART
                                </button>
                            </form>

                            <!-- Buy Now -->
                            <form id="buy-now-form" action="<?= e(app_url('api/cart.php')); ?>" method="post">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="box_option_id" value="<?= $defaultBox ? (int) $defaultBox['id'] : ''; ?>">
                                <input type="hidden" name="box_quantity" value="<?= $defaultBox ? '1' : '0'; ?>">
                                <input type="hidden" name="redirect" value="checkout.php">

                                <button
                                    type="submit"
                                    class="w-full bg-primary-medium px-6 py-3 text-sm font-semibold text-white-dark hover:bg-primary-medium/90 transition">
                                    BUY NOW
                                </button>
                            </form>

                        <?php else: ?>

                            <button
                                type="button"
                                disabled
                                class="cursor-not-allowed w-full bg-rose-100 px-6 py-3 text-sm font-bold text-rose-500 transition">
                                OUT OF STOCK
                            </button>

                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-6 text-xs uppercase tracking-[0.18em] text-primary-medium">Shipping days 4 to 7 days</div>

                <div class="mt-4 grid grid-cols-3 gap-2 border-t pt-5 text-center text-xs text-black-light">
                    <div>
                        <span class="mx-auto flex h-14 w-14 md:h-20 md:w-20 items-center justify-center rounded-full text-primary-medium/80 bg-white-light/40">
                            <i data-lucide="truck"></i>
                        </span>
                        <div class="mt-2 font-medium">Free Delivery</div>
                    </div>
                    <div>
                        <span class="mx-auto flex h-14 w-14 md:h-20 md:w-20 items-center justify-center rounded-full text-primary-medium/80 bg-white-light/40">
                            <i data-lucide="refresh-cw"></i>
                        </span>
                        <div class="mt-2 font-medium">48 Hours Returnable</div>
                    </div>
                    <div>
                        <span class="mx-auto flex h-14 w-14 md:h-20 md:w-20 items-center justify-center rounded-full text-primary-medium/80 bg-white-light/40">
                            <i data-lucide="dollar-sign"></i></span>
                        <div class="mt-2 font-medium">Cash On Delivery</div>
                    </div>
                </div>
            </div>

            <div class="space-y-3 rounded-xl border border bg-white-light/20 p-6 shadow-soft">
                <details open class="group border-b pb-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-[0.16em] text-black-medium/90">
                        Product Description
                        <span class="text-lg transition group-open:rotate-180">^</span>
                    </summary>
                    <div class="pt-4 text-sm leading-7 text-black-medium/80">
                        <p><?= nl2br(e((string) $product['description'])); ?></p>
                        <p class="mt-3">If you choose a box option, its price is added to the final total and stored with the order details.</p>
                    </div>
                </details>

                <details class="group border-b pb-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-[0.16em] text-black-medium/90">
                        Exchange Policy
                        <span class="text-lg transition group-open:rotate-180">^</span>
                    </summary>
                    <div class="pt-4 text-sm leading-7 text-black-medium/80">
                        Eligible exchange requests can be raised within 48 hours of delivery if the product is unused and in original packaging.
                    </div>
                </details>

                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-[0.16em] text-black-medium/90">
                        Return Policy
                        <span class="text-lg transition group-open:rotate-180">^</span>
                    </summary>
                    <div class="pt-4 text-sm leading-7 text-black-medium/80">
                        Returns are accepted only for damaged or incorrect items. Contact support with product photos and the order number.
                    </div>
                </details>
            </div>

            <div class="rounded-2xl p-6 bg-gradient-to-br from-[#0065a420] to-[#ff003320] border">

                <!-- Heading -->
                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-medium">
                    Step By Step
                </div>

                <!-- Steps -->
                <div class="mt-6 grid grid-cols-4 gap-5 text-center text-xs font-medium text-black-light">

                    <!-- Step 1 -->
                    <div class="group">
                        <div class="mx-auto flex h-12 w-12 md:w-24 md:h-24 items-center justify-center rounded-full 
                        bg-white-dark/40 transition duration-300 text-black-light/80">
                            <i data-lucide="shopping-bag" class="w-4 h-4 md:w-8 md:h-8 "></i>
                        </div>
                        <div class="mt-2 text-nowrap transition">
                            Order Now
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="group">
                        <div class="mx-auto flex h-12 w-12 md:w-24 md:h-24 items-center justify-center rounded-full 
                        bg-white-dark/40 transition duration-300 text-black-light/80">
                            <i data-lucide="users" class="w-4 h-4 md:w-8 md:h-8 "></i>
                        </div>
                        <div class="mt-2 text-nowrap transition">
                            Team Calls
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="group">
                        <div class="mx-auto flex h-12 w-12 md:w-24 md:h-24 items-center justify-center rounded-full 
                        bg-white-dark/40 transition duration-300 text-black-light/80">
                            <i data-lucide="truck" class="w-4 h-4 md:w-8 md:h-8 "></i>
                        </div>
                        <div class="mt-2 text-nowrap transition">
                            Delivery
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="group">
                        <div class="mx-auto flex h-12 w-12 md:w-24 md:h-24 items-center justify-center rounded-full 
                        bg-white-dark/40 transition duration-300 text-black-light/80">
                            <i data-lucide="messages-square" class="w-4 h-4 md:w-8 md:h-8 "></i>
                        </div>
                        <div class="mt-2 text-nowrap transition">
                            Feedback
                        </div>
                    </div>

                </div>

                <!-- CTA Button -->
                <a href="tel:+916235559500"
                    class="mt-8 inline-flex items-center justify-center rounded-full bg-primary-medium px-6 py-3 text-sm text-white-dark transition-all duration-300 hover:bg-primary-medium/90 active:scale-95">
                    Chat With Support: +91 62355 9500
                </a>

            </div>
        </section>
    </div>

    <section class="mt-14 border-t pt-8">
        <h2 class="text-xl font-semibold text-black-medium">Reviews & Rating</h2>

        <div class="grid md:grid-cols-2 gap-8 md:gap-20">

            <!-- Reviews List -->
            <?php if (!empty($reviews)): ?>
                <div class="mt-5 space-y-4">
                    <?php foreach ($reviews as $review): ?>
                        <article class="border bg-white-light/10 p-4 transition">

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">

                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center text-sm font-semibold text-black-medium">
                                        <?= strtoupper($review['name'][0]); ?>
                                    </div>

                                    <div>
                                        <div class="font-semibold text-black-medium">
                                            <?= e($review['name']); ?>
                                        </div>

                                        <!-- Stars -->
                                        <div class="text-lg text-amber-500">
                                            <?= str_repeat('★', (int)$review['rating']) . str_repeat('☆', 5 - (int)$review['rating']); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs text-white-medium ">
                                    <?= e(date('d M Y', strtotime((string)$review['created_at']))); ?>
                                </div>
                            </div>

                            <p class="flex items-center gap-2 px-2 pt-2 text-sm leading-6 text-black-light">
                                <i data-lucide="messages-square" class="w-4 h-4 text-white-medium"></i> <?= e((string)$review['comment']); ?>
                            </p>

                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="mt-5 flex items-center justify-center h-20 border border-dashed rounded-xl">
                    <p class="text-sm text-slate-500">No reviews yet. Be the first one</p>
                </div>
            <?php endif; ?>


            <!-- Review Form -->
            <div>
                <h3 class="text-lg font-semibold text-black-medium">
                    Be the first to review "<?= e($product['name']); ?>"
                </h3>

                <?php if (is_logged_in()): ?>
                    <form action="" method="post" class="mt-5 space-y-4">
                        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">

                        <div>
                            <label class="mb-2 block text-sm font-medium text-black-medium">
                                Your Rating
                            </label>

                            <div id="starRating" class="flex gap-2 text-4xl cursor-pointer select-none">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span data-value="<?= $i; ?>" class="star text-white-light transition">★</span>
                                <?php endfor; ?>
                            </div>

                            <input type="hidden" name="rating" id="ratingInput" value="5">
                        </div>

                        <!-- Review -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-black-medium">
                                Your Review
                            </label>

                            <textarea name="comment" rows="5"
                                class="w-full border px-4 py-3 text-sm outline-none focus:border-white-medium"
                                placeholder="Write your experience..."></textarea>
                        </div>

                        <!-- Button -->
                        <button type="submit" name="submit_review"
                            class="w-full bg-primary-medium px-5 py-3 text-sm font-semibold text-white-dark transition hover:bg-primary-medium/90">
                            Submit Review
                        </button>
                    </form>
                <?php else: ?>
                    <p class="mt-4 text-sm text-black-light">
                        Please
                        <a href="<?= e(app_url('user/login.php')); ?>" class="font-semibold text-primary-medium underline">
                            sign in
                        </a>
                        to write a review.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const stars = document.querySelectorAll('#starRating .star');
            const input = document.getElementById('ratingInput');

            let currentRating = 5;

            // Default stars
            updateStars(currentRating);

            stars.forEach((star, index) => {

                // Hover effect
                star.addEventListener('mouseenter', () => {
                    updateStars(index + 1);
                });

                // Click select
                star.addEventListener('click', () => {
                    currentRating = index + 1;
                    input.value = currentRating;
                    updateStars(currentRating);
                });
            });

            // Reset after hover
            document.getElementById('starRating').addEventListener('mouseleave', () => {
                updateStars(currentRating);
            });

            function updateStars(rating) {
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.add('text-amber-500');
                        s.classList.remove('text-slate-300');
                    } else {
                        s.classList.add('text-slate-300');
                        s.classList.remove('text-amber-500');
                    }
                });
            }

        });
    </script>

    <section class="mt-14 border-t pt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold uppercase tracking-[0.14em] text-black-medium">Related Products</h2>
            <a href="<?= e(app_url('shop.php?category=' . (int) $product['category_id'])); ?>"
                class="group flex items-center gap-2 rounded-full border px-5 py-2 text-sm transition hover:bg-white-light/40 text-black-light text-nowrap">

                <span>View More</span>

                <i data-lucide="arrow-right"
                    class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1">
                </i>
            </a>
        </div>
        <div class="mt-6">
            <?php if (!empty($related)): ?>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    <?php foreach ($related as $item): ?>
                        <div class="group overflow-hidden">
                            <div class="relative overflow-hidden rounded-lg bg-white-light/40">

                                <!-- Image -->
                                <a href="<?= e(product_link($item)); ?>">
                                    <img
                                        src="<?= e(upload_url((string) $item['image'])); ?>"
                                        alt="<?= e($item['name']); ?>"
                                        class="h-36 md:h-44 w-full object-contain p-2 transition duration-500 group-hover:scale-105"
                                        loading="lazy" />
                                </a>

                                <!-- WISHLIST -->
                                <?php if (is_logged_in()): ?>
                                    <?php $isWishlisted = $store->wishlist->has((int) current_user()['id'], (int) $item['id']); ?>

                                    <form action="<?= e(app_url('api/wishlist.php')); ?>"
                                        method="post"
                                        class="absolute top-3 right-3 z-10">

                                        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                        <input type="hidden" name="product_id" value="<?= (int) $item['id']; ?>">
                                        <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">

                                        <button
                                            type="submit"
                                            class="rounded-full border p-2 bg-white-dark hover:scale-105 transition
                                           <?= $isWishlisted ? 'text-red-500' : 'text-black-light'; ?>">

                                            <i data-lucide="heart"
                                                class="h-4 w-4 <?= $isWishlisted ? 'fill-current' : ''; ?>"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Stock Badge -->
                                <?php if (isset($item['stock']) && (int)$item['stock'] == 0): ?>
                                    <span class="absolute top-3 left-3 text-xs bg-red-100 text-red-600 font-semibold px-2 py-1 rounded-md">
                                        Out of Stock
                                    </span>
                                <?php elseif (isset($item['stock']) && (int)$item['stock'] < 20): ?>
                                    <span class="absolute top-3 left-3 text-xs bg-orange-100 text-orange-600 font-semibold px-2 py-1 rounded-md">
                                        Only <?= (int)$item['stock']; ?> left
                                    </span>
                                <?php else: ?>
                                    <span class="absolute top-3 left-3 text-xs bg-green-100 text-green-600 font-semibold px-2 py-1 rounded-md">
                                        In Stock
                                    </span>
                                <?php endif; ?>

                            </div>

                            <div class="py-4 px-2">
                                <h3 class="line-clamp-2 text-xs md:text-sm font-medium text-black-medium">
                                    <?= e($item['name']); ?>
                                </h3>

                                <p class="mt-2 text-sm font-semibold text-green-600">
                                    <?= e(money((float) $item['price'])); ?>
                                </p>

                                <?php if (!isset($item['stock']) || (int)$item['stock'] > 0): ?>
                                    <a href="<?= e(product_link($item)); ?>"
                                        class="mt-4 block bg-primary-medium py-2.5 text-center text-sm font-semibold text-white-dark transition hover:bg-primary-medium/80">
                                        BUY NOW
                                    </a>
                                <?php else: ?>
                                    <button disabled
                                        class="mt-4 w-full bg-red-100 py-2.5 text-center text-sm font-bold text-rose-500 cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>

                <!-- EMPTY STATE -->
                <div class="flex flex-col items-center justify-center py-16 border border-dashed rounded-xl text-center">

                    <i data-lucide="shopping-bag" class="w-12 h-12 text-white-medium mb-3"></i>

                    <h3 class="text-lg font-semibold text-black-medium">
                        No Related Products
                    </h3>

                    <p class="mt-1 text-sm text-white-medium">
                        We couldn't find similar items for this product.
                    </p>

                    <a href="<?= e(app_url('shop.php')); ?>"
                        class="mt-4 inline-flex items-center gap-2 rounded-full bg-primary-medium px-5 py-2 text-sm text-white-dark hover:bg-primary-medium/90 transition">
                        Explore Products
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>

                </div>

            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    const qtyInput = document.getElementById('product-qty');
    const boxQtyInput = document.getElementById('box-qty');
    const boxPanel = document.getElementById('box-option-panel');
    const boxSelect = document.getElementById('box-option-select');
    const mainImage = document.getElementById('main-product-image');

    const basePrice = <?= json_encode((float) $product['price']); ?>;
    const hasBoxOptions = <?= json_encode($boxOptions !== []); ?>;

    const cartForm = document.getElementById('add-to-cart-form');
    const buyForm = document.getElementById('buy-now-form');

    const formatter = new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    });

    /* ------------------ Quantity Sync ------------------ */
    function syncQty(value) {
        if (!qtyInput) return;

        const min = parseInt(qtyInput.min || '1', 10);
        const max = parseInt(qtyInput.max || '999', 10);

        let nextValue = parseInt(value || '1', 10);
        if (isNaN(nextValue)) nextValue = 1;

        nextValue = Math.max(min, Math.min(max, nextValue));
        qtyInput.value = nextValue;

        [cartForm, buyForm].forEach((form) => {
            const input = form?.querySelector('input[name="quantity"]');
            if (input) input.value = nextValue;
        });

        // sync box qty limit
        if (boxQtyInput) {
            const boxVal = parseInt(boxQtyInput.value || '1', 10);
            if (boxVal > nextValue) {
                boxQtyInput.value = nextValue;
            }
        }

        updateTotals();
    }

    /* ------------------ Box Fields Sync ------------------ */
    function syncBoxFields() {
        const enabled = hasBoxOptions && document.querySelector('.box-choice:checked')?.value === '1';
        const selected = boxSelect?.selectedOptions?.[0];

        const boxId = (enabled && selected) ? selected.value : '';
        const selectedQty = enabled ?
            Math.max(1, Math.min(parseInt(qtyInput?.value || '1'), parseInt(boxQtyInput?.value || '1'))) :
            0;

        [cartForm, buyForm].forEach((form) => {
            const boxIdField = form?.querySelector('input[name="box_option_id"]');
            const boxQtyField = form?.querySelector('input[name="box_quantity"]');

            if (boxIdField) boxIdField.value = boxId;
            if (boxQtyField) boxQtyField.value = selectedQty;
        });
    }

    /* ------------------ Box Preview ------------------ */
    function updateBoxPreview() {
        if (!hasBoxOptions || !boxSelect) return;

        const selected = boxSelect.selectedOptions[0];
        const enabled = document.querySelector('.box-choice:checked')?.value === '1';

        const image = document.getElementById('box-option-image');
        const name = document.getElementById('box-option-name');
        const price = document.getElementById('box-option-price');

        if (selected) {
            if (image) image.src = selected.dataset.image || '';
            if (name) name.textContent = selected.dataset.name || '';
            if (price) {
                const value = Number(selected.dataset.price || 0);
                price.textContent = formatter.format(value);
            }
        }

        if (boxPanel) {
            boxPanel.classList.toggle('hidden', !enabled);
        }

        updateTotals();
    }

    /* ------------------ Total Calculation ------------------ */
    function updateTotals() {
        const optionAmount = document.getElementById('box-option-total');
        const finalTotal = document.getElementById('final-total');

        const enabled = hasBoxOptions && document.querySelector('.box-choice:checked')?.value === '1';
        const selected = boxSelect?.selectedOptions?.[0];

        const productQty = Math.max(1, parseInt(qtyInput?.value || '1', 10));

        let optionTotal = 0;

        if (enabled && selected) {
            const price = Number(selected.dataset.price || 0);

            let boxQty = parseInt(boxQtyInput?.value || '1', 10);
            if (isNaN(boxQty)) boxQty = 1;

            // limit box qty
            if (boxQtyInput) {
                boxQty = Math.max(1, Math.min(productQty, boxQty));
                boxQtyInput.value = boxQty;
                boxQtyInput.max = productQty;
            }

            optionTotal = price * boxQty;
        }

        const total = (basePrice * productQty) + optionTotal;

        if (optionAmount) optionAmount.textContent = formatter.format(optionTotal);
        if (finalTotal) finalTotal.textContent = formatter.format(total);

        syncBoxFields();
    }

    /* ------------------ Events ------------------ */

    // gallery
    document.querySelectorAll('.gallery-thumb').forEach((btn) => {
        btn.addEventListener('click', () => {
            const img = btn.dataset.image;
            if (mainImage && img) mainImage.src = img;
        });
    });

    // qty buttons
    document.querySelectorAll('.qty-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!qtyInput || qtyInput.disabled) return;

            const step = parseInt(btn.dataset.step || '0', 10);
            syncQty(parseInt(qtyInput.value || '1', 10) + step);
        });
    });

    qtyInput?.addEventListener('input', () => syncQty(qtyInput.value));
    boxQtyInput?.addEventListener('input', updateTotals);
    boxSelect?.addEventListener('change', updateBoxPreview);

    document.querySelectorAll('.box-choice')
        .forEach((choice) => choice.addEventListener('change', updateBoxPreview));

    /* ------------------ Init ------------------ */
    syncQty(qtyInput?.value || 1);
    updateBoxPreview();
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>