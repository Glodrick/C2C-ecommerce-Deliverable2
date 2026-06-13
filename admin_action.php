<?php
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_metrics') {
    $target_user_id = (int)($_GET['user_id'] ?? 0);
    if ($target_user_id > 0) {
        $metrics = $user_obj->getUserMetrics($target_user_id);
        echo json_encode(['success' => true, 'metrics' => $metrics]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
    exit;
}

if ($action === 'approve_listing') {
    $listing_id = (int)($_POST['listing_id'] ?? 0);
    if ($listing_id > 0) {
        $success = $listing->approveListing($listing_id);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid listing ID']);
    }
    exit;
}

if ($action === 'reject_listing') {
    $listing_id = (int)($_POST['listing_id'] ?? 0);
    if ($listing_id > 0) {
        $success = $listing->rejectListing($listing_id);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid listing ID']);
    }
    exit;
}

if ($action === 'approve_seller') {
    $target_user_id = (int)($_POST['user_id'] ?? 0);
    if ($target_user_id > 0) {
        $stmt = $db->con->prepare("UPDATE `user` SET `role` = 'seller', `status` = 'active' WHERE `user_id` = ?");
        $stmt->bind_param('i', $target_user_id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
    exit;
}

if ($action === 'reject_seller') {
    $target_user_id = (int)($_POST['user_id'] ?? 0);
    if ($target_user_id > 0) {
        $stmt = $db->con->prepare("UPDATE `user` SET `status` = 'active' WHERE `user_id` = ?");
        $stmt->bind_param('i', $target_user_id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);

