<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (is_logged_in()) {
    redirect('');
}

if (is_post()) {
    verify_csrf();
    $result = (new AuthController())->login($_POST);
    set_flash($result['ok'] ? 'success' : 'error', $result['message']);
    redirect($result['ok'] ? '' : 'user/login.php');
}

$pageTitle = 'Login';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-md min-h-[calc(100vh-112px)] px-8 py-12">
    <div class="md:border md:bg-white-light/20 md:p-8">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-primary-medium">Login to Your Account</h1>
            <p class="mt-2 text-sm text-black-light">Welcome back! Please enter your details.</p>
        </div>

        <form action="" method="post" class="mt-8 space-y-5">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">

            <div>
                <label class="text-sm text-black-light">Email Address</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    class="mt-1 w-full border border-white-medium px-4 py-3 focus:border-black-light focus:outline-none">
            </div>

            <div>
                <div class="flex items-center justify-between gap-3">
                    <label class="text-sm text-black-light">Password</label>
                    <a href="<?= e(app_url('user/forgot_password.php')); ?>" class="text-xs text-primary-medium hover:underline">Forgot password?</a>
                </div>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    class="mt-1 w-full border border-white-medium px-4 py-3 focus:border-black-light focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-primary-medium py-3 text-white-dark transition hover:bg-primary-medium/90">Login</button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Don't have an account?
            <a class="text-primary-medium hover:underline" href="<?= e(app_url('user/signup.php')); ?>">Create Account</a>
        </p>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
