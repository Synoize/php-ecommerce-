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
$ratingStars = str_repeat('*', $roundedRating) . str_repeat('-', max(0, 5 - $roundedRating));
$boxOptions = $product['box_options'] ?? [];
$defaultBox = $boxOptions[0] ?? null;
$pageTitle = $product['name'] . ' | Watch Ecommerce';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-7xl px-4 py-10">
    <div class="grid gap-10 lg:grid-cols-[minmax(0,520px),minmax(0,1fr)] lg:items-start">
        <section>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                <div class="overflow-hidden rounded-xl bg-slate-100">
                    <img id="main-product-image" src="<?= e(upload_url((string) $product['images'][0]['image_url'])); ?>" alt="<?= e($product['name']); ?>" class="h-[420px] w-full object-cover md:h-[520px]">
                </div>
                <div class="mt-4 grid grid-cols-4 gap-3 md:grid-cols-5">
                    <?php foreach ($product['images'] as $image): ?>
                        <button type="button" class="gallery-thumb overflow-hidden rounded-lg border border-slate-200 bg-white" data-image="<?= e(upload_url((string) $image['image_url'])); ?>">
                            <img src="<?= e(upload_url((string) $image['image_url'])); ?>" alt="" class="h-20 w-full object-cover md:h-24" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
                <div class="text-xs uppercase tracking-[0.18em] text-slate-400">
                    Home / <?= e($product['category_name'] ?? 'Watches'); ?>
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900 md:text-4xl"><?= e($product['name']); ?></h1>

                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                    <span class="font-medium text-amber-500"><?= e($ratingStars); ?></span>
                    <span><?= number_format((float) $product['avg_rating'], 1); ?> average rating</span>
                    <span><?= (int) $product['review_count']; ?> review(s)</span>
                </div>

                <div class="mt-5 text-3xl font-semibold text-slate-900"><?= e(money((float) $product['price'])); ?></div>
                <div class="mt-2 text-sm text-slate-500">Inclusive of all taxes</div>

                <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">
                    <span class="rounded-full <?= (int) $product['stock'] > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?> px-3 py-1 font-semibold">
                        <?= (int) $product['stock'] > 0 ? 'In stock' : 'Out of stock'; ?>
                    </span>
                    <span class="text-slate-500">Stock available: <?= (int) $product['stock']; ?></span>
                </div>

                <p class="mt-6 text-sm leading-7 text-slate-600"><?= nl2br(e((string) $product['description'])); ?></p>

                <?php if ($boxOptions !== []): ?>
                    <div class="mt-8 border-t border-slate-200 pt-6">
                        <div class="text-2xl font-semibold text-slate-900">Buy with Box <span class="text-rose-500">*</span></div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700">
                                <input type="radio" name="buy_with_box" value="1" class="box-choice" <?= $defaultBox ? 'checked' : ''; ?>>
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700">
                                <input type="radio" name="buy_with_box" value="0" class="box-choice" <?= $defaultBox ? '' : 'checked'; ?>>
                                <span>No</span>
                            </label>
                        </div>

                        <div id="box-option-panel" class="mt-5 <?= $defaultBox ? '' : 'hidden'; ?> rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="grid gap-5 lg:grid-cols-[200px,1fr] lg:items-start">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Choose box</label>
                                    <select id="box-option-select" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none">
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
                                </div>
                                <div class="grid gap-4 sm:grid-cols-[140px,1fr,84px] sm:items-start">
                                    <div class="overflow-hidden rounded-xl bg-white">
                                        <img id="box-option-image" src="<?= $defaultBox ? e(upload_url((string) $defaultBox['image'])) : ''; ?>" alt="" class="h-36 w-full object-cover">
                                    </div>
                                    <div>
                                        <div id="box-option-name" class="text-xl font-semibold text-slate-900"><?= $defaultBox ? e((string) $defaultBox['name']) : ''; ?></div>
                                        <div id="box-option-price" class="mt-3 text-lg font-semibold text-slate-900"><?= $defaultBox ? e(money((float) $defaultBox['price'])) : ''; ?></div>
                                        <div class="mt-5 text-xs uppercase tracking-[0.18em] text-slate-400">Options amount</div>
                                        <div id="box-option-total" class="mt-1 text-base font-semibold text-slate-700"><?= $defaultBox ? e(money((float) $defaultBox['price'])) : e(money(0)); ?></div>
                                        <div class="mt-4 text-xs uppercase tracking-[0.18em] text-slate-400">Final total</div>
                                        <div id="final-total" class="mt-1 text-3xl font-semibold text-slate-900"><?= e(money((float) $product['price'] + (float) ($defaultBox['price'] ?? 0))); ?></div>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">Box qty</label>
                                        <input id="box-qty" type="number" min="1" max="<?= max(1, (int) $product['stock']); ?>" value="1" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-center text-sm outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-500">
                        Box options are not available for this watch.
                    </div>
                <?php endif; ?>

                <div class="mt-6 grid gap-4 md:grid-cols-[1fr,auto] md:items-end">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Quantity</label>
                        <div class="inline-flex items-center overflow-hidden rounded-md border border-slate-300">
                            <button type="button" class="qty-btn border-r border-slate-300 px-4 py-3 text-sm font-semibold" data-step="-1">-</button>
                            <input id="product-qty" type="number" min="1" max="<?= max(1, (int) $product['stock']); ?>" value="1" class="w-16 border-none text-center text-sm outline-none" <?= (int) $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <button type="button" class="qty-btn border-l border-slate-300 px-4 py-3 text-sm font-semibold" data-step="1">+</button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <?php if ((int) $product['stock'] > 0): ?>
                            <form id="add-to-cart-form" action="<?= e(app_url('api/cart.php')); ?>" method="post">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="box_option_id" value="<?= $defaultBox ? (int) $defaultBox['id'] : ''; ?>">
                                <input type="hidden" name="box_quantity" value="<?= $defaultBox ? '1' : '0'; ?>">
                                <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">
                                <button type="submit" class="bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">ADD TO CART</button>
                            </form>

                            <form id="buy-now-form" action="<?= e(app_url('api/cart.php')); ?>" method="post">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="box_option_id" value="<?= $defaultBox ? (int) $defaultBox['id'] : ''; ?>">
                                <input type="hidden" name="box_quantity" value="<?= $defaultBox ? '1' : '0'; ?>">
                                <input type="hidden" name="redirect" value="checkout.php">
                                <button type="submit" class="bg-black-light px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-500">BUY NOW</button>
                            </form>
                        <?php else: ?>
                            <button type="button" disabled class="cursor-not-allowed bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-500">OUT OF STOCK</button>
                        <?php endif; ?>

                        <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                            <input type="hidden" name="redirect" value="<?= e('product.php?id=' . $productId); ?>">
                            <button type="submit" class="inline-flex items-center gap-2 border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                <span><?= $wishlisted ? 'Remove Wishlist' : 'Save Wishlist'; ?></span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6 text-xs uppercase tracking-[0.18em] text-slate-400">Shipping days 4 to 7 days</div>

                <div class="mt-4 grid grid-cols-3 gap-4 border-t border-slate-200 pt-5 text-center text-xs text-slate-600">
                    <div>
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">FD</div>
                        <div class="mt-2 font-medium">Free Delivery</div>
                    </div>
                    <div>
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">RT</div>
                        <div class="mt-2 font-medium">48 Hours Returnable</div>
                    </div>
                    <div>
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">COD</div>
                        <div class="mt-2 font-medium">Cash On Delivery</div>
                    </div>
                </div>
            </div>

            <div class="space-y-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
                <details open class="group border-b border-slate-200 pb-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-[0.16em] text-slate-700">
                        Product Description
                        <span class="text-lg transition group-open:rotate-180">^</span>
                    </summary>
                    <div class="pt-4 text-sm leading-7 text-slate-600">
                        <p><?= nl2br(e((string) $product['description'])); ?></p>
                        <p class="mt-3">If you choose a box option, its price is added to the final total and stored with the order details.</p>
                    </div>
                </details>

                <details class="group border-b border-slate-200 pb-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-[0.16em] text-slate-700">
                        Exchange Policy
                        <span class="text-lg transition group-open:rotate-180">^</span>
                    </summary>
                    <div class="pt-4 text-sm leading-7 text-slate-600">
                        Eligible exchange requests can be raised within 48 hours of delivery if the product is unused and in original packaging.
                    </div>
                </details>

                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold uppercase tracking-[0.16em] text-slate-700">
                        Return Policy
                        <span class="text-lg transition group-open:rotate-180">^</span>
                    </summary>
                    <div class="pt-4 text-sm leading-7 text-slate-600">
                        Returns are accepted only for damaged or incorrect items. Contact support with product photos and the order number.
                    </div>
                </details>
            </div>

            <div class="rounded-2xl bg-gradient-to-br from-violet-100 via-fuchsia-50 to-indigo-100 p-6 shadow-soft">
                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-violet-700">Step By Step</div>
                <div class="mt-5 grid grid-cols-4 gap-4 text-center text-[11px] font-medium text-violet-800">
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white/80">1</div>
                        <div class="mt-2">Order Now</div>
                    </div>
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white/80">2</div>
                        <div class="mt-2">Team Calls</div>
                    </div>
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white/80">3</div>
                        <div class="mt-2">Video Call</div>
                    </div>
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white/80">4</div>
                        <div class="mt-2">Delivery</div>
                    </div>
                </div>
                <a href="tel:+916235559500" class="mt-6 inline-flex rounded-full bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">Chat With Support: +91 62355 9500</a>
            </div>
        </section>
    </div>

    <section class="mt-14 border-t border-slate-200 pt-8">
        <h2 class="text-xl font-semibold text-slate-900">Reviews</h2>
        <?php if ($reviews !== []): ?>
            <div class="mt-5 space-y-4">
                <?php foreach ($reviews as $review): ?>
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-900"><?= e($review['name']); ?></div>
                                <div class="mt-1 text-sm text-amber-500"><?= e(str_repeat('*', (int) $review['rating']) . str_repeat('-', 5 - (int) $review['rating'])); ?></div>
                            </div>
                            <div class="text-xs text-slate-400"><?= e(date('d M Y', strtotime((string) $review['created_at']))); ?></div>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600"><?= e((string) $review['comment']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-4 text-sm text-slate-500">There are no reviews yet.</p>
        <?php endif; ?>

        <div class="mt-8 border border-slate-900 bg-white p-5 md:p-6">
            <h3 class="text-lg font-semibold text-slate-900">Be the first to review &quot;<?= e($product['name']); ?>&quot;</h3>
            <?php if (is_logged_in()): ?>
                <form action="" method="post" class="mt-5 space-y-4">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Your rating</label>
                        <select name="rating" class="w-full border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900">
                            <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                                <option value="<?= $rating; ?>"><?= $rating; ?> star</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Your review</label>
                        <textarea name="comment" rows="5" class="w-full border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900"></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">SUBMIT</button>
                </form>
            <?php else: ?>
                <p class="mt-4 text-sm text-slate-600">Please <a href="<?= e(app_url('user/login.php')); ?>" class="font-semibold text-slate-900">sign in</a> to write a review.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="mt-14 border-t border-slate-200 pt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold uppercase tracking-[0.14em] text-slate-900">Related Products</h2>
            <a href="<?= e(app_url('shop.php?category=' . (int) $product['category_id'])); ?>" class="text-sm font-semibold text-slate-500">View More</a>
        </div>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($related as $item): ?>
                <article class="overflow-hidden bg-white shadow-soft">
                    <a href="<?= e(product_link($item)); ?>">
                        <img src="<?= e(upload_url((string) $item['image'])); ?>" alt="<?= e($item['name']); ?>" class="h-52 w-full object-cover" loading="lazy">
                    </a>
                    <div class="p-4">
                        <div class="text-[10px] uppercase tracking-[0.16em] text-slate-400">Watch</div>
                        <h3 class="mt-2 line-clamp-2 text-sm font-semibold text-slate-900"><?= e($item['name']); ?></h3>
                        <div class="mt-2 text-sm font-semibold text-slate-700"><?= e(money((float) $item['price'])); ?></div>
                        <a href="<?= e(product_link($item)); ?>" class="mt-4 inline-block bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">BUY NOW</a>
                    </div>
                </article>
            <?php endforeach; ?>
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

    function syncQty(value) {
        if (!qtyInput) {
            return;
        }

        const min = parseInt(qtyInput.min || '1', 10);
        const max = parseInt(qtyInput.max || '1', 10);
        const nextValue = Math.max(min, Math.min(max, value));
        qtyInput.value = String(nextValue);

        [cartForm, buyForm].forEach((form) => {
            const input = form?.querySelector('input[name="quantity"]');
            if (input) {
                input.value = String(nextValue);
            }
        });

        if (boxQtyInput && parseInt(boxQtyInput.value || '1', 10) > nextValue) {
            boxQtyInput.value = String(nextValue);
        }

        updateTotals();
    }

    function syncBoxFields() {
        const enabled = hasBoxOptions && document.querySelector('.box-choice:checked')?.value === '1';
        const selected = boxSelect?.selectedOptions?.[0] || null;
        const boxId = enabled && selected ? selected.value : '';
        const selectedQty = enabled ? Math.max(1, Math.min(parseInt(qtyInput?.value || '1', 10), parseInt(boxQtyInput?.value || '1', 10))) : 0;

        [cartForm, buyForm].forEach((form) => {
            const boxIdField = form?.querySelector('input[name="box_option_id"]');
            const boxQtyField = form?.querySelector('input[name="box_quantity"]');
            if (boxIdField) {
                boxIdField.value = boxId;
            }
            if (boxQtyField) {
                boxQtyField.value = String(selectedQty);
            }
        });
    }

    function updateBoxPreview() {
        if (!hasBoxOptions || !boxSelect) {
            return;
        }

        const selected = boxSelect.selectedOptions[0];
        const enabled = document.querySelector('.box-choice:checked')?.value === '1';
        const image = document.getElementById('box-option-image');
        const name = document.getElementById('box-option-name');
        const price = document.getElementById('box-option-price');

        if (selected && image) {
            image.src = selected.dataset.image || '';
        }
        if (selected && name) {
            name.textContent = selected.dataset.name || '';
        }
        if (selected && price) {
            const value = Number(selected.dataset.price || '0');
            price.textContent = new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR'
            }).format(value);
        }

        if (boxPanel) {
            boxPanel.classList.toggle('hidden', !enabled);
        }

        updateTotals();
    }

    function updateTotals() {
        const optionAmount = document.getElementById('box-option-total');
        const finalTotal = document.getElementById('final-total');
        const enabled = hasBoxOptions && document.querySelector('.box-choice:checked')?.value === '1';
        const selected = boxSelect?.selectedOptions?.[0] || null;
        const productQty = Math.max(1, parseInt(qtyInput?.value || '1', 10));
        let optionTotal = 0;

        if (enabled && selected) {
            const max = productQty;
            if (boxQtyInput) {
                boxQtyInput.max = String(max);
                boxQtyInput.value = String(Math.max(1, Math.min(max, parseInt(boxQtyInput.value || '1', 10))));
            }
            optionTotal = Number(selected.dataset.price || '0') * Math.max(1, parseInt(boxQtyInput?.value || '1', 10));
        }

        const total = (basePrice * productQty) + optionTotal;
        const formatter = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        });

        if (optionAmount) {
            optionAmount.textContent = formatter.format(optionTotal);
        }
        if (finalTotal) {
            finalTotal.textContent = formatter.format(total);
        }

        syncBoxFields();
    }

    document.querySelectorAll('.gallery-thumb').forEach((button) => {
        button.addEventListener('click', () => {
            const nextImage = button.dataset.image || '';
            if (mainImage && nextImage !== '') {
                mainImage.src = nextImage;
            }
        });
    });

    document.querySelectorAll('.qty-btn').forEach((button) => {
        button.addEventListener('click', () => {
            if (!qtyInput || qtyInput.disabled) {
                return;
            }
            const step = parseInt(button.dataset.step || '0', 10);
            syncQty(parseInt(qtyInput.value || '1', 10) + step);
        });
    });

    qtyInput?.addEventListener('input', () => syncQty(parseInt(qtyInput.value || '1', 10)));
    boxQtyInput?.addEventListener('input', updateTotals);
    boxSelect?.addEventListener('change', updateBoxPreview);
    document.querySelectorAll('.box-choice').forEach((choice) => choice.addEventListener('change', updateBoxPreview));

    syncQty(parseInt(qtyInput?.value || '1', 10));
    updateBoxPreview();
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>