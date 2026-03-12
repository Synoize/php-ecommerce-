<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT o.*, u.name user_name, u.email user_email FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) {
    setFlash('danger', 'Order not found.');
    redirect('/admin/manage_orders.php');
}

$itemsStmt = $pdo->prepare('SELECT oi.*, p.name product_name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id = ?');
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();
?>

<h1 class="text-xl font-semibold text-gray-900">Order #<?php echo (int)$order['id']; ?></h1>

<div class="mt-5 grid gap-4 lg:grid-cols-12">
  <div class="lg:col-span-4">
    <div class="rounded-2xl border bg-white p-5 shadow-soft">
      <div class="text-sm text-gray-600">User</div>
      <div class="mt-1 text-sm font-semibold text-gray-900"><?php echo e($order['user_name']); ?></div>
      <div class="text-xs text-gray-600"><?php echo e($order['user_email']); ?></div>

      <div class="my-4 border-t"></div>

      <div class="text-sm text-gray-600">Status</div>
      <div class="mt-1 text-sm font-semibold text-gray-900"><?php echo e($order['status']); ?></div>

      <div class="my-4 border-t"></div>

      <div class="text-sm text-gray-600">Payment</div>
      <div class="mt-1 text-sm font-semibold text-gray-900"><?php echo e($order['payment_method'] ?? '-'); ?> (<?php echo e($order['payment_status'] ?? '-'); ?>)</div>
      <?php if (!empty($order['razorpay_payment_id']) || !empty($order['razorpay_order_id'])): ?>
        <div class="mt-2 text-xs text-gray-600">Razorpay Payment ID: <?php echo e($order['razorpay_payment_id'] ?? '-'); ?></div>
        <div class="text-xs text-gray-600">Razorpay Order ID: <?php echo e($order['razorpay_order_id'] ?? '-'); ?></div>
      <?php endif; ?>

      <div class="my-4 border-t"></div>

      <div class="text-sm text-gray-600">Total</div>
      <div class="mt-1 text-lg font-bold text-gray-900">₹<?php echo e(number_format((float)$order['total_amount'],2)); ?></div>
    </div>
  </div>

  <div class="lg:col-span-8">
    <div class="overflow-hidden rounded-2xl border bg-white shadow-soft">
      <div class="px-5 py-4 border-b">
        <h2 class="text-base font-semibold text-gray-900">Items</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
            <tr>
              <th class="px-4 py-3">Product</th>
              <th class="px-4 py-3">Qty</th>
              <th class="px-4 py-3">Price</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php foreach ($items as $it): ?>
              <tr>
                <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($it['product_name']); ?></td>
                <td class="px-4 py-3 text-gray-900"><?php echo (int)$it['quantity']; ?></td>
                <td class="px-4 py-3 text-gray-900">₹<?php echo e(number_format((float)$it['price'],2)); ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($items) === 0): ?>
              <tr><td colspan="3" class="px-4 py-6 text-sm text-gray-600">No items.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
