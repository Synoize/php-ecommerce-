<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (is_post()) {
    verify_csrf();
    $result = (new AuthController())->login($_POST);
    if ($result['ok'] && is_admin()) {
        redirect('admin/index.php');
    }

    (new AuthController())->logout();
    set_flash('error', 'Admin credentials required.');
    redirect('admin/admin_login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 p-4">
    <form action="" method="post" class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl">
        <h1 class="text-3xl font-bold">Admin Login</h1>
        <p class="mt-2 text-sm text-slate-500">Use the seeded `admin@demo.com` account from `database.sql`.</p>
        <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
        <input class="mt-6 w-full rounded-2xl border border-slate-200 px-4 py-3" type="email" name="email" placeholder="Email" required>
        <input class="mt-4 w-full rounded-2xl border border-slate-200 px-4 py-3" type="password" name="password" placeholder="Password" required>
        <button class="mt-6 w-full rounded-full bg-slate-900 px-5 py-3 font-semibold text-white" type="submit">Login</button>
    </form>
</body>
</html>
