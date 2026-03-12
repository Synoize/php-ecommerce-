<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$userId = (int)($_SESSION['user']['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Order History - Scipwt Ecommerce Platform</title>
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
    <h1 class="text-xl font-semibold text-gray-900">Order History</h1>

    <div class="mt-4 overflow-hidden rounded-2xl border bg-white shadow-soft">
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
            <tr>
              <th class="px-4 py-3">#</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php foreach ($orders as $o): ?>
              <tr>
                <td class="px-4 py-3 text-gray-900"><?php echo (int)$o['id']; ?></td>
                <td class="px-4 py-3 text-gray-900">₹<?php echo e(number_format((float)$o['total_amount'], 2)); ?></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700"><?php echo e($o['status']); ?></span>
                </td>
                <td class="px-4 py-3 text-gray-600"><?php echo e($o['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($orders) === 0): ?>
              <tr><td colspan="4" class="px-4 py-6 text-sm text-gray-600">No orders yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
