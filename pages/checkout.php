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

    <!-- HEADER -->
    <h1 class="text-2xl md:text-3xl font-bold text-black-medium mb-8">Checkout</h1>

    <div class="flex flex-col-reverse lg:grid lg:grid-cols-[1fr,380px] gap-8">

        <!-- LEFT SIDE -->
        <section class="space-y-6">

            <!-- ADDRESS -->
            <div class="rounded-xl border bg-white-light/10 p-5">

                <h2 class="text-lg font-semibold text-black-medium">Select Delivery Address</h2>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php foreach ($data['addresses'] as $address): ?>
                        <label class="relative cursor-pointer rounded-xl bg-white-dark border p-4 hover:border-primary-medium transition">

                            <input type="radio"
                                name="address_id"
                                value="<?= (int) $address['id']; ?>"
                                form="checkout-form"
                                class="peer hidden outline-none"
                                <?= !empty($address['is_default']) ? 'checked' : ''; ?>>

                            <div class="space-y-2">

                                <div class="flex justify-between">
                                    <span class="font-semibold text-sm"><?= e($address['full_name']); ?></span>

                                    <?php if (!empty($address['is_default'])): ?>
                                        <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">
                                            Default
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-xs text-black-light">
                                    <?= e($address['address_line']); ?>,
                                    <?= e($address['city']); ?>,
                                    <?= e($address['state']); ?> -
                                    <?= e($address['pincode']); ?>
                                </p>

                            </div>

                            <!-- selected border -->
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-primary-medium rounded-xl"></div>

                        </label>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- ADD ADDRESS -->
            <div class="rounded-xl border bg-white-light/10 p-5">

                <h2 class="text-lg font-semibold text-black-medium">Add New Address</h2>

                <form action="" method="post" class="mt-4 grid gap-3 md:grid-cols-2 text-sm text-black-medium">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">

                    <input class="input border p-3 rounded-lg focus:border-white-medium outline-none" type="text" name="full_name" placeholder="Full Name" required>
                    <input class="input border p-3 rounded-lg focus:border-white-medium outline-none" type="text" name="phone" placeholder="Phone Number" required>

                    <textarea class="input border p-3 rounded-lg focus:border-white-medium outline-none md:col-span-2" name="address_line" rows="3" placeholder="Full Address" required></textarea>

                    <input class="input border p-3 rounded-lg focus:border-white-medium outline-none" type="text" name="city" placeholder="City" required>
                    <input class="input border p-3 rounded-lg focus:border-white-medium outline-none" type="text" name="state" placeholder="State" required>

                    <input class="input border p-3 rounded-lg focus:border-white-medium outline-none" type="text" name="pincode" placeholder="Pincode" required>
                    <input class="input border p-3 rounded-lg focus:border-white-medium outline-none" type="text" name="country" value="India">

                    <label class="md:col-span-2 flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_default" value="1">
                        Set as default
                    </label>

                    <button class="md:col-span-2 rounded-lg bg-primary-medium py-3 text-sm text-white-dark font-semibold hover:bg-primary-medium/90 transition"
                        type="submit" name="save_address">
                        Save Address
                    </button>
                </form>

            </div>

        </section>

        <!-- RIGHT SIDE -->
        <aside class="md:sticky top-28 h-fit rounded-xl border bg-white-light/10 p-5">

            <h2 class="text-lg font-semibold text-black-medium">Order Summary</h2>

            <!-- ITEMS -->
            <div class="mt-4 space-y-3 max-h-[300px] overflow-y-auto pr-1">

                <?php foreach ($data['items'] as $item): ?>
                    <div class="flex justify-between text-sm border-b pb-2">

                        <div>
                            <div class="font-medium"><?= e($item['name']); ?></div>
                            <div class="text-xs text-black-light">
                                Qty: <?= (int) $item['quantity']; ?>
                            </div>

                            <?php if (!empty($item['box_name']) && (int) $item['box_quantity'] > 0): ?>
                                <div class="text-xs text-black-light">
                                    <?= e($item['box_name']); ?> × <?= (int) $item['box_quantity']; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="font-semibold">
                            <?= e(money((float) $item['line_total'])); ?>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

            <!-- COUPON -->
            <form action="<?= e(app_url('api/coupon.php')); ?>" method="post" class="mt-4 flex gap-2">
                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">
                <input type="text" name="code" placeholder="Apply coupon"
                    class="flex-1 rounded-lg border px-3 py-2 text-sm">
                <button class="rounded-lg bg-primary-medium hover:bg-primary-medium/90 text-white-dark px-4 text-sm">Apply</button>
            </form>

            <!-- TOTAL -->
            <div class="mt-5 space-y-2 text-sm text-black-light">

                <div class="flex justify-between">
                    <span class="text-black-medium font-medium">Subtotal</span>
                    <span><?= e(money($data['subtotal'])); ?></span>
                </div>

                <div class="flex justify-between text-green-600">
                    <span class="font-medium">Discount</span>
                    <span>-<?= e(money($data['discount'])); ?></span>
                </div>

                <div class="border-t pt-3 flex justify-between font-semibold text-lg">
                    <span class="text-black-medium">Total</span>
                    <span class="text-green-600"><?= e(money($data['total'])); ?></span>
                </div>

            </div>

            <!-- PAYMENT -->
            <form id="checkout-form"
                action="<?= e(app_url('api/checkout.php')); ?>"
                method="post"
                class="mt-5 space-y-4">

                <input type="hidden" name="_token" value="<?= e(csrf_token()); ?>">

                <select name="payment_method"
                    class="w-full rounded border px-3 py-2 text-sm">
                    <option value="cod">Cash on Delivery</option>
                    <option value="razorpay">Razorpay</option>
                </select>

                <button class="w-full rounded-lg bg-primary-medium px-5 py-3 text-sm font-semibold text-white-dark hover:bg-primary-medium/90 transition">
                    Place Order
                </button>

            </form>

        </aside>

    </div>
</main>

<?php if (RAZORPAY_KEY_ID !== ''): ?>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('checkout-form')?.addEventListener('submit', async (event) => {
            const form = event.currentTarget;

            if (form.payment_method.value !== 'razorpay') return;

            event.preventDefault();

            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });

            const data = await res.json();

            if (!data.ok) {
                alert(data.message || 'Payment failed');
                return;
            }

            const rzp = new Razorpay({
                key: data.key,
                amount: data.razorpay_order.amount,
                currency: data.razorpay_order.currency,
                name: '<?= e(APP_NAME); ?>',
                order_id: data.razorpay_order.id,
                handler: async function(res) {

                    const verify = new FormData();
                    verify.append('_token', form.querySelector('[name="_token"]').value);
                    verify.append('order_id', data.order_id);
                    verify.append('razorpay_order_id', res.razorpay_order_id);
                    verify.append('razorpay_payment_id', res.razorpay_payment_id);
                    verify.append('razorpay_signature', res.razorpay_signature);

                    const verifyRes = await fetch('<?= e(app_url('api/razorpay_verify.php')); ?>', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: verify
                    });

                    const verifyData = await verifyRes.json();

                    if (!verifyData.ok) {
                        alert('Verification failed');
                        return;
                    }

                    window.location.href = '<?= e(app_url('user/orders.php?order_id=')); ?>' + data.order_id;
                }
            });

            rzp.open();
        });
    </script>
<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>