<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role<>'admin'");
        $stmt->execute([$id]);
        setFlash('success', 'User deleted.');
        redirect('/admin/manage_users.php');
    }
}

$users = $pdo->query('SELECT id, name, email, role FROM users ORDER BY id DESC')->fetchAll();
?>

<h1 class="text-xl font-semibold text-gray-900">Manage Users</h1>

<div class="mt-4 overflow-hidden rounded-2xl border bg-white shadow-soft">
  <div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm">
      <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
        <tr>
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($users as $u): ?>
          <tr>
            <td class="px-4 py-3 text-gray-900"><?php echo (int)$u['id']; ?></td>
            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($u['name']); ?></td>
            <td class="px-4 py-3 text-gray-700"><?php echo e($u['email']); ?></td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700"><?php echo e($u['role']); ?></span>
            </td>
            <td class="px-4 py-3 text-right">
              <?php if ($u['role'] !== 'admin'): ?>
                <a class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100" href="<?php echo e(BASE_URL); ?>/admin/manage_users.php?delete=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete user?')">Delete</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
