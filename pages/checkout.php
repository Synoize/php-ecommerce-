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
<main class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="font-display text-4xl font-bold">Checkout</h1>
    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr,380px]">
        <section class="space-y-8">
            <div class="rounded-[2rem] bg-white p-6 shadow-soft">
                <h2 class="font-display text-2xl font-bold">Select address</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <?php foreach ($data['addresses'] as $address): ?>
                        <label class="address-choice rounded-3xl border border-slate-200 p-4">
                            <input type="radio" name="address_id" value="<?= (int) $address['id']; ?>" form="checkout-form" <?= !empty($address['is_default']) ? 'checked' : ''; ?>>
                            <div class="mt-3">
                                <div class="font-semibold"><?= e($address['full_name']); ?></div>
                                <div class="mt-2 text-sm text-slate-500"><?= e($address['address_line']); ?>, <?= e($address['city']); ?>, <?= e($address['state']); ?> - <?= e($address['pincode']); ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-6 shadow-soft">
                <h2 class="font-display text-2xl font-bold">Add new address</h2>
                <form action="" method="post" class="mt-6 grid gap-4 md:grid-cols-2">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="full_name" placeholder="Full name" required>
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="phone" placeholder="Phone" required>
                    <textarea class="rounded-2xl border border-slate-200 px-4 py-3 md:col-span-2" name="address_line" rows="3" placeholder="Address line" required></textarea>
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="city" placeholder="City" required>
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="state" placeholder="State" required>
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="pincode" placeholder="Pincode" required>
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="country" value="India" placeholder="Country">
                    <label class="md:col-span-2 flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" value="1"> Set as default</label>
                    <button class="md:col-span-2 rounded-full bg-brand-600 px-6 py-3 font-semibold text-white" type="submit" name="save_address">Save address</button>
                </form>
            </div>
        </section>
        <aside class="rounded-[2rem] bg-white p-6 shadow-soft">
            <h2 class="font-display text-2xl font-bold">Order summary</h2>
            <div class="mt-6 space-y-4">
                <?php foreach ($data['items'] as $item): ?>
                    <div class="rounded-2xl border border-slate-100 p-4 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div><?= e($item['name']); ?> x <?= (int) $item['quantity']; ?></div>
                            <div><?= e(money((float) $item['price'] * (int) $item['quantity'])); ?></div>
                        </div>
                        <?php if (!empty($item['box_name']) && (int) $item['box_quantity'] > 0): ?>
                            <div class="mt-2 flex items-center justify-between gap-3 text-slate-500">
                                <div><?= e((string) $item['box_name']); ?> x <?= (int) $item['box_quantity']; ?></div>
                                <div><?= e(money((float) ($item['box_price'] ?? 0) * (int) $item['box_quantity'])); ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 font-semibold text-slate-700">
                            <span>Line total</span>
                            <span><?= e(money((float) $item['line_total'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form action="<?= e(app_url('api/coupon.php')); ?>" method="post" class="mt-6 flex gap-2">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <input type="text" name="code" placeholder="Coupon code" class="flex-1 rounded-full border border-slate-200 px-4 py-3">
                <button class="rounded-full bg-slate-100 px-4 py-3 text-sm font-semibold" type="submit">Apply</button>
            </form>

            <div class="mt-6 space-y-3 text-sm">
                <div class="flex items-center justify-between"><span>Subtotal</span><span><?= e(money($data['subtotal'])); ?></span></div>
                <div class="flex items-center justify-between"><span>Discount</span><span>-<?= e(money($data['discount'])); ?></span></div>
                <div class="border-t border-slate-100 pt-3 text-base font-semibold">
                    <div class="flex items-center justify-between"><span>Total</span><span><?= e(money($data['total'])); ?></span></div>
                </div>
            </div>

            <form id="checkout-form" action="<?= e(app_url('api/checkout.php')); ?>" method="post" class="mt-6 space-y-4">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <div>
                    <label class="mb-2 block text-sm font-semibold">Payment method</label>
                    <select name="payment_method" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                        <option value="cod">Cash on delivery</option>
                        <option value="razorpay">Razorpay</option>
                    </select>
                </div>
                <button class="w-full rounded-full bg-brand-600 px-6 py-3 font-semibold text-white" type="submit">Place order</button>
            </form>
            <p class="mt-4 text-xs text-slate-500">Add your live or test Razorpay keys in `config/app.php` to activate payment order creation and verification endpoints.</p>
        </aside>
    </div>
</main>
<?php if (RAZORPAY_KEY_ID !== ''): ?>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('checkout-form')?.addEventListener('submit', async (event) => {
            const form = event.currentTarget;
            if (form.payment_method.value !== 'razorpay') {
                return;
            }

            event.preventDefault();

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });

            const payload = await response.json();
            if (!payload.ok) {
                alert(payload.message || 'Unable to create payment order.');
                return;
            }

            const razorpay = new Razorpay({
                key: payload.key,
                amount: payload.razorpay_order.amount,
                currency: payload.razorpay_order.currency,
                name: 'Watch Ecommerce',
                order_id: payload.razorpay_order.id,
                handler: async function (paymentResponse) {
                    const verifyForm = new FormData();
                    verifyForm.append('_token', form.querySelector('input[name="_token"]').value);
                    verifyForm.append('order_id', payload.order_id);
                    verifyForm.append('razorpay_order_id', paymentResponse.razorpay_order_id);
                    verifyForm.append('razorpay_payment_id', paymentResponse.razorpay_payment_id);
                    verifyForm.append('razorpay_signature', paymentResponse.razorpay_signature);

                    const verifyResponse = await fetch('<?= e(app_url('api/razorpay_verify.php')); ?>', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: verifyForm
                    });
                    const verifyPayload = await verifyResponse.json();
                    if (!verifyPayload.ok) {
                        alert(verifyPayload.message || 'Payment verification failed.');
                        return;
                    }

                    window.location.href = '<?= e(app_url('user/orders.php?order_id=')); ?>' + payload.order_id;
                }
            });

            razorpay.open();
        });
    </script>
<?php endif; ?>
<?php require __DIR__ . '/layout/footer.php'; ?>
