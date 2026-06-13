$(document).ready(function() {

    // ── Owl Carousels ─────────────────────────────────────────────
    if ($("#banner-area .owl-carousel").length) {
        $("#banner-area .owl-carousel").owlCarousel({
            dots: true,
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000
        });
    }

    // Common carousel config
    const productCarouselConfig = {
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
    };

    if ($("#brandNewCarousel").length) {
        $("#brandNewCarousel").owlCarousel(productCarouselConfig);
    }
    
    if ($("#saleCarousel").length) {
        $("#saleCarousel").owlCarousel(productCarouselConfig);
    }

    if ($("#relatedCarousel").length) {
        $("#relatedCarousel").owlCarousel(productCarouselConfig);
    }

    // ── AJAX Cart Functionality ───────────────────────────────────

    // Update cart badge utility
    function updateCartBadge(count) {
        $('.cart-badge').text(count);
    }

    // Add to Cart (Product Cards / Detail Page)
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const itemId = btn.data('item-id');
        
        // Simple visual feedback
        const originalContent = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i>');
        btn.prop('disabled', true);

        $.ajax({
            url: 'cart_action.php',
            type: 'POST',
            data: { action: 'add', item_id: itemId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateCartBadge(res.cart_count);
                    // Temporarily show success icon
                    btn.html('&#10003;');
                    setTimeout(() => {
                        btn.html(originalContent);
                        btn.prop('disabled', false);
                    }, 1500);
                } else {
                    if (res.require_login) {
                        window.location.href = 'login.php';
                    } else {
                        alert(res.message);
                        btn.html(originalContent);
                        btn.prop('disabled', false);
                    }
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.html(originalContent);
                btn.prop('disabled', false);
            }
        });
    });

    // Buy Now (Product Detail Page)
    $(document).on('click', '.buy-now-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const itemId = btn.data('item-id');
        
        const originalContent = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        btn.prop('disabled', true);

        $.ajax({
            url: 'cart_action.php',
            type: 'POST',
            data: { action: 'buy_now', item_id: itemId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.location.href = 'checkout.php';
                } else {
                    if (res.require_login) {
                        window.location.href = 'login.php';
                    } else {
                        alert(res.message);
                        btn.html(originalContent);
                        btn.prop('disabled', false);
                    }
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.html(originalContent);
                btn.prop('disabled', false);
            }
        });
    });

    // Cart Quantity Increment/Decrement
    $(document).on('click', '.cart-qty-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const itemId = btn.data('item-id');
        const input = $(`.cart-qty-input[data-item-id="${itemId}"]`);
        let currentQty = parseInt(input.val());

        if (btn.hasClass('btnUp')) {
            currentQty++;
        } else if (btn.hasClass('btnDown')) {
            currentQty--;
        }

        if (currentQty < 1) currentQty = 1; // Don't drop below 1 here (use remove btn to delete)
        if (currentQty > 99) currentQty = 99;

        // Visual update immediately
        input.val(currentQty);

        // AJAX update
        $.ajax({
            url: 'cart_action.php',
            type: 'POST',
            data: { action: 'update', item_id: itemId, quantity: currentQty },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateCartBadge(res.cart_count);
                    // Update totals
                    $('#cart-subtotal').text('R' + res.cart_total);
                    $('#cart-total').text('R' + res.cart_total);
                    // Update line total
                    $(`.cart-line-total[data-unit-price]`).each(function() {
                        const row = $(this).closest('.cart-item-row');
                        if (row.data('item-id') == itemId) {
                            $(this).text('R' + res.line_total);
                        }
                    });
                } else {
                    alert(res.message);
                }
            }
        });
    });

    // Remove Item from Cart AJAX
    $(document).on('click', '.remove-item-link', function(e) {
        e.preventDefault();
        const link = $(this);
        const itemId = link.closest('.cart-item-row').data('item-id');
        
        if (!confirm('Remove this item from cart?')) return;

        $.ajax({
            url: 'cart_action.php',
            type: 'POST',
            data: { action: 'remove', item_id: itemId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateCartBadge(res.cart_count);
                    $('#cart-subtotal').text('R' + res.cart_total);
                    $('#cart-total').text('R' + res.cart_total);
                    // Slide up and remove the row
                    link.closest('.cart-item-row').slideUp(300, function() {
                        $(this).remove();
                        if (res.cart_count === 0) {
                            location.reload(); // Reload to show empty cart state
                        }
                    });
                } else {
                    alert(res.message);
                }
            }
        });
    });

    // ── Category Grid Filters ─────────────────────────────────────
    $('.filter-pill').on('click', function() {
        const pill = $(this);
        // Highlight active
        $('.filter-pill').removeClass('active-filter').css('border-color', '');
        pill.addClass('active-filter');
        
        const filterVal = pill.data('filter');
        const brandVal = pill.data('brand');
        
        $('.custom-product-card').each(function() {
            const card = $(this);
            // Since we don't have JS data array anymore, we check DOM for hints
            // Alternatively, PHP could output data attributes. Let's do simple text search.
            const textContent = card.text().toLowerCase();
            const isSale = card.find('.original-price').length > 0;
            const isNew = card.find('.in-stock').text().includes('Brand New') || true; // In our DB, new is a flag, we might not render it. We'll simulate it for now.
            
            let show = false;
            if (filterVal === 'all') show = true;
            else if (filterVal === 'sale' && isSale) show = true;
            else if (filterVal === 'new') show = true; // Fallback
            else if (brandVal && textContent.includes(brandVal.toLowerCase())) show = true;

            if (show) card.show();
            else card.hide();
        });
    });
});
