<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$users = (new UserModel())->all();
$adminPageTitle = 'Manage Users';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Users</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Name</th><th class="pb-3">Email</th><th class="pb-3">Phone</th><th class="pb-3">Role</th><th class="pb-3">Created</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><?= e($user['name']); ?></td>
                    <td class="py-3"><?= e($user['email']); ?></td>
                    <td class="py-3"><?= e((string) $user['phone']); ?></td>
                    <td class="py-3"><?= e($user['role']); ?></td>
                    <td class="py-3"><?= e((string) $user['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

