<?php
require_once __DIR__ . '/../config/bootstrap.php';

$store = new StoreController();
$featuredCategories = $store->categories->featured(4);
$featuredProducts = $store->products->featured(8);
$slides = (new SlideModel())->all();
$wishlistProductIds = [];

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

$pageTitle = 'Watch Ecommerce | Home';
$pageDescription = 'Shop premium watches with category filtering, reviews, wishlist, cart, and secure checkout.';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28">
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
    <section class="mx-auto flex max-w-7xl items-center justify-start gap-3 overflow-x-auto px-2 py-8 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden md:gap-8 md:py-20">
        <div class="relative flex h-[80px] w-[80px] shrink-0 items-center justify-center md:h-[260px] md:w-[260px]">
            <iframe
                src="https://lottie.host/embed/f6933fd2-3012-489e-8a56-8576e6e9501f/CYg29mu74N.lottie"
                class="absolute inset-0 h-full w-full pointer-events-none"></iframe>

            <a href="<?= e(app_url('categories.php')); ?>" class="relative text-center text-[10px] font-extrabold leading-tight text-white-dark md:text-3xl">
                NEW <br> LAUNCH
            </a>
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

    <!-- Featured Watches -->
    <section class="mx-auto max-w-7xl px-4 py-4">
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

        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            <?php foreach (array_slice($featuredProducts, 0, 6) as $product): ?>
                <div class="group overflow-hidden">
                    <div class="relative overflow-hidden rounded-lg bg-white-light">
                        <?php if (is_logged_in()): ?>
                            <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post" class="absolute top-3 right-3 z-10">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                <input type="hidden" name="redirect" value="index.php">
                                <button
                                    type="submit"
                                    class="rounded-full bg-white-dark/80 p-2 transition hover:bg-white-dark <?= isset($wishlistProductIds[(int) $product['id']]) ? 'text-red-500' : 'text-black-light'; ?>"
                                    aria-label="<?= isset($wishlistProductIds[(int) $product['id']]) ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                    <i data-lucide="heart" class="h-4 w-4 <?= isset($wishlistProductIds[(int) $product['id']]) ? 'fill-current' : ''; ?>"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <a
                                href="<?= e(app_url('user/login.php')); ?>"
                                class="absolute top-3 right-3 z-10 rounded-full bg-white-dark/80 p-2 text-black-light transition hover:bg-white-dark hover:text-red-500"
                                aria-label="Login to use wishlist">
                                <i data-lucide="heart" class="h-4 w-4"></i>
                            </a>
                        <?php endif; ?>

                        <a href="<?= e(product_link($product)); ?>">
                            <img
                                src="<?= e(upload_url((string) $product['image'])); ?>"
                                alt="<?= e($product['name']); ?>"
                                class="h-56 w-full object-contain p-2 transition duration-500 group-hover:scale-110"
                                loading="lazy" />
                        </a>
                    </div>

                    <div class="pt-4">
                        <p class="text-xs uppercase tracking-wider text-black-light">
                            <?= e($product['category_name'] ?? 'Watch'); ?>
                        </p>

                        <h3 class="mt-1 line-clamp-2 text-sm font-medium text-black-medium">
                            <?= e($product['name']); ?>
                        </h3>

                        <div class="mt-2 flex items-center gap-2">

                            <!-- Rating Badge -->
                            <span class="flex items-center gap-1 bg-yellow-100 text-yellow-600 text-xs font-semibold px-2 py-1 rounded-md">

                                <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>

                                <?= number_format((float) $product['avg_rating'], 1); ?>

                            </span>

                            <!-- Review Count -->
                            <!-- <span class="text-xs text-white-medium">
                                (<?= (int) $product['review_count']; ?> reviews)
                            </span> -->

                        </div>

                        <p class="mt-2 text-lg font-semibold text-green-600">
                            <?= e(money((float) $product['price'])); ?>
                        </p>

                        <div class="mt-2 flex justify-between items-center gap-3 text-xs">

                            <span class="flex items-center gap-1 text-black-light text-nowrap">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                <?= (int) $product['review_count']; ?> reviews
                            </span>

                            <?php if ((int)$product['stock'] == 0): ?>

                                <span class="flex items-center gap-1 bg-red-100 text-red-600 text-xs text-center font-semibold px-2 py-1 rounded-md">
                                    <i data-lucide="frown" class="w-3.5 h-3.5 hidden md:block"></i>
                                    Out of Stock
                                </span>

                            <?php elseif ((int)$product['stock'] < 20): ?>

                                <span class="flex items-center gap-1 bg-orange-100 text-orange-600 text-xs text-center font-semibold px-2 py-1 rounded-md">
                                    Only <?= (int)$product['stock']; ?> left
                                </span>

                            <?php else: ?>

                                <span class="flex items-center gap-1 bg-green-100 text-green-600 text-xs text-center font-semibold px-2 py-1 rounded-md">
                                    In Stock
                                </span>

                            <?php endif; ?>

                        </div>

                        <a
                            href="<?= e(product_link($product)); ?>"
                            class="mt-4 block bg-primary-medium py-3 text-center text-sm font-semibold text-white-dark transition hover:bg-primary-medium/80">
                            BUY NOW
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/layout/footer.php'; ?>