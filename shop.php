<?php
require_once 'functions.php';
$page_title = 'Shop All';
$products   = $product->getData('product');
require_once 'header.php';
?>

<section class="container mt-5" style="margin-bottom:60px;">
    <h2 class="mb-4" style="font-size:36px;font-weight:800;color:#111;margin-bottom:20px;">
        Shop All
    </h2>

    <!-- Filter Pills (Optional for All Shop) -->
    <div class="filters-container mb-4">
        <button class="filter-pill active-filter" data-filter="all">All</button>
        <button class="filter-pill" data-filter="sale">On Sale</button>
        <button class="filter-pill" data-filter="new">Brand New</button>
        <button class="filter-pill" data-brand="Apple">Apple</button>
        <button class="filter-pill" data-brand="Samsung">Samsung</button>
        <button class="filter-pill" data-brand="Intel">Intel</button>
    </div>

    <?php if (empty($products)): ?>
        <div style="text-align:center;padding:60px;color:#888;">
            <i class="fas fa-box-open" style="font-size:48px;margin-bottom:15px;display:block;"></i>
            <p>No products available right now. Check back soon!</p>
        </div>
    <?php else: ?>
        <div class="product-grid" id="categoryGrid" data-category="shop-all">
            <?php foreach ($products as $item): ?>
                <?php renderProductCard($item); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
