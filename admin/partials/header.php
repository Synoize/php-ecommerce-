<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_admin();
$adminPageTitle = $adminPageTitle ?? 'Admin';

function navLink($path, $label, $icon)
{
    $current = $_SERVER['REQUEST_URI'];
    $isActive = str_contains($current, $path);

    return '
        <a href="' . e(app_url($path)) . '" 
        class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group
        ' . ($isActive ? 'bg-white-dark/20 text-white-dark' : 'hover:bg-white-dark/10') . '">

            <i data-lucide="' . $icon . '" class="w-5 h-5"></i>

            <span class="nav-text transition-all duration-200 whitespace-nowrap">' . $label . '</span>
        </a>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminPageTitle ?? APP_NAME); ?></title>

    <link rel="icon" href="<?= e(asset_url('images/logo/favicon.svg')); ?>">

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
</head>

<body class="min-h-screen bg-white-dark">
    <header class="w-full bg-primary-dark h-20 flex items-center justify-between px-6 fixed top-0 left-0 z-50 text-nowrap">
        <div class="flex items-center gap-4">
            <img src="<?= e(asset_url('images/logo/logo.png')); ?>" class="h-12 w-12">
            <div>
                <h2 class="text-2xl font-bold text-white-dark"><?= e(APP_NAME); ?></h2>
                <p class="text-xs text-white-light">Admin - Store Operations</p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-white-dark text-sm">
            <span class="p-2 rounded-full bg-white-light/20 flex items-center justify-center">
                <i data-lucide="user" class="w-5 h-5"></i>
            </span>
            <p class="hidden md:inline text-white-light"><?= e(current_user()['name']); ?></p>
        </div>
    </header>

    <main class="flex pt-20">
        <aside id="sidebar" class="h-[calc(100vh-80px)] w-64 bg-primary-dark text-white-dark transition-all duration-300 flex flex-col fixed left-0 top-20">
            <div class="flex justify-end p-2">
                <button id="collapseBtn">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
            </div>

            <nav class="p-4 space-y-2 text-sm overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] flex-1">
                <?= navLink('admin/dashboard.php', 'Dashboard', 'layout-dashboard') ?>
                <?= navLink('admin/manage_products.php', 'Products', 'box') ?>
                <?= navLink('admin/box_options.php', 'Box Options', 'package-open') ?>
                <?= navLink('admin/categories_page.php', 'Categories', 'layers') ?>
                <?= navLink('admin/slides.php', 'Slides', 'image') ?>
                <?= navLink('admin/homepage_media.php', 'Home Media', 'film') ?>
                <?= navLink('admin/manage_orders.php', 'Orders', 'shopping-cart') ?>
                <?= navLink('admin/payments.php', 'Payments', 'credit-card') ?>
                <?= navLink('admin/manage_users.php', 'Users', 'users') ?>
                <?= navLink('admin/addresses.php', 'Addresses', 'map-pin') ?>
                <?= navLink('admin/carts.php', 'Carts', 'shopping-bag') ?>
                <?= navLink('admin/wishlists.php', 'Wishlists', 'heart') ?>
                <?= navLink('admin/coupons_page.php', 'Coupons', 'ticket') ?>
                <?= navLink('admin/reviews_page.php', 'Reviews', 'star') ?>
                <?= navLink('user/logout.php', 'Logout', 'log-out') ?>
            </nav>
        </aside>

        <div id="mainContent" class="ml-64 w-full transition-all duration-300 h-[calc(100vh-80px)] overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] p-6">
            <script>
                const sidebar = document.getElementById('sidebar');
                const collapseBtn = document.getElementById('collapseBtn');
                const navTexts = document.querySelectorAll('.nav-text');
                const mainContent = document.getElementById('mainContent');

                let collapsed = false;

                collapseBtn.addEventListener('click', () => {
                    collapsed = !collapsed;

                    if (collapsed) {
                        sidebar.classList.replace('w-64', 'w-20');
                        mainContent.classList.replace('ml-64', 'ml-20');
                        navTexts.forEach(el => el.classList.add('hidden'));
                        collapseBtn.innerHTML = '<i data-lucide="chevron-right" class="w-5 h-5"></i>';
                    } else {
                        sidebar.classList.replace('w-20', 'w-64');
                        mainContent.classList.replace('ml-20', 'ml-64');
                        navTexts.forEach(el => el.classList.remove('hidden'));
                        collapseBtn.innerHTML = '<i data-lucide="chevron-left" class="w-5 h-5"></i>';
                    }

                    lucide.createIcons();
                });

                lucide.createIcons();
            </script>
