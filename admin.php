<?php
require_once 'functions.php';
$page_title = 'Admin Dashboard';
$extra_css  = '<link rel="stylesheet" href="admin.css">';
$all_users  = $user_obj->getActiveUsers(15);
$total_users = count($all_users);
$pending_listings = $listing->getAllPendingListings();
// Fetch pending seller applications
$stmt_ps = $db->con->prepare("SELECT user_id, first_name, last_name, email, register_date FROM `user` WHERE `status` = 'pending' AND `role` = 'buyer'");
$stmt_ps->execute();
$pending_sellers = $stmt_ps->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_ps->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GloMart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous"/>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body style="background-color:#f4f7fa;">

<main id="admin-dashboard">
    <div class="container-fluid py-5" style="max-width:1300px;margin:0 auto;padding-top:40px;">

        <!-- Header -->
        <div class="user-management-header text-center" style="margin-bottom:30px;">
            <h1 style="color:#2b3b4e;font-weight:700;font-size:42px;margin-bottom:10px;">
                Admin Dashboard
            </h1>
            <p style="color:#4a5568;font-size:18px;margin-bottom:5px;">User Management</p>
            <a href="index.php" style="font-size:14px;color:#00A5C4;">
                <i class="fas fa-arrow-left" style="margin-right:5px;"></i>Back to Store
            </a>
        </div>

        
        <div class="row" style="margin-bottom:30px;">
            <div class="col-sm-3">
                <div style="background:#fff;border-radius:12px;padding:20px;text-align:center;
                            box-shadow:0 4px 15px rgba(0,0,0,0.06);">
                    <div style="font-size:32px;font-weight:800;color:#00A5C4;"><?php echo $total_users; ?></div>
                    <div style="color:#888;font-size:13px;margin-top:4px;">Total Users</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div style="background:#fff;border-radius:12px;padding:20px;text-align:center;
                            box-shadow:0 4px 15px rgba(0,0,0,0.06);">
                    <div style="font-size:32px;font-weight:800;color:#27ae60;">
                        <?php echo count(array_filter($all_users, fn($u) => $u['status'] === 'active')); ?>
                    </div>
                    <div style="color:#888;font-size:13px;margin-top:4px;">Active Users</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div style="background:#fff;border-radius:12px;padding:20px;text-align:center;
                            box-shadow:0 4px 15px rgba(0,0,0,0.06);">
                    <div style="font-size:32px;font-weight:800;color:#E44C4C;">
                        <?php echo count(array_filter($all_users, fn($u) => in_array($u['status'], ['banned','suspended']))); ?>
                    </div>
                    <div style="color:#888;font-size:13px;margin-top:4px;">Banned / Suspended</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div style="background:#fff;border-radius:12px;padding:20px;text-align:center;
                            box-shadow:0 4px 15px rgba(0,0,0,0.06);">
                    <div style="font-size:32px;font-weight:800;color:#f39c12;">
                        <?php echo count(array_filter($all_users, fn($u) => $u['status'] === 'pending')); ?>
                    </div>
                    <div style="color:#888;font-size:13px;margin-top:4px;">Pending Approval</div>
                </div>
            </div>
        </div>

        <div class="dashboard-container">
         
            <div class="table-toolbar">
                <div class="toolbar-left">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="adminSearchInput" placeholder="Search users...">
                    </div>
                    <select class="btn-outline" id="roleFilter" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="moderator">Moderator</option>
                        <option value="seller">Seller</option>
                        <option value="buyer">Buyer</option>
                    </select>
                    <select class="btn-outline" id="statusFilter" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                        <option value="banned">Banned</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="toolbar-right">
                    <button class="btn-outline"><i class="fas fa-upload"></i> Export</button>
                    <button class="btn-primary-dark"><i class="fas fa-plus"></i> Add User</button>
                </div>
            </div>

            
            <div class="table-responsive">
                <table class="table admin-table" id="usersTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Full Name <i class="fas fa-sort"></i></th>
                            <th>Email <i class="fas fa-sort"></i></th>
                            <th>Username <i class="fas fa-sort"></i></th>
                            <th>Status <i class="fas fa-sort"></i></th>
                            <th>Role <i class="fas fa-sort"></i></th>
                            <th>Joined Date <i class="fas fa-sort"></i></th>
                            <th>Last Active <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dashboard-table-body">
                        <?php foreach ($all_users as $u):
                            $full_name  = htmlspecialchars($u['first_name'] . ' ' . $u['last_name']);
                            $email      = htmlspecialchars($u['email']);
                            $username   = htmlspecialchars($u['username']);
                            $role       = htmlspecialchars($u['role']);
                            $status     = htmlspecialchars($u['status']);
                            $joined     = $u['register_date'] ? date('d M, Y', strtotime($u['register_date'])) : '—';
                            $last_active = $u['last_active'] ? date('d M, Y', strtotime($u['last_active'])) : '—';

                            
                            $statusClass = match($status) {
                                'active'    => 'status-active',
                                'inactive'  => 'status-inactive',
                                'banned'    => 'status-banned',
                                'pending'   => 'status-pending',
                                'suspended' => 'status-suspended',
                                default     => 'status-inactive'
                            };
                        ?>
                        <tr class="user-row" data-role="<?php echo $role; ?>" data-status="<?php echo $status; ?>" data-userid="<?php echo $u['user_id']; ?>" style="cursor:pointer;">
                            <td><input type="checkbox" class="row-select"></td>
                            <td>
                                <div class="user-name-cell"><?php echo $full_name; ?></div>
                            </td>
                            <td><?php echo $email; ?></td>
                            <td><?php echo $username; ?></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td><?php echo ucfirst($role); ?></td>
                            <td><?php echo $joined; ?></td>
                            <td><?php echo $last_active; ?></td>
                            <td>
                                <div class="action-icons">
                                    <i class="fas fa-pencil-alt" title="Edit"></i>
                                    <i class="far fa-trash-alt" title="Delete"></i>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($all_users)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:#888;">
                                No users found in the database.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination info -->
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <strong><?php echo $total_users; ?></strong> users
                </div>
            </div>
        </div>
    </div>
            
            <div style="margin-top:50px;">
                <h3 style="color:#2b3b4e;font-weight:700;margin-bottom:20px;">
                    <i class="fas fa-user-tie" style="color:#f39c12;margin-right:10px;"></i>Pending Seller Applications
                </h3>
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_sellers as $ps): ?>
                                <tr id="seller-row-<?php echo $ps['user_id']; ?>">
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($ps['first_name'] . ' ' . $ps['last_name']); ?></td>
                                    <td style="color:#666;"><?php echo htmlspecialchars($ps['email']); ?></td>
                                    <td style="color:#666;"><?php echo $ps['register_date'] ? date('d M Y', strtotime($ps['register_date'])) : '—'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success approve-seller-btn" data-id="<?php echo $ps['user_id']; ?>" style="border-radius:20px;padding:4px 12px;font-size:12px;">Approve</button>
                                        <button class="btn btn-sm btn-danger reject-seller-btn" data-id="<?php echo $ps['user_id']; ?>" style="border-radius:20px;padding:4px 12px;font-size:12px;margin-left:5px;">Reject</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pending_sellers)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;padding:40px;color:#888;">No pending seller applications.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div style="margin-top:50px;">
                <h3 style="color:#2b3b4e;font-weight:700;margin-bottom:20px;">Seller Moderation Queue</h3>
                <div class="table-responsive">
                    <table class="table admin-table" id="moderationTable">
                        <thead>
                            <tr>
                                <th>Seller <i class="fas fa-sort"></i></th>
                                <th>Item Details <i class="fas fa-sort"></i></th>
                                <th>Price <i class="fas fa-sort"></i></th>
                                <th>Qty <i class="fas fa-sort"></i></th>
                                <th>KYC Result <i class="fas fa-sort"></i></th>
                                <th>Match Score <i class="fas fa-sort"></i></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_listings as $pl): ?>
                                <tr id="listing-row-<?php echo $pl['listing_id']; ?>">
                                    <td>
                                        <div style="font-weight:600;"><?php echo htmlspecialchars($pl['first_name'] . ' ' . $pl['last_name']); ?></div>
                                        <div style="font-size:12px;color:#888;"><?php echo htmlspecialchars($pl['email']); ?></div>
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;">
                                            <img src="<?php echo htmlspecialchars(str_replace(' ', '%20', $pl['item_image'])); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">
                                            <div>
                                                <div style="font-weight:600;"><?php echo htmlspecialchars($pl['item_name']); ?></div>
                                                <div style="font-size:12px;color:#888;text-transform:capitalize;"><?php echo htmlspecialchars($pl['item_category']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight:700;color:#E44C4C;">R<?php echo number_format($pl['item_price'], 2); ?></td>
                                    <td style="font-weight:600;"><?php echo (int)$pl['item_quantity']; ?></td>
                                    <td>
                                        <?php if ($pl['kyc_status'] === 'verified'): ?>
                                            <span style="background:#d4edda;color:#155724;padding:4px 8px;border-radius:12px;font-size:12px;"><i class="fas fa-check-circle" style="margin-right:4px;"></i>Verified</span>
                                        <?php else: ?>
                                            <span style="background:#fff3cd;color:#856404;padding:4px 8px;border-radius:12px;font-size:12px;"><i class="fas fa-exclamation-triangle" style="margin-right:4px;"></i>Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pl['kyc_match_score'] >= 90): ?>
                                            <span style="color:#27ae60;font-weight:bold;"><?php echo $pl['kyc_match_score']; ?>%</span>
                                        <?php else: ?>
                                            <span style="color:#f39c12;font-weight:bold;"><?php echo $pl['kyc_match_score']; ?>%</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success approve-btn" data-id="<?php echo $pl['listing_id']; ?>" style="border-radius:20px;padding:4px 12px;font-size:12px;">Approve</button>
                                        <button class="btn btn-sm btn-danger reject-btn" data-id="<?php echo $pl['listing_id']; ?>" style="border-radius:20px;padding:4px 12px;font-size:12px;margin-left:5px;">Reject</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pending_listings)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px;color:#888;">No pending listings in the queue.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

            <!-- Disputes (Admin Escalations) -->
            <?php
                // Fetch escalated returns
                $stmt_disp = $db->con->prepare("SELECT t.*, p.item_name, p.item_image, u.first_name as buyer_first, u.last_name as buyer_last, s.first_name as seller_first, s.last_name as seller_last FROM `transaction` t JOIN `product` p ON t.item_id = p.item_id JOIN `user` u ON t.buyer_id = u.user_id LEFT JOIN `user` s ON t.seller_id = s.user_id WHERE t.order_status = 'return_escalated_admin'");
                $stmt_disp->execute();
                $disputes = $stmt_disp->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt_disp->close();
            ?>
            <div style="margin-top:50px;">
                <h3 style="color:#2b3b4e;font-weight:700;margin-bottom:20px;">Disputes (Escalated Returns)</h3>
                <div class="table-responsive">
                    <table class="table admin-table" id="disputesTable">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Seller</th>
                                <th>Item Details</th>
                                <th>Reason & Condition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disputes as $disp): ?>
                                <tr id="dispute-row-<?php echo $disp['transaction_id']; ?>">
                                    <td style="font-weight:600;">
                                        <?php echo htmlspecialchars($disp['buyer_first'] . ' ' . $disp['buyer_last']); ?>
                                    </td>
                                    <td style="font-weight:600;">
                                        <?php echo $disp['seller_id'] ? htmlspecialchars($disp['seller_first'] . ' ' . $disp['seller_last']) : 'System'; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;">
                                            <img src="<?php echo htmlspecialchars(str_replace(' ', '%20', $disp['item_image'])); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">
                                            <div>
                                                <div style="font-weight:600;"><?php echo htmlspecialchars($disp['item_name']); ?></div>
                                                <div style="font-size:12px;color:#E44C4C;font-weight:700;">R<?php echo number_format($disp['amount'], 2); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:13px;color:#555;"><strong>Reason:</strong> <?php echo htmlspecialchars($disp['return_reason']); ?></div>
                                        <div style="font-size:13px;color:#555;"><strong>Condition:</strong> <?php echo htmlspecialchars($disp['return_condition']); ?></div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="handleDispute(<?php echo $disp['transaction_id']; ?>, 'admin_accept')" style="border-radius:20px;padding:4px 12px;font-size:12px;">Accept (Refund Buyer)</button>
                                        <button class="btn btn-sm btn-danger" onclick="handleDispute(<?php echo $disp['transaction_id']; ?>, 'admin_deny')" style="border-radius:20px;padding:4px 12px;font-size:12px;margin-left:5px;">Deny</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($disputes)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:40px;color:#888;">No active disputes.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- User Metrics Modal -->
