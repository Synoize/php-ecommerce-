<?php
require_once __DIR__ . '/includes/config.php';

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$sort = isset($_GET['sort']) ? (string)$_GET['sort'] : '';

$cats = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

$sql = 'SELECT p.* FROM products p WHERE 1=1';
$params = [];
if ($categoryId > 0) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $categoryId;
}
if ($search !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($sort === 'price_asc') {
    $sql .= ' ORDER BY p.price ASC';
} elseif ($sort === 'price_desc') {
    $sql .= ' ORDER BY p.price DESC';
} else {
    $sql .= ' ORDER BY p.id DESC';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

if (isPost() && isset($_POST['add_to_cart_id'])) {
    $pid = (int)$_POST['add_to_cart_id'];
    cartAdd($pid, 1);
    setFlash('success', 'Added to cart.');
    redirect('/cart.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Shop - Scipwt Ecommerce Platform</title>
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
    <div class="rounded-2xl border bg-white p-4 shadow-soft">
      <div class="grid gap-3 lg:grid-cols-12 lg:items-end">
        <div class="lg:col-span-3">
          <label class="block text-sm font-medium text-gray-700">Category</label>
          <select class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" onchange="location.href='<?php echo e(BASE_URL); ?>/shop.php?category='+this.value">
            <option value="0">All</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?php echo (int)$c['id']; ?>" <?php echo $categoryId===(int)$c['id']?'selected':''; ?>><?php echo e($c['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="lg:col-span-6">
          <form class="mt-0 flex gap-2" method="get" action="">
            <input type="hidden" name="category" value="<?php echo (int)$categoryId; ?>" />
            <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search products..." />
            <button class="mt-1 inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-hover" type="submit">Search</button>
          </form>
        </div>

        <div class="lg:col-span-3">
          <label class="block text-sm font-medium text-gray-700">Sort</label>
          <select class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" onchange="location.href='<?php echo e(BASE_URL); ?>/shop.php?category=<?php echo (int)$categoryId; ?>&q=<?php echo urlencode($search); ?>&sort='+this.value">
            <option value="">Newest</option>
            <option value="price_asc" <?php echo $sort==='price_asc'?'selected':''; ?>>Price: Low to High</option>
            <option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>Price: High to Low</option>
          </select>
        </div>
      </div>
    </div>

    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
      <?php foreach ($products as $p): ?>
        <div class="overflow-hidden rounded-2xl border bg-white shadow-soft hover:-translate-y-0.5 hover:shadow-md transition">
          <a href="<?php echo e(BASE_URL); ?>/product.php?id=<?php echo (int)$p['id']; ?>">
            <img class="h-44 w-full object-cover" src="<?php echo e(uploadUrlOrLocal((string)$p['image'])); ?>" alt="product" />
          </a>
          <div class="p-4">
            <div class="line-clamp-1 text-sm font-semibold text-gray-900"><?php echo e($p['name']); ?></div>
            <div class="mt-1 text-sm text-gray-600">₹<?php echo e(number_format((float)$p['price'], 2)); ?></div>
            <form method="post" class="mt-3">
              <input type="hidden" name="add_to_cart_id" value="<?php echo (int)$p['id']; ?>" />
              <button class="inline-flex w-full items-center justify-center rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-hover" type="submit">Add to Cart</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script>
    lucide.createIcons();
  </script>
</body>

</html>
