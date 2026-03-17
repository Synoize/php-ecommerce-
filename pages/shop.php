<?php
require_once __DIR__ . '/../config/bootstrap.php';

$store = new StoreController();

$categories = $store->categories->all();
$priceRange = $store->products->priceRange();

$selectedCategoryIds = array_values(array_filter(
    array_map('intval', (array)($_GET['categories'] ?? [])),
    static fn($id) => $id > 0
));

$singleCategoryId = (int)($_GET['category'] ?? 0);

if ($selectedCategoryIds === [] && $singleCategoryId > 0) {
    $selectedCategoryIds = [$singleCategoryId];
}

$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== ''
    ? (float)$_GET['min_price']
    : $priceRange['min'];

$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== ''
    ? (float)$_GET['max_price']
    : $priceRange['max'];

if ($minPrice > $maxPrice) {
    [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
}

$filters = [
    'query' => trim((string)($_GET['q'] ?? '')),
    'category_ids' => $selectedCategoryIds,
    'sort' => (string)($_GET['sort'] ?? ''),
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'page' => (int)($_GET['page'] ?? 1),
    'per_page' => 8
];

$result = $store->products->search($filters);

$currentFilterParams = array_filter([
    'q' => $filters['query'],
    'sort' => $filters['sort'],
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'categories' => $selectedCategoryIds
]);

$shopRedirect = 'shop.php' . ($currentFilterParams ? '?' . http_build_query($currentFilterParams) : '');

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

$categoryMap = [];
foreach ($categories as $category) {
    $categoryMap[(int)$category['id']] = $category;
}

$pageHeading = 'Shop Watches';

if (count($selectedCategoryIds) === 1 && isset($categoryMap[$selectedCategoryIds[0]])) {
    $pageHeading = $categoryMap[$selectedCategoryIds[0]]['name'] . ' Watches';
}

$pageTitle = $pageHeading;

require __DIR__ . '/layout/header.php';
?>

<main class="mt-28 mx-auto max-w-7xl px-4 py-8 md:px-0">

    <!-- MOBILE FILTER BUTTON -->
    <div class="lg:hidden flex justify-between items-center mb-6">
        <button id="open-filter" class="flex items-center gap-2 border hover:bg-white-light/40 bg-white-dark text-black-light px-4 py-2 rounded-lg text-sm font-medium">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
        </button>
        <span>
            <?php require __DIR__ . '/../includes/sort.php'; ?>
        </span>
    </div>


    <div class="grid gap-8 lg:grid-cols-[300px,1fr]">


        <!-- FILTER DRAWER -->
        <aside id="filter-drawer" class="fixed lg:static top-0 left-0 h-full lg:h-fit w-[300px] bg-white-dark p-4 md:p-0 z-50 md:z-10 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto"> <!-- MOBILE CLOSE --> <button id="close-filter" class="mb-6 lg:hidden absolute top-4 right-4 text-black-light"> <i data-lucide="x" class="w-6 h-6"></i> </button>
            <form action="<?= e(app_url('shop.php')); ?>" method="get" class="space-y-8"> <input type="hidden" name="sort" value="<?= e($filters['sort']); ?>"> <!-- FILTER HEADER -->
                <div>
                    <h2 class="text-xl md:text-2xl font-semibold text-black-medium tracking-wide"> Filter Products </h2>
                    <p class="text-xs md:text-sm text-white-medium mt-1"> Find your perfect watch </p>
                </div> <!-- PRICE FILTER -->
                <section>
                    <h3 class="text-sm font-medium uppercase tracking-wider text-black-light"> Price Range </h3>
                    <div class="mt-4 px-1">
                        <div class="relative h-8">
                            <div class="absolute top-1.5 h-1 w-full rounded-full bg-white-light"></div> <input id="min-price-range" type="range" min="<?= (int) floor($priceRange['min']); ?>" max="<?= (int) ceil($priceRange['max']); ?>" value="<?= (int) floor($minPrice); ?>" class="absolute w-full appearance-none bg-transparent pointer-events-none accent-black"> <input id="max-price-range" type="range" min="<?= (int) floor($priceRange['min']); ?>" max="<?= (int) ceil($priceRange['max']); ?>" value="<?= (int) ceil($maxPrice); ?>" class="absolute w-full appearance-none bg-transparent accent-black">
                        </div> <input type="hidden" id="min-price-input" name="min_price" value="<?= e((string)$minPrice); ?>"> <input type="hidden" id="max-price-input" name="max_price" value="<?= e((string)$maxPrice); ?>">
                    </div>
                    <div class="mt-2 flex items-center justify-between"> <span id="price-range-label" class="rounded-full bg-white-light/40 px-4 py-2 text-sm font-semibold text-black-light"> ₹<?= number_format($minPrice, 0); ?> — ₹<?= number_format($maxPrice, 0); ?> </span> <button type="submit" class="px-5 py-2 text-sm font-semibold text-black-medium hover:text-black-medium/90 transition"> Apply </button> </div>
                </section> <!-- SEARCH -->
                <section>
                    <h3 class="text-sm font-medium uppercase tracking-wider text-black-light"> Search </h3>
                    <div class="mt-4 relative"> <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-black-light"></i> <input type="search" name="q" value="<?= e($filters['query']); ?>" placeholder="Search products..." class="w-full rounded-lg border border-white-light bg-white-light/40 py-3 pl-11 pr-4 text-sm outline-none focus:border-white-medium focus:bg-white-dark transition"> </div>
                </section> <!-- CATEGORIES -->
                <section>
                    <h3 class="text-sm font-medium uppercase tracking-wider text-black-light"> Categories </h3>
                    <div class="mt-4 space-y-3"> <?php foreach ($categories as $category): ?> <label class="group flex items-center gap-3 cursor-pointer"> <input type="checkbox" name="categories[]" value="<?= (int)$category['id']; ?>" class="h-4 w-4 rounded border-black-light" <?= in_array((int)$category['id'], $selectedCategoryIds, true) ? 'checked' : ''; ?>> <span class="text-sm text-black-light transition"> <?= e($category['name']); ?> </span> </label> <?php endforeach; ?> </div>
                </section> <button type="submit" class="w-full px-5 py-3 text-sm text-white-dark bg-primary-medium hover:bg-primary-medium/90 transition"> Filter </button>
            </form>
        </aside>


        <!-- OVERLAY -->

        <div
            id="drawer-overlay"
            class="fixed inset-0 bg-black-dark/40 hidden lg:hidden">
        </div>



        <!-- PRODUCTS -->

        <section class="md:p-4">
            <div class="flex flex-row flex-col-reverce gap-4 md:items-center md:justify-between">
                <div>
                    <h1 class="text-xl md:text-3xl font-semibold text-primary-medium text-nowrap"> <?= e($pageHeading); ?> </h1>
                    <p class="text-sm text-black-light mt-1"> <?= (int)$result['total']; ?> products found </p>
                </div>
                <span class="hidden md:block"> <?php require __DIR__ . '/../includes/sort.php'; ?> </span>
            </div>


            <!-- PRODUCT GRID -->

            <div class="mt-8 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">

                <?php foreach ($result['items'] as $product): ?>

                    <article class="group flex flex-col overflow-hidden transition duration-300">

                        <!-- IMAGE SECTION -->
                        <div class="relative bg-white-light/40 rounded-lg overflow-hidden">

                            <!-- WISHLIST -->
                            <?php if (is_logged_in()): ?>
                                <form action="<?= e(app_url('api/wishlist.php')); ?>" method="post" class="absolute top-3 right-3 z-10">
                                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                                    <input type="hidden" name="redirect" value="<?= e($shopRedirect); ?>">
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


                            <!-- PRODUCT IMAGE -->
                            <a href="<?= e(product_link($product)); ?>">

                                <img
                                    src="<?= e(upload_url($product['image'])); ?>"
                                    alt="<?= e($product['name']); ?>"
                                    class="h-40 sm:h-44 md:h-48 w-full object-contain p-3 transition duration-500 group-hover:scale-105">

                            </a>


                            <!-- STOCK BADGE -->
                            <?php if ((int)$product['stock'] === 0): ?>

                                <span class="absolute top-3 left-3 text-xs flex items-center gap-1 bg-red-100 text-red-600 font-semibold px-2 py-1 rounded-md">
                                    <i data-lucide="frown" class="w-3.5 h-3.5 hidden md:block"></i>
                                    Out of Stock
                                </span>

                            <?php elseif ((int)$product['stock'] < 20): ?>

                                <span class="absolute top-3 left-3 text-xs flex items-center gap-1 bg-orange-100 text-orange-600 font-semibold px-2 py-1 rounded-md">
                                    Only <?= (int)$product['stock']; ?> left
                                </span>

                            <?php else: ?>

                                <span class="absolute top-3 left-3 text-xs flex items-center gap-1 bg-green-100 text-green-600 font-semibold px-2 py-1 rounded-md">
                                    In Stock
                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- CONTENT -->
                        <div class="flex flex-col items-start flex-1 py-4 px-2 space-y-2">

                            <span class="text-[10px] uppercase tracking-wider text-black-light bg-primary-light px-2 py-1 rounded-md">
                                <?= e($product['category_name'] ?? 'Watch'); ?>
                            </span>

                            <h3 class="mt-1 line-clamp-2 text-xs md:text-sm font-medium text-black-medium">
                                <?= e($product['name']); ?>
                            </h3>

                            <!-- PRICE -->
                            <div class="flex items-center justify-between mt-auto">

                                <p class="text-sm font-semibold text-green-600">
                                    <?= e(money((float) $product['price'])); ?>
                                </p>

                            </div>




                            <!-- ACTION BUTTON -->
                            <div class="pt-2 w-full">

                                <?php if ((int)$product['stock'] > 0): ?>

                                    <form action="<?= e(app_url('api/cart.php')); ?>" method="post">

                                        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                        <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="redirect" value="<?= e($shopRedirect); ?>">

                                        <button
                                            type="submit"
                                            class="w-full block bg-primary-medium py-2.5 text-center text-sm font-semibold text-white-dark transition hover:bg-primary-medium/80">

                                            Add to Cart

                                        </button>

                                    </form>

                                <?php else: ?>

                                    <button
                                        disabled
                                        class="w-full block bg-red-500 py-2.5 text-center text-sm font-semibold text-white-dark transition hover:bg-red-600 cursor-not-allowed">

                                        Out of Stock

                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>



            <!-- PAGINATION -->

            <?php if ($result['pages'] > 1): ?>

                <div class="mt-10 flex gap-2 flex-wrap">

                    <?php for ($i = 1; $i <= $result['pages']; $i++): ?>

                        <?php
                        $params = array_merge($currentFilterParams, ['page' => $i]);
                        ?>

                        <a
                            href="<?= e(app_url('shop.php?' . http_build_query($params))); ?>"
                            class="px-4 py-2 rounded-full text-sm font-semibold
                            <?= $i == $result['page'] ? 'bg-white-light text-black-light' : 'text-black-medium'; ?>">

                            <?= $i ?>

                        </a>

                    <?php endfor; ?>

                </div>

            <?php endif; ?>


        </section>

    </div>

</main>


<script>
    const openBtn = document.getElementById("open-filter")
    const closeBtn = document.getElementById("close-filter")
    const drawer = document.getElementById("filter-drawer")
    const overlay = document.getElementById("drawer-overlay")

    openBtn?.addEventListener("click", () => {
        drawer.classList.remove("-translate-x-full")
        overlay.classList.remove("hidden")
    })

    closeBtn?.addEventListener("click", closeDrawer)
    overlay?.addEventListener("click", closeDrawer)

    function closeDrawer() {
        drawer.classList.add("-translate-x-full")
        overlay.classList.add("hidden")
    }



    const minRange = document.getElementById('min-price-range')
    const maxRange = document.getElementById('max-price-range')

    const minInput = document.getElementById('min-price-input')
    const maxInput = document.getElementById('max-price-input')

    const label = document.getElementById('price-range-label')

    function updatePrice() {

        let min = parseInt(minRange.value)
        let max = parseInt(maxRange.value)

        if (min > max) {
            [min, max] = [max, min]
        }

        minInput.value = min
        maxInput.value = max

        label.textContent =
            `₹${min.toLocaleString('en-IN')} — ₹${max.toLocaleString('en-IN')}`

    }

    minRange?.addEventListener("input", updatePrice)
    maxRange?.addEventListener("input", updatePrice)

    updatePrice()
</script>


<?php require __DIR__ . '/layout/footer.php'; ?>