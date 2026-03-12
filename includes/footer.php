<!-- MOBILE FOOTER NAV -->
<?php $cartCount = cartCount(); ?>
<div class="md:hidden block fixed bottom-0 left-0 w-full bg-white-dark border-t z-50 rounded-t-xl shadow-lg">
  <div class="grid grid-cols-5 text-center text-xs py-3">

    <!-- Home -->
    <a href="<?php echo e(BASE_URL); ?>"
      class="flex flex-col items-center gap-1 text-black-light active:text-primary-dark">
      <i data-lucide="home" class="w-5 h-5"></i>
      <span>Home</span>
    </a>

    <!-- Shop -->
    <a href="<?php echo e(BASE_URL); ?>/shop.php"
      class="flex flex-col items-center gap-1 text-black-light active:text-primary-dark">
      <i data-lucide="shopping-bag" class="w-5 h-5"></i>
      <span>Collections</span>
    </a>

    <!-- Categories -->
    <a href="<?php echo e(BASE_URL); ?>/categories.php"
      class="flex flex-col items-center gap-1 text-black-light active:text-primary-dark">
      <i data-lucide="grid-2x2" class="w-5 h-5"></i>
      <span>Categories</span>
    </a>

    <!-- Cart -->
    <a href="<?php echo e(BASE_URL); ?>/cart.php"
      class="relative flex flex-col items-center gap-1 text-black-light active:text-primary-dark">
      <i data-lucide="shopping-cart" class="w-5 h-5"></i>

      <?php if ($cartCount > 0): ?>
        <span class="absolute -top-1 right-4 bg-primary-light text-white-dark text-[10px] px-1.5 py-0.5 rounded-full">
          <?php echo $cartCount; ?>
        </span>
      <?php endif; ?>

      <span>Cart</span>
    </a>

    <!-- Account -->
    <button id="openMenu"
      class="flex flex-col items-center gap-1 text-black-light active:text-primary-dark">
      <i data-lucide="user" class="w-5 h-5"></i>
      <span>Account</span>
    </button>

  </div>
</div>

<!-- OVERLAY -->
<div id="menuOverlay"
  class="fixed inset-0 bg-black-dark/50 z-40 hidden">
</div>

<!-- BOTTOM SHEET MENU -->
<div id="bottomMenu"
  class="fixed bottom-0 left-0 w-full bg-white-dark rounded-t-3xl z-50
         translate-y-full transition-transform duration-300 ease-out">

  <!-- HANDLE -->
  <div class="flex justify-center py-3">
    <div class="w-12 h-1 bg-white-light rounded-full"></div>
  </div>

  <!-- USER INFO -->
  <div class="px-6 pb-4 border-b">
    <?php if ($user): ?>
      <p class="font-semibold text-black-light">
        Hello, <?php echo e($user['name']); ?>
      </p>
      <p class="text-sm text-white-light">
        Manage your account & orders
      </p>
    <?php else: ?>
      <p class="font-semibold text-black-light">Welcome</p>
      <p class="text-sm text-white-light">Login to access your account</p>
    <?php endif; ?>
  </div>

  <!-- MENU ITEMS -->
  <div class="grid grid-cols-2 gap-4 px-6 py-6 text-center">

    <?php if ($user): ?>

      <a href="<?php echo e(BASE_URL); ?>/user/profile.php"
        class="py-4 rounded-xl bg-white-light/10 hover:bg-white-light/20">
        Dashboard
      </a>

      <a href="<?php echo e(BASE_URL); ?>/user/orders.php"
        class="py-4 rounded-xl bg-white-light/10 hover:bg-white-light/20">
        Orders
      </a>

      <a href="<?php echo e(BASE_URL); ?>/wishlist.php"
        class="py-4 rounded-xl bg-white-light/10 hover:bg-white-light/20">
        Wishlist
      </a>

      <a href="<?php echo e(BASE_URL); ?>/user/logout.php"
        class="py-4 rounded-xl bg-red-100 text-red-600 hover:bg-red-200">
        Logout
      </a>

    <?php else: ?>

      <a href="<?php echo e(BASE_URL); ?>/user/login.php"
        class="py-4 rounded-xl bg-white-light/10 hover:bg-white-light/20">
        Login
      </a>

      <a href="<?php echo e(BASE_URL); ?>/user/signup.php"
        class="py-4 rounded-xl bg-red-500 text-white-dark hover:bg-red-600">
        Signup
      </a>

    <?php endif; ?>

  </div>

  <!-- CLOSE BUTTON -->
  <button id="closeMenu"
    class="w-full flex justify-center py-4 text-red-500 border-t">
    <i data-lucide="circle-x"></i>
  </button>

</div>

