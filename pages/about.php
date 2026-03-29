<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pageTitle = 'About Us | Watch Ecommerce';
$pageDescription = 'Learn about Watch Ecommerce, our quality promise, shipping support, and why customers shop with us across India.';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-7xl px-4 py-12 md:pt-12">
    <section class="overflow-hidden rounded-[2rem] border bg-white-light/20">
        <div class="grid gap-10 px-6 py-10 md:px-10 lg:grid-cols-[1.2fr,0.8fr] lg:items-center lg:px-14 lg:py-16">
            <div>
                <span class="inline-flex rounded-full bg-primary-light px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary-dark">About Watch Ecommerce</span>
                <h1 class="mt-5 max-w-3xl font-display text-4xl font-bold leading-tight text-black-medium md:text-5xl">Premium-inspired watches, straightforward pricing, and support that stays helpful after the sale.</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-black-light md:text-lg">Watch Ecommerce is built for customers who want stylish everyday watches, trending designs, and a simple shopping experience. We focus on value, clear product details, secure checkout, and responsive support across India.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="<?= e(app_url('shop.php')); ?>" class="rounded-full bg-primary-medium px-6 py-3 text-sm font-semibold text-white-dark transition hover:bg-primary-medium/90">Shop Collection</a>
                    <a href="<?= e(app_url('help.php')); ?>" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-black-medium transition hover:bg-white-dark">Need Help?</a>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-3xl bg-white p-6 shadow-sm">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-medium">Quality Promise</div>
                    <p class="mt-3 text-sm leading-6 text-black-light">We highlight master-quality products clearly so customers know what they are buying. Product descriptions and support notes are written to reduce confusion before checkout.</p>
                </div>
                <div class="rounded-3xl bg-primary-dark p-6 text-white-dark shadow-sm">
                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-white-light">Store Focus</div>
                    <p class="mt-3 text-sm leading-6 text-white-light">Automatic styles, men&apos;s classics, and G-Shock-inspired models are organized so shoppers can browse quickly by category, price, and availability.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-12 grid gap-6 md:grid-cols-3">
        <article class="rounded-3xl bg-white p-6 shadow">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-light text-primary-dark">
                <i data-lucide="shield-check" class="h-6 w-6"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-black-medium">Trusted Shopping</h2>
            <p class="mt-3 text-sm leading-6 text-black-light">Secure checkout, order tracking, wishlist support, and account history are built in so customers can shop with confidence.</p>
        </article>
        <article class="rounded-3xl bg-white p-6 shadow">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-light text-primary-dark">
                <i data-lucide="truck" class="h-6 w-6"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-black-medium">Pan-India Delivery</h2>
            <p class="mt-3 text-sm leading-6 text-black-light">We support orders across India with clear communication around shipping, payment, and delivery expectations.</p>
        </article>
        <article class="rounded-3xl bg-white p-6 shadow">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-light text-primary-dark">
                <i data-lucide="messages-square" class="h-6 w-6"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-black-medium">Responsive Support</h2>
            <p class="mt-3 text-sm leading-6 text-black-light">Questions about products, boxes, checkout, or order issues can be handled through our help channels and support team.</p>
        </article>
    </section>

    <section class="mt-12 rounded-[2rem] bg-white p-6 shadow md:p-10">
        <div class="grid gap-8 lg:grid-cols-2">
            <div>
                <h2 class="text-2xl font-bold text-black-medium">Why Customers Choose Us</h2>
                <ul class="mt-5 space-y-4 text-sm leading-6 text-black-light">
                    <li class="flex gap-3"><i data-lucide="check-circle-2" class="mt-1 h-5 w-5 text-emerald-600"></i><span>Wide variety of watches across trending styles and classic looks.</span></li>
                    <li class="flex gap-3"><i data-lucide="check-circle-2" class="mt-1 h-5 w-5 text-emerald-600"></i><span>Simple admin-managed catalog so stock, pricing, and box options stay updated.</span></li>
                    <li class="flex gap-3"><i data-lucide="check-circle-2" class="mt-1 h-5 w-5 text-emerald-600"></i><span>Helpful storefront features like wishlist, cart, checkout, and order history.</span></li>
                </ul>
            </div>
            <div class="rounded-3xl bg-white-light/30 p-6">
                <h2 class="text-2xl font-bold text-black-medium">Important Store Notes</h2>
                <div class="mt-5 space-y-4 text-sm leading-6 text-black-light">
                    <p>Some products are sold as master-quality pieces, and those details are mentioned in the description so expectations stay clear.</p>
                    <p>Original box kit charges may apply depending on the product and selected box option.</p>
                    <p>Cash on Delivery availability, shipping updates, and issue resolution support are provided for customers across India.</p>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
