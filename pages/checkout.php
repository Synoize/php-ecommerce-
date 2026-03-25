<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$checkout = new CheckoutController();
$addressModel = new AddressModel();

if (is_post() && isset($_POST['save_address'])) {
    verify_csrf();
    $addressModel->save((int) current_user()['id'], $_POST);
    set_flash('success', 'Address added.');
    redirect('checkout.php');
}

$data = $checkout->checkoutData((int) current_user()['id']);
$pageTitle = 'Checkout';
require __DIR__ . '/layout/header.php';
?>
<main class="mt-28 mx-auto max-w-7xl px-4 py-8">
    <h1 class="mb-8 text-2xl font-bold text-black-medium md:text-3xl">Checkout</h1>

    <div class="flex flex-col-reverse gap-8 lg:grid lg:grid-cols-[1fr,380px]">
        <section class="space-y-6">
            <div class="rounded-xl border bg-white-light/10 p-5">
                <h2 class="text-lg font-semibold text-black-medium">Select Delivery Address</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php foreach ($data['addresses'] as $address): ?>
                        <label class="relative cursor-pointer rounded-xl border bg-white-dark p-4 transition hover:border-primary-medium">
                            <input type="radio" name="address_id" value="<?= (int) $address['id']; ?>" form="checkout-form" class="peer hidden outline-none" <?= !empty($address['is_default']) ? 'checked' : ''; ?>>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-semibold"><?= e($address['full_name']); ?></span>
                                    <?php if (!empty($address['is_default'])): ?>
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-600">Default</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-black-light"><?= e($address['address_line']); ?>, <?= e($address['city']); ?>, <?= e($address['state']); ?> - <?= e($address['pincode']); ?></p>
                            </div>
                            <div class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-primary-medium"></div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-xl border bg-white-light/10 p-5">
                <h2 class="text-lg font-semibold text-black-medium">Add New Address</h2>
                <form action="" method="post" class="mt-4 grid gap-3 text-sm text-black-medium md:grid-cols-2">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                    <input class="input rounded-lg border p-3 outline-none focus:border-white-medium" type="text" name="full_name" placeholder="Full Name" required>
                    <input class="input rounded-lg border p-3 outline-none focus:border-white-medium" type="text" name="phone" placeholder="Phone Number" required>
                    <textarea class="input rounded-lg border p-3 outline-none focus:border-white-medium md:col-span-2" name="address_line" rows="3" placeholder="Full Address" required></textarea>
                    <input class="input rounded-lg border p-3 outline-none focus:border-white-medium" type="text" name="city" placeholder="City" required>
                    <input class="input rounded-lg border p-3 outline-none focus:border-white-medium" type="text" name="state" placeholder="State" required>
                    <input class="input rounded-lg border p-3 outline-none focus:border-white-medium" type="text" name="pincode" placeholder="Pincode" required>
                    <input class="input rounded-lg border p-3 outline-none focus:border-white-medium" type="text" name="country" value="India">
                    <label class="flex items-center gap-2 text-sm md:col-span-2"><input type="checkbox" name="is_default" value="1"> Set as default</label>
                    <button class="rounded-lg bg-primary-medium py-3 text-sm font-semibold text-white-dark transition hover:bg-primary-medium/90 md:col-span-2" type="submit" name="save_address">Save Address</button>
                </form>
            </div>
        </section>

        <aside class="md:sticky md:top-28 h-fit rounded-xl border bg-white-light/10 p-5">
            <h2 class="text-lg font-semibold text-black-medium">Order Summary</h2>

            <div class="mt-4 max-h-[300px] space-y-3 overflow-y-auto pr-1">
                <?php foreach ($data['items'] as $item): ?>
                    <div class="flex justify-between border-b pb-2 text-sm">
                        <div>
                            <div class="font-medium"><?= e($item['name']); ?></div>
                            <div class="text-xs text-black-light">Qty: <?= (int) $item['quantity']; ?></div>
                            <?php if (!empty($item['box_name']) && (int) $item['box_quantity'] > 0): ?>
                                <div class="text-xs text-black-light"><?= e($item['box_name']); ?> x <?= (int) $item['box_quantity']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="font-semibold"><?= e(money((float) $item['line_total'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form action="<?= e(app_url('api/coupon.php')); ?>" method="post" class="mt-4 flex gap-2">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <input type="text" name="code" placeholder="Apply coupon" class="flex-1 rounded-lg border px-3 py-2 text-sm">
                <button class="rounded-lg bg-primary-medium px-4 text-sm text-white-dark hover:bg-primary-medium/90">Apply</button>
            </form>

            <div class="mt-5 space-y-2 text-sm text-black-light">
                <div class="flex justify-between"><span class="font-medium text-black-medium">Subtotal</span><span><?= e(money($data['subtotal'])); ?></span></div>
                <div class="flex justify-between text-green-600"><span class="font-medium">Discount</span><span>-<?= e(money($data['discount'])); ?></span></div>
                <div class="flex justify-between border-t pt-3 text-lg font-semibold"><span class="text-black-medium">Total</span><span class="text-green-600"><?= e(money($data['total'])); ?></span></div>
            </div>

            <div id="payment-note" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                Cash on Delivery requires an online booking payment of <?= e(money((float) $data['cod_booking_amount'])); ?> via Pay0 first. The remaining amount is paid on delivery.
            </div>

            <form id="checkout-form" action="<?= e(app_url('payment/create-order.php')); ?>" method="post" class="mt-5 space-y-4">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">

                <select name="payment_method" id="payment-method" class="w-full rounded border px-3 py-2 text-sm">
                    <option value="cod">Cash on Delivery (<?= e(money((float) $data['cod_booking_amount'])); ?> booking payment via Pay0)</option>
                    <option value="pay0">Pay0 (full payment)</option>
                </select>

                <button id="checkout-button" class="w-full rounded-lg bg-primary-medium px-5 py-3 text-sm font-semibold text-white-dark transition hover:bg-primary-medium/90">
                    Continue to Pay0
                </button>
            </form>
        </aside>
    </div>
</main>
<script>
    const paymentMethod = document.getElementById('payment-method');
    const paymentNote = document.getElementById('payment-note');
    const checkoutButton = document.getElementById('checkout-button');
    const codBookingAmount = <?= json_encode((float) $data['cod_booking_amount']); ?>;
    const orderTotal = <?= json_encode((float) $data['total']); ?>;

    function formatINR(amount) {
        return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amount);
    }

    function syncPaymentCopy() {
        if (!paymentMethod || !paymentNote || !checkoutButton) {
            return;
        }

        if (paymentMethod.value === 'cod') {
            paymentNote.textContent = 'Cash on Delivery requires an online booking payment of ' + formatINR(codBookingAmount) + ' via Pay0 first. The remaining amount is paid on delivery.';
            checkoutButton.textContent = 'Pay ' + formatINR(codBookingAmount) + ' via Pay0 & Book COD Order';
            return;
        }

        paymentNote.textContent = 'You will be redirected to Pay0 to complete the full order payment securely.';
        checkoutButton.textContent = 'Pay ' + formatINR(orderTotal) + ' via Pay0';
    }

    paymentMethod?.addEventListener('change', syncPaymentCopy);
    syncPaymentCopy();
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>
