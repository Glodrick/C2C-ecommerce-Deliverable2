<?php
require_once 'functions.php';
$page_title = 'Phones';
$products   = $product->getProductsByCategory('phones');
require_once 'header.php';
?>

<section class="container mt-5" style="margin-bottom:60px;">
    <h2 class="mb-4 text-capitalize"
        style="font-size:36px;font-weight:800;color:#111;margin-bottom:20px;">
        Phones
    </h2>

    <!-- Filter Pills -->
    <div class="filters-container mb-4" style="margin-bottom:30px;">
        <button class="filter-pill active-filter" data-filter="all">All</button>
        <button class="filter-pill" data-filter="sale">On Sale</button>
        <button class="filter-pill" data-filter="new">Brand New</button>
        <button class="filter-pill" data-brand="Apple">Apple</button>
        <button class="filter-pill" data-brand="Samsung">Samsung</button>
    </div>

    <?php if (empty($products)): ?>
        <div style="text-align:center;padding:60px;color:#888;">
            <i class="fas fa-mobile-alt" style="font-size:48px;margin-bottom:15px;display:block;"></i>
            <p>No phones available right now. Check back soon!</p>
        </div>
    <?php else: ?>
        <!-- PHP loops directly echo product cards into the grid -->
        <div class="product-grid" id="categoryGrid" data-category="phones">
            <?php foreach ($products as $item): ?>
                <?php renderProductCard($item); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
