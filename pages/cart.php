<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$cart = new CartModel();
$items = $cart->items((int) current_user()['id']);
$subtotal = $cart->subtotal((int) current_user()['id']);
$pageTitle = 'Shopping Cart';
require __DIR__ . '/layout/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="font-display text-4xl font-bold">Shopping cart</h1>
    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr,360px]">
        <section class="space-y-4">
            <?php foreach ($items as $item): ?>
                <article class="grid gap-4 rounded-[2rem] bg-white p-5 shadow-soft md:grid-cols-[120px,1fr,auto] md:items-center">
                    <img src="<?= e(upload_url((string) $item['image'])); ?>" alt="<?= e($item['name']); ?>" class="h-28 w-full rounded-2xl object-cover">
                    <div>
                        <div class="text-lg font-semibold"><?= e($item['name']); ?></div>
                        <div class="mt-1 text-sm text-slate-500"><?= e(money((float) $item['price'])); ?> each</div>
                        <form action="<?= e(app_url('api/cart.php')); ?>" method="post" class="mt-3 flex flex-wrap gap-3">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id']; ?>">
                            <input type="number" name="quantity" value="<?= (int) $item['quantity']; ?>" min="1" class="w-24 rounded-full border border-slate-200 px-4 py-2">
                            <button type="submit" name="action" value="update" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold">Update</button>
                            <button type="submit" name="action" value="remove" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">Remove</button>
                        </form>
                    </div>
                    <div class="text-right font-semibold text-brand-600"><?= e(money((float) $item['line_total'])); ?></div>
                </article>
            <?php endforeach; ?>
            <?php if (!$items): ?>
                <div class="rounded-[2rem] bg-white p-10 text-center shadow-soft">
                    <p class="text-slate-600">Your cart is empty.</p>
                    <a href="<?= e(app_url('shop.php')); ?>" class="mt-4 inline-flex rounded-full bg-brand-600 px-5 py-3 font-semibold text-white">Continue shopping</a>
                </div>
            <?php endif; ?>
        </section>
        <aside class="rounded-[2rem] bg-white p-6 shadow-soft">
            <h2 class="font-display text-2xl font-bold">Summary</h2>
            <div class="mt-6 space-y-3 text-sm">
                <div class="flex items-center justify-between"><span>Subtotal</span><span><?= e(money($subtotal)); ?></span></div>
                <div class="flex items-center justify-between"><span>Shipping</span><span>Free</span></div>
                <div class="border-t border-slate-100 pt-3 text-base font-semibold">
                    <div class="flex items-center justify-between"><span>Total</span><span><?= e(money($subtotal)); ?></span></div>
                </div>
            </div>
            <a href="<?= e(app_url('checkout.php')); ?>" class="mt-6 inline-flex w-full justify-center rounded-full bg-brand-600 px-5 py-3 font-semibold text-white">Proceed to checkout</a>
        </aside>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>

