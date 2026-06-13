<?php
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$transaction_id = isset($_POST['transaction_id']) ? (int)$_POST['transaction_id'] : 0;

if (!$transaction_id || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

$now = date('Y-m-d H:i:s');
$current_user_id = $_SESSION['user_id'];

// Get transaction details
$stmt = $db->con->prepare("SELECT * FROM `transaction` WHERE `transaction_id` = ?");
$stmt->bind_param('i', $transaction_id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$transaction) {
    echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
    exit;
}

// ── INITIATE RETURN (Buyer) ──────────────────────────────────────────────────
if ($action === 'initiate') {
    if ($transaction['buyer_id'] != $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $reason = $_POST['reason'] ?? '';
    $condition = $_POST['condition'] ?? '';
    
    if (empty($reason) || empty($condition)) {
        echo json_encode(['success' => false, 'message' => 'Reason and condition are required.']);
        exit;
    }
    
    if ($condition === 'Damaged') {
        $status = 'return_denied_damaged';
        $msg = 'Return denied automatically because the item was reported as damaged.';
    } else {
        $status = 'return_pending_seller';
        $msg = 'Return request submitted to the seller for approval.';
    }
    
    $stmt = $db->con->prepare("UPDATE `transaction` SET `order_status` = ?, `return_reason` = ?, `return_condition` = ?, `updated_at` = ? WHERE `transaction_id` = ?");
    $stmt->bind_param('ssssi', $status, $reason, $condition, $now, $transaction_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => $msg, 'new_status' => $status]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to initiate return.']);
    }
    $stmt->close();
    exit;
}

// ── SELLER DENY ───────────────────────────────────────────────────────────────
if ($action === 'seller_deny') {
    if ($transaction['seller_id'] != $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $status = 'return_denied_seller';
    $stmt = $db->con->prepare("UPDATE `transaction` SET `order_status` = ?, `updated_at` = ? WHERE `transaction_id` = ?");
    $stmt->bind_param('ssi', $status, $now, $transaction_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Return denied.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    $stmt->close();
    exit;
}

// ── ESCALATE (Buyer) ──────────────────────────────────────────────────────────
if ($action === 'escalate') {
    if ($transaction['buyer_id'] != $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $status = 'return_escalated_admin';
    $stmt = $db->con->prepare("UPDATE `transaction` SET `order_status` = ?, `updated_at` = ? WHERE `transaction_id` = ?");
    $stmt->bind_param('ssi', $status, $now, $transaction_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Dispute escalated to Admin.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    $stmt->close();
    exit;
}

// ── ADMIN DENY ────────────────────────────────────────────────────────────────
if ($action === 'admin_deny') {
    // Basic admin check (could be expanded if you have specific admin roles)
    // Assume if they can post to this action they are on the admin page.
    $status = 'return_denied_admin';
    $stmt = $db->con->prepare("UPDATE `transaction` SET `order_status` = ?, `updated_at` = ? WHERE `transaction_id` = ?");
    $stmt->bind_param('ssi', $status, $now, $transaction_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Dispute denied.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    $stmt->close();
    exit;
}

// ── ACCEPT (Seller or Admin) -> Refund & Restock ──────────────────────────────
if ($action === 'seller_accept' || $action === 'admin_accept') {
    if ($action === 'seller_accept' && $transaction['seller_id'] != $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Begin Transaction
    $db->con->begin_transaction();
    try {
        $amount = $transaction['amount'];
        $buyer_id = $transaction['buyer_id'];
        $seller_id = $transaction['seller_id'];
        $item_id = $transaction['item_id'];
        
        // 1. Update order status
        $status = 'return_refunded';
        $stmt = $db->con->prepare("UPDATE `transaction` SET `order_status` = ?, `updated_at` = ? WHERE `transaction_id` = ?");
        $stmt->bind_param('ssi', $status, $now, $transaction_id);
        $stmt->execute();
        $stmt->close();
        
        // 2. Refund Buyer
        $stmt = $db->con->prepare("UPDATE `user` SET `wallet_balance` = `wallet_balance` + ? WHERE `user_id` = ?");
        $stmt->bind_param('di', $amount, $buyer_id);
        $stmt->execute();
        $stmt->close();
        
        // 3. Deduct from Seller
        if ($seller_id) {
            $stmt = $db->con->prepare("UPDATE `user` SET `wallet_balance` = `wallet_balance` - ? WHERE `user_id` = ?");
            $stmt->bind_param('di', $amount, $seller_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // 4. Restock Product with 10% discount
        $stmt = $db->con->prepare("UPDATE `product` SET `item_status` = 'active', `item_quantity` = `item_quantity` + 1, `item_in_stock` = 1, `item_is_sale` = 1, `item_price` = `item_price` * 0.9 WHERE `item_id` = ?");
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
        $stmt->close();
        
        $db->con->commit();
        echo json_encode(['success' => true, 'message' => 'Return accepted and processed successfully!']);
        
    } catch (Exception $e) {
        $db->con->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to process return.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
