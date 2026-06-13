const allProductsList = [
    // Phones
    {
        name: "Apple iPhone 17",
        details: ["256GB", "A18 Chip", "Unlocked"],
        price: 19999.99,
        originalPrice: null,
        dateAdded: "2024-11-01",
        image: "./assets/phones/iphone17.jpg",
        inStock: true,
        category: "phones",
        brand: "Apple",
        isBrandNew: true
    },
    {
        name: "Samsung Galaxy S25",
        details: ["512GB", "Snapdragon 8 Gen 3", "5G"],
        price: 24999.99,
        originalPrice: null,
        dateAdded: "2024-11-15",
        image: "./assets/phones/samsung S25.png",
        inStock: true,
        category: "phones",
        brand: "Samsung",
        isBrandNew: true
    },
    {
        name: "Apple iPhone X Space Grey",
        details: ["256GB", "A11 Bionic", "Unlocked"],
        price: 4999.99,
        originalPrice: 8999.99,
        dateAdded: "2025-01-10",
        image: "./assets/phones/Apple+iPhone+X+Space+Grey+256GB-4122078476.jpg",
        inStock: true,
        category: "phones",
        brand: "Apple",
        isSale: true
    },
    {
        name: "Samsung Galaxy Note 20",
        details: ["256GB", "Exynos 990", "5G"],
        price: 6999.99,
        originalPrice: 11999.99,
        dateAdded: "2025-02-05",
        image: "./assets/phones/note20.jpg",
        inStock: true,
        category: "phones",
        brand: "Samsung",
        isSale: true
    },
    {
        name: "Samsung Galaxy A72",
        details: ["128GB", "Snapdragon 720G", "4G"],
        price: 4599.99,
        originalPrice: 6599.99,
        dateAdded: "2025-03-15",
        image: "./assets/phones/Samsung A72.jpg",
        inStock: true,
        category: "phones",
        brand: "Samsung",
        isSale: true
    },
    // PC Parts
    {
        name: "Intel Core i7",
        details: ["Processor", "14700K", "20 Cores"],
        price: 8999.99,
        originalPrice: null,
        dateAdded: "2024-12-01",
        image: "./assets/computerParts/IntelCorei7.png",
        inStock: true,
        category: "pc parts",
        brand: "Intel",
        isBrandNew: true
    },
    {
        name: "ZOTAC RTX 5070 AMP White",
        details: ["Graphics Card", "12GB GDDR6X", "PCIe 4.0"],
        price: 13999.99,
        originalPrice: null,
        dateAdded: "2024-12-10",
        image: "./assets/computerParts/ZOTAC GAMING GeForce RTX 5070 AMP White Edition.jpg",
        inStock: true,
        category: "pc parts",
        brand: "ZOTAC",
        isBrandNew: true
    },
    {
        name: "MEK 5080 Gaming PC",
        details: ["PreBuild", "RTX 5080", "32GB RAM"],
        price: 49999.99,
        originalPrice: null,
        dateAdded: "2024-12-20",
        image: "./assets/computerParts/MEK 5080 Gaming PC_PreBuild.jpg",
        inStock: true,
        category: "pc parts",
        brand: "MEK",
        isBrandNew: true
    },
    {
        name: "ZOTAC GAMING GeForce RTX 8GB",
        details: ["Graphics Card", "8GB GDDR6", "PCIe 4.0"],
        price: 5499.99,
        originalPrice: 7999.99,
        dateAdded: "2025-03-01",
        image: "./assets/computerParts/ZOTAC_GamingGeForceRTX8GB.jpg",
        inStock: true,
        category: "pc parts",
        brand: "ZOTAC",
        isSale: true
    },
    // Peripherals
    {
        name: "Airpods 4",
        details: ["Wireless", "ANC", "H2 Chip"],
        price: 3499.99,
        originalPrice: 4499.99,
        dateAdded: "2025-02-20",
        image: "./assets/airpod4.jpg",
        inStock: true,
        category: "peripherals",
        brand: "Apple",
        isSale: true
    },
    {
        name: "TeckNet Wireless Keyboard",
        details: ["Wireless", "Ergonomic", "Black"],
        price: 599.99,
        originalPrice: null,
        dateAdded: "2024-10-05",
        image: "./assets/peripherals/TeckNetWireless Keyboard.webp",
        inStock: true,
        category: "peripherals",
        brand: "TeckNet",
        isBrandNew: true
    },
    {
        name: "Wireless JBL Headphones",
        details: ["Over-ear", "ANC", "Black"],
        price: 1299.99,
        originalPrice: null,
        dateAdded: "2024-11-20",
        image: "./assets/peripherals/wirelessJBL.jpg",
        inStock: true,
        category: "peripherals",
        brand: "JBL",
        isBrandNew: true
    },
    // Accessories
    {
        name: "SuperPuff iPhone 13 Case",
        details: ["Silicone", "Puff Design", "Red"],
        price: 299.99,
        originalPrice: null,
        dateAdded: "2024-09-10",
        image: "./assets/covers/SuperPuffiPhone13.jpg",
        inStock: true,
        category: "accessories",
        brand: "Generic",
        isBrandNew: true
    },
    {
        name: "Generic Clear Cover",
        details: ["Clear", "Shockproof"],
        price: 150.00,
        originalPrice: null,
        dateAdded: "2024-08-15",
        image: "./assets/covers/cover.jpg",
        inStock: true,
        category: "accessories",
        brand: "Generic"
    },
    {
        name: "Large Desk Mat",
        details: ["Leather", "Large", "Waterproof"],
        price: 499.99,
        originalPrice: 650.00,
        dateAdded: "2024-10-25",
        image: "./assets/covers/deskMat.jpg",
        inStock: true,
        category: "accessories",
        brand: "Generic",
        isSale: true
    },
    {
        name: "iPhone 13 Leather Cover",
        details: ["Leather", "MagSafe", "Brown"],
        price: 399.99,
        originalPrice: null,
        dateAdded: "2024-11-05",
        image: "./assets/covers/iPhone13Cover.jpg",
        inStock: true,
        category: "accessories",
        brand: "Generic"
    },
    {
        name: "Samsung Galaxy S25 Case",
        details: ["Rugged", "Stand", "Black"],
        price: 349.99,
        originalPrice: null,
        dateAdded: "2024-12-15",
        image: "./assets/covers/samsungGalaxyS25.jpg",
        inStock: true,
        category: "accessories",
        brand: "Generic"
    }
];

