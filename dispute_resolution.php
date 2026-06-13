<?php
$page_title = 'Dispute Resolution';
require_once 'header.php';
?>

<section class="container mt-5 mb-5">
    <div style="background:#fff;padding:50px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
        <h1 class="font-baloo" style="font-weight:800;color:#00A5C4;margin-bottom:30px;">Dispute Resolution</h1>
        <div class="font-rale" style="line-height:1.8;color:#444;font-size:16px;">
            <p>Our dispute resolution process is designed to be fair, swift, and transparent. We step in when buyers and sellers cannot reach an agreement on their own.</p>

            <h4 style="margin-top:30px;color:#2b3b4e;font-weight:700;">Step 1: The Initial Return Request</h4>
            <p>If you are unsatisfied with an item, your first step is to click <strong>Return Order</strong> from the My Orders page. You must provide a valid reason and select the condition.</p>
            <ul>
                <li>If you mark it as <strong>Damaged</strong>, the return is automatically denied to protect sellers from fraud.</li>
                <li>If you mark it as <strong>Okay/Good</strong>, it is sent to the seller for approval.</li>
            </ul>

            <h4 style="margin-top:30px;color:#2b3b4e;font-weight:700;">Step 2: Seller Mediation</h4>
            <p>The seller has the opportunity to review the request from their dashboard. If they click <strong>Accept</strong>, the dispute is resolved immediately! Your wallet is refunded, the seller's wallet is deducted, and the item is restocked into the catalog at a 10% discount.</p>

            <h4 style="margin-top:30px;color:#2b3b4e;font-weight:700;">Step 3: Escalation to Admin</h4>
            <p>If the seller clicks <strong>Deny</strong>, you will see a <strong>Report / Escalate</strong> button appear next to the order. Clicking this sends the dispute directly to our Admin team.</p>

            <h4 style="margin-top:30px;color:#2b3b4e;font-weight:700;">Step 4: Final Ruling</h4>
            <p>Our Admins have a dedicated Dispute dashboard. They will review the buyer's reason and the seller's history. Admins hold the power to forcefully accept the return and initiate the wallet refund process, or permanently deny it.</p>
            
            <div style="margin-top:40px;padding:20px;background:#fff3cd;color:#856404;border-left:5px solid #ffeeba;border-radius:5px;">
                <strong>Note:</strong> Abuse of the dispute system (e.g., repeatedly escalating false claims) may result in your wallet being frozen or your account being suspended.
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
