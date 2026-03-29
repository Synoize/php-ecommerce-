<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$homepageMediaModel = new HomepageMediaModel();
$errorMessage = null;

if (is_post()) {
    verify_csrf();

    try {
        if (isset($_POST['delete_review_id'])) {
            $homepageMediaModel->deleteUserReview((int) $_POST['delete_review_id']);
            set_flash('success', 'Review image deleted.');
            redirect('admin/homepage_media.php');
        }

        if (isset($_POST['delete_video_id'])) {
            $homepageMediaModel->deleteFeaturedProductVideo((int) $_POST['delete_video_id']);
            set_flash('success', 'Featured video deleted.');
            redirect('admin/homepage_media.php');
        }

        $action = trim((string) ($_POST['action'] ?? ''));

        if ($action === 'add_review') {
            $reviewPath = request_uploaded_image('review_image_upload', 'reviews');
            if ($reviewPath === null || trim($reviewPath) === '') {
                throw new RuntimeException('Please upload a review image.');
            }

            $homepageMediaModel->addUserReview($reviewPath);
            set_flash('success', 'Review image added.');
            redirect('admin/homepage_media.php');
        }

        if ($action === 'add_video') {
            $videoPath = request_uploaded_video('featured_video_upload', 'ugcs');
            if ($videoPath === null || trim($videoPath) === '') {
                throw new RuntimeException('Please upload a featured video.');
            }

            $homepageMediaModel->addFeaturedProductVideo($videoPath);
            set_flash('success', 'Featured video added.');
            redirect('admin/homepage_media.php');
        }

        throw new RuntimeException('Unsupported action.');
    } catch (Throwable $exception) {
        $errorMessage = $exception->getMessage();
    }
}

$reviewImages = $homepageMediaModel->allUserReviews();
$featuredVideos = $homepageMediaModel->allFeaturedProductVideos();
$adminPageTitle = 'Homepage Media';
require __DIR__ . '/partials/header.php';
?>
<div class="space-y-6">
    <?php if ($errorMessage): ?>
        <div class="rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($errorMessage); ?></div>
    <?php endif; ?>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-3xl bg-white p-6 shadow">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Review Images</h1>
                    <p class="mt-1 text-sm text-slate-500">These images power the homepage review carousel.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= count($reviewImages); ?> items</span>
            </div>

            <form action="" method="post" enctype="multipart/form-data" class="mt-6 space-y-4">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="action" value="add_review">

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Upload review image</label>
                    <input class="block w-full rounded-2xl border border-slate-200 px-4 py-3" type="file" name="review_image_upload" accept="image/*" required>
                </div>

                <button class="w-full rounded-full bg-primary-medium px-5 py-3 font-semibold text-white-dark hover:bg-primary-medium/90" type="submit">Add review image</button>
            </form>

            <div class="mt-6 space-y-3">
                <?php foreach ($reviewImages as $review): ?>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <img src="<?= e(upload_url((string) $review['file_path'])); ?>" alt="Review" class="h-16 w-16 rounded-2xl object-cover">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900"><?= e((string) $review['file_path']); ?></div>
                                <div class="text-xs text-slate-500"><?= e((string) $review['created_at']); ?></div>
                            </div>
                        </div>
                        <form action="" method="post" onsubmit="return confirm('Delete this review image?')">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="delete_review_id" value="<?= (int) $review['id']; ?>">
                            <button class="text-sm font-semibold text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php if ($reviewImages === []): ?>
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">No review images added yet.</div>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Featured Videos</h2>
                    <p class="mt-1 text-sm text-slate-500">These videos power the homepage featured products carousel.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= count($featuredVideos); ?> items</span>
            </div>

            <form action="" method="post" enctype="multipart/form-data" class="mt-6 space-y-4">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <input type="hidden" name="action" value="add_video">

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Upload featured video</label>
                    <input class="block w-full rounded-2xl border border-slate-200 px-4 py-3" type="file" name="featured_video_upload" accept="video/mp4,video/webm,video/quicktime" required>
                </div>

                <button class="w-full rounded-full bg-primary-medium px-5 py-3 font-semibold text-white-dark hover:bg-primary-medium/90" type="submit">Add featured video</button>
            </form>

            <div class="mt-6 space-y-3">
                <?php foreach ($featuredVideos as $video): ?>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-xs font-semibold text-slate-500">VIDEO</div>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900"><?= e((string) $video['file_path']); ?></div>
                                <div class="text-xs text-slate-500"><?= e((string) $video['created_at']); ?></div>
                            </div>
                        </div>
                        <form action="" method="post" onsubmit="return confirm('Delete this featured video?')">
                            <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="delete_video_id" value="<?= (int) $video['id']; ?>">
                            <button class="text-sm font-semibold text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php if ($featuredVideos === []): ?>
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">No featured videos added yet.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

