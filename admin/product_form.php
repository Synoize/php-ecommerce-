<?php
require_once __DIR__ . '/../includes/header.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

$cats = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        setFlash('danger', 'Product not found.');
        redirect('/admin/manage_products.php');
    }
}

$errors = [];

if (isPost()) {
    $name = trim((string)($_POST['name'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $image = trim((string)($_POST['image'] ?? ''));

    if ($name === '') $errors[] = 'Name is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($stock < 0) $errors[] = 'Stock cannot be negative.';

    // Optional upload to /assets/images/uploads/
    if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($_FILES['image_file']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed.';
        } else {
            $tmp = (string)$_FILES['image_file']['tmp_name'];
            $orig = (string)$_FILES['image_file']['name'];
            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Invalid image type. Use JPG/PNG/WEBP.';
            } else {
                $newName = 'p_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = __DIR__ . '/../assets/images/uploads/' . $newName;
                if (!move_uploaded_file($tmp, $dest)) {
                    $errors[] = 'Could not save uploaded image.';
                } else {
                    $image = $newName;
                }
            }
        }
    }

    if (count($errors) === 0) {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, category_id=?, price=?, stock=?, image=? WHERE id=?');
            $stmt->execute([$name, $description, $categoryId, $price, $stock, $image, $id]);
            setFlash('success', 'Product updated.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (name, description, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $description, $categoryId, $price, $stock, $image]);
            setFlash('success', 'Product added.');
        }
        redirect('/admin/manage_products.php');
    }
}
?>

<h1 class="text-xl font-semibold text-gray-900"><?php echo $id>0?'Edit Product':'Add Product'; ?></h1>

<?php if (count($errors) > 0): ?>
  <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
    <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="mt-5 rounded-2xl border bg-white p-6 shadow-soft">
  <form method="post" enctype="multipart/form-data" class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="name" value="<?php echo e($product['name'] ?? ''); ?>" required />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="category_id" required>
          <option value="0">Select</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo isset($product['category_id']) && (int)$product['category_id']===(int)$c['id']?'selected':''; ?>><?php echo e($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="description" rows="3"><?php echo e($product['description'] ?? ''); ?></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="price" type="number" step="0.01" value="<?php echo e($product['price'] ?? ''); ?>" required />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Stock</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="stock" type="number" value="<?php echo e($product['stock'] ?? ''); ?>" required />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Image URL or filename</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="image" value="<?php echo e($product['image'] ?? ''); ?>" placeholder="https://... OR local.jpg" />
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
        <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" type="file" name="image_file" accept="image/png,image/jpeg,image/webp" />
        <div class="mt-2 text-xs text-gray-600">If uploaded, it will be stored in /assets/images/uploads/</div>
      </div>
    </div>

    <div class="flex flex-wrap gap-2 pt-2">
      <button class="inline-flex items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover" type="submit">Save</button>
      <a class="inline-flex items-center justify-center rounded-lg border px-5 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50" href="<?php echo e(BASE_URL); ?>/admin/manage_products.php">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
