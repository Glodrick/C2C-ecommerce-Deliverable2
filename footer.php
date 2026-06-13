</main><!-- /#main-site -->

<!-- ── Footer ───────────────────────────────────────────────── -->
<footer class="site-footer" style="background-color:#1a1a2e;color:#fff;padding:50px 0 0;">
    <div class="container">
        <div class="row" style="margin-bottom:30px;">
            <!-- Brand column -->
            <div class="col-md-6 mb-4" style="margin-bottom:30px;">
                <a href="<?php echo $root_path ?? ''; ?>index.php"
                   style="font-size:28px;font-weight:800;color:#00A5C4;text-decoration:none;">GloMart</a>
                <p class="font-rale" style="color:rgba(255,255,255,.55);font-size:14px;margin-top:12px;line-height:1.7;max-width:400px;">
                    Your trusted C2C tech marketplace. Buy and sell the latest smartphones, computers, peripherals, and accessories safely and securely. All transactions are protected through our integrated digital wallet and KYC verification systems.
                </p>
            </div>

            <!-- Buy & Sell column -->
            <div class="col-md-6 mb-4" style="margin-bottom:30px;">
                <h5 class="font-rubik" style="font-weight:700;margin-bottom:15px;font-size:15px;text-transform:uppercase;letter-spacing:1px;">Buy &amp; Sell</h5>
                <ul class="list-unstyled font-rale" style="line-height:2.2;font-size:14px;">
                    <li><a href="<?php echo $root_path ?? ''; ?>how_it_works.php" style="color:rgba(255,255,255,.55);text-decoration:none;">How it Works</a></li>
                    <li><a href="<?php echo $root_path ?? ''; ?>buyer_protection.php" style="color:rgba(255,255,255,.55);text-decoration:none;">Buyer Protection</a></li>
                    <li><a href="<?php echo $root_path ?? ''; ?>seller_guidelines.php" style="color:rgba(255,255,255,.55);text-decoration:none;">Seller Guidelines</a></li>
                    <li><a href="<?php echo $root_path ?? ''; ?>safe_trade_tips.php" style="color:rgba(255,255,255,.55);text-decoration:none;">Safe Trade Tips</a></li>
                    <li><a href="<?php echo $root_path ?? ''; ?>dispute_resolution.php" style="color:rgba(255,255,255,.55);text-decoration:none;">Dispute Resolution</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="row" style="border-top:1px solid rgba(255,255,255,.1);padding:20px 0;">
            <div class="col-12 text-center font-rale" style="font-size:13px;color:rgba(255,255,255,.4);">
                &copy; <?php echo date('Y'); ?> GloMart. All Rights Reserved. | A C2C Tech Marketplace.
            </div>
        </div>
    </div>
</footer>

<!-- ── Scripts ───────────────────────────────────────────────── -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha256-pTxD+DSzIwmwhOqTFN+DB+nHjO4iAsbgfyFq5K5bcE0="
        crossorigin="anonymous"></script>
<script src="<?php echo $root_path ?? ''; ?>app.js"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>

</body>
</html>