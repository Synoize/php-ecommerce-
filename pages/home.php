<?php
require_once __DIR__ . '/../config/bootstrap.php';

$store = new StoreController();
$featuredCategories = $store->categories->featured(4);
$featuredProducts = $store->products->featured(8);
$slides = (new SlideModel())->all();
$wishlistProductIds = [];

if (is_logged_in()) {
    $wishlistItems = (new WishlistModel())->items((int) current_user()['id']);
    $wishlistProductIds = array_fill_keys(
        array_map(
            static fn(array $item): int => (int) $item['product_id'],
            $wishlistItems
        ),
        true
    );
}

$featuredProductsVideo = [
    "images/uploads/reviews/_01.mp4",
    "images/uploads/reviews/_01.mp4",
    "images/uploads/reviews/_01.mp4",
    "images/uploads/reviews/_01.mp4",
    "images/uploads/reviews/_01.mp4",
];

$clientsReview = [
    "images/uploads/reviews/_01.png",
    "images/uploads/reviews/_01.png",
    "images/uploads/reviews/_01.png",
    "images/uploads/reviews/_01.png",
    "images/uploads/reviews/_01.png",
];

if ($slides === []) {
    $slides[] = [
        'type' => 'image',
        'file_path' => 'images/uploads/carousel/_01.mp4',
        'title' => 'Watch Ecommerce',
        'description' => 'Browse premium watches with cart, reviews, wishlist, checkout, and admin order tracking.',
        'button_name' => 'Shop Now',
        'button_link' => 'shop.php',
    ];
}

