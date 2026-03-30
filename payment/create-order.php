<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/payment-functions.php';
require_login();
verify_csrf();

$addressId = (int) ($_POST['address_id'] ?? 0);
$paymentMethod = (string) ($_POST['payment_method'] ?? 'cod');
$address = (new AddressModel())->findOwned((int) current_user()['id'], $addressId);

if (!$address) {
    set_flash('error', 'Please select a valid address.');
    redirect('checkout.php');
}

if (trim((string) current_user()['phone']) === '') {
    set_flash('error', 'Please add a mobile number to your account before payment.');
    redirect('checkout.php');
}

try {
    $checkout = new CheckoutController();
    $pending = $checkout->beginPay0Order((int) current_user()['id'], $addressId, $paymentMethod);

    $response = pay0_create_order([
        'customer_mobile' => $pending['customer_mobile'],
        'customer_name' => $pending['customer_name'],
        'amount' => $pending['collect_amount'],
        'order_id' => $pending['pay0_order_id'],
        'redirect_url' => app_url('payment/payment-success.php?order_id=' . urlencode($pending['pay0_order_id'])),
    ]);

    $paymentUrl = pay0_extract_payment_url($response);
} catch (Throwable $e) {
    if (!empty($pending['pay0_order_id'])) {
        (new CheckoutController())->handlePay0Failure((string) $pending['pay0_order_id']);
    }

    set_flash('error', $e->getMessage());
    redirect('checkout.php');
}

$pageTitle = 'Processing Payment';
require __DIR__ . '/../pages/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-xl px-4 py-12">
    <div class="rounded-2xl border bg-white p-8 text-center shadow-soft">
        <div class="mx-auto h-14 w-14 animate-spin rounded-full border-4 border-slate-200 border-t-slate-900"></div>
        <h1 class="mt-6 text-2xl font-semibold text-slate-900">Processing Payment...</h1>
        <p class="mt-3 text-sm text-slate-600">Your Pay0 order is ready. You are being redirected to the payment page.</p>
        <div class="mt-6 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
            Order ID: <?= e((string) $pending['pay0_order_id']); ?><br>
            Amount: <?= e(money((float) $pending['collect_amount'])); ?>
        </div>
        <a href="<?= e($paymentUrl); ?>" class="mt-6 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Continue to Payment</a>
    </div>
</main>
<script>
    window.setTimeout(() => {
        window.location.href = <?= json_encode($paymentUrl); ?>;
    }, 1200);
</script>
<?php require __DIR__ . '/../pages/layout/footer.php'; ?>

