<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (is_logged_in() && current_user()['role'] === 'admin') {
    redirect('admin/dashboard.php');
}

if (is_post()) {
    verify_csrf();
    if ((new AuthController())->login($_POST)) {
        redirect('admin/admin_login.php');
    }
}

$pageTitle = 'Admin Login';
require __DIR__ . '/../pages/layout/header.php';
?>
<main class="mx-auto mt-28 max-w-md min-h-[calc(100vh-112px)] px-4 py-12">
    <div class="rounded-3xl bg-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-white-medium">Secure Area</p>
                <h1 class="mt-3 text-3xl font-semibold text-black-medium">Admin login</h1>
                <p class="mt-2 text-sm text-black-light">Sign in with an administrator account.</p>
            </div>
            <div class="rounded-full border border-primary-medium p-3 text-primary-medium">
                <i data-lucide="shield-check" class="h-5 w-5"></i>
            </div>
        </div>
        <form action="" method="post" class="mt-8 space-y-5">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <div>
                <label class="mb-2 block text-sm font-medium text-black-light">Email</label>
                <input type="email" name="email" class="w-full border px-4 py-3 outline-none transition focus:border-white-medium" placeholder="admin@gmail.com" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black-light">Password</label>
                <input type="password" name="password" class="w-full border px-4 py-3 outline-none transition focus:border-white-medium" placeholder="admin123" required>
            </div>
            <button type="submit" class="w-full bg-primary-medium px-5 py-3 text-white-dark transition hover:bg-primary-medium/90">Login to admin</button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../pages/layout/footer.php'; ?>
