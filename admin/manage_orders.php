<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

if (isPost() && isset($_POST['order_id'], $_POST['status'])) {
    $oid = (int)$_POST['order_id'];
    $status = (string)$_POST['status'];
    $allowed = ['pending','shipped','delivered'];
    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status=? WHERE id=?');
        $stmt->execute([$status, $oid]);
        setFlash('success', 'Order status updated.');
        redirect('/admin/manage_orders.php');
    }
}

$orders = $pdo->query('SELECT o.*, u.name user_name, u.email user_email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();
?>

<h1 class="text-xl font-semibold text-gray-900">Manage Orders</h1>

<div class="mt-4 overflow-hidden rounded-2xl border bg-white shadow-soft">
  <div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
        <tr>
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">User</th>
          <th class="px-4 py-3">Total</th>
          <th class="px-4 py-3">Payment</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Date</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($orders as $o): ?>
          <tr>
            <td class="px-4 py-3 text-gray-900"><?php echo (int)$o['id']; ?></td>
            <td class="px-4 py-3">
              <div class="font-medium text-gray-900"><?php echo e($o['user_name']); ?></div>
              <div class="text-xs text-gray-600"><?php echo e($o['user_email']); ?></div>
            </td>
            <td class="px-4 py-3 text-gray-900">₹<?php echo e(number_format((float)$o['total_amount'],2)); ?></td>
            <td class="px-4 py-3">
              <div class="text-xs text-gray-600"><?php echo e($o['payment_method'] ?? '-'); ?></div>
              <div class="text-xs font-semibold text-gray-900"><?php echo e($o['payment_status'] ?? '-'); ?></div>
            </td>
            <td class="px-4 py-3">
              <form method="post" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>" />
                <select class="rounded-lg border px-3 py-2 text-sm" name="status">
                  <?php foreach (['pending','shipped','delivered'] as $s): ?>
                    <option value="<?php echo e($s); ?>" <?php echo $o['status']===$s?'selected':''; ?>><?php echo e($s); ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="inline-flex items-center justify-center rounded-lg bg-brand px-3 py-2 text-sm font-semibold text-white hover:bg-brand-hover" type="submit">Update</button>
              </form>
            </td>
            <td class="px-4 py-3 text-gray-600"><?php echo e($o['created_at']); ?></td>
            <td class="px-4 py-3 text-right">
              <a class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50" href="<?php echo e(BASE_URL); ?>/admin/order_view.php?id=<?php echo (int)$o['id']; ?>">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (count($orders) === 0): ?>
          <tr><td colspan="7" class="px-4 py-6 text-sm text-gray-600">No orders.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
