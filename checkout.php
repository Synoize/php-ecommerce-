<?php
require_once __DIR__ . '/includes/config.php';
requireLogin();
require_once __DIR__ . '/includes/razorpay.php';

$totals = cartTotals($pdo);
if (count($totals['items']) === 0) {
    setFlash('danger', 'Your cart is empty.');
    redirect('/cart.php');
}

$errors = [];

if (isPost() && isset($_POST['place_order'])) {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $zip = trim((string)($_POST['zip'] ?? ''));

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($address === '') $errors[] = 'Address is required.';

    if (count($errors) === 0) {
        try {
            $pdo->beginTransaction();

            $userId = (int)($_SESSION['user']['id'] ?? 0);
            if ($userId <= 0) {
                throw new RuntimeException('User session missing. Please login again.');
            }

            $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, ?, NOW())');
            $orderStmt->execute([$userId, $totals['total'], 'pending']);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');

            foreach ($totals['items'] as $it) {
                $p = $it['product'];
                $pid = (int)$p['id'];
                $qty = (int)$it['quantity'];
                $price = (float)$p['price'];

                $itemStmt->execute([$orderId, $pid, $qty, $price]);

                $stockStmt->execute([$qty, $pid, $qty]);
                if ($stockStmt->rowCount() !== 1) {
                    throw new RuntimeException('Insufficient stock for: ' . (string)$p['name']);
                }
            }

            $pdo->commit();
            cartClear();
            setFlash('success', 'Order placed successfully.');
            redirect('/user/orders.php');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Checkout error: ' . $e->getMessage());
            $errors[] = 'Order failed: ' . $e->getMessage();
        }
    }
}

$user = currentUser();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="keywords" content="watches, ecommerce, online store, luxury watches, shopping" />
  <title>Checkout - Scipwt Ecommerce Platform</title>
  <link rel="icon" href="<?php echo e(asset('images/logo/favicon.svg')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="<?php echo e(BASE_URL); ?>/tailwind.config.js"></script>
</head>

<body>
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <main class="mt-28 mb-12 mx-auto max-w-7xl px-4">
    <h1 class="text-xl font-semibold text-gray-900">Checkout</h1>

    <?php if (count($errors) > 0): ?>
      <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <?php foreach ($errors as $err): ?>
          <div><?php echo e($err); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="mt-5 grid gap-4 lg:grid-cols-12">
      <div class="lg:col-span-7">
        <div class="rounded-2xl border bg-white p-5 shadow-soft">
          <h2 class="text-base font-semibold text-gray-900">Billing / Shipping</h2>
          <form method="post" novalidate class="mt-4">
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="name" required value="<?php echo e($user['name'] ?? ''); ?>" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="email" type="email" required value="<?php echo e($user['email'] ?? ''); ?>" />
              </div>
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="address" required placeholder="House no, street, area" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">City</label>
                <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="city" placeholder="City" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">ZIP</label>
                <input class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" name="zip" placeholder="ZIP" />
              </div>
            </div>

            <button class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white hover:bg-brand-hover" type="submit" name="place_order">Confirm Order (Cash/Mock Payment)</button>
          </form>

          <div class="my-5 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200"></div>
            <div class="text-xs font-semibold text-gray-500">OR</div>
            <div class="h-px flex-1 bg-gray-200"></div>
          </div>

          <button class="inline-flex w-full items-center justify-center rounded-lg border px-5 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50" type="button" id="rzpPayBtn">Pay Online (Razorpay)</button>

          <form id="rzpVerifyForm" method="post" action="<?php echo e(BASE_URL); ?>/payments/verify_razorpay_payment.php" class="hidden">
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" />
            <input type="hidden" name="razorpay_signature" id="razorpay_signature" />
          </form>
        </div>
      </div>

      <div class="lg:col-span-5">
        <div class="rounded-2xl border bg-white p-5 shadow-soft">
          <h2 class="text-base font-semibold text-gray-900">Order Summary</h2>
          <div class="mt-4 space-y-2 text-sm">
            <?php foreach ($totals['items'] as $it): $p = $it['product']; ?>
              <div class="flex justify-between text-gray-700">
                <div class="text-gray-600"><?php echo e($p['name']); ?> × <?php echo (int)$it['quantity']; ?></div>
                <div class="font-medium">₹<?php echo e(number_format((float)$it['line_total'], 2)); ?></div>
              </div>
            <?php endforeach; ?>
            <div class="border-t pt-3 space-y-2">
              <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹<?php echo e(number_format((float)$totals['subtotal'], 2)); ?></span></div>
              <div class="flex justify-between text-gray-600"><span>Discount</span><span>- ₹<?php echo e(number_format((float)$totals['discount'], 2)); ?></span></div>
              <div class="flex justify-between font-semibold text-gray-900"><span>Total</span><span>₹<?php echo e(number_format((float)$totals['total'], 2)); ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    (function(){
      const payBtn = document.getElementById('rzpPayBtn');
      if (!payBtn) return;

      let isPaying = false;

      payBtn.addEventListener('click', async function(){
        if (isPaying) return;
        isPaying = true;
        payBtn.disabled = true;
        payBtn.innerText = 'Processing...';

        try {
          const res = await fetch('<?php echo e(BASE_URL); ?>/payments/create_razorpay_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
          });

          const data = await res.json();
          if (!data.success) {
            throw new Error(data.message || 'Could not start Razorpay payment.');
          }

          const options = {
            key: data.key_id,
            amount: data.amount,
            currency: data.currency,
            name: data.name,
            order_id: data.razorpay_order_id,
            prefill: data.prefill || {},
            handler: function (response){
              document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
              document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
              document.getElementById('razorpay_signature').value = response.razorpay_signature;
              document.getElementById('rzpVerifyForm').submit();
            },
            modal: {
              ondismiss: function(){
                isPaying = false;
                payBtn.disabled = false;
                payBtn.innerText = 'Pay Online (Razorpay)';
              }
            }
          };

          const rzp = new Razorpay(options);
          rzp.on('payment.failed', function(){
            isPaying = false;
            payBtn.disabled = false;
            payBtn.innerText = 'Pay Online (Razorpay)';
          });
          rzp.open();
        } catch (e) {
          alert(e.message || 'Payment initialization failed.');
          isPaying = false;
          payBtn.disabled = false;
          payBtn.innerText = 'Pay Online (Razorpay)';
        }
      });
    })();
    lucide.createIcons();
  </script>
</body>

</html>