<div class="modal fade" id="userMetricsModal" tabindex="-1" role="dialog" aria-labelledby="userMetricsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:500px;">
    <div class="modal-content" style="border-radius:12px;border:none;">
      <div class="modal-header" style="background:#f8f9fa;border-bottom:1px solid #eee;border-radius:12px 12px 0 0;">
        <h5 class="modal-title font-baloo" id="userMetricsModalLabel" style="font-weight:700;font-size:20px;">User Metrics</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-22px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding:30px;">
        <div id="metrics-loading" style="text-align:center;padding:20px;color:#888;">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading metrics...
        </div>
        <div id="metrics-content" style="display:none;">
            <div class="row text-center mb-4">
                <div class="col-xs-6 border-right">
                    <div style="font-size:32px;font-weight:800;color:#00A5C4;" id="m_bought">0</div>
                    <div style="color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Products Bought</div>
                </div>
                <div class="col-xs-6">
                    <div style="font-size:32px;font-weight:800;color:#27ae60;" id="m_sold">0</div>
                    <div style="color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Products Sold</div>
                </div>
            </div>
            <div style="background:#f8f9fa;padding:15px;border-radius:8px;text-align:center;">
                <div style="color:#888;font-size:13px;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px;">Preferred Category</div>
                <div style="font-size:24px;font-weight:700;color:#E44C4C;" id="m_category">None</div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="admin.js"></script>
<script>
async function handleDispute(id, action) {
    if (!confirm("Are you sure you want to " + (action === 'admin_accept' ? "ACCEPT (Refund Buyer & Deduct Seller)" : "DENY") + " this dispute?")) return;
    
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
</body>
</html>
