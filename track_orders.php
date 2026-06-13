<?php
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Orders';
$user_obj = new User($db);
$orders = $user_obj->getTransactions($current_user_id);

require_once 'header.php';
?>

<section class="container mt-5 mb-5">
    <h2 class="font-baloo" style="font-weight:800;color:#333;margin-bottom:20px;">
        <i class="fas fa-box-open" style="color:#00A5C4;margin-right:10px;"></i>My Orders
    </h2>

    <?php if (empty($orders)): ?>
        <div style="background:#fff;padding:60px 40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);text-align:center;">
            <i class="fas fa-shopping-bag" style="font-size:64px;color:#ddd;margin-bottom:20px;"></i>
            <h4 class="font-rubik text-muted">You haven't placed any orders yet.</h4>
            <a href="index.php" class="btn color-primary-bg text-white font-baloo mt-4" style="border-radius:25px;padding:10px 25px;font-weight:600;">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive" style="background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);padding:20px;">
            <table class="table table-hover font-rale" style="margin:0;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="border:none;color:#555;font-weight:600;">Order No.</th>
                        <th style="border:none;color:#555;font-weight:600;">Item</th>
                        <th style="border:none;color:#555;font-weight:600;">Date</th>
                        <th style="border:none;color:#555;font-weight:600;">Price</th>
                        <th style="border:none;color:#555;font-weight:600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td style="vertical-align:middle;font-weight:600;color:#00A5C4;">
                                #<?php echo str_pad($order['transaction_id'], 6, '0', STR_PAD_LEFT); ?>
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="display:flex;align-items:center;">
                                    <img src="<?php echo htmlspecialchars(str_replace(' ', '%20', $order['item_image'])); ?>" alt="item" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;border:1px solid #eee;">
                                    <span style="font-weight:600;color:#333;"><?php echo htmlspecialchars($order['item_name']); ?></span>
                                </div>
                            </td>
                            <td style="vertical-align:middle;color:#666;">
                                <?php echo date('M j, Y', strtotime($order['transaction_date'])); ?>
                            </td>
                            <td style="vertical-align:middle;font-weight:700;color:#E44C4C;">
                                R<?php echo number_format($order['amount'], 2); ?>
                            </td>
                            <td style="vertical-align:middle;">
                                <?php if ($order['order_status'] === 'received'): ?>
                                    <span style="background:#fff3cd;color:#856404;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-box" style="margin-right:4px;"></i>Received</span>
                                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="openReturnModal(<?php echo $order['transaction_id']; ?>)" style="font-size:12px;border-radius:15px;">Return Order</button>
                                <?php elseif ($order['order_status'] === 'shipped'): ?>
                                    <span style="background:#cce5ff;color:#004085;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-truck" style="margin-right:4px;"></i>Shipped</span>
                                <?php elseif ($order['order_status'] === 'delivered'): ?>
                                    <span style="background:#d4edda;color:#155724;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-check-circle" style="margin-right:4px;"></i>Delivered</span>
                                <?php elseif ($order['order_status'] === 'return_pending_seller'): ?>
                                    <span style="background:#e2e3e5;color:#383d41;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-clock" style="margin-right:4px;"></i>Pending Seller</span>
                                    <button class="btn btn-sm btn-warning mt-2" onclick="escalateReturn(<?php echo $order['transaction_id']; ?>)" style="font-size:12px;border-radius:15px;">Report / Escalate</button>
                                <?php elseif ($order['order_status'] === 'return_denied_damaged'): ?>
                                    <span style="background:#f8d7da;color:#721c24;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-ban" style="margin-right:4px;"></i>Denied (Damaged)</span>
                                <?php elseif ($order['order_status'] === 'return_denied_seller'): ?>
                                    <span style="background:#f8d7da;color:#721c24;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-ban" style="margin-right:4px;"></i>Denied by Seller</span>
                                    <button class="btn btn-sm btn-warning mt-2" onclick="escalateReturn(<?php echo $order['transaction_id']; ?>)" style="font-size:12px;border-radius:15px;">Report / Escalate</button>
                                <?php elseif ($order['order_status'] === 'return_escalated_admin'): ?>
                                    <span style="background:#cce5ff;color:#004085;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-gavel" style="margin-right:4px;"></i>Escalated to Admin</span>
                                <?php elseif ($order['order_status'] === 'return_denied_admin'): ?>
                                    <span style="background:#f8d7da;color:#721c24;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-ban" style="margin-right:4px;"></i>Denied by Admin</span>
                                <?php elseif ($order['order_status'] === 'return_refunded'): ?>
                                    <span style="background:#d4edda;color:#155724;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-undo" style="margin-right:4px;"></i>Refunded</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- Return Modal -->
<div class="modal" id="returnModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:12px;">
      <div class="modal-header border-0">
        <h5 class="modal-title font-baloo" style="font-weight:700;"><i class="fas fa-undo"></i> Return Order</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#returnModal').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body font-rale">
        <input type="hidden" id="return_transaction_id">
        <div class="form-group mb-3">
            <label style="font-weight:600;color:#555;">Reason for return:</label>
            <textarea id="return_reason" class="form-control" rows="3" style="border-radius:8px;" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label style="font-weight:600;color:#555;">Condition of item:</label>
            <select id="return_condition" class="form-control" style="border-radius:8px;" required>
                <option value="">Select Condition</option>
                <option value="Okay/Good">Okay/Good</option>
                <option value="Damaged">Damaged</option>
            </select>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" style="border-radius:20px;padding:8px 20px;" onclick="$('#returnModal').modal('hide')">Cancel</button>
        <button type="button" class="btn btn-danger" style="border-radius:20px;padding:8px 20px;" onclick="submitReturn()">Submit Return</button>
      </div>
    </div>
  </div>
</div>

<script>
function openReturnModal(id) {
    document.getElementById('return_transaction_id').value = id;
    document.getElementById('return_reason').value = '';
    document.getElementById('return_condition').value = '';
    $('#returnModal').modal('show');
}

async function submitReturn() {
    const id = document.getElementById('return_transaction_id').value;
    const reason = document.getElementById('return_reason').value;
    const condition = document.getElementById('return_condition').value;

    if (!reason || !condition) {
        alert("Please provide both reason and condition.");
        return;
    }

    const fd = new FormData();
    fd.append('action', 'initiate');
    fd.append('transaction_id', id);
    fd.append('reason', reason);
    fd.append('condition', condition);

    try {
        const res = await fetch('return_action.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message);
        if (data.success) location.reload();
    } catch (e) {
        alert("An error occurred.");
    }
}

async function escalateReturn(id) {
    if (!confirm("Are you sure you want to escalate this dispute to an administrator?")) return;
    
    const fd = new FormData();
    fd.append('action', 'escalate');
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
