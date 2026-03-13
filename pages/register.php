<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (is_logged_in()) {
    redirect('');
}

if (is_post()) {
    verify_csrf();
    $result = (new AuthController())->register($_POST);
    set_flash($result['ok'] ? 'success' : 'error', $result['message']);
    redirect($result['ok'] ? '' : 'user/signup.php');
}

$pageTitle = 'Register';
require __DIR__ . '/layout/header.php';
?>
<main class="mx-auto max-w-md px-4 py-16">
    <div class="rounded-[2rem] bg-white p-8 shadow-soft">
        <h1 class="font-display text-3xl font-bold">Create account</h1>
        <form action="" method="post" class="mt-6 space-y-4">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="name" placeholder="Full name" required>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="email" name="email" placeholder="Email" required>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="phone" placeholder="Phone">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="password" name="password" placeholder="Password" required>
            <button class="w-full rounded-full bg-brand-600 px-6 py-3 font-semibold text-white" type="submit">Register</button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
