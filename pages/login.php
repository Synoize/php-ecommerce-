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
<main class="mt-28 mx-auto max-w-md px-4 py-12">

    <div class="md:bg-white-light/20 md:border md:p-8">

        <!-- Title -->
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-primary-medium">
                Login to Your Account
            </h1>

            <p class="text-sm text-black-light mt-2">
                Welcome back! Please enter your details.
            </p>
        </div>

        <!-- Form -->
        <form action="" method="post" class="mt-8 space-y-5">

            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">

            <!-- Email -->
            <div>
                <label class="text-sm text-black-light">Email Address</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    class="mt-1 w-full border border-white-medium px-4 py-3 focus:outline-none focus:border-black-light">
            </div>

            <!-- Password -->
            <div>
                <label class="text-sm text-black-light">Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    class="mt-1 w-full border border-white-medium px-4 py-3 focus:outline-none focus:border-black-light">
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="w-full bg-primary-medium text-white-dark py-3 hover:bg-primary-medium/90 transition">

                Login

            </button>

        </form>

        <!-- Register -->
        <p class="mt-6 text-sm text-center text-gray-600">
            Don't have an account?
            <a class="text-primary-medium hover:underline"
                href="<?= e(app_url('user/signup.php')); ?>">
                Create Account
            </a>
        </p>

    </div>

</main>