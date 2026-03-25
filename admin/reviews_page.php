<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$reviewModel = new ReviewModel();
if (is_post()) {
    verify_csrf();
    $reviewModel->delete((int) ($_POST['review_id'] ?? 0));
    set_flash('success', 'Review deleted.');
    redirect('admin/reviews_page.php');
}

$reviews = $reviewModel->all();
$adminPageTitle = 'Manage Reviews';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Reviews</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Product</th><th class="pb-3">User</th><th class="pb-3">Rating</th><th class="pb-3">Comment</th><th class="pb-3">Created</th><th class="pb-3">Action</th></tr></thead>
            <tbody>
            <?php foreach ($reviews as $review): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><?= e($review['product_name']); ?></td>
                    <td class="py-3"><?= e($review['user_name']); ?></td>
                    <td class="py-3"><?= (int) $review['rating']; ?>/5</td>
                    <td class="py-3 max-w-md text-slate-600"><?= e((string) $review['comment']); ?></td>
                    <td class="py-3"><?= e((string) $review['created_at']); ?></td>
                    <td class="py-3">
                        <form action="" method="post" onsubmit="return confirm('Delete this review?')">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="review_id" value="<?= (int) $review['id']; ?>">
                            <button class="text-rose-600" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
