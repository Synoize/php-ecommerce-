<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pageTitle = 'Help & Support | Watch Ecommerce';
$pageDescription = 'Get help with orders, shipping, payments, returns, warranty questions, and general support for Watch Ecommerce.';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-7xl px-4 py-12 md:pt-12">
    <section class="rounded-[2rem] border bg-white p-6 shadow md:p-10">
        <span class="inline-flex rounded-full bg-primary-light px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary-dark">Help & Support</span>
        <h1 class="mt-5 max-w-3xl font-display text-4xl font-bold text-black-medium md:text-5xl">Answers for common questions before and after your order.</h1>
        <p class="mt-4 max-w-2xl text-base leading-7 text-black-light">Use this page for fast help with products, payments, shipping, warranty support, and returns. If you still need assistance, our support team can guide you further.</p>
    </section>

    <section class="mt-10 grid gap-6 lg:grid-cols-[0.9fr,1.1fr]">
        <div class="rounded-[2rem] bg-primary-dark p-6 text-white-dark shadow md:p-8">
            <h2 class="text-2xl font-bold">Support Contacts</h2>
            <div class="mt-6 space-y-5 text-sm text-white-light">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-white-medium">Call / WhatsApp</div>
                    <a href="tel:+916235559500" class="mt-2 inline-block text-lg font-semibold text-white-dark">+91 62355 9500</a>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-white-medium">Store Support</div>
                    <p class="mt-2 leading-6">Support is available for order updates, payment issues, damaged product reports, and product questions.</p>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-white-medium">Quick Actions</div>
                    <div class="mt-3 flex flex-wrap gap-3">
                        <a href="<?= e(app_url('shop.php')); ?>" class="rounded-full bg-white-dark px-5 py-2 text-sm font-semibold text-primary-dark">Shop Now</a>
                        <a href="<?= e(app_url('about.php')); ?>" class="rounded-full border border-white-light/30 px-5 py-2 text-sm font-semibold text-white-dark">About Us</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl bg-white p-6 shadow">
                <h2 class="text-lg font-bold text-black-medium">Shipping</h2>
                <p class="mt-3 text-sm leading-6 text-black-light">Orders are typically delivered within 3 to 7 working days depending on location and service availability.</p>
            </article>
            <article class="rounded-3xl bg-white p-6 shadow">
                <h2 class="text-lg font-bold text-black-medium">Cash On Delivery</h2>
                <p class="mt-3 text-sm leading-6 text-black-light">Cash on Delivery is available across India for eligible orders and serviceable areas.</p>
            </article>
            <article class="rounded-3xl bg-white p-6 shadow">
                <h2 class="text-lg font-bold text-black-medium">Returns & Damage Issues</h2>
                <p class="mt-3 text-sm leading-6 text-black-light">Returns are generally supported for damaged or incorrect items. Contact support with product photos and your order details.</p>
            </article>
            <article class="rounded-3xl bg-white p-6 shadow">
                <h2 class="text-lg font-bold text-black-medium">Warranty</h2>
                <p class="mt-3 text-sm leading-6 text-black-light">Many listed products mention a machine replacement warranty period in the product description. Check each product page for exact coverage.</p>
            </article>
        </div>
    </section>

    <section class="mt-12 rounded-[2rem] bg-white p-6 shadow md:p-10">
        <h2 class="text-2xl font-bold text-black-medium">Frequently Asked Questions</h2>
        <div class="mt-6 space-y-4">
            <details class="rounded-2xl border border-slate-200 p-5" open>
                <summary class="cursor-pointer text-base font-semibold text-black-medium">Do box kits cost extra?</summary>
                <p class="mt-3 text-sm leading-6 text-black-light">Yes, some products may have extra charges for original box kits. If shared box options are available, the added cost will be shown during selection.</p>
            </details>
            <details class="rounded-2xl border border-slate-200 p-5">
                <summary class="cursor-pointer text-base font-semibold text-black-medium">How can I track my order?</summary>
                <p class="mt-3 text-sm leading-6 text-black-light">Log in to your account and open the orders page to view your recent purchases and order status updates.</p>
            </details>
            <details class="rounded-2xl border border-slate-200 p-5">
                <summary class="cursor-pointer text-base font-semibold text-black-medium">What should I do if my product arrives damaged?</summary>
                <p class="mt-3 text-sm leading-6 text-black-light">Contact support as soon as possible with clear photos of the item and packaging. Include your order number for faster resolution.</p>
            </details>
            <details class="rounded-2xl border border-slate-200 p-5">
                <summary class="cursor-pointer text-base font-semibold text-black-medium">Can I place an order without creating an account?</summary>
                <p class="mt-3 text-sm leading-6 text-black-light">Account features help with order history, address management, and wishlist access. If the checkout flow requires sign-in, creating an account is the fastest option.</p>
            </details>
        </div>
    </section>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
