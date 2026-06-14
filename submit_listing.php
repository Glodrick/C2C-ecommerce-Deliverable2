<?php
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$seller_id = $_SESSION['user_id'];
$item_name = $_POST['item_name'] ?? '';
$item_price = $_POST['item_price'] ?? 0;
$item_category = $_POST['item_category'] ?? '';
$item_quantity = isset($_POST['item_quantity']) ? (int)$_POST['item_quantity'] : 1;
$item_description = $_POST['item_description'] ?? '';
$idNumber = $_POST['idNumber'] ?? '';

if (empty($item_name) || empty($item_price) || empty($item_category)) {
    echo json_encode(['success' => false, 'message' => 'Missing required item fields']);
    exit;
}

if (empty($idNumber) || strlen($idNumber) !== 13 || !is_numeric($idNumber)) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID number provided.']);
    exit;
}

$kycData = verifyKycIdentity($idNumber);
if (!isset($kycData['status']) || $kycData['status'] !== 'verified') {
    $errorMsg = $kycData['message'] ?? 'Unknown error';
    echo json_encode(['success' => false, 'message' => 'KYC verification failed: ' . $errorMsg]);
    exit;
}

$kyc_status = $kycData['status'];
$kyc_match_score = $kycData['match_score'] ?? 0;

$item_image = '';
if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'assets/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_ext = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
    $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
    $target_file = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
        $item_image = './' . $target_file;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Image upload is required or upload failed.']);
    exit;
}

$listing_obj = new Listing($db);
$saved = $listing_obj->addPendingListing(
    $seller_id, 
    $item_name, 
    $item_price, 
    $item_category,
    $item_quantity,
    $item_description, 
    $item_image, 
    $kyc_status, 
    $kyc_match_score, 
    $kyc_data
);

if ($saved) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database insertion failed.']);
}
