<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$categoryModel = new CategoryModel();
$edit = isset($_GET['id']) ? $categoryModel->find((int) $_GET['id']) : null;

if (is_post()) {
    verify_csrf();
    if (isset($_POST['delete_id'])) {
        $categoryModel->delete((int) $_POST['delete_id']);
        set_flash('success', 'Category deleted.');
    } else {
        $categoryModel->save($_POST, isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null);
        set_flash('success', 'Category saved.');
    }
    redirect('admin/categories.php');
}

$categories = $categoryModel->all();
$adminPageTitle = 'Manage Categories';
require __DIR__ . '/partials/header.php';
?>
<div class="grid gap-6 lg:grid-cols-[360px,1fr]">
    <div class="rounded-3xl bg-white p-6 shadow">
        <h1 class="text-2xl font-bold"><?= $edit ? 'Edit category' : 'Add category'; ?></h1>
        <form action="" method="post" class="mt-6 space-y-4">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0); ?>">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="name" value="<?= e((string) ($edit['name'] ?? '')); ?>" placeholder="Category name" required>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="image" value="<?= e((string) ($edit['image'] ?? '')); ?>" placeholder="Image URL or asset path">
            <button class="w-full rounded-full bg-slate-900 px-5 py-3 font-semibold text-white" type="submit">Save category</button>
        </form>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <h2 class="text-2xl font-bold">All categories</h2>
        <div class="mt-4 space-y-3">
            <?php foreach ($categories as $category): ?>
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-4">
                    <div class="flex items-center gap-3">
                        <img src="<?= e(upload_url((string) $category['image'])); ?>" alt="" class="h-12 w-12 rounded-2xl object-cover">
                        <div>
                            <div class="font-semibold"><?= e($category['name']); ?></div>
                            <div class="text-sm text-slate-500"><?= (int) $category['product_count']; ?> products</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <a class="text-sky-600" href="<?= e(app_url('admin/categories.php?id=' . (int) $category['id'])); ?>">Edit</a>
                        <form action="" method="post" onsubmit="return confirm('Delete category?')">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="delete_id" value="<?= (int) $category['id']; ?>">
                            <button class="text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
