<footer class="w-full bg-white-light/20 border-t">

    <div class="mx-auto max-w-7xl py-10 px-4 md:px-0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

            <div class="lg:col-span-2">
                <img
                    src="<?= e(asset_url('images/logo/logo.png')); ?>"
                    alt="Watch Ecommerce"
                    class="w-24">

                <p class="mt-4 text-sm text-black-light leading-6 max-w-sm">
                    A premium watch storefront offering modern and classic timepieces.
                    Built with secure checkout, customer accounts, and powerful admin
                    management tools.
                </p>

                <a href="<?= e(app_url('shop.php')); ?>"
                    class="mt-6 inline-block text-sm text-blue-500 underline font-medium hover:text-blue-600">
                    Explore Watch Collection
                </a>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-black-medium mb-4">Watch Collection</h4>

                <ul class="space-y-3 text-sm text-black-medium">
                    <li class="flex flex-col space-y-3">
                        <?php foreach ($headerCategories as $navCategory): ?>
                            <a
                                href="<?= e(app_url('shop.php?category=' . (int)$navCategory['id'])); ?>" class="text-nowrap hover:text-black-light">
                                <?= e($navCategory['name']); ?> Watch's
                            </a>
                        <?php endforeach; ?>
                    </li>

                    <li>
                        <a href="<?= e(app_url('shop.php')); ?>" class="hover:text-black-light">
                            All Watches
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-black-medium mb-4">Customer Support</h4>

                <ul class="space-y-3 text-sm text-black-medium">
                    <li>
                        <a href="<?= e(app_url('help.php')); ?>" class="hover:text-black-light">
                            Help Center
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(app_url('about.php')); ?>" class="hover:text-black-light">
                            About Us
                        </a>
                    </li>
                    <li>
                        <a href="tel:+916235559500" class="hover:text-black-light">
                            Call Support
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(app_url('checkout.php')); ?>" class="hover:text-black-light">
                            Payment & Checkout
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-black-medium mb-4">
                    My Account
                </h4>

                <ul class="space-y-3 text-sm text-black-medium">
                    <li>
                        <a href="<?= e(app_url('user/profile.php')); ?>" class="hover:text-black-light">
                            My Account
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(app_url('user/orders.php')); ?>" class="hover:text-black-light">
                            Order History
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(app_url('wishlist.php')); ?>" class="hover:text-black-light">
                            Wishlist
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(app_url('cart.php')); ?>" class="hover:text-black-light">
                            Shopping Cart
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="border-t border-dashed border-white-medium"></div>

    <div class="mx-auto max-w-7xl flex flex-col lg:flex-row items-center justify-between gap-6 py-4 px-4 md:px-0">

        <!-- LEFT -->
        <div class="text-center md:text-start text-xs text-black-light">
            © <?= date("Y"); ?> <?= e(APP_NAME); ?> INDIA. All Rights Reserved.<br>
            <span class="text-primary-medium font-medium">Designed by Websolvit.</span>
        </div>

        <!-- SOCIAL ICONS (COLORFUL) -->
        <div class="flex items-center gap-4">

            <a href="#" class="w-9 h-9 flex justify-center items-center rounded-lg bg-gradient-to-tr from-pink-500 via-red-500 to-yellow-500 hover:scale-105 transition">
                <i class="fab fa-instagram text-white-dark"></i>
            </a>

            <a href="#" class="w-9 h-9 flex justify-center items-center rounded-lg bg-blue-600 hover:scale-105 transition">
                <i class="fab fa-facebook-f text-white-dark"></i>
            </a>

            <a href="#" class="w-9 h-9 flex justify-center items-center rounded-lg bg-black-medium hover:scale-105 transition">
                <i class="fab fa-x-twitter text-white-dark"></i>
            </a>

            <a href="#" class="w-9 h-9 flex justify-center items-center rounded-lg bg-red-600 hover:scale-105 transition">
                <i class="fab fa-youtube text-white-dark"></i>
            </a>

        </div>

        <!-- PAYMENT ICONS -->
        <div class="flex items-center gap-3 bg-white-light/40 px-4 py-2 rounded-full border">

            <!-- UPI -->
            <span class="text-xs font-semibold text-black-medium px-2 py-1 bg-white-dark rounded">UPI</span>

            <!-- Visa -->
            <svg class="h-5" viewBox="0 0 48 16">
                <text x="0" y="14" font-size="14" font-weight="bold" fill="#1A1F71">VISA</text>
            </svg>

            <!-- MasterCard -->
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                <span class="w-3 h-3 bg-yellow-400 rounded-full -ml-2 opacity-80"></span>
            </div>

            <!-- RuPay -->
            <span class="text-xs font-semibold text-blue-600 px-2 py-1 bg-white-dark rounded">RuPay</span>

        </div>

    </div>

</footer>
<script src="<?= e(asset_url('js/app.js')); ?>"></script>
<script>
    lucide.createIcons();
</script>
</body>

</html>