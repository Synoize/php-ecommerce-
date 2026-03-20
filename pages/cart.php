<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$cart = new CartModel();
$items = $cart->items((int) current_user()['id']);
$subtotal = $cart->subtotal((int) current_user()['id']);
$pageTitle = 'Shopping Cart';
require __DIR__ . '/layout/header.php';
?>

<main class="mt-28 mx-auto max-w-7xl px-4 py-10">

    <!-- Heading -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-black-medium">
            Shopping Cart
        </h1>
        <span class="text-sm text-black-light">
            <?= count($items); ?> items
        </span>
    </div>

    <div class="mt-8 min-h-[calc(100vh-262px)] grid gap-8 lg:grid-cols-[1fr,380px]">

        <!-- CART ITEMS -->
        <section class="space-y-5">

            <?php if ($items): ?>
                <?php foreach ($items as $item): ?>

                    <article class="group relative grid gap-4 rounded-xl border bg-white-light/10 p-4 transition
               grid-cols-[80px,1fr] lg:grid-cols-[120px,1fr,auto]">

                        <!-- IMAGE -->
                        <div class="overflow-hidden rounded-lg border bg-white-dark h-24 md:h-28 mx-auto sm:mx-0 w-full max-w-[140px]">
                            <img src="<?= e(upload_url((string) $item['image'])); ?>"
                                alt="<?= e($item['name']); ?>"
                                class="h-24 md:h-28 w-full object-contain p-2 transition duration-300 group-hover:scale-105">
                        </div>

                        <!-- DETAILS -->
                        <div class="flex flex-col justify-between">

                            <div>
                                <h3 class="text-sm sm:text-base md:text-lg font-semibold text-black-medium">
                                    <?= e($item['name']); ?>
                                </h3>

                                <p class="mt-1 text-xs sm:text-sm text-black-light">
                                    <?= e(money((float) $item['price'])); ?> each
                                </p>

                                <!-- BOX OPTION -->
                                <?php if (!empty($item['box_name']) && (int) $item['box_quantity'] > 0): ?>
                                    <div class="mt-2 rounded-lg bg-white-light/40 px-3 py-2 text-xs sm:text-sm">
                                        <div class="font-medium text-black-medium">
                                            Box: <?= e($item['box_name']); ?>
                                        </div>
                                        <div class="text-black-light">
                                            <?= e(money((float) ($item['box_price'] ?? 0))); ?> × <?= (int) $item['box_quantity']; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <form action="<?= e(app_url('api/cart.php')); ?>"
                                method="post"
                                class="mt-3 flex items-center gap-3">

                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $item['product_id']; ?>">
                                <input type="hidden" name="box_option_id" value="<?= (int) ($item['box_option_id'] ?? 0); ?>">
                                <input type="hidden" name="box_quantity" value="<?= (int) ($item['box_quantity'] ?? 0); ?>">

                                <!-- hidden action -->
                                <input type="hidden" name="action" value="update">

                                <!-- QUANTITY -->
                                <div class="flex items-center rounded-lg border overflow-hidden">

                                    <button type="button"
                                        class="p-2 md:p-3 bg-white-light/40 hover:bg-white-light/60 qty-btn"
                                        data-step="-1">
                                        <i data-lucide="minus" class="w-4 h-4"></i>
                                    </button>

                                    <input type="number"
                                        name="quantity"
                                        value="<?= (int) $item['quantity']; ?>"
                                        min="1"
                                        max="<?= max(1, (int) $item['stock']); ?>"
                                        class="w-10 md:ml-3 text-center text-sm outline-none bg-transparent qty-input">

                                    <button type="button"
                                        class="p-2 md:p-3 bg-white-light/40 hover:bg-white-light/60 qty-btn"
                                        data-step="1">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </button>
                                </div>

                            </form>
                        </div>

                        <!-- Total Price -->
                        <div class="flex flex-row self-end items-center gap-10 text-nowrap">
                            <span class="text-xs text-black-light">
                                Quantity: <span class="font-semibold"><?= (int) $item['quantity']; ?></span>
                            </span>

                            <span class="text-lg sm:text-xl font-bold text-black-medium">
                                <?= e(money((float) $item['line_total'])); ?>
                            </span>
                            
                        </div>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <!-- EMPTY STATE -->
                <div class="flex flex-col items-center justify-center py-16 border border-dashed rounded-xl text-center">

                    <i data-lucide="shopping-cart" class="w-14 h-14 text-white-medium mb-3"></i>

                    <h3 class="text-lg font-semibold text-black-medium">
                        Your cart is empty
                    </h3>

                    <p class="text-sm text-white-medium mt-1">
                        Looks like you haven't added anything yet.
                    </p>

                    <a href="<?= e(app_url('shop.php')); ?>"
                        class="mt-5 inline-flex items-center gap-2 rounded-full bg-primary-medium px-6 py-3 text-sm text-white-dark hover:bg-primary-medium/90 transition">
                        Start Shopping
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>

                </div>

            <?php endif; ?>

        </section>

        <!-- SUMMARY -->
        <?php if ($items): ?>
            <aside class="sticky top-28 h-fit rounded-xl border bg-white-light/10 p-6">

                <h2 class="text-xl font-semibold text-black-medium">Order Summary</h2>

                <div class="mt-6 space-y-4 text-sm">

                    <div class="flex justify-between text-black-light">
                        <span>Subtotal</span>
                        <span><?= e(money($subtotal)); ?></span>
                    </div>

                    <div class="flex justify-between text-black-light">
                        <span>Shipping</span>
                        <span class="text-green-600 font-medium">Free</span>
                    </div>

                    <div class="border-t pt-4 flex justify-between text-lg font-bold text-black-medium">
                        <span>Total</span>
                        <span><?= e(money($subtotal)); ?></span>
                    </div>

                </div>

                <a href="<?= e(app_url('checkout.php')); ?>"
                    class="mt-6 w-full inline-flex justify-center rounded-lg bg-primary-medium px-5 py-3 text-sm font-semibold text-white-dark hover:bg-primary-medium/90 transition">
                    Proceed to Checkout
                </a>

            </aside>
        <?php endif; ?>

    </div>
</main>

<!-- QUANTITY SCRIPT -->
<script>
    document.querySelectorAll('form').forEach(form => {

        const input = form.querySelector('.qty-input');
        const buttons = form.querySelectorAll('.qty-btn');
        const actionInput = form.querySelector('input[name="action"]');

        if (!input) return;

        const submitForm = () => {
            form.submit();
        };

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {

                let value = parseInt(input.value || 1);
                const step = parseInt(btn.dataset.step);

                value += step;

                const min = parseInt(input.min || 1);
                const max = parseInt(input.max || 999);

                // IMPORTANT FIX
                if (value <= 0) {
                    actionInput.value = 'remove'; // send remove
                    submitForm();
                    return;
                }

                value = Math.max(min, Math.min(max, value));
                input.value = value;

                actionInput.value = 'update'; // normal update
                submitForm();
            });
        });

        // manual input change
        input.addEventListener('change', () => {

            let value = parseInt(input.value || 1);

            if (value <= 0) {
                actionInput.value = 'remove';
            } else {
                actionInput.value = 'update';
            }

            submitForm();
        });

    });
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>