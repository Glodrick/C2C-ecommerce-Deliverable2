<!DOCTYPE html>
<html lang="en">
<head>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha256-UhQQ4fxEeABh4JrcmAJ1+16id/1dnlOEVCFOxDef9Lw=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha256-kksNxjDRxd/5+jGurZUJd1sdR2v+ClrCl3svESBaJqw=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" /> 
    <link rel="stylesheet" href="./style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C2C Plat</title>

     <?php
   require ('functions.php');
  ?>
</head>

<body>

<header id="header">
    <div class="strip d-flex justify-content-between px-4 py-1 bg-light">
         <span class="font-rale font-size-10 text-black-50 m-0">Welcome :)</span>
        <a href="#" class="font-rale font-size-9">Sign In/Sign Up</a>
        <a href="#" class="font-rale font-size-8">ON SALE</a>
        <a href="#appleBanner" class="font-rale font-size-8">BRANDS</a>
        <a href="#" class="font-rale font-size-8">FAQ</a>
        <a href="#" class="font-rale font-size-8">CONTACT</a>
        <form action="#" class="font-size-14 font-rale" style="position: absolute; top: -4px; right: 0; padding: 10px;">
                      <a href="#" class="color-third">
                        <span class="font-size-16 px-2 text-white"><i class="fas fa-shopping-cart"></i></span>
                        <span class="num-items"style="border-radius: 50%; background-color:white; color:black; margin: -8x; border: 1px solid #ccc;">0</span>
                      </a>
                  </form>
        
    </div>
    <!--Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark color-primary-bg">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand color-third" href="#">Brand</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      
      <form class="navbar-form navbar-left">
       <div class="flex-grow-1 d-flex">
            <form class="form-inline flex-nowrap bg-light mx-0 mx-lg-auto rounded p-1">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
      </form>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
  
</nav>

<div class="cartBody">
        <div class="cartContainer">
            <div class="cartHeader">
    <h3 class="cartHeading">Shopping Cart</h3>
    <h5 class="cartAction">Remove all</h5>
</div>
  <?php
 foreach ($product->getData('cart') as $item) :
    $cart= $product->getProduct($item['item_id']);
    print_r($item);
    array_map(function($item){
?>
<div class="cartItems">
   
    <div class="imageBox">
        <img src="<?php echo $item['item_image']?? "./assets/IPHONE 8.jpg" ?>" alt="<?php echo $item['item_name']?>" class="imgThumbnail">
    </div>
    <div class="about">
        <h1 class="cartTitle"><?php echo $item['item_name'] ??"item"?></h1>
        <h3 class="cartInfo"><?php echo $item[`item_details`]?? "Unknown"?> </h3>
    </div>
    <div class="counter">
        <button data-id= "pro1" class="btnUp">+</button>
        <input  data-id= "pro1"type="text" class="btnInput" disabled value="1" placeholder="1" >
        <button data-id= "pro1" class="btnDown">-</button>
    </div>
    <div class="prices">
        <div class="amount"><?php echo $item['item_price']?></div>
        <div class="remove">Remove</div>
    </div> 
</div>
<?php
},$cart);
endforeach;
?>

    <hr class="cartHr">
    <div class="checkout">
        <div class="total">
            <div>
                <div class="subTotal">Sub-Total</div>
                <div class="items">2 items</div>
            </div>
            <div class="totalAmount">Something</div>
        </div>
        <button class="button">Checkout</button>
    </div>
        </div>
    </div>
</header>

 <main id="main-site">