<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$slideModel = new SlideModel();
$edit = isset($_GET['id']) ? $slideModel->find((int) $_GET['id']) : null;

if (is_post()) {
    verify_csrf();
    if (isset($_POST['delete_id'])) {
        $slideModel->delete((int) $_POST['delete_id']);
        set_flash('success', 'Slide deleted.');
    } else {
        $slideModel->save($_POST, isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null);
        set_flash('success', 'Slide saved.');
    }
    redirect('admin/slides.php');
}

$slides = $slideModel->all();
$adminPageTitle = 'Manage Slides';
require __DIR__ . '/partials/header.php';
?>
<div class="grid gap-6 lg:grid-cols-[360px,1fr]">
    <div class="rounded-3xl bg-white p-6 shadow">
        <h1 class="text-2xl font-bold"><?= $edit ? 'Edit slide' : 'Add slide'; ?></h1>
        <form action="" method="post" class="mt-6 space-y-4">
            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
            <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0); ?>">
            <select class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="type">
                <option value="image" <?= ($edit['type'] ?? 'image') === 'image' ? 'selected' : ''; ?>>Image</option>
                <option value="video" <?= ($edit['type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video</option>
            </select>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="file_path" value="<?= e((string) ($edit['file_path'] ?? '')); ?>" placeholder="File path or URL" required>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="title" value="<?= e((string) ($edit['title'] ?? '')); ?>" placeholder="Title">
            <textarea class="w-full rounded-2xl border border-slate-200 px-4 py-3" name="description" rows="4" placeholder="Description"><?= e((string) ($edit['description'] ?? '')); ?></textarea>
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="button_name" value="<?= e((string) ($edit['button_name'] ?? '')); ?>" placeholder="Button text">
            <input class="w-full rounded-2xl border border-slate-200 px-4 py-3" type="text" name="button_link" value="<?= e((string) ($edit['button_link'] ?? '')); ?>" placeholder="Button link">
            <button class="w-full rounded-full bg-slate-900 px-5 py-3 font-semibold text-white" type="submit">Save slide</button>
        </form>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow">
        <h2 class="text-2xl font-bold">All slides</h2>
        <div class="mt-4 space-y-3">
            <?php foreach ($slides as $slide): ?>
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-4">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-20 overflow-hidden rounded-2xl bg-slate-100">
                            <?php if (($slide['type'] ?? 'image') === 'video'): ?>
                                <div class="flex h-full items-center justify-center text-xs text-slate-500">VIDEO</div>
                            <?php else: ?>
                                <img src="<?= e(upload_url((string) $slide['file_path'])); ?>" alt="" class="h-full w-full object-cover">
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="font-semibold"><?= e((string) ($slide['title'] ?? 'Untitled slide')); ?></div>
                            <div class="text-sm text-slate-500"><?= e((string) $slide['button_link']); ?></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <a class="text-sky-600" href="<?= e(app_url('admin/slides.php?id=' . (int) $slide['id'])); ?>">Edit</a>
                        <form action="" method="post" onsubmit="return confirm('Delete this slide?')">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="delete_id" value="<?= (int) $slide['id']; ?>">
                            <button class="text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
