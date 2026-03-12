<?php
require_once __DIR__ . '/includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo 'Product not found';
    exit;
}

$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    http_response_code(404);
    echo 'Product not found';
    exit;
}

if (isPost() && isset($_POST['add_to_cart'])) {
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
    cartAdd((int)$product['id'], $qty);
    setFlash('success', 'Added to cart.');
    redirect('/cart.php');
}

$rel = $pdo->prepare('SELECT * FROM products WHERE category_id = ? AND id <> ? ORDER BY id DESC LIMIT 8');
$rel->execute([(int)$product['category_id'], (int)$product['id']]);
$related = $rel->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title><?php echo e($product['name'] ?? 'Product'); ?> - Scipwt Ecommerce Platform</title>
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
    <div class="grid gap-6 lg:grid-cols-12">
      <div class="lg:col-span-5">
        <div class="overflow-hidden rounded-2xl border bg-white shadow-soft">
          <img src="<?php echo e(uploadUrlOrLocal((string)$product['image'])); ?>" class="h-[360px] w-full object-cover" alt="product" />
        </div>
      </div>

      <div class="lg:col-span-7">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900"><?php echo e($product['name']); ?></h1>
            <div class="mt-1 text-sm text-gray-600">Category: <?php echo e($product['category_name'] ?? '-'); ?></div>
          </div>
          <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">In Stock: <?php echo (int)$product['stock']; ?></span>
        </div>

        <div class="mt-4 text-2xl font-bold text-gray-900">₹<?php echo e(number_format((float)$product['price'], 2)); ?></div>
        <p class="mt-4 text-sm leading-6 text-gray-700"><?php echo e($product['description']); ?></p>

        <form method="post" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
          <div class="w-full sm:w-40">
            <label class="block text-sm font-medium text-gray-700">Qty</label>
            <input type="number" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="qty" min="1" value="1" />
          </div>
          <button class="inline-flex w-full items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover sm:w-auto" type="submit" name="add_to_cart">Add to Cart</button>
        </form>

        <div class="mt-8 border-t pt-6">
          <h2 class="text-base font-semibold text-gray-900">Reviews</h2>
          <div class="mt-2 text-sm text-gray-600">No reviews yet.</div>
        </div>
      </div>
    </div>

    <h2 class="mt-12 text-lg font-semibold text-gray-900">Related Products</h2>
    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
      <?php foreach ($related as $p): ?>
        <a class="overflow-hidden rounded-2xl border bg-white shadow-soft hover:-translate-y-0.5 hover:shadow-md transition" href="<?php echo e(BASE_URL); ?>/product.php?id=<?php echo (int)$p['id']; ?>">
          <img class="h-36 w-full object-cover" src="<?php echo e(uploadUrlOrLocal((string)$p['image'])); ?>" alt="product" />
          <div class="p-4">
            <div class="line-clamp-1 text-sm font-semibold text-gray-900"><?php echo e($p['name']); ?></div>
            <div class="mt-1 text-sm text-gray-600">₹<?php echo e(number_format((float)$p['price'], 2)); ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
