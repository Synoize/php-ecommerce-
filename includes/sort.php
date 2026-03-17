<form action="<?= e(app_url('shop.php')); ?>" method="get" class="w-full sm:w-64">

    <!-- KEEP FILTERS -->
    <input type="hidden" name="q" value="<?= e($filters['query']); ?>">
    <input type="hidden" name="min_price" value="<?= e((string)$minPrice); ?>">
    <input type="hidden" name="max_price" value="<?= e((string)$maxPrice); ?>">

    <?php foreach ($selectedCategoryIds as $categoryId): ?>
        <input type="hidden" name="categories[]" value="<?= (int)$categoryId; ?>">
    <?php endforeach; ?>

    <!-- SELECT WRAPPER -->
    <div class="relative">

        <select
            name="sort"
            onchange="this.form.submit()"
            class="w-full appearance-none border px-4 py-2 text-sm text-black-light pr-10 focus:outline-none focus:border-black-medium">

            <?php
            $options = [
                '' => 'Sort by latest',
                'rating' => 'Average rating',
                'price_asc' => 'Price: low to high',
                'price_desc' => 'Price: high to low',
            ];
            ?>

            <?php foreach ($options as $value => $label): ?>
                <option
                    value="<?= e($value); ?>"
                    <?= $filters['sort'] === $value ? 'selected' : ''; ?>>
                    <?= e($label); ?>
                </option>
            <?php endforeach; ?>

        </select>

        <!-- CUSTOM ARROW -->
        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-600">
            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
        </div>

    </div>

</form>