<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (is_logged_in()) {
    redirect('');
}

$selector = trim((string) ($_GET['selector'] ?? $_POST['selector'] ?? ''));
$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$result = null;

if (is_post()) {
    verify_csrf();
    $result = (new AuthController())->resetPassword(
        $selector,
        $token,
        (string) ($_POST['password'] ?? ''),
        (string) ($_POST['confirm_password'] ?? '')
    );

    if ($result['ok']) {
        set_flash('success', $result['message']);
        redirect('user/login.php');
    }

    set_flash('error', $result['message']);
}

$isValidLink = $selector !== '' && $token !== '';
$pageTitle = 'Reset Password';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-md px-4 py-12">
    <div class="border p-8 md:bg-white-light/20">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-primary-medium">Reset Password</h1>
            <p class="mt-2 text-sm text-black-light">Choose a new password for your account.</p>
        </div>

        <?php if ($isValidLink): ?>
            <form action="" method="post" class="mt-8 space-y-5">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="selector" value="<?= e($selector); ?>">
                <input type="hidden" name="token" value="<?= e($token); ?>">

                <div>
                    <label class="text-sm text-black-light">New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required class="mt-1 w-full border border-white-medium px-4 py-3 focus:border-black-light focus:outline-none">
                </div>

                <div>
                    <label class="text-sm text-black-light">Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required class="mt-1 w-full border border-white-medium px-4 py-3 focus:border-black-light focus:outline-none">
                </div>

                <button type="submit" class="w-full bg-primary-medium py-3 text-white-dark transition hover:bg-primary-medium/90">Reset Password</button>
            </form>
        <?php else: ?>
            <div class="mt-6 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                Invalid reset link. Please request a new password reset.
            </div>
        <?php endif; ?>

        <p class="mt-6 text-center text-sm text-black-light">
            <a class="text-primary-medium hover:underline" href="<?= e(app_url('user/forgot_password.php')); ?>">Request a new reset link</a>
        </p>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