$pageTitle = 'Watch Ecommerce | Home';
$pageDescription = 'Shop premium watches with category filtering, reviews, wishlist, cart, and secure checkout.';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28">
    <!-- Carousel -->
    <section class="w-full">
        <div class="relative w-full overflow-hidden">
            <div id="slider" class="flex transition-transform duration-700 ease-in-out">
                <?php foreach ($slides as $slide): ?>
                    <div class="min-w-full relative">
                        <?php if (($slide['type'] ?? 'image') === 'video'): ?>
                            <video
                                src="<?= e(upload_url((string) $slide['file_path'])); ?>"
                                class="h-[260px] w-full object-cover brightness-50 md:h-[calc(100vh-112px)]"
                                autoplay
                                muted
                                loop></video>
                        <?php else: ?>
                            <img
                                src="<?= e(upload_url((string) $slide['file_path'])); ?>"
                                alt="<?= e((string) $slide['title']); ?>"
                                class="h-[260px] w-full object-cover brightness-50 md:h-[calc(100vh-112px)]">
                        <?php endif; ?>

                        <div class="absolute inset-0 flex items-center">
                            <div class="flex h-full w-full flex-col justify-between px-6 py-10 md:px-32 md:py-20">
                                <div class="max-w-3xl">
                                    <span class="inline-flex rounded-full bg-white-light px-4 py-2 text-xs text-black-light md:text-sm">Spring 2026 collection</span>
                                    <h2 class="mt-4 max-w-2xl text-2xl font-bold leading-tight text-white-dark md:mt-6 md:text-6xl">
                                        <?= e((string) $slide['title']); ?>
                                    </h2>
                                    <p class="mt-4 max-w-xl text-xs font-light text-white-light md:text-lg">
                                        <?= e((string) ($slide['description'] ?? '')); ?>
                                    </p>
                                </div>

                                <a
                                    href="<?= e(app_url((string) ($slide['button_link'] ?? 'shop.php'))); ?>"
                                    class="group mt-6 inline-flex w-fit items-center gap-2 rounded-full bg-primary-medium px-6 py-2 text-[10px] font-semibold text-white-dark transition-all duration-300 md:py-3 md:text-sm">
                                    <span><?= e((string) ($slide['button_name'] ?? 'Explore')); ?></span>
                                    <span class="transition-transform duration-300 group-hover:translate-x-1"><i data-lucide="arrow-right" class="h-3 w-3 md:h-4 md:w-4"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($slides) > 1): ?>
                <button
                    type="button"
                    onclick="prevSlide()"
                    class="absolute left-4 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white-dark/90 text-lg text-black-medium shadow-lg transition duration-300 hover:scale-105 hover:bg-white-dark md:h-12 md:w-12 md:text-xl">
                    &#8249;
                </button>

                <button
                    type="button"
                    onclick="nextSlide()"
                    class="absolute right-4 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white-dark/90 text-lg text-black-medium shadow-lg transition duration-300 hover:scale-105 hover:bg-white-dark md:h-12 md:w-12 md:text-xl">
                    &#8250;
                </button>

                <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-3">
                    <?php foreach ($slides as $i => $slide): ?>
                        <button
                            type="button"
                            onclick="goToSlide(<?= $i; ?>)"
                            class="dotSlider h-2 w-2 rounded-full bg-white-dark/40 transition"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        const slider = document.getElementById("slider");
        const dots = document.querySelectorAll(".dotSlider");

        if (slider && slider.children.length > 1) {
            let currentIndex = 0;
            const totalSlides = slider.children.length;
            let autoplay;

            function updateSlider() {
                slider.style.transform = `translateX(-${currentIndex * 100}%)`;

                dots.forEach((dot, index) => {
                    dot.classList.toggle("bg-white-dark", index === currentIndex);
                    dot.classList.toggle("bg-white-dark/40", index !== currentIndex);
                });
            }

            window.nextSlide = function nextSlide() {
                currentIndex = (currentIndex + 1) % totalSlides;
                updateSlider();
            };

            window.prevSlide = function prevSlide() {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                updateSlider();
            };

            window.goToSlide = function goToSlide(index) {
                currentIndex = index;
                updateSlider();
            };

            function startAutoplay() {
                autoplay = setInterval(window.nextSlide, 4000);
            }

            function stopAutoplay() {
                clearInterval(autoplay);
            }

            startAutoplay();
            slider.parentElement.addEventListener("mouseenter", stopAutoplay);
            slider.parentElement.addEventListener("mouseleave", startAutoplay);

            let startX = 0;

            slider.addEventListener("touchstart", (event) => {
                startX = event.touches[0].clientX;
            });

            slider.addEventListener("touchend", (event) => {
                const endX = event.changedTouches[0].clientX;

                if (startX - endX > 50) {
                    window.nextSlide();
                }

                if (endX - startX > 50) {
                    window.prevSlide();
                }
            });

            updateSlider();
        }
    </script>

    <!-- Categories -->
    <section class="mx-auto flex max-w-7xl items-center justify-start gap-3 overflow-x-auto px-4 md:px-0 py-8 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden md:gap-8 md:py-20">
        <!-- <div class="relative flex h-[80px] w-[80px] shrink-0 items-center justify-center md:h-[260px] md:w-[260px]">
            <iframe
                src="https://lottie.host/embed/f6933fd2-3012-489e-8a56-8576e6e9501f/CYg29mu74N.lottie"
                class="absolute inset-0 h-full w-full pointer-events-none"></iframe>

            <a href="<?= e(app_url('categories.php')); ?>" class="relative text-center text-[10px] font-extrabold leading-tight text-white-dark md:text-3xl">
                NEW <br> LAUNCH
            </a>
        </div> -->

        <div class="group min-w-[70px] max-w-[90px] h-24 md:h-64 shrink-0 flex-col items-center md:min-w-[200px] md:max-w-[220px]">
            <div class="flex w-full h-full items-center justify-center overflow-hidden rounded-full border border-primary-medium/40 bg-gradient-to-b from-[#0065a420] to-[#ff003320] transition shadow-inner">
                <a href="<?= e(app_url('categories.php')); ?>" class="relative text-center text-[10px] font-bold text-transparent [-webkit-text-stroke:0.5px_#0065a4] leading-tight text-primary-medium md:text-3xl">
                    NEW <br> LAUNCH
                </a>
            </div>
        </div>

        <?php foreach ($featuredCategories as $category): ?>
            <a href="<?= e(app_url('shop.php?category=' . (int) $category['id'])); ?>" class="group min-w-[70px] max-w-[90px] shrink-0 flex-col items-center md:min-w-[180px] md:max-w-[220px]">
                <div class="flex w-full items-center justify-center overflow-hidden rounded-t-[1.5rem] border border-primary-medium/40 bg-gradient-to-b from-[#0065a420] to-[#ff003320] transition md:rounded-t-[4rem]">
                    <img
                        src="<?= e(upload_url((string) $category['image'])); ?>"
                        alt="<?= e($category['name']); ?>"
                        class="h-20 w-full object-contain p-2 transition duration-300 group-hover:scale-105 md:h-60 md:p-8"
                        loading="lazy">
                </div>

                <p class="mt-2 text-center text-xs text-black-medium md:text-sm">
                    <?= e($category['name']); ?>
                </p>
            </a>
        <?php endforeach; ?>
    </section>

    <!-- Featured Products Video -->
    <section class="mx-auto max-w-7xl pb-8 pt-4 md:pb-12 overflow-hidden">

        <div class="relative">

            <!-- CAROUSEL -->
            <div id="featureCarousel"
                class="flex gap-4 md:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory px-4 md:px-0 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                <?php foreach ($featuredProductsVideo as $video): ?>

                    <!-- CARD -->
                    <div class="snap-center shrink-0
                    w-[72%]
                    sm:w-[55%]
                    md:w-[38%]
                    lg:w-[26%]
                    xl:w-[22%]">

                        <div class="group relative rounded-3xl overflow-hidden
                        h-[320px] sm:h-[340px] md:h-[380px]
                        bg-white-light/40 cursor-pointer">

                            <!-- VIDEO -->
                            <video
                                src="<?= e(upload_url((string)$video)); ?>"
                                class="feature-video w-full h-full object-cover transition duration-500 group-hover:scale-105"
                                playsinline
                                muted
                                loop>
                            </video>

                            <!-- PLAY ICON -->
                            <div class="playIcon absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="bg-white-light/40 backdrop-blur-sm rounded-full p-3">
                                    <i data-lucide="play" class="w-6 h-6 text-white-dark"></i>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- LEFT BUTTON -->
            <button onclick="scrollCarousel(-1)"
                class="absolute left-2 md:left-1 top-1/2 -translate-y-1/2 z-10
            w-8 h-8 flex items-center justify-center
            rounded-full bg-white-light text-black-light hover:scale-105 transition">

                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>

            <!-- RIGHT BUTTON -->
            <button onclick="scrollCarousel(1)"
                class="absolute right-2 md:right-1 top-1/2 -translate-y-1/2 z-10
            w-8 h-8 flex items-center justify-center
            rounded-full bg-white-light text-black-light hover:scale-105 transition">

                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

        </div>

    </section>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const container = document.getElementById("featureCarousel");
            if (!container) return;

            let autoScroll;

            // GET SCROLL SIZE (dynamic)
            function getScrollAmount() {
                const firstCard = container.children[0];
                if (!firstCard) return 300;

                const style = window.getComputedStyle(container);
                const gap = parseInt(style.gap) || 16;

                return firstCard.offsetWidth + gap;
            }

            // BUTTON SCROLL (same as before)
            window.scrollCarousel = function(direction) {
                const scrollAmount = getScrollAmount();

                container.scrollBy({
                    left: direction * scrollAmount,
                    behavior: "smooth"
                });
            };

            // AUTO SLIDE
            function startAutoSlide() {
                autoScroll = setInterval(() => {

                    const scrollAmount = getScrollAmount();

                    container.scrollBy({
                        left: scrollAmount,
                        behavior: "smooth"
                    });

                    // Infinite loop reset
                    if (container.scrollLeft >= container.scrollWidth - container.clientWidth - 5) {
                        container.scrollTo({
                            left: 0,
                            behavior: "auto"
                        });
                    }

                }, 2500);
            }

            function stopAutoSlide() {
                clearInterval(autoScroll);
            }

            // START AUTO
            startAutoSlide();

            // Pause on hover (desktop)
            container.addEventListener('mouseenter', stopAutoSlide);
            container.addEventListener('mouseleave', startAutoSlide);

            // Pause on touch (mobile)
            container.addEventListener('touchstart', stopAutoSlide);
            container.addEventListener('touchend', startAutoSlide);

            // VIDEO CONTROL (unchanged)
            document.querySelectorAll('.feature-video').forEach(video => {

                const wrapper = video.closest('.group');
                const playIcon = wrapper.querySelector('.playIcon');

                wrapper.addEventListener('mouseenter', () => {
                    video.play().catch(() => {});
                    playIcon.classList.add('hidden');
                });

                wrapper.addEventListener('mouseleave', () => {
                    video.pause();
                    video.currentTime = 0;
                    playIcon.classList.remove('hidden');
                });

                wrapper.addEventListener('click', () => {
                    if (video.paused) {
                        video.play().catch(() => {});
                        playIcon.classList.add('hidden');
                    } else {
                        video.pause();
                        video.currentTime = 0;
                        playIcon.classList.remove('hidden');
                    }
                });

            });

        });
    </script>

    <!-- Featured Watches -->
    <section class="mx-auto max-w-7xl px-4 py-4 md:px-0">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wider text-black-light">Trending</p>
                <h2 class="text-xl font-semibold text-primary-medium md:text-3xl">Featured Watches</h2>
            </div>

            <a href="<?= e(app_url('shop.php')); ?>" class="group flex items-center gap-2 rounded-full border px-4 py-2 text-sm transition hover:bg-white-light/40">
                <span class="text-sm text-black-light">See All</span>
                <i data-lucide="arrow-right" class="h-4 w-4 text-black-light transition-transform duration-300 group-hover:translate-x-1"></i>
            </a>
        </div>

        <div class="my-10 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            <?php foreach (array_slice($featuredProducts, 0, 6) as $product): ?>
                <div class="group overflow-hidden">
                    <div class="relative overflow-hidden rounded-lg bg-white-light/40">

                        <?php if (is_logged_in()): ?>
                            <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post" class="absolute top-3 right-3 z-10">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="redirect" value="">
                                <button
                                    type="submit"
                                    class="rounded-full bg-white-dark/80 p-2 transition hover:bg-white-dark hover:scale-105 <?= isset($wishlistProductIds[(int) $product['id']]) ? 'text-red-500' : 'text-black-light'; ?>"
                                    aria-label="<?= isset($wishlistProductIds[(int) $product['id']]) ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                    <i data-lucide="heart" class="h-4 w-4 <?= isset($wishlistProductIds[(int) $product['id']]) ? 'fill-current' : ''; ?>"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <a
                                href="<?= e(app_url('user/login.php')); ?>"
                                class="absolute top-3 right-3 z-10 rounded-full bg-white-dark/80 p-2 text-black-light transition hover:bg-white-dark hover:text-red-500 hover:scale-105"
                                aria-label="Login to use wishlist">
                                <i data-lucide="heart" class="h-4 w-4"></i>
                            </a>
                        <?php endif; ?>


                        <a href="<?= e(product_link($product)); ?>">
                            <img
                                src="<?= e(upload_url((string) $product['image'])); ?>"
                                alt="<?= e($product['name']); ?>"
                                class="h-36 md:h-44 w-full object-contain p-2 transition duration-500 group-hover:scale-105"
                                loading="lazy" />
                        </a>


                        <?php if ((int)$product['stock'] == 0): ?>

                            <span class="absolute top-3 left-3 text-xs flex items-center gap-1 bg-red-100 text-red-600 text-xs text-center font-semibold px-2 py-1 rounded-md">
                                <i data-lucide="frown" class="w-3.5 h-3.5 hidden md:block"></i>
                                Out of Stock
                            </span>

                        <?php elseif ((int)$product['stock'] < 20): ?>

                            <span class="absolute top-3 left-3 text-xs flex items-center gap-1 bg-orange-100 text-orange-600 text-xs text-center font-semibold px-2 py-1 rounded-md">
                                Only <?= (int)$product['stock']; ?> left
                            </span>

                        <?php else: ?>

                            <span class="absolute top-3 left-3 text-xs flex items-center gap-1 bg-green-100 text-green-600 text-xs text-center font-semibold px-2 py-1 rounded-md">
                                In Stock
                            </span>

                        <?php endif; ?>


                    </div>

                    <div class="py-4 px-2">
                        <span class="text-[10px] uppercase tracking-wider text-black-light bg-primary-light px-2 py-1 rounded-md">
                            <?= e($product['category_name'] ?? 'Watch'); ?>
                        </span>

                        <h3 class="mt-1 line-clamp-2 text-xs md:text-sm font-medium text-black-medium">
                            <?= e($product['name']); ?>
                        </h3>

                        <!-- <div class="mt-2 flex items-center gap-2">
                            <span class="flex items-center gap-1 bg-yellow-100 text-yellow-600 text-xs font-semibold px-2 py-1 rounded-md">

                                <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>

                                <?= number_format((float) $product['avg_rating'], 1); ?>

                            </span>

                        </div> -->

                        <p class="mt-2 text-sm font-semibold text-green-600">
                            <?= e(money((float) $product['price'])); ?>
                        </p>



                        <?php if ((int)$product['stock'] > 0): ?>

                            <a
                                href="<?= e(product_link($product)); ?>"
                                class="mt-4 block bg-primary-medium py-2.5 text-center text-sm font-semibold text-white-dark transition hover:bg-primary-medium/80">
                                BUY NOW
                            </a>

                        <?php else: ?>

                            <button
                                disabled
                                class="mt-4 w-full block bg-red-100 py-2.5 text-center text-sm font-bold text-rose-500 transition cursor-not-allowed">

                                Out of Stock

                            </button>

                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Banner -->
    <section class="mx-auto max-w-7xl px-4 pb-6 md:px-0 md:pb-0 md:pt-4">

        <div class="mx-auto max-w-7xl px-[8%] 
  bg-primary-medium rounded-xl md:rounded-3xl flex items-center justify-between overflow-hidden">

            <!-- Text -->
            <div class="text-white-dark">
                <h1 class="text-2xl md:text-6xl font-light tracking-[0.35em]">
                    ROLEX
                </h1>

                <p class="mt-3 text-xs md:text-lg tracking-[0.25em] uppercase opacity-90">
                    Swiss Luxury Watches
                </p>
            </div>

            <!-- Watch Image -->
            <img
                src="assets/images/banner_watch.png"
                alt="Rolex Watch"
                class="h-32 md:h-[320px] object-contain select-none">

        </div>

    </section>

    <!-- Client Review -->
    <section class="mx-auto max-w-7xl py-10 md:py-20 overflow-hidden">

        <!-- Heading -->
        <div class="px-4 md:px-0">
            <p class="text-sm uppercase tracking-wider text-black-light">
                Client's Review
            </p>

            <h2 class="text-xl md:text-3xl font-semibold text-primary-medium">
                Building trust through great work.
            </h2>
        </div>

        <!-- Carousel -->
        <div class="relative mt-10">

            <!-- Scroll Container -->
            <div id="reviewCarousel"
                class="flex gap-4 md:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory
            px-4 md:px-0
            [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                <?php
                // Duplicate reviews for smooth infinite effect
                $loopReviews = array_merge($clientsReview, $clientsReview);
                foreach ($loopReviews as $review):
                ?>

                    <div
                        class="snap-center shrink-0
                    w-[80%]
                    sm:w-[55%]
                    md:w-[38%]
                    lg:w-[26%]
                    xl:w-[22%]">

                        <div class="relative rounded-xl overflow-hidden
                        h-[360px] md:h-[380px]
                        bg-white-light/40 group">

                            <!-- Image -->
                            <img
                                src="<?= e(upload_url((string)$review)); ?>"
                                class="w-full h-full object-cover transition duration-500 group-hover:scale-105">

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- Left Arrow -->
            <button
                onclick="scrollCarousel(-1)"
                class="absolute left-2 md:left-1 top-1/2 -translate-y-1/2 z-10
            w-8 h-8 flex items-center justify-center
            rounded-full bg-white-dark text-black-light hover:scale-105 transition">

                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>

            <!-- Right Arrow -->
            <button
                onclick="scrollCarousel(1)"
                class="absolute right-2 md:right-1 top-1/2 -translate-y-1/2 z-10
            w-8 h-8 flex items-center justify-center
            rounded-full bg-white-dark text-black-light hover:scale-105 transition">

                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

        </div>

        <!-- See All -->
        <div class="flex items-center justify-center mt-8">
            <a href="<?= e(app_url('reviews.php')); ?>"
                class="group flex items-center gap-2 rounded-full border px-5 py-2 text-sm transition hover:bg-white-light/40 text-black-light">

                <span>See All</span>

                <i data-lucide="arrow-right"
                    class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1">
                </i>
            </a>
        </div>

    </section>
    <script>
        const carousel = document.getElementById('reviewCarousel');

        let autoScroll;
        const scrollStep = 300;

        function startAutoSlide() {
            autoScroll = setInterval(() => {
                carousel.scrollBy({
                    left: scrollStep,
                    behavior: 'smooth'
                });

                // Infinite loop reset
                if (carousel.scrollLeft >= carousel.scrollWidth / 2) {
                    carousel.scrollTo({
                        left: 0,
                        behavior: 'auto'
                    });
                }

            }, 2500);
        }

        function stopAutoSlide() {
            clearInterval(autoScroll);
        }

        // Start autoplay
        startAutoSlide();

        // Pause on hover
        carousel.addEventListener('mouseenter', stopAutoSlide);
        carousel.addEventListener('mouseleave', startAutoSlide);

        // Manual arrows
        function scrollCarousel(direction) {
            carousel.scrollBy({
                left: direction * scrollStep,
                behavior: 'smooth'
            });
        }
    </script>

    <!-- Process Scripts -->
    <section class="py-6 md:pt-10 pb-20 max-w-7xl mx-auto px-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 text-center">

            <!-- Item 1 -->
            <div class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 flex items-center justify-center transition">
                    <i data-lucide="truck" class="w-12 h-12 text-black-medium"></i>
                </div>
                <p class="text-sm md:text-base font-medium text-black-light">
                    Free Delivery
                </p>
            </div>

            <!-- Item 2 -->
            <div class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 flex items-center justify-center transition">
                    <i data-lucide="refresh-ccw" class="w-12 h-12 text-black-medium"></i>
                </div>
                <p class="text-sm md:text-base font-medium text-black-light">
                    7 days replace / exchange
                </p>
            </div>

            <!-- Item 3 -->
            <div class="flex flex-col items-center gap-3 group">
                <div class="w-14 h-14 flex items-center justify-center rounded-full transition">
                    <i data-lucide="shield-check" class="w-12 h-12 text-black-medium"></i>
                </div>
                <p class="text-sm md:text-base font-medium text-black-light">
                    Secure Checkout
                </p>
            </div>

        </div>

    </section>

</main>

<?php require __DIR__ . '/../includes/importent.php'; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>