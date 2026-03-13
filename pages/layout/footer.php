<footer class="mt-20 border-t border-slate-200 bg-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 md:grid-cols-4">
        <div>
            <div class="font-display text-lg font-bold">Watch Ecommerce</div>
            <p class="mt-3 text-sm text-slate-600">A complete watch storefront with secure checkout, account tools, and admin operations.</p>
        </div>
        <div>
            <div class="font-semibold">Shop</div>
            <div class="mt-3 space-y-2 text-sm text-slate-600">
                <a class="block" href="<?= e(app_url('shop.php')); ?>">All watches</a>
                <a class="block" href="<?= e(app_url('wishlist.php')); ?>">Wishlist</a>
                <a class="block" href="<?= e(app_url('cart.php')); ?>">Cart</a>
            </div>
        </div>
        <div>
            <div class="font-semibold">Account</div>
            <div class="mt-3 space-y-2 text-sm text-slate-600">
                <a class="block" href="<?= e(app_url('user/profile.php')); ?>">My account</a>
                <a class="block" href="<?= e(app_url('user/orders.php')); ?>">Orders</a>
                <a class="block" href="<?= e(app_url('checkout.php')); ?>">Checkout</a>
            </div>
        </div>
        <div>
            <div class="font-semibold">Admin</div>
            <div class="mt-3 space-y-2 text-sm text-slate-600">
                <a class="block" href="<?= e(app_url('admin/index.php')); ?>">Dashboard</a>
                <a class="block" href="<?= e(app_url('admin/manage_products.php')); ?>">Products</a>
                <a class="block" href="<?= e(app_url('admin/manage_orders.php')); ?>">Orders</a>
            </div>
        </div>
    </div>
</footer>
<script src="<?= e(asset_url('js/app.js')); ?>"></script>
</body>
</html>

