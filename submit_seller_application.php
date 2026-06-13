<?php
require_once 'functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$kyc_status      = $_POST['kyc_status'] ?? 'unknown';
$kyc_match_score = (int)($_POST['kyc_match_score'] ?? 0);

// Update user status to pending
$stmt = $db->con->prepare("UPDATE `user` SET `status` = 'pending' WHERE `user_id` = ? AND `status` != 'pending'");
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Your seller application has been submitted! An admin will review it shortly.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Application already submitted or could not be processed.'
    ]);
}
