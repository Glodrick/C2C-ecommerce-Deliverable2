<?php
// Start session at the very top, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Database classes ──────────────────────────────────────────
require_once __DIR__ . '/database/DBController.php';
require_once('database/product.php');
require_once('database/Cart.php');
require_once('database/User.php');
require_once('database/Listing.php');

// ── Database Controller Object ───────────────────────────────
$db = new DBController();

// ── Instantiate Models ───────────────────────────────────────
$product = new Product($db);
$cart    = new Cart($db);
$user_obj = new User($db);
$listing = new Listing($db);

$current_user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// ── Global Authentication Check ──────────────────────────────
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'register.php', 'logout.php'];

if ($current_user_id === 0 && !in_array($current_page, $public_pages)) {
    header('Location: login.php');
    exit;
}

// ── Cart count for the header badge ──────────────────────────
$cart_count = $current_user_id > 0 ? $cart->getCartCount($current_user_id) : 0;

// ════════════════════════════════════════════════════════════════
// HELPER: renderProductCard($item)
// Echoes a single product card HTML directly — pure PHP rendering,
// no JSON bridge. Works in carousels AND category grids.
// ════════════════════════════════════════════════════════════════
function renderProductCard(array $item): void
{
    $id       = (int)$item['item_id'];
    $name     = htmlspecialchars($item['item_name'], ENT_QUOTES);
    $image    = htmlspecialchars(str_replace(' ', '%20', $item['item_image']), ENT_QUOTES);
    $details  = htmlspecialchars($item['item_details'], ENT_QUOTES);
    $price    = number_format((float)$item['item_price'], 2);
    $inStock  = $item['item_in_stock'] ? 'In-Stock' : 'Out of Stock';
    $stockClass = $item['item_in_stock'] ? 'in-stock' : 'out-of-stock';

    // Build price HTML — show original (struck-through) if on sale
    $priceHtml = '<span class="prodcutPrice">R' . $price . '</span>';
    if (!empty($item['item_original_price'])) {
        $orig = number_format((float)$item['item_original_price'], 2);
        $priceHtml .= ' <span class="original-price">R' . $orig . '</span>';
    }

    // Bullet-separated details
    $detailParts = array_map('trim', explode('•', $details));
    $detailsHtml = implode(
        ' <span class="detail-dot">&bull;</span> ',
        array_map('htmlspecialchars', $detailParts)
    );

    // Seller Info
    $sellerName = "Unknown Seller";
    if (!empty($item['seller_first_name'])) {
        $sellerName = htmlspecialchars($item['seller_first_name'] . ' ' . ($item['seller_last_name'] ?? ''));
    }

    echo <<<HTML
    <div class="product custom-product-card">
        <a href="product_detail.php?id={$id}" class="card-img-link">
            <img src="{$image}" alt="{$name}" class="img-fluid custom-product-img">
        </a>
        <div class="custom-card-body font-rale">
            <h5 class="custom-product-title">
                <a href="product_detail.php?id={$id}" style="color:inherit; text-decoration:none;">
                    {$name}
                </a>
            </h5>
            <div style="font-size:12px; color:#666; margin-bottom:8px;">
                Sold by: <span style="font-weight:600; color:#00A5C4;">{$sellerName}</span>
            </div>
            <p class="custom-product-details">
                {$detailsHtml}
            </p>
            <div class="custom-product-status font-size-12 {$stockClass}">
                {$inStock}
            </div>
            <div class="price-row">
                <div class="price-container">{$priceHtml}</div>
                <button class="cart-square-btn add-to-cart-btn"
                        data-item-id="{$id}"
                        title="Add to cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </div>
        </div>
    </div>
    HTML;
}

// ════════════════════════════════════════════════════════════════
// HELPER: renderCartRow($item)
// Echoes a single cart item row inside the shopping cart page.
// ════════════════════════════════════════════════════════════════
function renderCartRow(array $item): void
{
    $id       = (int)$item['item_id'];
    $name     = htmlspecialchars($item['item_name'], ENT_QUOTES);
    $image    = htmlspecialchars(str_replace(' ', '%20', $item['item_image']), ENT_QUOTES);
    $details  = htmlspecialchars($item['item_details'], ENT_QUOTES);
    $price    = number_format((float)$item['item_price'], 2);
    $qty      = (int)$item['quantity'];
    $lineTotal = number_format((float)$item['item_price'] * $qty, 2);

    echo <<<HTML
    <div class="cart-item-row"
         style="display:flex;align-items:center;gap:12px;padding:15px 0;border-bottom:1px solid #f5f5f5;"
         data-item-id="{$id}">

        <div class="cart-item-thumb"
             style="width:90px;height:90px;background:#f9f9f9;border-radius:12px;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <img src="{$image}" alt="{$name}"
                 style="max-width:75px;max-height:75px;object-fit:contain;">
        </div>

        <div class="cart-item-info" style="flex:1;min-width:0;">
            <div class="font-baloo"
                 style="font-size:17px;font-weight:700;color:#222;
                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {$name}
            </div>
            <div class="font-rale" style="font-size:13px;color:#888;margin-top:2px;">
                {$details}
            </div>
            <a href="cart_action.php?action=remove&item_id={$id}"
               class="remove-item-link"
               style="color:#E44C4C;font-size:13px;font-weight:600;
                      cursor:pointer;margin-top:6px;display:inline-block;text-decoration:none;">
                Remove
            </a>
        </div>

        <div class="cart-item-actions"
             style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:8px;">
                <button class="cart-qty-btn btnDown"
                        data-item-id="{$id}"
                        style="width:34px;height:34px;border-radius:50%;background:#f0f0f0;
                               border:none;font-size:18px;font-weight:700;cursor:pointer;
                               color:#333;line-height:1;">−</button>
                <input type="text" class="cart-qty-input" data-item-id="{$id}"
                       value="{$qty}"
                       style="width:36px;text-align:center;border:1px solid #ddd;
                              border-radius:8px;padding:5px;font-weight:700;font-size:15px;"
                       readonly>
                <button class="cart-qty-btn btnUp"
                        data-item-id="{$id}"
                        style="width:34px;height:34px;border-radius:50%;background:#00A5C4;
                               border:none;font-size:18px;font-weight:700;cursor:pointer;
                               color:#fff;line-height:1;">+</button>
            </div>
            <div class="font-baloo cart-line-total"
                 style="font-size:20px;font-weight:800;color:#222;text-align:right;min-width:90px;"
                 data-unit-price="{$item['item_price']}">
                R{$lineTotal}
            </div>
        </div>
    </div>
    HTML;
}

// ════════════════════════════════════════════════════════════════
// HELPER: verifyKycIdentity($idNumber)
// Securely verify an ID number against the VerifyNow API
// ════════════════════════════════════════════════════════════════
define('VERIFYNOW_API_KEY', 'vn_live_85175b1c66de25f2bde3d53d12b7e772eb80a5fa221d042b80ea3ac5f49c9049');

function verifyKycIdentity(string $idNumber): array
{
    $url = 'https://www.verifynow.co.za/api/external/verify';
    
    $payload = json_encode([
        'bundle' => 'kyc_bundle',
        'idNumber' => $idNumber,
        'mode' => 'sandbox'
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . VERIFYNOW_API_KEY,
        'Idempotency-Key: kyc-txn-' . uniqid() . '-' . time()
    ]);
    
    // Using false for local dev environment SSL issues
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300 && $response) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            return $data;
        }
    }
    
    return [
        'status' => 'failed',
        'match_score' => 0,
        'message' => 'API request failed or returned invalid response.'
    ];
}
