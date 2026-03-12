<?php
require_once __DIR__ . '/includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Support - Scipwt Ecommerce Platform</title>
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
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <main class="mt-28 mb-12 mx-auto max-w-7xl px-4">
    <h1 class="text-2xl font-semibold text-gray-900">Support</h1>
    <p class="mt-4 text-gray-700">Need help? Contact us or check our FAQ.</p>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
      <div class="rounded-2xl border bg-white p-6 shadow-soft">
        <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
        <div class="mt-4 space-y-3 text-sm text-gray-600">
          <p><i class="fas fa-envelope mr-2"></i>Email: support@scipwt.com</p>
          <p><i class="fas fa-phone mr-2"></i>Phone: +91 1234567890</p>
          <p><i class="fas fa-map-marker-alt mr-2"></i>Address: 123 Watch Street, Mumbai, India</p>
        </div>
      </div>

      <div class="rounded-2xl border bg-white p-6 shadow-soft">
        <h2 class="text-lg font-semibold text-gray-900">Frequently Asked Questions</h2>
        <div class="mt-4 space-y-4">
          <div>
            <h3 class="font-medium text-gray-900">How to track my order?</h3>
            <p class="mt-1 text-sm text-gray-600">You can track your order from your account dashboard.</p>
          </div>
          <div>
            <h3 class="font-medium text-gray-900">Return policy?</h3>
            <p class="mt-1 text-sm text-gray-600">We accept returns within 30 days of purchase.</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
