<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$addresses = (new AddressModel())->all();
$adminPageTitle = 'Manage Addresses';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Addresses</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Customer</th><th class="pb-3">Recipient</th><th class="pb-3">Phone</th><th class="pb-3">Address</th><th class="pb-3">Default</th><th class="pb-3">Created</th></tr></thead>
            <tbody>
            <?php foreach ($addresses as $address): ?>
                <tr class="border-t border-slate-100 align-top">
                    <td class="py-3">
                        <div class="font-semibold"><?= e($address['user_name']); ?></div>
                        <div class="text-xs text-slate-500"><?= e($address['email']); ?></div>
                    </td>
                    <td class="py-3"><?= e($address['full_name']); ?></td>
                    <td class="py-3"><?= e($address['phone']); ?></td>
                    <td class="py-3"><?= e($address['address_line']); ?>, <?= e($address['city']); ?>, <?= e($address['state']); ?> - <?= e($address['pincode']); ?>, <?= e($address['country']); ?></td>
                    <td class="py-3"><?= (int) $address['is_default'] === 1 ? 'Yes' : 'No'; ?></td>
                    <td class="py-3"><?= e((string) $address['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
