<?php
require_once 'functions.php';
$page_title = 'Home';

// Handle search query
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter      = isset($_GET['filter']) ? trim($_GET['filter']) : '';

if ($search_term !== '') {
    $search_results = $product->searchProducts($search_term);
} else {
    $brand_new_products = $product->getBrandNewProducts();
    $sale_products      = $product->getSaleProducts();
}

require_once 'header.php';
?>

<!-- ── Banner Carousel ──────────────────────────────────────── -->
<section id="banner-area" class="container-fluid mt-3">
    <div class="owl-carousel owl-theme" id="heroBannerCarousel">
        <div class="bannerItem">
            <img src="./assets/Banner/apple.jpg" alt="Apple Banner">
        </div>
        <div class="bannerItem">
            <img src="./assets/Banner/intelBanner.png" alt="Intel Banner">
        </div>
        <div class="bannerItem">
            <img src="./assets/Banner/samsung banner.png" alt="Samsung Banner">
        </div>
    </div>
</section>

<?php if ($search_term !== ''): ?>
<!-- ── Search Results ───────────────────────────────────────── -->
<section class="container mt-5 mb-5">
    <h2 style="font-size:28px;font-weight:800;color:#111;margin-bottom:25px;">
        Search results for: <em style="color:#00A5C4;"><?php echo htmlspecialchars($search_term); ?></em>
    </h2>
    <?php if (empty($search_results)): ?>
        <div style="text-align:center;padding:60px 0;color:#888;">
            <i class="fas fa-search" style="font-size:48px;margin-bottom:15px;display:block;"></i>
            <p style="font-size:18px;">No products found for "<?php echo htmlspecialchars($search_term); ?>".</p>
            <a href="index.php" class="btn color-primary-bg" style="color:#fff;margin-top:10px;border-radius:25px;padding:10px 30px;">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($search_results as $item): ?>
                <?php renderProductCard($item); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php else: ?>

<!-- ── Category Overview ────────────────────────────────────── -->
<section id="categoryOverview">
    <div class="container py-5">
        <h4>The future in your hands</h4>
        <div class="custom-category-row">
            <div class="category-item">
                <a href="pc_parts.php">
                    <div class="category-img-box">
                        <img src="./assets/COMPUTER_CATEGORY.jpg" alt="computer category">
                    </div>
                </a>
                <h6>Computers</h6>
            </div>
            <div class="category-item">
                <a href="phones.php">
                    <div class="category-img-box">
                        <img src="./assets/iPhone-17发-191812927.jpg" alt="phone category">
                    </div>
                </a>
                <h6>Phones</h6>
            </div>
            <div class="category-item">
                <a href="peripherals.php">
                    <div class="category-img-box">
                        <img src="./assets/peripheral.jpg" alt="peripheral category">
                    </div>
                </a>
                <h6>Peripherals</h6>
            </div>
            <div class="category-item">
                <a href="accessories.php">
                    <div class="category-img-box">
                        <img src="./assets/Otterbox-Phone-Cases-1200x1114-3247074711.webp" alt="accessory category">
                    </div>
                </a>
                <h6>Accessories</h6>
            </div>
        </div>
    </div>
</section>

<!-- ── Shop All Banner ──────────────────────────────────────── -->
<section id="homeBanner2">
    <div class="saOverlay">
        <h1 class="saHeading">A World of Tech Awaits.</h1>
        <p class="saBody">Shop through our wide selection of the latest tech products</p>
        <a href="shop.php" class="btn">Shop All</a>
    </div>
</section>

<!-- ── Brand New Section ────────────────────────────────────── -->
<section id="brandNewSection" style="margin-top:50px;">
    <div class="container-fluid">
        <h2 class="carousel-heading"
            style="font-size:28px;font-weight:800;color:#111;text-align:left;
                   margin-bottom:30px;text-transform:uppercase;letter-spacing:2px;">
            Brand New
        </h2>
        <div class="products">
            <div class="allProducts" style="display:block;">
                <div id="brandNewCarousel" class="owl-carousel owl-theme">
                    <?php foreach ($brand_new_products as $item): ?>
                        <?php renderProductCard($item); ?>
                    <?php endforeach; ?>
                    <?php if (empty($brand_new_products)): ?>
                        <p style="color:#888;padding:20px;">No brand new products available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Sale Section ─────────────────────────────────────────── -->
<section id="saleSection" style="margin-top:50px;margin-bottom:50px;">
    <div class="container-fluid">
        <h2 class="carousel-heading"
            style="font-size:28px;font-weight:800;color:#111;text-align:left;
                   margin-bottom:30px;text-transform:uppercase;letter-spacing:2px;">
            Sale
        </h2>
        <div class="products">
            <div class="allProducts" style="display:block;">
                <div id="saleCarousel" class="owl-carousel owl-theme">
                    <?php foreach ($sale_products as $item): ?>
                        <?php renderProductCard($item); ?>
                    <?php endforeach; ?>
                    <?php if (empty($sale_products)): ?>
                        <p style="color:#888;padding:20px;">No sale products available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; // end search / normal view ?>

<?php require_once 'footer.php'; ?>