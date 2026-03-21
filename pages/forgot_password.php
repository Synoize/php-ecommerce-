<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (is_logged_in()) {
    redirect('');
}

$result = null;

if (is_post()) {
    verify_csrf();
    $result = (new AuthController())->requestPasswordReset((string) ($_POST['email'] ?? ''));
    if (!$result['ok']) {
        set_flash('error', $result['message']);
    }
}

$pageTitle = 'Forgot Password';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-md px-4 py-12">
    <div class="border p-8 md:bg-white-light/20">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-primary-medium">Forgot Password</h1>
            <p class="mt-2 text-sm text-black-light">Enter your email address to generate a password reset link.</p>
        </div>

        <form action="" method="post" class="mt-8 space-y-5">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <div>
                <label class="text-sm text-black-light">Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required class="mt-1 w-full border border-white-medium px-4 py-3 focus:border-black-light focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-primary-medium py-3 text-white-dark transition hover:bg-primary-medium/90">Generate Reset Link</button>
        </form>

        <?php if ($result && $result['ok']): ?>
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <div><?= e($result['message']); ?></div>
                <?php if (!empty($result['reset_link'])): ?>
                    <div class="mt-3 break-all">
                        Reset link:
                        <a class="font-semibold underline" href="<?= e((string) $result['reset_link']); ?>"><?= e((string) $result['reset_link']); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <p class="mt-6 text-center text-sm text-black-light">
            Remembered your password?
            <a class="text-primary-medium hover:underline" href="<?= e(app_url('user/login.php')); ?>">Back to login</a>
        </p>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