const brandNewProducts = allProductsList.filter(p => p.isBrandNew);
const saleProducts = allProductsList.filter(p => p.isSale);

function createProductCard(product) {
    let priceHtml = '';
    if (product.originalPrice) {
        priceHtml = `<span class="prodcutPrice">R${product.price.toFixed(2)}</span> <span class="original-price">R${product.originalPrice.toFixed(2)}</span>`;
    } else {
        priceHtml = `<span class="prodcutPrice">R${product.price.toFixed(2)}</span>`;
    }
    
    let detailsHtml = product.details.join(' <span class="detail-dot">&bull;</span> ');

    return `
    <div class="product custom-product-card">
        <img src="${product.image}" alt="${product.name}" class="custom-thumbnail">
        <div class="productInfo custom-product-info">
            <h4 class="productTitle custom-product-title">${product.name}</h4>
            <div class="product-details">${detailsHtml}</div>
            <div class="product-stock">In-Stock</div>
            <div class="price-row">
                <div class="price-container">
                    ${priceHtml}
                </div>
                <a href="javascript:void(0)" class="cart-square-btn"><i class="fas fa-shopping-cart"></i></a>
            </div>
        </div>
    </div>
    `;
}

function renderProducts() {
    const brandNewContainer = $("#brandNewCarousel");
    const saleContainer = $("#saleCarousel");
    const relatedContainer = $("#relatedCarousel");

    if (brandNewContainer.length) {
        brandNewProducts.forEach(product => {
            brandNewContainer.append(createProductCard(product));
        });
        brandNewContainer.owlCarousel({
            loop: false,
            nav: true,
            dots: false,
            margin: 10,
            responsive: {
                0:    { items: 2, margin: 8 },
                480:  { items: 2, margin: 12 },
                768:  { items: 3, margin: 15 },
                1000: { items: 4, margin: 20 },
                1200: { items: 5, margin: 20 }
            }
        });
    }

    if (saleContainer.length) {
        saleProducts.forEach(product => {
            saleContainer.append(createProductCard(product));
        });
        saleContainer.owlCarousel({
            loop: false,
            nav: true,
            dots: false,
            margin: 10,
            responsive: {
                0:    { items: 2, margin: 8 },
                480:  { items: 2, margin: 12 },
                768:  { items: 3, margin: 15 },
                1000: { items: 4, margin: 20 },
                1200: { items: 5, margin: 20 }
            }
        });
    }
    
    if (relatedContainer.length) {
        allProductsList.slice(0, 8).forEach(product => {
            relatedContainer.append(createProductCard(product));
        });
        relatedContainer.owlCarousel({
            loop: false,
            nav: true,
            dots: false,
            margin: 10,
            responsive: {
                0:    { items: 2, margin: 8 },
                480:  { items: 2, margin: 12 },
                768:  { items: 3, margin: 15 },
                1000: { items: 4, margin: 20 },
                1200: { items: 5, margin: 20 }
            }
        });
    }
}

function renderCategoryGrid(categoryName) {
    const gridContainer = $("#categoryGrid");
    if (gridContainer.length === 0) return;

    const filtered = allProductsList.filter(p => p.category === categoryName);
    
    filtered.forEach(product => {
        gridContainer.append(createProductCard(product));
    });
}

$(document).ready(function(){

    // banner owl carousel
    if ($("#banner-area .owl-carousel").length) {
        $("#banner-area .owl-carousel").owlCarousel({
            dots: true,
            items: 1
        });
    }

    if ($("#categoryOverview .owl-carousel").length) {
        $("#categoryOverview .owl-carousel").owlCarousel({
            loop:false,
            nav:true,
            dots: false,
            responsive:{ 0:{ items:1 }, 600:{ items:3 }, 1000:{ items:5 } }
        });
    }

    renderProducts();

    const gridEl = $("#categoryGrid");
    if (gridEl.length > 0) {
        const cat = gridEl.data("category");
        renderCategoryGrid(cat);
    }

    if ($(".products .owl-carousel").length && !$(".products .owl-carousel").hasClass('owl-loaded')) {
        $(".products .owl-carousel").owlCarousel({
            loop:false,
            nav:true,
            dots: false,
            responsive:{ 0:{ items:1 }, 600:{ items:3 }, 1000:{ items:5 } }
        });
    }
});

let $btnUp= $(".counter .btnUp");
let $btnDown = $(".counter .btnDown");

$btnUp.click(function(e){
    let $input = $(`.btnInput[data-id='${$(this).data("id")}']`);
    if($input.val()>= 1 && $input.val() <=9){
        $input.val(function(i,oldval){
            return ++oldval;
        });
    }
});

$btnDown.click(function(e){
    let $input = $(`.btnInput[data-id='${$(this).data("id")}']`);
    if($input.val()>1 && $input.val() <=10){
        $input.val(function(i, oldval){
            return --oldval;
        })
    }
});
