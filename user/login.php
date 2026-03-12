<?php
require_once __DIR__ . '/../includes/config.php';

$errors = [];

if (isPost()) {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($password === '') $errors[] = 'Password is required.';

    if (count($errors) === 0) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, (string)$user['password'])) {
            $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
            setFlash('success', 'Welcome back.');
            redirect('/index.php');
        } else {
            $errors[] = 'Invalid credentials.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Login - Scipwt Ecommerce Platform</title>
  <link rel="icon" href="<?php echo e(asset('images/logo/favicon.svg')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="<?php echo e(BASE_URL); ?>/tailwind.config.js"></script>
</head>

<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="flex items-center justify-center min-h-[calc(100vh-112px)]">
    <div class="mx-auto max-w-xl">
      <div class="rounded-2xl border bg-white p-6">
        <h1 class="text-xl font-semibold text-gray-900">Login</h1>

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

        <div class="mt-4 text-center text-sm text-gray-600">
          <a class="font-semibold text-brand hover:underline" href="<?php echo e(BASE_URL); ?>/user/signup.php">Create an account</a>
        </div>
      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
