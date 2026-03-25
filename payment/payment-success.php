<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/payment-functions.php';

$pay0OrderId = trim((string) ($_GET['order_id'] ?? $_POST['order_id'] ?? ''));

if ($pay0OrderId === '') {
    set_flash('error', 'Invalid payment reference.');
    redirect('checkout.php');
}

$orderModel = new OrderModel();
$order = $orderModel->findByPay0OrderId($pay0OrderId);
if (!$order) {
    set_flash('error', 'Order not found.');
    redirect('checkout.php');
}

$statusResponse = null;
$errorMessage = null;
$orderId = 0;
$finalState = 'pending';

try {
    $statusResponse = pay0_check_order_status($pay0OrderId);

    if (pay0_is_success($statusResponse)) {
        $txnId = pay0_extract_transaction_id($statusResponse);
        $orderId = (new CheckoutController())->handlePay0Success($pay0OrderId, $txnId);
        $finalState = 'success';
    } elseif (pay0_is_failed($statusResponse)) {
        (new CheckoutController())->handlePay0Failure($pay0OrderId);
        $finalState = 'failed';
        $errorMessage = 'Payment was not completed.';
    }
} catch (Throwable $e) {
    $errorMessage = $e->getMessage();
}

$pageTitle = 'Payment Status';
require __DIR__ . '/../pages/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-xl px-4 py-12">
    <div class="rounded-2xl border bg-white p-8 shadow-soft">
        <?php if ($finalState === 'success'): ?>
            <div class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700 inline-block">Payment Success</div>
            <h1 class="mt-4 text-2xl font-semibold text-slate-900">Order confirmed</h1>
            <p class="mt-3 text-sm text-slate-600">Your payment was verified with Pay0 and the order has been updated successfully.</p>
            <a href="<?= e(app_url('user/orders.php?order_id=' . $orderId)); ?>" class="mt-6 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white">View Order</a>
        <?php elseif ($finalState === 'failed'): ?>
            <div class="rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 inline-block">Payment Failed</div>
            <h1 class="mt-4 text-2xl font-semibold text-slate-900">Order not confirmed</h1>
            <p class="mt-3 text-sm text-slate-600"><?= e($errorMessage ?? 'Payment failed.'); ?></p>
            <a href="<?= e(app_url('checkout.php')); ?>" class="mt-6 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Try Again</a>
        <?php else: ?>
            <div class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700 inline-block">Processing</div>
            <h1 class="mt-4 text-2xl font-semibold text-slate-900">Payment status is being checked</h1>
            <p class="mt-3 text-sm text-slate-600">We could not confirm the final status yet. Please check again from your orders page in a moment.</p>
            <?php if ($errorMessage): ?>
                <div class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= e($errorMessage); ?></div>
            <?php endif; ?>
            <a href="<?= e(app_url('user/orders.php')); ?>" class="mt-6 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Go to Orders</a>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../pages/layout/footer.php'; ?>
