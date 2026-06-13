<?php
// functions.php must be required before header.php
// It sets $cart_count, $current_user_id, etc.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GloMart — Buy and sell the latest tech: phones, computers, peripherals, and accessories.">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | GloMart' : 'GloMart — Tech Marketplace'; ?></title>

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha256-UhQQ4fxEeABh4JrcmAJ1+16id/1dnlOEVCFOxDef9Lw=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha256-kksNxjDRxd/5+jGurZUJd1sdR2v+ClrCl3svESBaJqw=" crossorigin="anonymous"/>
    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <!-- Site Stylesheet -->
    <link rel="stylesheet" href="<?php echo $root_path ?? ''; ?>style.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>

<body>

<header id="header">
    <!-- ── Top Strip ─────────────────────────────────────────── -->
    <div class="custom-top-strip font-rale font-size-12">
        <div>
            <span class="text-black-50 font-size-10">Welcome :)</span>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="font-size-10 color-primary font-weight-bold">
                    <?php echo htmlspecialchars($_SESSION['user_first_name'] ?? 'User'); ?>
                </span>
                <a href="<?php echo $root_path ?? ''; ?>track_orders.php">MY ORDERS</a>
                <a href="<?php echo $root_path ?? ''; ?>wallet.php">WALLET</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'seller'): ?>
                    <a href="<?php echo $root_path ?? ''; ?>my_listings.php">MY LISTINGS</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['role']) && !in_array($_SESSION['role'], ['seller','admin'])): ?>
                    <a href="<?php echo $root_path ?? ''; ?>become_seller.php">BECOME A SELLER</a>
                <?php endif; ?>
                <a href="<?php echo $root_path ?? ''; ?>logout.php" style="color:#E44C4C;">LOG OUT</a>
            <?php else: ?>
                <a href="<?php echo $root_path ?? ''; ?>login.php">SIGN IN</a>
            <?php endif; ?>
        </div>
        <div>
            <a href="<?php echo $root_path ?? ''; ?>index.php#sale-section">ON SALE</a>
            <a href="<?php echo $root_path ?? ''; ?>shop.php">BRANDS</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'seller'): ?>
                <a href="<?php echo $root_path ?? ''; ?>add_listing.php">SELL</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $root_path ?? ''; ?>admin.php">ADMIN</a>
            <?php endif; ?>
            <a href="#">CONTACT</a>
            <!-- Cart Icon with badge -->
            <a href="<?php echo $root_path ?? ''; ?>cart.php" style="position:relative;" id="cart-icon-link">
                <span class="font-size-16"><i class="fas fa-shopping-cart"></i></span>
                <span class="num-items cart-badge"
                      style="position:absolute;top:-5px;right:-10px;border-radius:50%;
                             background-color:#E44C4C;color:white;width:15px;height:15px;
                             display:flex;justify-content:center;align-items:center;font-size:10px;">
                    <?php echo $cart_count; ?>
                </span>
            </a>
        </div>
    </div>

    <!-- ── Main Navigation ───────────────────────────────────── -->
    <nav class="custom-navbar">
        <div class="custom-navbar-container">
            <!-- Brand -->
            <a class="font-weight-bold color-primary"
               href="<?php echo $root_path ?? ''; ?>index.php"
               style="font-size:32px;margin-right:20px;text-decoration:none;">GloMart</a>

            <!-- Search Form -->
            <form class="custom-search-form" action="<?php echo $root_path ?? ''; ?>index.php" method="GET">
                <div class="custom-search-input-group">
                    <span class="custom-search-icon"><i class="fas fa-search"></i></span>
                    <input class="custom-search-input font-rale"
                           type="search" name="q"
                           placeholder="Search for anything"
                           aria-label="Search"
                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                </div>
                <button class="custom-search-btn color-primary-bg" type="submit">Search</button>
            </form>
        </div>
    </nav>
</header>

<main id="main-site">