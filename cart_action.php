<?php
/**
 * cart_action.php — AJAX endpoint for all cart operations.
 *
 * Accepted ?action= values:
 *   add        — add item to cart (POST: item_id)
 *   remove     — remove item from cart (GET/POST: item_id)
 *   update     — update quantity (POST: item_id, quantity)
 *   clear      — remove all items for current user
 *   count      — return current cart count (JSON)
 *
 * All responses are JSON except ?redirect=1 for non-AJAX remove/clear.
 */

require_once 'functions.php';

header('Content-Type: application/json');

$action    = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
$item_id   = isset($_REQUEST['item_id']) ? (int)$_REQUEST['item_id'] : 0;
$quantity  = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;
$is_ajax   = (
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
);

// ── Helper: build JSON response ──────────────────────────────
function jsonResponse(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $extra
    ));
    exit;
}

// ── Handle each action ───────────────────────────────────────
switch ($action) {

    // ── ADD ──────────────────────────────────────────────────
    case 'add':
        if ($current_user_id <= 0) {
            jsonResponse(false, 'Please log in to add items to your cart.', ['require_login' => true]);
        }
        if ($item_id <= 0) {
            jsonResponse(false, 'Invalid item ID.');
        }
        $result = $cart->addToCart($current_user_id, $item_id);
        if ($result) {
            $new_count = $cart->getCartCount($current_user_id);
            jsonResponse(true, 'Item added to cart.', ['cart_count' => $new_count]);
        }
        jsonResponse(false, 'Item is out of stock or could not be added.');
        break;

    // ── REMOVE ───────────────────────────────────────────────
    case 'remove':
        if ($current_user_id <= 0) jsonResponse(false, 'Not logged in.');
        if ($item_id <= 0) {
            jsonResponse(false, 'Invalid item ID.');
        }
        $result = $cart->removeFromCart($current_user_id, $item_id);
        if ($result) {
            $new_count = $cart->getCartCount($current_user_id);
            $new_total = $cart->getCartTotal($current_user_id);
            if (!$is_ajax) {
                // Direct link click — redirect back to cart
                $_SESSION['cart_msg'] = 'Item removed from cart.';
                header('Location: cart.php');
                exit;
            }
            jsonResponse(true, 'Item removed.', [
                'cart_count' => $new_count,
                'cart_total' => number_format($new_total, 2)
            ]);
        }
        jsonResponse(false, 'Could not remove item.');
        break;

    // ── UPDATE QUANTITY ──────────────────────────────────────
    case 'update':
        if ($current_user_id <= 0) jsonResponse(false, 'Not logged in.');
        if ($item_id <= 0) {
            jsonResponse(false, 'Invalid item ID.');
        }
        if ($quantity < 1) $quantity = 1;
        if ($quantity > 99) $quantity = 99;

        $result = $cart->updateQuantity($current_user_id, $item_id, $quantity);
        if ($result) {
            $new_count = $cart->getCartCount($current_user_id);
            $new_total = $cart->getCartTotal($current_user_id);
            // Calculate this item's line total
            $items = $cart->getCartItems($current_user_id);
            $line_total = 0;
            foreach ($items as $i) {
                if ((int)$i['item_id'] === $item_id) {
                    $line_total = $i['item_price'] * $quantity;
                    break;
                }
            }
            jsonResponse(true, 'Quantity updated.', [
                'cart_count' => $new_count,
                'cart_total' => number_format($new_total, 2),
                'line_total' => number_format($line_total, 2),
                'quantity'   => $quantity
            ]);
        }
        jsonResponse(false, 'Not enough stock to update quantity.');
        break;

    // ── CLEAR ────────────────────────────────────────────────
    case 'clear':
        if ($current_user_id <= 0) jsonResponse(false, 'Not logged in.');
        $result = $cart->clearCart($current_user_id);
        if ($result) {
            if (!$is_ajax) {
                $_SESSION['cart_msg'] = 'All items removed from cart.';
                header('Location: cart.php');
                exit;
            }
            jsonResponse(true, 'Cart cleared.', ['cart_count' => 0, 'cart_total' => '0.00']);
        }
        jsonResponse(false, 'Could not clear cart.');
        break;

    // ── COUNT (badge refresh) ────────────────────────────────
    case 'count':
        $count = $cart->getCartCount($current_user_id);
        jsonResponse(true, 'OK', ['cart_count' => $count]);
        break;

    // ── BUY NOW ──────────────────────────────────────────────
    case 'buy_now':
        if ($current_user_id <= 0) {
            jsonResponse(false, 'Please log in to buy items.', ['require_login' => true]);
        }
        if ($item_id <= 0) {
            jsonResponse(false, 'Invalid item ID.');
        }
        // Clear cart first, then add item
        $cart->clearCart($current_user_id);
        $result = $cart->addToCart($current_user_id, $item_id);
        
        if ($result) {
            $new_count = $cart->getCartCount($current_user_id);
            jsonResponse(true, 'Item ready for checkout.', ['cart_count' => $new_count]);
        }
        jsonResponse(false, 'Item is out of stock.');
        break;

    default:
        jsonResponse(false, 'Unknown action.');
}
