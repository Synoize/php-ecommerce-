<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$totalUsers = (int)$pdo->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch()['c'];
$totalProducts = (int)$pdo->query("SELECT COUNT(*) AS c FROM products")->fetch()['c'];
$totalOrders = (int)$pdo->query("SELECT COUNT(*) AS c FROM orders")->fetch()['c'];
$sales = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM orders")->fetch()['s'];

$chartRows = $pdo->query("SELECT DATE(created_at) d, SUM(total_amount) s FROM orders GROUP BY DATE(created_at) ORDER BY d ASC LIMIT 14")->fetchAll();
$labels = array_map(fn($r) => $r['d'], $chartRows);
$values = array_map(fn($r) => (float)$r['s'], $chartRows);
?>

<h1 class="text-xl font-semibold text-gray-900">Admin Dashboard</h1>

<div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
  <div class="rounded-2xl border bg-white p-5 shadow-soft">
    <div class="text-sm text-gray-600">Users</div>
    <div class="mt-2 text-2xl font-bold text-gray-900"><?php echo $totalUsers; ?></div>
  </div>
  <div class="rounded-2xl border bg-white p-5 shadow-soft">
    <div class="text-sm text-gray-600">Products</div>
    <div class="mt-2 text-2xl font-bold text-gray-900"><?php echo $totalProducts; ?></div>
  </div>
  <div class="rounded-2xl border bg-white p-5 shadow-soft">
    <div class="text-sm text-gray-600">Orders</div>
    <div class="mt-2 text-2xl font-bold text-gray-900"><?php echo $totalOrders; ?></div>
  </div>
  <div class="rounded-2xl border bg-white p-5 shadow-soft">
    <div class="text-sm text-gray-600">Sales</div>
    <div class="mt-2 text-2xl font-bold text-gray-900">₹<?php echo e(number_format($sales, 2)); ?></div>
  </div>
</div>

<div class="mt-6 rounded-2xl border bg-white p-5 shadow-soft">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-base font-semibold text-gray-900">Sales Chart</h2>
    <div class="flex flex-wrap gap-2">
      <a class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50" href="<?php echo e(BASE_URL); ?>/admin/manage_products.php">Products</a>
      <a class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50" href="<?php echo e(BASE_URL); ?>/admin/manage_orders.php">Orders</a>
      <a class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50" href="<?php echo e(BASE_URL); ?>/admin/manage_users.php">Users</a>
    </div>
  </div>
  <div class="mt-4">
    <canvas id="salesChart" height="90"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const labels = <?php echo json_encode($labels); ?>;
  const values = <?php echo json_encode($values); ?>;
  const ctx = document.getElementById('salesChart');
  const brandColor = (window.tailwind && window.tailwind.config && window.tailwind.config.theme && window.tailwind.config.theme.extend && window.tailwind.config.theme.extend.colors && window.tailwind.config.theme.extend.colors.brand && window.tailwind.config.theme.extend.colors.brand.DEFAULT)
    ? window.tailwind.config.theme.extend.colors.brand.DEFAULT
    : '#0d6efd';
  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Sales',
        data: values,
        borderColor: brandColor,
        tension: 0.3,
        fill: false
      }]
    }
  });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