<footer class="px-4 md:px-[5%] w-full bg-white-light/5 border-t ">

  <!-- TOP SECTION -->
  <div class="py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

      <!-- BRAND -->
      <div class="lg:col-span-2">
        <h3 class="text-xl font-semibold text-primary-dark">
          BIG BRANDS INDIA
        </h3>

        <p class="mt-4 text-sm text-black-light leading-6 max-w-sm">
          Discover premium watches for every style. From elegant classic
          timepieces to modern smart designs, BIG BRANDS offers quality,
          durability, and style for every men, and women.
        </p>

        <a href="<?php echo e(BASE_URL); ?>"
          class="mt-6 inline-block text-sm text-blue-600 underline font-medium hover:text-blue-700">
          Explore Watch Collection
        </a>
      </div>

      <!-- WATCH COLLECTION -->
      <div>
        <h4 class="text-sm font-semibold text-black-light mb-4">Watch Collection</h4>
        <ul class="space-y-3 text-sm text-black-light">
          <li><a href="<?php echo e(BASE_URL); ?>/shop.php?category=1" class="hover:text-white-light">Men Watches</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/shop.php?category=2" class="hover:text-white-light">Women Watches</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/shop.php?category=3" class="hover:text-white-light">Kids Watches</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/shop.php" class="hover:text-white-light">All Watches</a></li>
        </ul>
      </div>

      <!-- CUSTOMER SUPPORT -->
      <div>
        <h4 class="text-sm font-semibold text-black-light mb-4">Customer Support</h4>
        <ul class="space-y-3 text-sm text-black-light">
          <li><a href="<?php echo e(BASE_URL); ?>/contact.php" class="hover:text-white-light">Contact Us</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/faq.php" class="hover:text-white-light">Watch Care Guide</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/shipping.php" class="hover:text-white-light">Shipping Policy</a></li>
          <li><a href="<?php echo e(BASE_URL); ?>/returns.php" class="hover:text-white-light">Returns & Warranty</a></li>
        </ul>
      </div>

      <!-- ACCOUNT -->
      <div>
        <h4 class="text-sm font-semibold text-black-light mb-4">My Account</h4>
        <ul class="space-y-3 text-sm text-black-light">

          <?php if ($user): ?>
            <li><a href="<?php echo e(BASE_URL); ?>/user/profile.php" class="hover:text-white-light">Dashboard</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/user/orders.php" class="hover:text-white-light">My Orders</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/wishlist.php" class="hover:text-white-light">Wishlist</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/user/logout.php" class="hover:text-red-600">Logout</a></li>
          <?php else: ?>
            <li><a href="<?php echo e(BASE_URL); ?>/user/login.php" class="hover:text-white-light">Login</a></li>
            <li><a href="<?php echo e(BASE_URL); ?>/user/signup.php" class="hover:text-white-light">Create Account</a></li>
          <?php endif; ?>

        </ul>
      </div>

    </div>
  </div>

  <!-- DIVIDER -->
  <div class="border-t-2 border-dashed border-white-light/50"></div>

  <!-- WATCH CATEGORIES -->
  <div class="py-8">
    <h3 class="text-lg font-semibold text-black-light mb-4">
      Browse Watch Categories
    </h3>

    <ul class="text-black-light text-sm flex gap-3 flex-wrap">
      <?php foreach ($navCats as $c): ?>
        <a class="flex items-center gap-1 hover:text-white-light text-nowrap"
           href="<?php echo e(BASE_URL); ?>/shop.php?category=<?php echo (int)$c['id']; ?>">
          <?php echo e($c['name']); ?> Watches
        </a>
        <span class="text-white-light">|</span>
      <?php endforeach; ?>

      <a href="<?php echo e(BASE_URL); ?>/shop.php" class="hover:text-white-light">View All</a>
    </ul>
  </div>

  <!-- DIVIDER -->
  <div class="border-t-2 border-dashed border-white-light/50"></div>

  <!-- BOTTOM SECTION -->
  <div class="pt-6 pb-24 md:pb-12">
    <div class="flex flex-col lg:flex-row items-center justify-between gap-6">

      <!-- LOGO -->
      <img src="<?php echo e(asset('images/logo/logo.png')); ?>"
        alt="BIG BRANDS WATCH STORE"
        class="w-24 md:w-32">

      <!-- COPYRIGHT -->
      <div class="text-center text-xs text-white-light">
        © <?= date("Y"); ?> BIG BRANDS INDIA. All Rights Reserved.
      </div>

      <!-- SOCIAL -->
      <div class="flex items-center gap-4">
        <a href="#"><i data-lucide="instagram" class="w-5 h-5 text-white-light hover:text-primary-light"></i></a>
        <a href="#"><i data-lucide="facebook" class="w-5 h-5 text-white-light hover:text-primary-light"></i></a>
        <a href="#"><i data-lucide="twitter" class="w-5 h-5 text-white-light hover:text-primary-light"></i></a>
        <a href="#"><i data-lucide="youtube" class="w-5 h-5 text-white-light hover:text-primary-light"></i></a>
      </div>

    </div>

    <!-- PAYMENT METHODS -->
    <div class="flex justify-center mt-6">
      <div class="text-xs text-white-light flex items-center gap-2 text-center px-4 py-2 border rounded-full shadow-sm">
        Secure Payments | UPI | Cards | Net Banking
      </div>
    </div>

  </div>

</footer>

<script>
  const openBtn = document.getElementById("openMenu");
  const closeBtn = document.getElementById("closeMenu");
  const menu = document.getElementById("bottomMenu");
  const overlay = document.getElementById("menuOverlay");

  function openMenu() {
    menu.classList.remove("translate-y-full");
    overlay.classList.remove("hidden");
  }

  function closeMenu() {
    menu.classList.add("translate-y-full");
    overlay.classList.add("hidden");
  }

  openBtn.addEventListener("click", openMenu);
  closeBtn.addEventListener("click", closeMenu);
  overlay.addEventListener("click", closeMenu);

  document.querySelectorAll("#bottomMenu a").forEach(link => {
    link.addEventListener("click", closeMenu);
  });
</script>