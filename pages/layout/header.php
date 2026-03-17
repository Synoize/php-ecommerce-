<?php
$headerCategories = (new CategoryModel())->featured(6);
$headerCartCount = is_logged_in() ? (new CartModel())->count((int) current_user()['id']) : 0;
$headerWishlistCount = is_logged_in() ? (new WishlistModel())->count((int) current_user()['id']) : 0;
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? APP_NAME); ?></title>
    <meta name="description" content="<?= e($pageDescription ?? 'Luxury and everyday watches with secure checkout and admin tooling.'); ?>">
    <link rel="icon" href="<?= e(asset_url('images/logo/favicon.svg')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#d9ebff',
                            medium: '#0066a4',
                            dark: '#0b3b67'
                        },
                        white: {
                            light: '#ebebeb',
                            medium: '#bababa',
                            dark: '#ffffff'
                        },
                        black: {
                            light: '#777777',
                            medium: '#333333',
                            dark: '#000000'
                        }
                    },
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif']
                    },
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= e(asset_url('css/style.css')); ?>">
</head>

<body>

    <header class="fixed top-0 left-0 w-full bg-white-dark border-b z-40">

        <!-- PROMO BAR -->
        <div id="topPromoBar"
            class="bg-primary-medium text-white-dark text-xs transition-all duration-300 ease-in-out overflow-hidden opacity-100 max-h-10">

            <div class="mx-auto max-w-7xl px-4 md:px-0 h-8 flex justify-center items-center text-center">
                Free Gifts on orders above <span class="font-semibold ml-1">₹1499</span>
            </div>

        </div>


        <!-- MAIN NAVBAR -->
        <div class="mx-auto max-w-7xl px-4 md:px-0">

            <div class="h-20 flex items-center justify-between gap-4">


                <!-- LOGO -->
                <a href="<?= e(app_url()); ?>" class="flex items-center gap-3">

                    <img src="<?= e(asset_url('images/logo/logo.png')); ?>" alt="<?= e(APP_NAME); ?>" class="h-10 w-10 object-cover">
                    <div class="text-2xl text-primary-medium"><?= e(APP_NAME); ?></div>

                </a>


                <!-- MOBILE MENU BUTTON -->
                <button id="navToggle" class="lg:hidden text-black-light">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>


                <!-- DESKTOP NAVIGATION -->
                <nav class="hidden lg:flex items-center gap-6 text-sm font-medium text-black-medium">

                    <a href="<?= e(app_url()); ?>" class="hover:text-white-medium">
                        Home
                    </a>

                    <a href="<?= e(app_url('shop.php')); ?>" class="hover:text-white-medium">
                        Collection
                    </a>

                    <!-- <a href="<?= e(app_url('categories.php')); ?>" class="hover:text-white-medium">
                        Categories
                    </a> -->

                    <!-- CATEGORIES -->
                    <div class="space-x-4">
                        <?php foreach ($headerCategories as $navCategory): ?>

                            <a
                                href="<?= e(app_url('shop.php?category=' . (int)$navCategory['id'])); ?>" class="text-nowrap text-white-medium ">

                                <?= e($navCategory['name']); ?>

                            </a>

                        <?php endforeach; ?>
                    </div>

                </nav>


                <!-- RIGHT SIDE -->
                <div class="hidden lg:flex items-center gap-6 text-sm">


                    <!-- SEARCH BAR -->
                    <form action="<?= e(app_url('shop.php')); ?>" method="get"
                        class="hidden lg:flex items-center overflow-hidden">

                        <input
                            type="search"
                            name="q"
                            placeholder="Search watches..."
                            class="px-4 py-2 text-sm outline-none border focus:border-primary-medium">

                        <button class="bg-primary-medium border border-primary-medium text-white-dark px-3 py-2">

                            <i data-lucide="search" class="w-5 h-5"></i>

                        </button>

                    </form>

                    <!-- WISHLIST -->
                    <a href="<?= e(app_url('wishlist.php')); ?>"
                        class="relative text-black-medium hover:text-white-medium">

                        <i data-lucide="heart" class="w-5 h-5"></i>

                        <?php if ($headerWishlistCount > 0): ?>

                            <span
                                class="absolute -top-1.5 -right-2 bg-red-500 text-white-dark text-xs px-1.5 rounded-full">
                                <?= (int)$headerWishlistCount ?>
                            </span>

                        <?php endif; ?>

                    </a>

                    <!-- CART -->
                    <a href="<?= e(app_url('cart.php')); ?>"
                        class="relative text-black-medium hover:text-white-medium">

                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>

                        <?php if ($headerCartCount > 0): ?>

                            <span
                                class="absolute -top-1.5 -right-2 bg-primary-medium text-white-dark text-xs px-1.5 rounded-full">
                                <?= (int)$headerCartCount ?>
                            </span>

                        <?php endif; ?>

                    </a>

                    <!-- USER -->
                    <?php if (is_logged_in()): ?>

                        <div class="relative group">

                            <!-- USER BUTTON -->
                            <button class="relative text-black-medium hover:text-white-medium flex items-center gap-1">

                                <i data-lucide="chevron-down" class="w-5 h-5 transition-transform duration-300 group-hover:rotate-180"></i>
                                <i data-lucide="user" class="w-5 h-5"></i>

                            </button>


                            <!-- DROPDOWN -->
                            <?php if (is_logged_in()): ?>

                                <div class="absolute right-0 mt-7 w-48 bg-white-dark opacity-0 invisible -translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">

                                    <div class="py-2 text-sm text-black-medium">

                                        <?php if (is_admin()): ?>

                                            <a href="<?= e(app_url('admin/index.php')); ?>" class="hover:text-white-medium">
                                                Admin Dashboard
                                            </a>

                                        <?php endif; ?>

                                        <!-- PROFILE -->
                                        <a href="<?= e(app_url('user/profile.php')); ?>"
                                            class="flex items-center gap-2 px-4 py-2 hover:bg-white-light">

                                            <i data-lucide="user" class="w-4 h-4"></i>
                                            <span><?= e(current_user()['name']); ?></span>

                                        </a>

                                        <!-- ORDERS -->
                                        <a href="<?= e(app_url('user/orders.php')); ?>"
                                            class="flex items-center gap-2 px-4 py-2 hover:bg-white-light">

                                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                            Orders

                                        </a>

                                        <!-- CHECKOUT -->
                                        <a href="<?= e(app_url('checkout.php')); ?>"
                                            class="flex items-center gap-2 px-4 py-2 hover:bg-white-light">

                                            <i data-lucide="badge-indian-rupee" class="w-4 h-4"></i>
                                            Checkout

                                        </a>

                                        <!-- ABOUT -->
                                        <a href="<?= e(app_url('about.php')); ?>"
                                            class="flex items-center gap-2 px-4 py-2 hover:bg-white-light">

                                            <i data-lucide="badge-info" class="w-4 h-4"></i>
                                            About

                                        </a>

                                        <!-- Help -->
                                        <a href="<?= e(app_url('help.php')); ?>"
                                            class="flex items-center gap-2 px-4 py-2 hover:bg-white-light">

                                            <i data-lucide="message-circle-question-mark" class="w-4 h-4"></i>
                                            Help

                                        </a>

                                        <div class="border-t my-2"></div>

                                        <!-- LOGOUT -->
                                        <a href="<?= e(app_url('user/logout.php')); ?>"
                                            class="flex items-center gap-2 px-4 py-2 text-red-500 hover:bg-white-light">

                                            <i data-lucide="log-out" class="w-4 h-4"></i>
                                            Logout

                                        </a>

                                    </div>

                                </div>

                            <?php endif; ?>

                        </div>

                    <?php else: ?>

                        <a href="<?= e(app_url('user/login.php')); ?>"
                            class="px-4 py-2 bg-primary-medium hover:bg-primary-medium/90 text-white-dark">

                            Sign Up

                        </a>

                    <?php endif; ?>


                </div>

            </div>

        </div>

        <!-- MOBILE MENU -->
        <div id="mobileNav"
            class="fixed inset-0 bg-black-dark/40 hidden z-50 lg:hidden ">

            <div id="mobileDrawer"
                class="absolute right-0 top-0 h-full w-72 bg-white-dark shadow-lg transform translate-x-full transition-transform duration-300">

                <div class="p-4 h-full flex flex-col">

                    <!-- CLOSE BUTTON -->
                    <button id="navClose" class="self-end text-black-light">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>


                    <div class="mt-4 text-sm flex flex-col flex-1 h-full">

                        <!-- SEARCH BAR -->
                        <form action="<?= e(app_url('shop.php')); ?>" method="get"
                            class="flex items-center w-full mb-6">

                            <input
                                type="search"
                                name="q"
                                placeholder="Search watches..."
                                class="px-4 py-2 text-sm outline-none border focus:border-primary-medium w-full">

                            <button
                                class="bg-primary-medium border border-primary-medium text-white-dark px-3 py-2">

                                <i data-lucide="search" class="w-5 h-5"></i>

                            </button>

                        </form>



                        <!-- MENU LINKS -->
                        <div class="px-2 space-y-6 flex-1 text-black-medium overflow-y-auto h-full">

                            <!-- HOME -->
                            <a href="<?= e(app_url()); ?>"
                                class="flex items-center gap-3 hover:text-white-medium">

                                <i data-lucide="home" class="w-5 h-5 text-black-light"></i>
                                Home

                            </a>



                            <!-- COLLECTION DROPDOWN -->
                            <div>

                                <button id="mobileCollectionBtn"
                                    class="flex items-center justify-between w-full hover:text-white-medium">

                                    <span class="flex items-center gap-3">

                                        <i data-lucide="layers" class="text-black-light w-5 h-5"></i>
                                        Collection

                                    </span>

                                    <span id="mobileCollectionIcon"
                                        class="transition-transform duration-300">

                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>

                                    </span>

                                </button>


                                <div id="mobileCollectionMenu"
                                    class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">

                                    <div class="py-3 pl-8 space-y-3 text-black-light">

                                        <?php foreach ($headerCategories as $navCategory): ?>

                                            <a
                                                href="<?= e(app_url('shop.php?category=' . (int)$navCategory['id'])); ?>"
                                                class="flex items-center gap-3 text-sm hover:text-white-medium">

                                                <?= e($navCategory['name']); ?>

                                            </a>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            </div>



                            <!-- WISHLIST -->
                            <a href="<?= e(app_url('wishlist.php')); ?>"
                                class="flex items-center gap-3 relative hover:text-white-medium">

                                <i data-lucide="heart" class="text-red-500 w-5 h-5"></i>

                                Wishlist

                                <?php if ($headerWishlistCount > 0): ?>

                                    <span
                                        class="absolute -top-1 left-[5.4rem] bg-red-500 text-white-dark text-xs px-1.5 rounded-full">

                                        <?= (int)$headerWishlistCount ?>

                                    </span>

                                <?php endif; ?>

                            </a>



                            <!-- CART -->
                            <a href="<?= e(app_url('cart.php')); ?>"
                                class="flex items-center gap-3 relative hover:text-white-medium">

                                <i data-lucide="shopping-cart" class="text-black-light w-5 h-5"></i>

                                Cart

                                <?php if ($headerCartCount > 0): ?>

                                    <span
                                        class="absolute -top-1 left-16 bg-primary-medium text-white-dark text-xs px-1.5 rounded-full">

                                        <?= (int)$headerCartCount ?>

                                    </span>

                                <?php endif; ?>

                            </a>


                            <!-- PROFILE -->
                            <?php if (is_logged_in()): ?>

                                <a href="<?= e(app_url('user/profile.php')); ?>"
                                    class="flex items-center gap-3 hover:text-white-medium">

                                    <i data-lucide="user" class="text-black-light w-5 h-5"></i>
                                    Profile
                                </a>

                            <?php endif; ?>



                            <!-- ORDERS -->
                            <?php if (is_logged_in()): ?>

                                <a href="<?= e(app_url('user/orders.php')); ?>"
                                    class="flex items-center gap-3 hover:text-white-medium">

                                    <i data-lucide="shopping-bag" class="text-black-light w-5 h-5"></i>
                                    Orders
                                </a>

                            <?php endif; ?>



                            <!-- CHECKOUT -->
                            <?php if (is_logged_in()): ?>

                                <a href="<?= e(app_url('checkout.php')); ?>"
                                    class="flex items-center gap-3 hover:text-white-medium">

                                    <i data-lucide="badge-indian-rupee" class="text-black-light w-5 h-5"></i>
                                    Checkout
                                </a>

                            <?php endif; ?>



                            <!-- ABOUT -->
                            <a href="<?= e(app_url('about.php')); ?>"
                                class="flex items-center gap-3 hover:text-white-medium">

                                <i data-lucide="badge-info" class="text-black-light w-5 h-5"></i>
                                About
                            </a>



                            <!-- HELP SUPPORT -->
                            <a href="<?= e(app_url('help.php')); ?>"
                                class="flex items-center gap-3 hover:text-white-medium">

                                <i data-lucide="message-circle-question-mark" class="text-black-light w-5 h-5"></i>
                                Help
                            </a>

                        </div>



                        <!-- BOTTOM AUTH SECTION -->
                        <div class="mt-auto pt-6 p-2 border-t">

                            <?php if (is_logged_in()): ?>

                                <a href="<?= e(app_url('user/logout.php')); ?>"
                                    class="flex items-center gap-3 text-red-500 hover:text-red-600">

                                    <i data-lucide="log-out" class="text-red-500 w-5 h-5"></i>
                                    Logout
                                </a>

                            <?php else: ?>

                                <div class="grid grid-cols-2 gap-4 text-center">

                                    <a href="<?= e(app_url('user/login.php')); ?>"
                                        class="block text-black-medium bg-white-light px-4 py-2">
                                        Login
                                    </a>

                                    <a href="<?= e(app_url('user/signup.php')); ?>"
                                        class="block text-white-dark bg-primary-medium px-4 py-2">
                                        Sign Up
                                    </a>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </header>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // PROMO BAR HIDE ON SCROLL
            const promoBar = document.getElementById("topPromoBar");
            let lastScroll = 0;

            window.addEventListener("scroll", () => {

                const currentScroll = window.scrollY;

                if (currentScroll > 50 && currentScroll > lastScroll) {
                    promoBar.classList.remove("max-h-10", "opacity-100");
                    promoBar.classList.add("max-h-0", "opacity-0");
                } else {
                    promoBar.classList.remove("max-h-0", "opacity-0");
                    promoBar.classList.add("max-h-10", "opacity-100");
                }

                lastScroll = currentScroll;

            });


            // MOBILE DRAWER MENU
            const navToggle = document.getElementById("navToggle");
            const navClose = document.getElementById("navClose");
            const mobileNav = document.getElementById("mobileNav");
            const mobileDrawer = document.getElementById("mobileDrawer");

            // CLOSE DRAWER WHEN CLICKING OUTSIDE
            mobileNav.addEventListener("click", (e) => {

                if (!mobileDrawer.contains(e.target)) {

                    mobileDrawer.classList.add("translate-x-full");

                    setTimeout(() => {
                        mobileNav.classList.add("hidden");
                    }, 300);

                }

            });

            if (navToggle) {

                navToggle.addEventListener("click", () => {

                    mobileNav.classList.remove("hidden");

                    setTimeout(() => {
                        mobileDrawer.classList.remove("translate-x-full");
                    }, 10);

                });

            }

            if (navClose) {

                navClose.addEventListener("click", () => {

                    mobileDrawer.classList.add("translate-x-full");

                    setTimeout(() => {
                        mobileNav.classList.add("hidden");
                    }, 300);

                });

            }


            // MOBILE COLLECTION DROPDOWN
            const collectionBtn = document.getElementById("mobileCollectionBtn");
            const collectionMenu = document.getElementById("mobileCollectionMenu");
            const collectionIcon = document.getElementById("mobileCollectionIcon");

            if (collectionBtn) {

                collectionBtn.addEventListener("click", () => {

                    const isClosed = collectionMenu.classList.contains("max-h-0");

                    collectionMenu.classList.toggle("max-h-0", !isClosed);
                    collectionMenu.classList.toggle("max-h-96", isClosed);

                    collectionIcon.classList.toggle("rotate-180", isClosed);

                });

            }

        });

        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
    <?php if ($flash): ?>
        <div class="absolute top-20 mx-auto max-w-7xl px-4 md:px-0 z-50">
            <div class="flash-message rounded-2xl border px-4 py-3 text-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'; ?>">
                <?= e($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>