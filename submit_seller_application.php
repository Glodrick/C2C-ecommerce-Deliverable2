<?php
require_once 'functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_number = $_POST['id_number'] ?? '';
if (empty($id_number) || strlen($id_number) !== 13 || !is_numeric($id_number)) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID number provided.']);
    exit;
}

// Perform KYC verification securely on the backend
$kycData = verifyKycIdentity($id_number);

if (!isset($kycData['status']) || $kycData['status'] !== 'verified') {
    $errorMsg = $kycData['message'] ?? 'Unknown error';
    echo json_encode([
        'success' => false,
        'message' => 'KYC verification failed: ' . $errorMsg
    ]);
    exit;
}

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
