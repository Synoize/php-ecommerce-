<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$boxOptionModel = new BoxOptionModel();
$edit = isset($_GET['id']) ? $boxOptionModel->find((int) $_GET['id']) : null;
$errorMessage = null;

if (is_post()) {
    verify_csrf();

    try {
        if (isset($_POST['delete_id'])) {
            $boxOptionModel->delete((int) $_POST['delete_id']);
            set_flash('success', 'Box option deleted.');
            redirect('admin/box_options.php');
        }

        $boxOptionId = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
        $existingImage = trim((string) ($_POST['existing_image'] ?? ''));
        $imagePath = request_uploaded_image('image_upload', 'boxes', $existingImage !== '' ? $existingImage : null);

        $boxOptionModel->save([
            'name' => trim((string) ($_POST['name'] ?? '')),
            'image' => $imagePath,
            'price' => (float) ($_POST['price'] ?? 0),
            'is_active' => !empty($_POST['is_active']) ? 1 : 0,
        ], $boxOptionId);

        set_flash('success', 'Box option saved.');
        redirect('admin/box_options.php');
    } catch (Throwable $exception) {
        $errorMessage = $exception->getMessage();
        $edit = [
            'id' => (int) ($_POST['id'] ?? 0),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'image' => trim((string) ($_POST['existing_image'] ?? '')),
            'price' => (string) ($_POST['price'] ?? '0'),
            'is_active' => !empty($_POST['is_active']) ? 1 : 0,
        ];
    }
}

$boxOptions = $boxOptionModel->all();
$adminPageTitle = 'Manage Box Options';
require __DIR__ . '/partials/header.php';
?>
<div class="grid gap-6 lg:grid-cols-[360px,1fr]">
    <div class="rounded-3xl bg-white p-6 shadow">
        <h1 class="text-2xl font-bold"><?= $edit ? 'Edit box option' : 'Add box option'; ?></h1>
        <?php if ($errorMessage): ?>
            <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($errorMessage); ?></div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data" class="mt-6 space-y-4">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0); ?>">
            <input type="hidden" name="existing_image" value="<?= e((string) ($edit['image'] ?? '')); ?>">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Box name</label>
                <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="name" value="<?= e((string) ($edit['name'] ?? '')); ?>" placeholder="Rolex Box" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Price</label>
                <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="number" min="0" step="0.01" name="price" value="<?= e((string) ($edit['price'] ?? '0')); ?>" placeholder="499" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Upload box image</label>
                <input class="block w-full rounded-2xl border border-slate-200 px-4 py-3" type="file" name="image_upload" accept="image/*">
            </div>
            <?php if (!empty($edit['image'])): ?>
                <div>
                    <div class="mb-2 text-sm font-semibold text-slate-700">Current image</div>
                    <img src="<?= e(upload_url((string) $edit['image'])); ?>" alt="<?= e((string) ($edit['name'] ?? 'Box option')); ?>" class="h-28 w-28 rounded-2xl object-cover">
                </div>
            <?php endif; ?>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" <?= !isset($edit['is_active']) || (int) ($edit['is_active'] ?? 1) === 1 ? 'checked' : ''; ?>> Active</label>
            <button class="w-full rounded-full bg-primary-medium hover:bg-primary-medium/90 px-5 py-3 font-semibold text-white-dark" type="submit">Save box option</button>
        </form>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">All box options</h2>
                <p class="mt-1 text-sm text-slate-500">These options appear on every product page.</p>
            </div>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($boxOptions as $box): ?>
                <div class="rounded-2xl border border-slate-100 p-4">
                    <img src="<?= e(upload_url((string) $box['image'])); ?>" alt="<?= e((string) $box['name']); ?>" class="h-40 w-full rounded-2xl object-contain">
                    <div class="mt-4 flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold"><?= e($box['name']); ?></div>
                            <div class="text-sm text-slate-500"><?= e(money((float) $box['price'])); ?></div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold <?= (int) $box['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'; ?>">
                            <?= (int) $box['is_active'] === 1 ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                    <div class="mt-4 flex items-center gap-3 text-sm">
                        <a class="text-sky-600" href="<?= e(app_url('admin/box_options.php?id=' . (int) $box['id'])); ?>">Edit</a>
                        <form action="" method="post" onsubmit="return confirm('Delete box option?')">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="delete_id" value="<?= (int) $box['id']; ?>">
                            <button class="text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
