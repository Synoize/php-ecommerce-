<?php
require_once __DIR__ . '/includes/config.php';
$flash = getFlash();
$user = currentUser();
$stmtCat = $pdo->query('SELECT * FROM categories ORDER BY id DESC LIMIT 6');
$categories = $stmtCat->fetchAll();

$stmtTrend = $pdo->query('SELECT * FROM products ORDER BY id DESC LIMIT 8');
$trending = $stmtTrend->fetchAll();

$slides = [
  [
    'file_path' => 'images/carousel/_01.mp4',
    'title' => 'Summer Collection 2026',
    'description' => 'Discover lightweight styles & fresh arrivals for this season.',
    'button_name' => 'Shop Now',
    'button_link' => 'shop.php?category=all'
  ],
  [
    'file_path' => 'images/carousel/_01.png',
    'title' => 'Mega Electronics Sale',
    'description' => 'Up to 40% Off on gadgets, accessories & more.',
    'button_name' => 'Our Brands',
    'button_link' => 'shop.php?category=1'
  ],
  [
    'file_path' => 'images/carousel/_01.png',
    'title' => 'Streetwear Drop',
    'description' => 'Limited edition fashion pieces. Grab yours today.',
    'button_name' => 'Explore More',
    'button_link' => 'shop.php?category=2'
  ],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>BIG BRANDS INDIA - WATCHES</title>
  <link rel="icon" href="<?php echo e(asset('images/logo/favicon.svg')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="<?php echo e(BASE_URL); ?>/tailwind.config.js"></script>
</head>

<body>
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <main class="mt-28 mb-12">
    <?php if ($flash): ?>
      <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?php echo $flash['type'] === 'success' ? 'border-green-200 bg-green-50 text-green-800' : ($flash['type'] === 'danger' ? 'border-red-200 bg-red-50 text-red-800' : 'border bg-white-light/5 text-black-light'); ?>" role="alert">
        <?php echo e($flash['message']); ?>
      </div>
    <?php endif; ?>

    <section class="w-full">
      <div class="relative w-full overflow-hidden">

        <!-- Slides -->
        <div id="slider" class="flex transition-transform duration-700 ease-in-out">

          <?php foreach ($slides as $slide): ?>
            <div class="min-w-full relative">

              <!-- Video or Image -->
              <?php if (pathinfo($slide['file_path'], PATHINFO_EXTENSION) === 'mp4'): ?>
                <video src="<?= asset($slide['file_path']); ?>"
                  class="w-full h-[260px] md:h-[calc(100vh-112px)] object-cover brightness-50" autoplay muted loop></video>
              <?php else: ?>
                <img src="<?= asset($slide['file_path']); ?>"
                  class="w-full h-[260px] md:h-[calc(100vh-112px)] object-cover brightness-50">
              <?php endif; ?>

              <!-- Dark Overlay -->
              <div class="absolute inset-0 bg-black/40"></div>

              <!-- Content -->
              <div class="absolute inset-0 flex items-center">
                <div class="p-10 md:px-32 md:py-20 max-w-3xl w-full h-full flex flex-col justify-between items-start">

                  <?php if (!empty($slide['title'])): ?>
                    <div class="flex flex-col">
                      <h2 class="text-2xl md:text-5xl font-bold text-white-dark leading-tight">
                        <?= $slide['title']; ?>
                      </h2>

                      <p class="mt-4 font-light text-sm md:text-lg text-white-dark">
                        <?= htmlspecialchars((string)($slide['description'] ?? '')); ?>
                      </p>
                    </div>

                    <!-- CTA Button -->
                    <a href="<?= e(BASE_URL); ?>/<?= e($slide['button_link']); ?>"
                      class="group mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-full
                        text-white-dark bg-primary-dark hover:bg-primary-light transition-all duration-300 shadow-lg">

                      <span class="text-sm font-medium"><?= htmlspecialchars((string)($slide['button_name'] ?? '')); ?></span>

                      <i data-lucide="arrow-right"
                        class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"></i>
                    </a>
                  <?php endif; ?>

                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div>

        <!-- Arrows -->
        <button onclick="prevSlide()"
          class="absolute left-4 top-1/2 -translate-y-1/2 bg-white-dark/90 hover:bg-white-dark
             w-9 h-9 md:w-11 md:h-11 rounded-full flex items-center
             justify-center shadow-lg z-10 transition">
          <i data-lucide="chevron-left" class="w-5 h-5 text-black-light"></i>
        </button>

        <button onclick="nextSlide()"
          class="absolute right-4 top-1/2 -translate-y-1/2 bg-white-dark/90 hover:bg-white-dark
             w-9 h-9 md:w-11 md:h-11 rounded-full flex items-center
             justify-center shadow-lg z-10 transition">
          <i data-lucide="chevron-right" class="w-5 h-5 text-black-light"></i>
        </button>

        <!-- Dots -->
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-3 z-10">
          <?php foreach ($slides as $i => $s): ?>
            <button onclick="goToSlide(<?= $i; ?>)"
              class="dotSlider w-3 h-3 bg-white-light/50 rounded-full transition"></button>
          <?php endforeach; ?>
        </div>

      </div>
    </section>
    <script>
      const slider = document.getElementById("slider");
      const dots = document.querySelectorAll(".dotSlider");

      let currentIndex = 0;
      const totalSlides = slider.children.length;
      let autoplay;

      function updateSlider() {
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;

        dots.forEach(dot => {
          dot.classList.remove("bg-white-dark");
          dot.classList.add("bg-white-light/50");
        });

        dots[currentIndex].classList.remove("bg-white-light/50");
        dots[currentIndex].classList.add("bg-white-dark");
      }

      function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
      }

      function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlider();
      }

      function goToSlide(index) {
        currentIndex = index;
        updateSlider();
      }

      // Autoplay
      function startAutoplay() {
        autoplay = setInterval(nextSlide, 4000);
      }

      function stopAutoplay() {
        clearInterval(autoplay);
      }

      startAutoplay();

      slider.parentElement.addEventListener("mouseenter", stopAutoplay);
      slider.parentElement.addEventListener("mouseleave", startAutoplay);

      // Swipe Support
      let startX = 0;

      slider.addEventListener("touchstart", (e) => {
        startX = e.touches[0].clientX;
      });

      slider.addEventListener("touchend", (e) => {
        let endX = e.changedTouches[0].clientX;

        if (startX - endX > 50) nextSlide();
        if (endX - startX > 50) prevSlide();
      });

      updateSlider();
      lucide.createIcons();
    </script>

    <section class="mx-auto max-w-7xl px-4">
      <h2 class="mt-14 text-xl md:text-3xl text-center font-semibold text-black-light">Featured Categories</h2>
      <div class="mt-10 max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">

        <?php foreach ($categories as $cat): ?>
          <a href="<?php echo e(BASE_URL); ?>/shop.php?category=<?php echo (int)$cat['id']; ?>"
            class="group flex flex-col items-center text-center">

            <!-- Image Box -->
            <div class="w-40 h-40 overflow-hidden flex items-center justify-center shadow-sm group-hover:brightness-90 transition duration-300">
              <img
                src="<?php echo e(uploadUrlOrLocal((string)$cat['image'])); ?>"
                alt="<?php echo e($cat['name']); ?>"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />

              <!-- <p class="absolute text-sm font-semibold text-white-dark transition-colors">
                <?php echo e($cat['name']); ?>
              </p> -->
            </div>

            <!-- Category Name -->
            <p class="mt-3 text-lg font-light text-black-light transition-colors">
              <?php echo e($cat['name']); ?>
            </p>

          </a>
        <?php endforeach; ?>

      </div>

      <div class="mt-14 flex justify-between items-center ">
        <h2 class="text-xl md:text-3xl font-semibold text-black-light">Trending Products</h2>
        <a href="<?php echo e(BASE_URL); ?>/shop.php"
          class="group p-2 px-4 border rounded-full text-sm hover:bg-white-light/10  inline-flex items-center gap-2 transition-all duration-300">

          <span class="text-sm text-white-light">See All</span>
          <i data-lucide="arrow-right"
            class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1 text-white-light"></i>
        </a>
      </div>
      <div class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-5 lg:grid-cols-6">

        <?php foreach (array_slice($trending, 0, 6) as $p): ?>
          <div class="group overflow-hidden">

            <!-- Image Container -->
            <div class="relative overflow-hidden bg-white-light/10">

              <!-- Wishlist Icon -->
              <button class="absolute top-3 right-3 z-10 bg-white-dark/80 p-2 rounded-full hover:bg-white-dark">
                <i data-lucide="heart" class="w-4 h-4"></i>
              </button>

              <!-- Product Image -->
              <a href="<?php echo e(BASE_URL); ?>/product.php?id=<?php echo (int)$p['id']; ?>">
                <img
                src="<?php echo e(uploadUrlOrLocal((string)$p['image'])); ?>"
                class="w-full h-54 object-cover transition duration-500 group-hover:scale-110"
                alt="<?php echo e($p['name']); ?>" />
              </a>

              <!-- Quick View -->
              <!-- <div class="absolute bottom-0 left-0 right-0 bg-black-dark hover:bg-black-light text-white-dark text-center py-2 opacity-0 group-hover:opacity-100 transition">
                QUICK VIEW
              </div> -->

            </div>

            <!-- Product Info -->
            <div class="pt-4">

              <!-- Category -->
              <p class="text-xs uppercase text-black-light tracking-wider">
                <?php
                $categories = [
                  1 => 'G - Shock Watch',
                  2 => 'Ladies Watch',
                  3 => 'Gents Watch',
                  4 => 'Automatic Mechanical Watch'
                ];

                echo e($categories[$p['category_id']] ?? 'Watch');
                ?>
              </p>

              <!-- Product Name -->
              <h3 class="text-sm font-medium text-black-light mt-1 line-clamp-2">
                <?php echo e($p['name']); ?>
              </h3>

              <!-- Price -->
              <p class="mt-3 text-lg font-semibold text-green-500">
                ₹<?php echo e(number_format((float)$p['price'], 2)); ?>
              </p>

              <!-- Button -->
              <a
                href="<?php echo e(BASE_URL); ?>/product.php?id=<?php echo (int)$p['id']; ?>"
                class="mt-4 block text-center bg-primary-dark hover:bg-primary-light text-white-dark py-3 text-sm font-semibold transition">
                BUY NOW
              </a>

            </div>

          </div>
        <?php endforeach; ?>

      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>