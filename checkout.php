<?php
require_once 'functions.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Checkout';

$user_obj = new User($db);
$balance = $user_obj->getWalletBalance($current_user_id);
$total = $cart->getCartTotal($current_user_id);
$items = $cart->getCartItems($current_user_id);

// If cart is empty, redirect to cart
if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $checkout_result = $cart->checkoutCart($current_user_id);
    if ($checkout_result['success']) {
        // Successful checkout
        $_SESSION['checkout_success'] = true;
        header('Location: checkout.php?success=1');
        exit;
    } else {
        $msg = $checkout_result['message'];
        $msg_type = 'danger';
        // Re-fetch items in case they changed
        $items = $cart->getCartItems($current_user_id);
        $total = $cart->getCartTotal($current_user_id);
    }
}

require_once 'header.php';
?>

<section class="container mt-5 mb-5" style="max-width:800px;">
    <?php if (isset($_GET['success'])): ?>
        <div style="background:#fff;padding:60px 40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);text-align:center;">
            <i class="fas fa-check-circle" style="font-size:72px;color:#2e7d32;margin-bottom:20px;"></i>
            <h2 class="font-baloo" style="font-weight:800;color:#333;margin-bottom:15px;">Payment Successful!</h2>
            <p class="font-rale text-muted" style="font-size:16px;">Your order has been placed and funds have been securely transferred to the seller(s).</p>
            <a href="index.php" class="btn color-primary-bg text-white font-baloo mt-4" style="border-radius:25px;padding:12px 30px;font-size:18px;font-weight:700;">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
            <h2 class="font-baloo" style="font-weight:800;color:#333;margin-bottom:20px;">
                Review Your Order
            </h2>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?php echo $msg_type; ?> font-rale" style="border-radius:8px;font-size:14px;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-7">
                    <h5 class="font-rubik" style="font-weight:600;color:#555;margin-bottom:15px;border-bottom:1px solid #eee;padding-bottom:10px;">Order Summary</h5>
                    <ul class="list-group mb-4" style="border-radius:8px;">
                        <?php foreach ($items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center font-rale" style="padding:15px;">
                                <div>
                                    <h6 style="margin:0;font-weight:600;"><?php echo htmlspecialchars($item['item_name']); ?></h6>
                                    <small class="text-muted">Sold by: <?php echo htmlspecialchars($item['seller_first_name'] . ' ' . $item['seller_last_name']); ?></small><br>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span style="font-weight:700;color:#E44C4C;">R<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-5">
                    <h5 class="font-rubik" style="font-weight:600;color:#555;margin-bottom:15px;border-bottom:1px solid #eee;padding-bottom:10px;">Payment Details</h5>
                    <div style="background:#f8f9fa;padding:20px;border-radius:12px;margin-bottom:20px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;" class="font-rale text-muted">
                            <span>Subtotal:</span>
                            <span>R<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;" class="font-rale text-muted">
                            <span>Shipping:</span>
                            <span style="color:#27ae60;">Free</span>
                        </div>
                        <hr>
                        <div style="display:flex;justify-content:space-between;font-size:20px;" class="font-baloo font-weight-bold">
                            <span>Total:</span>
                            <span style="color:#E44C4C;">R<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>

                    <div style="background:#e8f5e9;padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid #c8e6c9;">
                        <h6 class="font-rubik text-success" style="margin-bottom:5px;font-weight:600;"><i class="fas fa-wallet" style="margin-right:8px;"></i>Wallet Balance</h6>
                        <div class="font-baloo" style="font-size:24px;font-weight:800;color:#2e7d32;">
                            R<?php echo number_format($balance, 2); ?>
                        </div>
                    </div>

                    <?php if ($balance >= $total): ?>
                        <form action="checkout.php" method="POST">
                            <button type="submit" name="confirm_payment" value="1" class="btn btn-block color-primary-bg text-white font-baloo" 
                                    style="border-radius:25px;padding:15px;font-size:18px;font-weight:700;">
                                <i class="fas fa-lock" style="margin-right:8px;"></i> Confirm & Pay
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger font-rale text-center" style="border-radius:8px;font-size:14px;padding:10px;">
                            Insufficient funds. <a href="wallet.php" style="font-weight:bold;color:#c62828;text-decoration:underline;">Add funds to wallet</a>
                        </div>
                        <button class="btn btn-block font-baloo text-muted" disabled
                                style="background:#eee;border:none;border-radius:25px;padding:15px;font-size:18px;font-weight:700;">
                            <i class="fas fa-lock" style="margin-right:8px;"></i> Confirm & Pay
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
