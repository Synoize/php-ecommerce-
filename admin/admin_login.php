<?php
require_once __DIR__ . '/../includes/header.php';

$errors = [];

if (isPost()) {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($password === '') $errors[] = 'Password is required.';

    if (count($errors) === 0) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = ? LIMIT 1');
        $stmt->execute([$email, 'admin']);
        $user = $stmt->fetch();
        if ($user && password_verify($password, (string)$user['password'])) {
            $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
            setFlash('success', 'Admin logged in.');
            redirect('/admin/index.php');
        } else {
            $errors[] = 'Invalid admin credentials.';
        }
    }
}
?>

<div class="mx-auto max-w-md">
  <div class="rounded-2xl border bg-white p-6 shadow-soft">
    <h1 class="text-xl font-semibold text-gray-900">Admin Login</h1>

    <?php if (count($errors) > 0): ?>
      <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="mt-5 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="email" type="email" required />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="password" type="password" required />
      </div>
      <button class="inline-flex w-full items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover" type="submit">Login</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
