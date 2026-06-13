<?php
require_once 'functions.php';

// Get item ID from URL — validate it's a positive integer
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($item_id <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch this product from DB
$product_data = $product->getProduct($item_id);

if (empty($product_data)) {
    // Product not found — send to homepage
    header('Location: index.php');
    exit;
}

$p = $product_data[0]; // Single product row

// Build page title from product name
$page_title = htmlspecialchars($p['item_name']);

// Fetch related products (same category, exclude current)
$related_raw = $product->getProductsByCategory($p['item_category']);
$related = array_filter($related_raw, fn($r) => (int)$r['item_id'] !== $item_id);

require_once 'header.php';
?>

<!-- ── Product Display Section ──────────────────────────────── -->
<section id="productDisplay"
         class="container mt-5 mb-5 p-5 rounded"
         style="background:white;box-shadow:0 10px 30px rgba(0,0,0,0.05);">
    <div class="row">

        <!-- Product Image -->
        <div class="col-md-6 text-center"
             style="display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
            <img src="<?php echo htmlspecialchars(str_replace(' ', '%20', $p['item_image'])); ?>"
                 alt="<?php echo htmlspecialchars($p['item_name']); ?>"
                 class="img-responsive rounded"
                 style="max-height:400px;object-fit:contain;width:100%;">
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h2 class="font-baloo"
                style="font-size:32px;font-weight:700;color:#333;margin-top:0;">
                <?php echo htmlspecialchars($p['item_name']); ?>
            </h2>
            <small class="text-muted" style="font-size:14px;">
                by <?php echo htmlspecialchars($p['item_brand']); ?>
            </small>

            <div style="margin-top:8px;">
                <?php
                    $sellerNameDetail = "Unknown Seller";
                    if (!empty($p['seller_first_name'])) {
                        $sellerNameDetail = htmlspecialchars($p['seller_first_name'] . ' ' . ($p['seller_last_name'] ?? ''));
                    }
                ?>
                <span style="font-size:14px; color:#666;">
                    Sold by: <span style="font-weight:600; color:#00A5C4;"><?php echo $sellerNameDetail; ?></span>
                </span>
            </div>

            <!-- Rating + Condition -->
            <div style="display:flex;align-items:center;margin:10px 0 20px;">
                <div class="rating text-warning font-size-12">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="px-2 font-rale font-size-14"
                   style="margin:0;color:#666;margin-left:10px;">
                    Condition |
                    <?php echo $p['item_is_new'] ? 'Brand New in Box' : ($p['item_is_sale'] ? 'Sale Item' : 'Good Condition'); ?>
                </p>
            </div>
            <hr>

            <!-- Price -->
            <div class="productPrice" style="margin:20px 0;">
                <span style="font-size:36px;font-weight:700;color:#E44C4C;">
                    R<?php echo number_format($p['item_price'], 2); ?>
                </span>
                <?php if (!empty($p['item_original_price'])): ?>
                    <span style="font-size:20px;color:#aaa;text-decoration:line-through;margin-left:12px;">
                        R<?php echo number_format($p['item_original_price'], 2); ?>
                    </span>
                    <span style="font-size:14px;color:#27ae60;margin-left:8px;font-weight:600;">
                        <?php
                            $saving = $p['item_original_price'] - $p['item_price'];
                            $pct    = round(($saving / $p['item_original_price']) * 100);
                            echo "Save {$pct}%";
                        ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Specifications -->
            <?php
                $specs = array_map('trim', explode('•', $p['item_details']));
            ?>
            <div class="mb-4">
                <h6 class="font-weight-bold" style="font-size:16px;margin-bottom:10px;">
                    Specifications:
                </h6>
                <ul style="color:#555;font-size:14px;padding-left:20px;line-height:1.8;">
                    <?php foreach ($specs as $spec): ?>
                        <?php if (trim($spec) !== ''): ?>
                            <li><?php echo htmlspecialchars(trim($spec)); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Stock Badge -->
            <div style="margin-bottom:15px;">
                <?php if ($p['item_in_stock']): ?>
                    <span style="background:#e8f5e9;color:#2e7d32;padding:6px 14px;
                                 border-radius:20px;font-size:13px;font-weight:600;">
                        <i class="fas fa-check-circle" style="margin-right:4px;"></i> In Stock
                    </span>
                <?php else: ?>
                    <span style="background:#ffeeed;color:#c62828;padding:6px 14px;
                                 border-radius:20px;font-size:13px;font-weight:600;">
                        <i class="fas fa-times-circle" style="margin-right:4px;"></i> Out of Stock
                    </span>
                <?php endif; ?>
            </div>

            <!-- Policy Icons -->
            <div id="policy"
                 style="display:flex;justify-content:space-between;margin-bottom:25px;text-align:center;">
                <div class="return">
                    <div class="my-2 color-primary"
                         style="font-size:20px;display:inline-flex;align-items:center;
                                justify-content:center;width:60px;height:60px;
                                border-radius:50%;border:1px solid #ddd;background:#fff;">
                        <span class="fas fa-retweet"></span>
                    </div>
                    <a href="#" class="font-rale text-dark text-decoration-none d-block"
                       style="font-size:13px;">10 Days<br>Replacement</a>
                </div>
                <div class="return">
                    <div class="my-2 color-primary"
                         style="font-size:20px;display:inline-flex;align-items:center;
                                justify-content:center;width:60px;height:60px;
                                border-radius:50%;border:1px solid #ddd;background:#fff;">
                        <span class="fas fa-truck"></span>
                    </div>
                    <a href="#" class="font-rale text-dark text-decoration-none d-block"
                       style="font-size:13px;">Free<br>Delivery</a>
                </div>
                <div class="return">
                    <div class="my-2 color-primary"
                         style="font-size:20px;display:inline-flex;align-items:center;
                                justify-content:center;width:60px;height:60px;
                                border-radius:50%;border:1px solid #ddd;background:#fff;">
                        <span class="fas fa-check-double"></span>
                    </div>
                    <a href="#" class="font-rale text-dark text-decoration-none d-block"
                       style="font-size:13px;">1 Year<br>Warranty</a>
                </div>
            </div>
            <hr>

            <!-- Action Buttons -->
            <div class="row" style="margin-top:30px;">
                <div class="col-sm-6 mb-2">
                    <?php if ($p['item_in_stock']): ?>
                    <button class="btn btn-danger btn-block py-3 font-weight-bold buy-now-btn"
                            data-item-id="<?php echo $p['item_id']; ?>"
                            style="font-size:16px;border-radius:30px;padding:12px 0;">
                        Buy Now
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary btn-block py-3 font-weight-bold" disabled
                            style="font-size:16px;border-radius:30px;padding:12px 0;">
                        Out of Stock
                    </button>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <?php if ($p['item_in_stock']): ?>
                        <button class="btn btn-block py-3 font-weight-bold text-white add-to-cart-btn"
                                data-item-id="<?php echo $p['item_id']; ?>"
                                style="font-size:16px;border-radius:30px;padding:12px 0;
                                       background-color:#00A5C4;border:none;
                                       box-shadow:0 4px 10px rgba(0,165,196,0.3);">
                            <i class="fas fa-shopping-cart" style="margin-right:6px;"></i>Add to Cart
                        </button>
                    <?php else: ?>
                        <button class="btn btn-block py-3 font-weight-bold" disabled
                                style="font-size:16px;border-radius:30px;padding:12px 0;
                                       background-color:#ccc;border:none;color:#666;cursor:not-allowed;">
                            Out of Stock
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Product Description Section ─────────────────────────── -->
<section class="container mt-4 mb-5 p-5 rounded"
         style="background:white;box-shadow:0 10px 30px rgba(0,0,0,0.05);">
    <h4 class="font-rubik" style="font-weight:bold;margin-bottom:20px;">Product Description</h4>
    <hr>
    <?php if (!empty($p['item_description'])): ?>
        <p class="font-rale font-size-14 text-muted"
           style="line-height:1.8;color:#555;">
            <?php echo nl2br(htmlspecialchars($p['item_description'])); ?>
        </p>
    <?php else: ?>
        <p class="font-rale font-size-14 text-muted" style="color:#555;">
            <?php echo htmlspecialchars($p['item_name']); ?> by <?php echo htmlspecialchars($p['item_brand']); ?>.
            Full details: <?php echo htmlspecialchars($p['item_details']); ?>.
        </p>
    <?php endif; ?>
</section>

<!-- ── Related Products Carousel ────────────────────────────── -->
<?php if (!empty($related)): ?>
<section id="relatedSection" style="margin-top:50px;margin-bottom:50px;">
    <div class="container-fluid">
        <h2 class="carousel-heading"
            style="font-size:28px;font-weight:800;color:#111;text-align:left;
                   margin-bottom:30px;text-transform:uppercase;letter-spacing:2px;">
            Related Products
        </h2>
        <div class="products">
            <div class="allProducts" style="display:block;">
                <div id="relatedCarousel" class="owl-carousel owl-theme">
                    <?php foreach ($related as $rel): ?>
                        <?php renderProductCard($rel); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
