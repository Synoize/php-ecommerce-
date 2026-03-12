<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$user = currentUser();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Profile - Scipwt Ecommerce Platform</title>
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

  <main class="mt-28 mb-12 mx-auto max-w-7xl px-4">
    <div class="grid gap-4 lg:grid-cols-12">
      <div class="lg:col-span-4">
        <div class="rounded-2xl border bg-white p-5 shadow-soft">
          <h1 class="text-base font-semibold text-gray-900">Profile</h1>
          <div class="mt-4 text-sm text-gray-600">Name</div>
          <div class="text-sm font-semibold text-gray-900"><?php echo e($user['name'] ?? ''); ?></div>
          <div class="mt-3 text-sm text-gray-600">Email</div>
          <div class="text-sm font-semibold text-gray-900"><?php echo e($user['email'] ?? ''); ?></div>
        </div>
      </div>
      <div class="lg:col-span-8">
        <div class="rounded-2xl border bg-white p-5 shadow-soft">
          <h2 class="text-base font-semibold text-gray-900">Orders</h2>
          <a class="mt-4 inline-flex items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover" href="<?php echo e(BASE_URL); ?>/user/orders.php">View Order History</a>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
