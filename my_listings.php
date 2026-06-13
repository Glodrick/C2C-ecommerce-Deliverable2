<?php
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Listings';
$active_listings = $product->getListingsBySeller($current_user_id);
$pending_listings = $listing->getPendingListings($current_user_id);

require_once 'header.php';
?>

<section class="container mt-5 mb-5">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 class="font-baloo" style="font-weight:800;color:#333;margin:0;">
            <i class="fas fa-store" style="color:#00A5C4;margin-right:10px;"></i>My Listings
        </h2>
        <a href="add_listing.php" class="btn color-primary-bg text-white font-baloo" style="border-radius:25px;padding:10px 25px;font-weight:600;">
            <i class="fas fa-plus" style="margin-right:5px;"></i> Create a Listing
        </a>
    </div>

    <!-- Active Listings -->
    <h4 class="font-rubik mt-4 mb-3" style="font-weight:600;color:#555;">Active & Sold Listings</h4>
    <?php if (empty($active_listings)): ?>
        <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);text-align:center;margin-bottom:40px;">
            <p class="font-rale text-muted" style="margin:0;">You don't have any active listings yet.</p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($active_listings as $item):
                $id     = (int)$item['item_id'];
                $name   = htmlspecialchars($item['item_name'], ENT_QUOTES);
                $image  = htmlspecialchars(str_replace(' ', '%20', $item['item_image']), ENT_QUOTES);
                $price  = number_format((float)$item['item_price'], 2);
                $isActive = $item['item_status'] === 'active';
                $badgeClass = $isActive ? 'badge-active' : 'badge-sold';
                $badgeLabel = $isActive ? 'Active' : 'Sold';
            ?>
                <div class="product custom-product-card" style="position:relative;">
                    <!-- Status Badge -->
                    <span class="badge-status <?php echo $badgeClass; ?>" style="position:absolute;top:12px;right:12px;z-index:2;">
                        <?php echo $badgeLabel; ?>
                    </span>

                    <a href="product_detail.php?id=<?php echo $id; ?>" class="card-img-link">
                        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="img-fluid custom-product-img custom-thumbnail">
                    </a>
                    <div class="custom-card-body font-rale">
                        <h5 class="custom-product-title">
                            <a href="product_detail.php?id=<?php echo $id; ?>" style="color:inherit;text-decoration:none;">
                                <?php echo $name; ?>
                            </a>
                        </h5>
                        <div class="price-row">
                            <div class="price-container">
                                <span class="prodcutPrice">R<?php echo $price; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pending Listings -->
    <h4 class="font-rubik mt-4 mb-3" style="font-weight:600;color:#555;">Pending Approval</h4>
    <?php if (empty($pending_listings)): ?>
        <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);text-align:center;">
            <p class="font-rale text-muted" style="margin:0;">No pending listings awaiting approval.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive" style="background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);padding:20px;">
            <table class="table table-hover font-rale" style="margin:0;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="border:none;color:#555;font-weight:600;">Item Name</th>
                        <th style="border:none;color:#555;font-weight:600;">Price</th>
                        <th style="border:none;color:#555;font-weight:600;">Qty</th>
                        <th style="border:none;color:#555;font-weight:600;">Category</th>
                        <th style="border:none;color:#555;font-weight:600;">KYC Status</th>
                        <th style="border:none;color:#555;font-weight:600;">Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_listings as $item): ?>
                        <tr>
                            <td style="vertical-align:middle;">
                                <div style="display:flex;align-items:center;">
                                    <img src="<?php echo htmlspecialchars(str_replace(' ', '%20', $item['item_image'])); ?>" alt="item" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;border:1px solid #eee;">
                                    <span style="font-weight:600;color:#333;"><?php echo htmlspecialchars($item['item_name']); ?></span>
                                </div>
                            </td>
                            <td style="vertical-align:middle;font-weight:700;color:#333;">
                                R<?php echo number_format($item['item_price'], 2); ?>
                            </td>
                            <td style="vertical-align:middle;font-weight:600;">
                                <?php echo (int)$item['item_quantity']; ?>
                            </td>
                            <td style="vertical-align:middle;color:#666;text-transform:capitalize;">
                                <?php echo htmlspecialchars($item['item_category']); ?>
                            </td>
                            <td style="vertical-align:middle;">
                                <span style="background:#fff3cd;color:#856404;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">Pending Review</span>
                            </td>
                            <td style="vertical-align:middle;color:#666;">
                                <?php echo date('M j, Y', strtotime($item['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Return Requests -->
    <?php
        // Fetch return requests for this seller
        $stmt_rr = $db->con->prepare("SELECT t.*, p.item_name, p.item_image, u.first_name, u.last_name FROM `transaction` t JOIN `product` p ON t.item_id = p.item_id JOIN `user` u ON t.buyer_id = u.user_id WHERE t.seller_id = ? AND t.order_status = 'return_pending_seller'");
        $stmt_rr->bind_param('i', $current_user_id);
        $stmt_rr->execute();
        $return_requests = $stmt_rr->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_rr->close();
    ?>
    <h4 class="font-rubik mt-5 mb-3" style="font-weight:600;color:#555;">Return Requests</h4>
    <?php if (empty($return_requests)): ?>
        <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);text-align:center;">
            <p class="font-rale text-muted" style="margin:0;">No return requests from buyers.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive" style="background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);padding:20px;">
            <table class="table table-hover font-rale" style="margin:0;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="border:none;color:#555;font-weight:600;">Buyer</th>
                        <th style="border:none;color:#555;font-weight:600;">Item</th>
                        <th style="border:none;color:#555;font-weight:600;">Reason</th>
                        <th style="border:none;color:#555;font-weight:600;">Condition</th>
                        <th style="border:none;color:#555;font-weight:600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($return_requests as $req): ?>
                        <tr>
                            <td style="vertical-align:middle;font-weight:600;">
                                <?php echo htmlspecialchars($req['first_name'] . ' ' . $req['last_name']); ?>
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="display:flex;align-items:center;">
                                    <img src="<?php echo htmlspecialchars(str_replace(' ', '%20', $req['item_image'])); ?>" alt="item" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;border:1px solid #eee;">
                                    <span style="font-weight:600;color:#333;"><?php echo htmlspecialchars($req['item_name']); ?></span>
                                </div>
                            </td>
                            <td style="vertical-align:middle;color:#666;">
                                <?php echo htmlspecialchars($req['return_reason']); ?>
                            </td>
                            <td style="vertical-align:middle;">
                                <span style="background:#fff3cd;color:#856404;padding:4px 8px;border-radius:12px;font-size:12px;"><?php echo htmlspecialchars($req['return_condition']); ?></span>
                            </td>
                            <td style="vertical-align:middle;">
                                <button class="btn btn-sm btn-success" onclick="handleReturn(<?php echo $req['transaction_id']; ?>, 'seller_accept')" style="border-radius:15px;margin-right:5px;">Accept</button>
                                <button class="btn btn-sm btn-danger" onclick="handleReturn(<?php echo $req['transaction_id']; ?>, 'seller_deny')" style="border-radius:15px;">Deny</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</section>

<script>
async function handleReturn(id, action) {
    if (!confirm("Are you sure you want to " + (action === 'seller_accept' ? "ACCEPT" : "DENY") + " this return?")) return;
    
    const fd = new FormData();
    fd.append('action', action);
    fd.append('transaction_id', id);

    try {
        const res = await fetch('return_action.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message);
        if (data.success) location.reload();
    } catch (e) {
        alert("An error occurred.");
    }
}
</script>

<?php require_once 'footer.php'; ?>
