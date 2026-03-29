<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set(APP_TIMEZONE);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BoxOptionModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/WishlistModel.php';
require_once __DIR__ . '/../models/AddressModel.php';
require_once __DIR__ . '/../models/CouponModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/SlideModel.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/StoreController.php';
require_once __DIR__ . '/../controllers/CheckoutController.php';
require_once __DIR__ . '/../controllers/AdminController.php';

function app_url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function e(null|string|int|float $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function asset_url(string $path): string
{
    return app_url('assets/' . ltrim($path, '/'));
}

function upload_url(?string $path): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return asset_url('images/logo/logo.png');
    }

    if (preg_match('/^https?:\/\//i', $path) === 1) {
        return $path;
    }

    return asset_url(ltrim($path, '/'));
}

function upload_storage_path(string $path = ''): string
{
    $base = dirname(__DIR__) . '/assets/';
    return $path === '' ? $base : $base . ltrim($path, '/');
}

function ensure_upload_directory(string $directory): void
{
    if (is_dir($directory)) {
        return;
    }

    if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create upload directory.');
    }
}

function normalize_uploaded_file_name(string $name): string
{
    $name = strtolower(pathinfo($name, PATHINFO_FILENAME));
    $name = preg_replace('/[^a-z0-9]+/', '-', $name) ?? '';
    return trim($name, '-') ?: 'image';
}

function store_uploaded_image(array $file, string $targetDirectory): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }

    if (!is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new RuntimeException('Invalid uploaded file.');
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new RuntimeException('Only valid image files are allowed.');
    }

    $mime = strtolower((string) ($imageInfo['mime'] ?? ''));
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Allowed image types: JPG, PNG, GIF, WEBP.');
    }

    $relativeDirectory = 'images/uploads/' . trim($targetDirectory, '/');
    $absoluteDirectory = upload_storage_path($relativeDirectory);
    ensure_upload_directory($absoluteDirectory);

    $fileName = normalize_uploaded_file_name((string) ($file['name'] ?? 'upload'))
        . '-' . bin2hex(random_bytes(6))
        . '.' . $extensions[$mime];
    $absolutePath = rtrim($absoluteDirectory, '/\\') . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        throw new RuntimeException('Unable to move uploaded image.');
    }

    return $relativeDirectory . '/' . $fileName;
}

function request_uploaded_image(string $field, string $targetDirectory, ?string $fallback = null): ?string
{
    $file = $_FILES[$field] ?? null;
    if (!is_array($file)) {
        return $fallback;
    }

    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        return $fallback;
    }

    return store_uploaded_image($file, $targetDirectory);
}

function request_uploaded_images(string $field, string $targetDirectory): array
{
    $files = $_FILES[$field] ?? null;
    if (!is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
        return [];
    }

    $uploads = [];
    $count = count($files['name']);

    for ($index = 0; $index < $count; $index++) {
        $error = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $uploads[] = store_uploaded_image([
            'name' => $files['name'][$index] ?? '',
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $error,
            'size' => $files['size'][$index] ?? 0,
        ], $targetDirectory);
    }

    return $uploads;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    if (!hash_equals($_SESSION['csrf_token'] ?? '', (string) ($_POST['_token'] ?? ''))) {
        http_response_code(419);
        exit('Invalid CSRF token');
    }
}

function is_post(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please sign in to continue.');
        redirect('user/login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        set_flash('error', 'Admin access required.');
        redirect('admin/admin_login.php');
    }
}

function money(float $amount): string
{
    return 'Rs. ' . number_format($amount, 2);
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-') ?: 'item';
}

function product_link(array $product): string
{
    return app_url('product/' . (int) $product['id'] . '/' . slugify((string) $product['name']));
}

function order_status_badge(string $status): string
{
    return match ($status) {
        'pending' => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-sky-100 text-sky-700',
        'shipped' => 'bg-violet-100 text-violet-700',
        'delivered' => 'bg-emerald-100 text-emerald-700',
        'cancelled', 'failed' => 'bg-rose-100 text-rose-700',
        'paid' => 'bg-emerald-100 text-emerald-700',
        default => 'bg-slate-100 text-slate-700',
    };
}

function order_tracking_steps(string $status): array
{
    $steps = [
        ['key' => 'pending', 'label' => 'Order placed'],
        ['key' => 'confirmed', 'label' => 'Confirmed'],
        ['key' => 'shipped', 'label' => 'Shipped'],
        ['key' => 'delivered', 'label' => 'Delivered'],
    ];

    if ($status === 'cancelled') {
        return array_map(
            static fn(array $step): array => $step + ['complete' => false, 'current' => false],
            $steps
        );
    }

    $order = ['pending' => 0, 'confirmed' => 1, 'shipped' => 2, 'delivered' => 3];
    $currentIndex = $order[$status] ?? 0;

    return array_map(
        static fn(array $step, int $index): array => $step + [
            'complete' => $index <= $currentIndex,
            'current' => $index === $currentIndex,
        ],
        $steps,
        array_keys($steps)
    );
}

function wants_json(): bool
{
    return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
        || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}
