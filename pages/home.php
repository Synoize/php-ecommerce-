<?php
require_once __DIR__ . '/../config/bootstrap.php';

$store = new StoreController();
$featuredCategories = $store->categories->featured(4);
$featuredProducts = $store->products->featured(8);
$slides = (new SlideModel())->all();



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
                            <div class="flex h-full w-full flex-col justify-between px-6 py-10 md:px-24 md:py-20">
                                <div class="max-w-3xl">
                                    <span class="inline-flex rounded-full bg-white-light px-4 py-2 text-xs md:text-sm text-black-light">Spring 2026 collection</span>
                                    <h2 class="mt-4 md:mt-6 max-w-2xl text-2xl md:text-3xl font-bold leading-tight text-white-dark md:text-6xl">
                                        <?= e((string) $slide['title']); ?>
                                    </h2>
                                    <p class="mt-4 max-w-xl text-xs md:text-sm font-light text-white-light md:text-lg">
                                        <?= e((string) ($slide['description'] ?? '')); ?>
                                    </p>
                                </div>

                                <a
                                    href="<?= e(app_url((string) ($slide['button_link'] ?? 'shop.php'))); ?>"
                                    class="group mt-6 inline-flex w-fit items-center gap-2 rounded-full bg-primary-medium px-6 py-2 md:py-3 text-[10px] md:text-sm font-semibold text-white-dark transition-all duration-300">
                                    <span><?= e((string) ($slide['button_name'] ?? 'Explore')); ?></span>
                                    <span class="transition-transform duration-300 group-hover:translate-x-1"><i data-lucide="arrow-right" class="w-3 h-3 md:w-4 md:h-4"></i></span>
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
                    class="absolute left-4 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white-dark/90 hover:bg-white-dark text-black-medium text-lg md:text-xl shadow-lg transition hover:scale-105 duration-300 md:h-12 md:w-12">
                    &#8249;
                </button>

                <button
                    type="button"
                    onclick="nextSlide()"
                    class="absolute right-4 top-1/2 z-10 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white-dark/90 hover:bg-white-dark text-black-medium text-lg md:text-xl shadow-lg transition hover:scale-105 duration-300 md:h-12 md:w-12">
                    &#8250;
                </button>

                <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-3">
                    <?php foreach ($slides as $i => $slide): ?>
                        <button
                            type="button"
                            onclick="goToSlide(<?= $i; ?>)"
                            class="dotSlider h-3 w-3 rounded-full bg-white-dark/40 transition"></button>
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
    <section class="mx-auto max-w-7xl px-2 py-8 md:py-20 flex items-center justify-start gap-3 md:gap-8 overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">

        <!-- NEW LAUNCH -->
        <div class="relative flex items-center justify-center
                w-[80px] h-[80px] md:w-[260px] md:h-[260px] shrink-0">

            <iframe
                src="https://lottie.host/embed/f6933fd2-3012-489e-8a56-8576e6e9501f/CYg29mu74N.lottie"
                class="absolute inset-0 w-full h-full pointer-events-none">
            </iframe>

            <a href="<?= e(app_url('categories.php')); ?>"
                class="relative text-[10px] md:text-3xl font-extrabold
           text-white-dark text-center leading-tight">
                NEW <br> LAUNCH
            </a>

        </div>

        <!-- CATEGORY LOOP -->
        <?php foreach ($featuredCategories as $category): ?>
            <a href="<?= e(app_url('shop.php?category=' . (int) $category['id'])); ?>"
                class="flex flex-col items-center min-w-[70px] max-w-[90px] md:min-w-[200px] md:max-w-[240px] group shrink-0">

                <div class="flex items-center justify-center w-full
                rounded-t-[1.5rem] md:rounded-t-[4rem]
                border border-primary-medium/40
                bg-gradient-to-b from-[#0065a420] to-[#ff003320]
                overflow-hidden transition">

                    <img
                        src="<?= e(upload_url((string) $category['image'])); ?>"
                        alt="<?= e($category['name']); ?>"
                        class="p-2 md:p-8 w-full h-20 md:h-60 object-contain
                    transition duration-300 group-hover:scale-105"
                        loading="lazy">

                </div>

                <p class="mt-2 text-xs md:text-sm text-black-medium text-center">
                    <?= e($category['name']); ?>
                </p>

            </a>
        <?php endforeach; ?>

    </section>

    <!-- Featured Products -->
    <section class="mx-auto max-w-7xl px-4 py-4">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm uppercase tracking-wider text-black-light">Trending</p>
                <h2 class="text-xl md:text-3xl font-semibold text-primary-medium">
                    Featured Watches
                </h2>
            </div>

            <a href="<?= e(app_url('shop.php')); ?>"
                class="group px-4 py-2 border rounded-full text-sm flex items-center gap-2 hover:bg-white-light/40 transition">

                <span class="text-sm text-black-light">See All</span>

                <i data-lucide="arrow-right"
                    class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1 text-black-light">
                </i>
            </a>
        </div>

        <!-- Product Grid -->
        <div class="mt-10 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">

            <?php foreach ($featuredProducts as $product): ?>

                <div class="group overflow-hidden">

                    <!-- Image Container -->
                    <div class="relative overflow-hidden bg-white-light rounded-lg">

                        <!-- Wishlist -->
                        <button class="absolute top-3 right-3 z-10 bg-white-dark/80 p-2 rounded-full hover:bg-white-dark text-red-500">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        </button>

                        <!-- Product Image -->
                        <a href="<?= e(product_link($product)); ?>">
                            <img
                                src="<?= e(upload_url((string) $product['image'])); ?>"
                                alt="<?= e($product['name']); ?>"
                                class="w-full h-56 object-cover transition duration-500 group-hover:scale-110"
                                loading="lazy" />
                        </a>

                    </div>

                    <!-- Product Info -->
                    <div class="pt-4">

                        <!-- Category -->
                        <p class="text-xs uppercase tracking-wider text-primary-medium">
                            <?= e($product['category_name'] ?? 'Watch'); ?>
                        </p>

                        <!-- Product Name -->
                        <h3 class="text-sm font-medium text-black-light mt-1 line-clamp-2">
                            <?= e($product['name']); ?>
                        </h3>

                        <!-- Rating -->
                        <p class="text-xs text-yellow-500 mt-1 flex gap-1 items-center ">
                            <i data-lucide="star" class="w-4 h-4"></i>
                            <?= number_format((float) $product['avg_rating'], 1); ?> / 5
                        </p>

                        <!-- Price -->
                        <p class="mt-2 text-lg font-semibold text-green-500">
                            <?= e(money((float) $product['price'])); ?>
                        </p>

                        <!-- Reviews -->
                        <p class="text-xs text-black-light mt-1">
                            <?= (int) $product['review_count']; ?> reviews • Stock <?= (int) $product['stock']; ?>
                        </p>

                        <!-- Button -->
                        <a
                            href="<?= e(product_link($product)); ?>"
                            class="mt-4 block text-center bg-primary-medium hover:bg-primary-medium/80 text-white-dark py-3 text-sm font-semibold transition">
                            BUY NOW
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </section>

</main>
<?php require __DIR__ . '/layout/footer.php'; ?>