<?php
require_once __DIR__ . '/config.php';
$flash = getFlash();
$user = currentUser();
?>
<?php
try {
  $navCats = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
} catch (Throwable $e) {
  $navCats = [];
}
$cartCount = cartCount();
?>

<header class="fixed top-0 left-0 w-full bg-white-dark border-b z-40">
  <div id="topPromoBar"
    class="bg-primary-dark text-white-dark text-xs 
            transition-all duration-300 ease-in-out 
            overflow-hidden opacity-100">
    <div class="mx-auto max-w-7xl px-4 h-8 flex justify-center items-center text-center">
      Free Gifts on orders above <span class="font-semibold">₹1499</span>
    </div>
  </div>

  <div class="mx-auto max-w-7xl px-4">
    <div class="h-20 flex items-center justify-between gap-4 ">
      <a class="flex items-center" href="<?php echo e(BASE_URL); ?>">
        <img src="<?php echo e(asset('images/logo/logo.png')); ?>" alt="logo" class="h-12 w-auto" />
      </a>

      <button id="twNavToggle" class="lg:hidden inline-flex items-center justify-center text-primary-dark" type="button" aria-expanded="false" aria-controls="twNav">
        <i data-lucide="menu"></i>
      </button>

      <!-- Menu -->
      <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-primary-dark">

        <!-- Home -->
        <a href="<?php echo e(BASE_URL); ?>" class="hover:text-primary-light">
          Home
        </a>

        <!-- Shop -->
        <a class="flex items-center gap-1 hover:text-primary-light" href="<?php echo e(BASE_URL); ?>/shop.php">Collections</a>

        <!-- For categories -->
        <div class="flex items-center gap-6">
          <?php foreach ($navCats as $c): ?>
            <a class="flex items-center gap-1 text-black-light/70 hover:text-white-light" href="<?php echo e(BASE_URL); ?>/shop.php?category=<?php echo (int)$c['id']; ?>">
              <?php echo e($c['name']); ?>
            </a>
          <?php endforeach; ?>
        </div>

      </nav>

      <!-- Right -->
      <div class="hidden lg:flex items-center gap-4 text-black-light">
        <form action="<?php echo e(BASE_URL); ?>/shop.php" method="get" class="relative flex items-center">
          <input type="text" name="q" placeholder="Search products..." class="absolute right-9 px-3 py-2 text-sm outline-none border focus:border-primary-dark" />
          <button type="submit" class="p-2 bg-primary-dark text-white-dark hover:bg-primary-dark/80 border border-primary-dark">
            <i data-lucide="search" class="w-5 h-5"></i>
          </button>
        </form>

        <div class="relative" data-tw-dropdown>
          <?php if ($user): ?>
            <button type="button" class="p-2 rounded-md hover:text-white-light" data-tw-dropdown-btn title="Account">
              <i data-lucide="user" class="w-5 h-5"></i>
            </button>
          <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/user/profile.php">
              <button type="button" class="p-2 hover:text-white-light" title="Account">
                <i data-lucide="user" class="w-5 h-5"></i>
              </button>
            </a>
          <?php endif; ?>

          <div class="hidden absolute right-0 top-full mt-6 w-40 overflow-hidden border bg-white-dark shadow-soft z-50" data-tw-dropdown-menu>
            <?php if ($user): ?>
              <a class="block px-4 py-2 text-sm text-black-light hover:bg-white-light/10" href="<?php echo BASE_URL; ?>/user/profile.php">Dashboard</a>

              <a class="block px-4 py-2 text-sm text-black-light hover:bg-white-light/10" href="<?php echo BASE_URL; ?>/user/orders.php">Orders</a>

              <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a class="block px-4 py-2 text-sm text-black-light hover:bg-white-light/10" href="<?php echo BASE_URL; ?>/admin">Admin</a>
              <?php endif; ?>

              <div class="my-1 border-t"></div>

              <a class="block px-4 py-2 text-sm text-black-light hover:bg-white-light/10" href="<?php echo BASE_URL; ?>/user/logout.php">Logout</a>

            <?php else: ?>

              <a class="block px-4 py-2 text-sm text-black-light hover:bg-white-light/10" href="<?php echo BASE_URL; ?>/user/login.php">Login</a>

              <a class="block px-4 py-2 text-sm text-black-light hover:bg-white-light/10" href="<?php echo BASE_URL; ?>/user/signup.php">Signup</a>

            <?php endif; ?>
          </div>
        </div>

        <a class="relative p-2 rounded-md hover:text-white-light" href="<?php echo e(BASE_URL); ?>/cart.php" title="Cart">
          <i class="fas fa-bag-shopping text-lg"></i>
          <?php if ($cartCount > 0): ?>
            <span class="absolute -top-1 -right-1 bg-primary-light text-white-dark text-[10px] px-1.5 py-0.5 rounded-full">
              <?php echo $cartCount; ?>
            </span>
          <?php endif; ?>
        </a>
      </div>
    </div>
  </div>

  <!-- Mobile Nav Overlay -->
  <div id="twNav" class="fixed inset-0 bg-black-dark bg-opacity-50 z-50 lg:hidden hidden">
    <div class="absolute right-0 top-0 h-full w-72 bg-white-dark backdrop-blur-xl shadow-lg transform translate-x-full transition-transform duration-300">
      <div class="p-4">
        <button id="twNavClose" class="float-right text-black-light/70 hover:text-white-light">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <div class="clear-both mt-8 space-y-5">
          <form action="<?php echo e(BASE_URL); ?>/shop.php" method="get" class="flex items-center mb-4">
            <input type="text" name="q" placeholder="Search products..." class="flex-1 px-3 py-2 text-sm outline-none border focus:border-primary-dark" />
            <button type="submit" class="p-2 bg-primary-dark text-white-dark hover:bg-primary-dark/80 border border-primary-dark">
              <i data-lucide="search" class="w-5 h-5"></i>
            </button>
          </form>
          <div class="space-y-4 px-2">
            <a href="<?php echo e(BASE_URL); ?>" class="block text-primary-dark hover:text-primary-light">Home</a>
            <a href="<?php echo e(BASE_URL); ?>/shop.php" class="block text-primary-dark hover:text-primary-light">Collections</a>
            <?php foreach ($navCats as $c): ?>
              <a href="<?php echo e(BASE_URL); ?>/shop.php?category=<?php echo (int)$c['id']; ?>" class="block text-black-light/70 hover:text-white-light"><?php echo e($c['name']); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo e(BASE_URL); ?>/support.php" class="block text-primary-dark hover:text-primary-light">Help</a>
            <div class="border-t pt-4 space-y-4">
              <?php if ($user): ?>
                <a href="<?php echo BASE_URL; ?>/user/profile.php" class="block text-primary-dark hover:text-primary-light">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/user/orders.php" class="block text-primary-dark hover:text-primary-light">Orders</a>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                  <a href="<?php echo BASE_URL; ?>/admin" class="block text-primary-dark hover:text-primary-light">Admin</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/user/logout.php" class="block text-red-500 hover:text-red-600">Logout</a>
              <?php else: ?>
                <div class="grid grid-cols-2 gap-2">
                  <a href="<?php echo BASE_URL; ?>/user/login.php" class="block text-black-light bg-white-light/20 hover:bg-white-light/10 p-2 text-center">Login</a>
                  <a href="<?php echo BASE_URL; ?>/user/signup.php" class="block text-white-dark bg-primary-dark hover:bg-primary-light p-2 text-center">Signup</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<script>
  const promoBar = document.getElementById("topPromoBar");
  let lastScroll = 0;

  window.addEventListener("scroll", () => {
    const currentScroll = window.scrollY;

    if (currentScroll > 50 && currentScroll > lastScroll) {
      // scrolling down
      promoBar.classList.remove("max-h-10", "opacity-100");
      promoBar.classList.add("max-h-0", "opacity-0");
    } else {
      // scrolling up or top
      promoBar.classList.remove("max-h-0", "opacity-0");
      promoBar.classList.add("max-h-10", "opacity-100");
    }

    lastScroll = currentScroll;
  });

  // Mobile nav toggle
  const navToggle = document.getElementById("twNavToggle");
  const nav = document.getElementById("twNav");
  const navClose = document.getElementById("twNavClose");

  if (navToggle && nav) {
    navToggle.addEventListener("click", () => {
      nav.classList.remove("hidden");
      setTimeout(() => nav.querySelector("div").classList.remove("translate-x-full"), 10);
    });
  }

  if (navClose && nav) {
    navClose.addEventListener("click", () => {
      nav.querySelector("div").classList.add("translate-x-full");
      setTimeout(() => nav.classList.add("hidden"), 300);
    });
  }

  // Close on outside click
  if (nav) {
    nav.addEventListener("click", (e) => {
      if (e.target === nav) {
        nav.querySelector("div").classList.add("translate-x-full");
        setTimeout(() => nav.classList.add("hidden"), 300);
      }
    });
  }

  // Open User Menu
  document.querySelectorAll('[data-tw-dropdown]').forEach(dropdown => {

    const btn = dropdown.querySelector('[data-tw-dropdown-btn]');
    const menu = dropdown.querySelector('[data-tw-dropdown-menu]');

    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });

    document.addEventListener('click', function() {
      menu.classList.add('hidden');
    });

  });
</script>