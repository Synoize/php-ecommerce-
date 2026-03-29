<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$userModel = new UserModel();
if (is_post()) {
    verify_csrf();
    $userModel->updateRole((int) ($_POST['user_id'] ?? 0), (string) ($_POST['role'] ?? 'user'));
    set_flash('success', 'User role updated.');
    redirect('admin/manage_users.php');
}

$users = $userModel->all();
$adminPageTitle = 'Manage Users';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Users</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Name</th><th class="pb-3">Email</th><th class="pb-3">Phone</th><th class="pb-3">Orders</th><th class="pb-3">Addresses</th><th class="pb-3">Role</th><th class="pb-3">Created</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><?= e($user['name']); ?></td>
                    <td class="py-3"><?= e($user['email']); ?></td>
                    <td class="py-3"><?= e((string) $user['phone']); ?></td>
                    <td class="py-3"><?= (int) $user['orders_count']; ?></td>
                    <td class="py-3"><?= (int) $user['address_count']; ?></td>
                    <td class="py-3">
                        <form action="" method="post" class="flex items-center gap-2">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="user_id" value="<?= (int) $user['id']; ?>">
                            <select name="role" class="rounded-xl border border-slate-200 px-3 py-2">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button class="rounded-xl bg-primary-medium hover:bg-primary-medium/90 px-4 py-2 text-white-dark" type="submit">Save</button>
                        </form>
                    </td>
                    <td class="py-3"><?= e((string) $user['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
