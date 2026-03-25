<footer class="w-full bg-white-light/20 border-t">

    <!-- TOP SECTION -->
    <div class="mx-auto max-w-7xl py-10 px-4 md:px-0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

            <!-- BRAND -->
            <div class="lg:col-span-2">
                <!-- LOGO -->
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

            <!-- WATCH COLLECTION -->
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


            <!-- CUSTOMER SUPPORT -->
            <div>
                <h4 class="text-sm font-semibold text-black-medium mb-4">Customer Support</h4>

                <ul class="space-y-3 text-sm text-black-medium">

                    <li>
                        <a href="<?= e(app_url('contact.php')); ?>" class="hover:text-black-light">
                            Contact Us
                        </a>
                    </li>

                    <li>
                        <a href="<?= e(app_url('faq.php')); ?>" class="hover:text-black-light">
                            FAQ
                        </a>
                    </li>

                    <li>
                        <a href="<?= e(app_url('shipping.php')); ?>" class="hover:text-black-light">
                            Shipping Policy
                        </a>
                    </li>

                    <li>
                        <a href="<?= e(app_url('returns.php')); ?>" class="hover:text-black-light">
                            Returns & Warranty
                        </a>
                    </li>

                </ul>
            </div>


            <!-- MY ACCOUNT -->
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


    <!-- DIVIDER -->
    <div class="border-t border-dashed border-white-medium"></div>


    <!-- BOTTOM SECTION -->
    <div class="mx-auto max-w-7xl flex flex-col lg:flex-row items-center justify-between gap-8 py-4 px-4 md:px-0">

        <!-- COPYRIGHT -->
        <div class="text-center text-xs text-white-medium">
            © <?= date("Y"); ?> <?= e(APP_NAME); ?>. All Rights Reserved.
        </div>

        <!-- SOCIAL -->
        <div class="flex items-center gap-4">

            <a href="#" class="group w-8 h-8 flex justify-center items-center rounded-full bg-gray-100 hover:bg-pink-100 transition">
                <i class="fab fa-instagram text-black-light group-hover:text-pink-500"></i>
            </a>

            <a href="#" class="group w-8 h-8 flex justify-center items-center rounded-full bg-gray-100 hover:bg-blue-100 transition">
                <i class="fab fa-facebook-f text-black-light group-hover:text-blue-600"></i>
            </a>

            <a href="#" class="group w-8 h-8 flex justify-center items-center rounded-full bg-gray-100 hover:bg-gray-200 transition">
                <i class="fab fa-twitter text-black-light group-hover:text-black-medium"></i>
            </a>

            <a href="#" class="group w-8 h-8 flex justify-center items-center rounded-full bg-gray-100 hover:bg-red-100 transition">
                <i class="fab fa-youtube text-black-light group-hover:text-red-500"></i>
            </a>

        </div>

        <!-- PAYMENT METHODS -->
        <div class="text-xs text-black-light flex items-center gap-2 text-center px-4 py-2 border rounded-full">
            Secure Payments | UPI | Cards | Net Banking
        </div>


    </div>

</footer>
<script src="<?= e(asset_url('js/app.js')); ?>"></script>
<script>
    // Initialize Lucide Icons
    lucide.createIcons();
</script>
</body>

</html>