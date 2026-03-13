<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$reviews = (new ReviewModel())->all();
$adminPageTitle = 'Manage Reviews';
require __DIR__ . '/partials/header.php';
?>
<div class="rounded-3xl bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">Reviews</h1>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th class="pb-3">Product</th><th class="pb-3">User</th><th class="pb-3">Rating</th><th class="pb-3">Comment</th></tr></thead>
            <tbody>
            <?php foreach ($reviews as $review): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold"><?= e($review['product_name']); ?></td>
                    <td class="py-3"><?= e($review['user_name']); ?></td>
                    <td class="py-3"><?= (int) $review['rating']; ?>/5</td>
                    <td class="py-3"><?= e((string) $review['comment']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

