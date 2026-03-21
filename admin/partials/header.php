<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_admin();
$adminPageTitle = $adminPageTitle ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminPageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen lg:grid lg:grid-cols-[260px,1fr]">
    <aside class="bg-slate-950 p-6 text-white">
        <div class="text-2xl font-bold">Admin Panel</div>
        <div class="mt-2 text-sm text-slate-400">Store operations</div>
        <nav class="mt-8 space-y-2 text-sm">
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/index.php')); ?>">Dashboard</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/manage_products.php')); ?>">Products</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/categories.php')); ?>">Categories</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/slides.php')); ?>">Slides</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/manage_orders.php')); ?>">Orders</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/payments.php')); ?>">Payments</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/manage_users.php')); ?>">Users</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/addresses.php')); ?>">Addresses</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/carts.php')); ?>">Carts</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/wishlists.php')); ?>">Wishlists</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/coupons.php')); ?>">Coupons</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('admin/reviews.php')); ?>">Reviews</a>
            <a class="block rounded-xl px-4 py-3 hover:bg-white/10" href="<?= e(app_url('user/logout.php')); ?>">Logout</a>
        </nav>
    </aside>
    <main class="p-6">
