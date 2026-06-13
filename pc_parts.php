<?php
require_once 'functions.php';
$page_title = 'PC Parts';
$products   = $product->getProductsByCategory('pc parts');
require_once 'header.php';
?>

<section class="container mt-5" style="margin-bottom:60px;">
    <h2 class="mb-4" style="font-size:36px;font-weight:800;color:#111;margin-bottom:20px;">
        PC Parts
    </h2>

    <div class="filters-container mb-4">
        <button class="filter-pill active-filter" data-filter="all">All</button>
        <button class="filter-pill" data-filter="sale">On Sale</button>
        <button class="filter-pill" data-filter="new">Brand New</button>
        <button class="filter-pill" data-brand="Intel">Intel</button>
        <button class="filter-pill" data-brand="ZOTAC">ZOTAC</button>
        <button class="filter-pill" data-brand="MEK">MEK</button>
    </div>

    <?php if (empty($products)): ?>
        <div style="text-align:center;padding:60px;color:#888;">
            <i class="fas fa-microchip" style="font-size:48px;margin-bottom:15px;display:block;"></i>
            <p>No PC parts available right now. Check back soon!</p>
        </div>
    <?php else: ?>
        <div class="product-grid" id="categoryGrid" data-category="pc parts">
            <?php foreach ($products as $item): ?>
                <?php renderProductCard($item); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
