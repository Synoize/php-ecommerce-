<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$couponModel = new CouponModel();
$edit = isset($_GET['id']) ? $couponModel->find((int) $_GET['id']) : null;

if (is_post()) {
    verify_csrf();
    if (isset($_POST['delete_id'])) {
        $couponModel->delete((int) $_POST['delete_id']);
        set_flash('success', 'Coupon deleted.');
    } else {
        $couponModel->save($_POST, isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null);
        set_flash('success', 'Coupon saved.');
    }
    redirect('admin/coupons_page.php');
}

$coupons = $couponModel->all();
$adminPageTitle = 'Manage Coupons';
require __DIR__ . '/partials/header.php';
?>
<div class="grid gap-6 lg:grid-cols-[360px,1fr]">
    <div class="rounded-3xl bg-white p-6 shadow">
        <h1 class="text-2xl font-bold"><?= $edit ? 'Edit coupon' : 'Add coupon'; ?></h1>
        <form action="" method="post" class="mt-6 space-y-4">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0); ?>">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="code" value="<?= e((string) ($edit['code'] ?? '')); ?>" placeholder="Coupon code" required>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="number" name="discount_percent" value="<?= e((string) ($edit['discount_percent'] ?? '10')); ?>" min="1" max="100" required>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="date" name="valid_from" value="<?= e((string) ($edit['valid_from'] ?? '')); ?>">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="date" name="valid_to" value="<?= e((string) ($edit['valid_to'] ?? '')); ?>">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" <?= !isset($edit['is_active']) || (int) $edit['is_active'] === 1 ? 'checked' : ''; ?>> Active</label>
            <button class="w-full rounded-full bg-primary-medium hover:bg-primary-medium/90 px-5 py-3 font-semibold text-white-dark" type="submit">Save coupon</button>
        </form>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <h2 class="text-2xl font-bold">Coupons</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead><tr class="text-left text-slate-500"><th class="pb-3">Code</th><th class="pb-3">Discount</th><th class="pb-3">Validity</th><th class="pb-3">Status</th><th class="pb-3">Action</th></tr></thead>
                <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr class="border-t border-slate-100">
                        <td class="py-3 font-semibold"><?= e($coupon['code']); ?></td>
                        <td class="py-3"><?= (int) $coupon['discount_percent']; ?>%</td>
                        <td class="py-3"><?= e((string) $coupon['valid_from']); ?> to <?= e((string) $coupon['valid_to']); ?></td>
                        <td class="py-3"><?= (int) $coupon['is_active'] === 1 ? 'Active' : 'Inactive'; ?></td>
                        <td class="py-3">
                            <a class="text-sky-600" href="<?= e(app_url('admin/coupons_page.php?id=' . (int) $coupon['id'])); ?>">Edit</a>
                            <form action="" method="post" class="inline">
                                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                                <input type="hidden" name="delete_id" value="<?= (int) $coupon['id']; ?>">
                                <button class="ml-3 text-rose-600" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
