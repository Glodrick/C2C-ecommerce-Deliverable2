<?php
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Digital Wallet';
$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_funds'])) {
    $amount = (float)$_POST['amount'];
    if ($amount > 0) {
        $user_obj = new User($db);
        if ($user_obj->addFunds($current_user_id, $amount)) {
            $msg = "Successfully added R" . number_format($amount, 2) . " to your wallet.";
            $msg_type = "success";
        } else {
            $msg = "Failed to add funds.";
            $msg_type = "danger";
        }
    } else {
        $msg = "Invalid amount.";
        $msg_type = "danger";
    }
}

$user_obj = new User($db);
$current_balance = $user_obj->getWalletBalance($current_user_id);

require_once 'header.php';
?>

<section class="container mt-5 mb-5" style="max-width:600px;">
    <div style="background:#fff;padding:40px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
        <h2 class="text-center font-baloo" style="font-weight:800;color:#333;margin-bottom:10px;">
            <i class="fas fa-wallet" style="color:#00A5C4;margin-right:10px;"></i>Digital Wallet
        </h2>
        <p class="text-center font-rale text-muted mb-4">Manage your funds to buy items instantly.</p>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msg_type; ?> font-rale" style="border-radius:8px;font-size:14px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div style="background:#f8f9fa;padding:30px;border-radius:12px;text-align:center;margin-bottom:30px;">
            <h5 class="font-rubik text-muted" style="margin-bottom:10px;font-weight:600;">Current Balance</h5>
            <div style="font-size:48px;font-weight:800;color:#E44C4C;font-family:'Baloo Thambi 2', cursive;">
                R<?php echo number_format($current_balance, 2); ?>
            </div>
        </div>

        <h5 class="font-rubik" style="font-weight:600;color:#555;margin-bottom:15px;">Add Mock Credits</h5>
        <form action="wallet.php" method="POST">
            <input type="hidden" name="add_funds" value="1">
            <div class="row">
                <div class="col-xs-4 mb-3">
                    <button type="submit" name="amount" value="100" class="btn btn-outline-secondary btn-block font-baloo" 
                            style="border-radius:20px;font-size:16px;font-weight:700;">+ R100</button>
                </div>
                <div class="col-xs-4 mb-3">
                    <button type="submit" name="amount" value="500" class="btn btn-outline-secondary btn-block font-baloo" 
                            style="border-radius:20px;font-size:16px;font-weight:700;">+ R500</button>
                </div>
                <div class="col-xs-4 mb-3">
                    <button type="submit" name="amount" value="1000" class="btn btn-outline-secondary btn-block font-baloo" 
                            style="border-radius:20px;font-size:16px;font-weight:700;">+ R1000</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once 'footer.php'; ?>
