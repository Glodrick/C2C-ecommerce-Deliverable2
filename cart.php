<?php
require_once 'functions.php';
$page_title = 'Shopping Cart';

// Fetch cart items from DB
$cart_items = $cart->getCartItems($current_user_id);
$cart_total = $cart->getCartTotal($current_user_id);
$item_count = array_sum(array_column($cart_items, 'quantity'));

require_once 'header.php';
?>

<div class="container" style="padding:40px 15px 60px;">
    <h2 class="font-baloo" style="font-size:30px;font-weight:800;color:#111;margin-bottom:30px;">
        <i class="fas fa-shopping-cart color-primary" style="margin-right:10px;"></i>Shopping Cart
    </h2>

    <?php if (!empty($_SESSION['cart_msg'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>
            <?php echo htmlspecialchars($_SESSION['cart_msg']); unset($_SESSION['cart_msg']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <!-- Empty cart state -->
        <div style="text-align:center;padding:80px 20px;background:#fff;border-radius:16px;
                    box-shadow:0 8px 25px rgba(0,0,0,0.06);">
            <i class="fas fa-shopping-cart" style="font-size:60px;color:#ddd;margin-bottom:20px;display:block;"></i>
            <h4 style="color:#888;font-weight:600;margin-bottom:15px;">Your cart is empty</h4>
            <p style="color:#aaa;font-size:15px;margin-bottom:25px;">Looks like you haven't added anything yet.</p>
            <a href="index.php" class="btn color-primary-bg"
               style="color:#fff;border-radius:25px;padding:12px 35px;font-size:15px;font-weight:600;">
                Start Shopping
            </a>
        </div>

    <?php else: ?>
        <div class="row">
            <!-- ── Cart Items Column ─────────────────────────── -->
            <div class="col-md-8 mb-4">
                <div style="background:#fff;border-radius:16px;box-shadow:0 8px 25px rgba(0,0,0,0.06);padding:25px;">

                    <div style="display:flex;justify-content:space-between;align-items:center;
                                margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #f0f0f0;">
                        <h5 class="font-rubik" style="font-weight:700;color:#333;margin:0;">
                            Your Items
                            <span style="font-size:13px;color:#888;font-weight:400;margin-left:8px;">
                                (<?php echo $item_count; ?> item<?php echo $item_count !== 1 ? 's' : ''; ?>)
                            </span>
                        </h5>
                        <a href="cart_action.php?action=clear"
                           style="color:#E44C4C;font-size:14px;font-weight:600;
                                  cursor:pointer;text-decoration:none;"
                           onclick="return confirm('Remove all items from cart?')">
                            <i class="fas fa-trash-alt" style="margin-right:5px;"></i>Remove All
                        </a>
                    </div>

                    <!-- PHP loop renders each cart item row directly -->
                    <div id="cart-items-wrapper">
                        <?php foreach ($cart_items as $item): ?>
                            <?php renderCartRow($item); ?>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

            <!-- ── Order Summary Column ──────────────────────── -->
            <div class="col-md-4">
                <div style="background:#fff;border-radius:16px;box-shadow:0 8px 25px rgba(0,0,0,0.06);
                            padding:25px;position:sticky;top:20px;">
                    <h5 class="font-rubik"
                        style="font-weight:700;color:#333;margin-bottom:20px;
                               padding-bottom:15px;border-bottom:1px solid #f0f0f0;">
                        Order Summary
                    </h5>

                    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                        <span class="font-rale" style="color:#888;font-size:14px;">
                            Subtotal (<?php echo $item_count; ?> item<?php echo $item_count !== 1 ? 's' : ''; ?>)
                        </span>
                        <span class="font-baloo" id="cart-subtotal"
                              style="font-weight:700;color:#333;">
                            R<?php echo number_format($cart_total, 2); ?>
                        </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                        <span class="font-rale" style="color:#888;font-size:14px;">Delivery</span>
                        <span class="font-baloo" style="font-weight:700;color:#27ae60;">Free</span>
                    </div>
                    <hr style="margin:15px 0;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:25px;">
                        <span class="font-rubik" style="font-size:16px;font-weight:700;color:#333;">Total</span>
                        <span class="font-baloo" id="cart-total"
                              style="font-size:22px;font-weight:800;color:#E44C4C;">
                            R<?php echo number_format($cart_total, 2); ?>
                        </span>
                    </div>

                    <a href="checkout.php" class="btn btn-block"
                            style="background-color:#00A5C4;color:white;font-size:16px;font-weight:700;
                                   border-radius:30px;padding:14px;border:none;
                                   box-shadow:0 4px 12px rgba(0,165,196,0.3);">
                        Proceed to Checkout
                    </a>
                    <a href="index.php"
                       style="display:block;text-align:center;margin-top:15px;color:#888;
                              font-size:14px;text-decoration:none;">
                        <i class="fas fa-arrow-left" style="margin-right:5px;"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>