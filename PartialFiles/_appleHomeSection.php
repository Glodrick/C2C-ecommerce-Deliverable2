 <?php

 shuffle($product_shuffle);

 if($_SERVER['REQUEST_METHOD']=="POST"){
    if(isset($_POST['productSubmit'])){
 $cart->addToCart($_POST['user_id'],$_POST['item_id']);
    }
   
 }
 ?>
 
 <section id="appleBanner">
                <div class="col-lg-12">
                    <h2>Apple Products</h2>
                </div>
        <div class="brandBanner">
        </div>
        </section> 
    
        <section class="products">
                <div class="allProducts">
                    <div class="owl-carousel owl-theme">
                    <?php foreach ($product_shuffle as $item) {?>
                <div class="product">
                                       <img src="<?php echo $item['item_image']??"./assets/IPHONE7PLUS.png";?>" alt="iPhone 7 Plus" class="imgThumbnail">
                    <div class="productInfo">
                        <h4 class="productTitle"><?php echo $item['item_name'] ??"Unknown";?></h4>
                        <p class="prodcutPrice"><?php echo $item['item_price']??"0";?> </p>
                        <form method="post">
                            <input type="hidden" name="item_id" value="<?php echo $item['item_id']??'1';?>">
                            <input type="hidden" name="user_id" value="<?php echo 1;?>">
                            <button type="submit" name="productSubmit"class="productBtn">Add to Cart</button>
                        </form>
                        
                    </div>
                </div>
                 <?php }?>
            </div>
          
        </section>