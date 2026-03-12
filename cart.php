<?php
require_once __DIR__ . '/includes/config.php';

if (isPost()) {
    if (isset($_POST['remove_id'])) {
        cartRemove((int)$_POST['remove_id']);
        setFlash('success', 'Item removed.');
        redirect('/cart.php');
    }
    if (isset($_POST['update_qty_id'])) {
        cartUpdate((int)$_POST['update_qty_id'], (int)($_POST['qty'] ?? 1));
        setFlash('success', 'Cart updated.');
        redirect('/cart.php');
    }
}

$totals = cartTotals($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Cart - Scipwt Ecommerce Platform</title>
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
    <h1 class="text-xl font-semibold text-gray-900">Your Cart</h1>

    <?php if (count($totals['items']) === 0): ?>
      <div class="mt-4 rounded-2xl border bg-white p-6 shadow-soft">
        <div class="text-sm text-gray-600">Your cart is empty.</div>
        <a class="mt-4 inline-flex items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover" href="<?php echo e(BASE_URL); ?>/shop.php">Continue shopping</a>
      </div>
    <?php else: ?>
      <div class="mt-5 grid gap-4 lg:grid-cols-12">
        <div class="lg:col-span-8">
          <?php foreach ($totals['items'] as $it): $p = $it['product']; ?>
            <div class="mb-4 rounded-2xl border bg-white p-4 shadow-soft">
              <div class="grid grid-cols-12 gap-4 items-center">
                <div class="col-span-4 sm:col-span-3">
                  <img class="h-24 w-full rounded-xl object-cover" src="<?php echo e(uploadUrlOrLocal((string)$p['image'])); ?>" alt="product" />
                </div>
                <div class="col-span-8 sm:col-span-5">
                  <div class="text-sm font-semibold text-gray-900"><?php echo e($p['name']); ?></div>
                  <div class="mt-1 text-sm text-gray-600">₹<?php echo e(number_format((float)$p['price'], 2)); ?></div>
                </div>
                <div class="col-span-12 sm:col-span-2">
                  <form method="post" class="flex items-center gap-2">
                    <input type="hidden" name="update_qty_id" value="<?php echo (int)$p['id']; ?>" />
                    <input type="number" class="w-20 rounded-lg border px-3 py-2 text-sm" name="qty" min="1" value="<?php echo (int)$it['quantity']; ?>" />
                    <button class="inline-flex items-center justify-center rounded-lg bg-brand px-3 py-2 text-sm font-semibold text-white hover:bg-brand-hover" type="submit">Update</button>
                  </form>
                </div>
                <div class="col-span-12 sm:col-span-2 sm:text-right">
                  <div class="text-sm font-semibold text-gray-900">₹<?php echo e(number_format((float)$it['line_total'], 2)); ?></div>
                  <form method="post" class="mt-2">
                    <input type="hidden" name="remove_id" value="<?php echo (int)$p['id']; ?>" />
                    <button class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100" type="submit">Remove</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="lg:col-span-4">
          <div class="rounded-2xl border bg-white p-5 shadow-soft">
            <h2 class="text-base font-semibold text-gray-900">Summary</h2>
            <div class="mt-4 space-y-2 text-sm">
              <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹<?php echo e(number_format((float)$totals['subtotal'], 2)); ?></span></div>
              <div class="flex justify-between text-gray-600"><span>Discount</span><span>- ₹<?php echo e(number_format((float)$totals['discount'], 2)); ?></span></div>
              <div class="border-t pt-3 flex justify-between font-semibold text-gray-900"><span>Total</span><span>₹<?php echo e(number_format((float)$totals['total'], 2)); ?></span></div>
            </div>
            <a class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover" href="<?php echo e(BASE_URL); ?>/checkout.php">Checkout</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
