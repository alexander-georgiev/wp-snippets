jQuery(document).ready(function($){
	$('.select-dropdown-wrapper li').click(function() {
		let name = $(this).html(),
			product_card = $(this).closest('.card-loop.product'),
			wrapper = $(this).closest('.select-dropdown-wrapper'),
			selected_var_div = wrapper.find('.selected-variation'),
			variations_wrapper = $(this).closest('.variations-wrapper'),
			variation_buttons = variations_wrapper.find('.variation-buttons');
		wrapper.find('.selected').removeClass('selected');
		$(this).addClass('selected');
		var selected_count = variation_buttons.find('.selected').length;
		selected_var_div.html(name);
		if (selected_count === variation_buttons.length) {
			var variations = [];
			let productId = product_card.find('.ajax_add_to_cart').data('product_id'),
				variations_el = variation_buttons.find('.selected');
			variations_el.each(function() {
				let key = $(this).data('attr-name'),
					value = $(this).data('attr-slug');
				var variation_obj = {
                    key: key,
                    value: value,
                }
				
				variations.push(variation_obj);
});
			$.ajax({
                    type: "post",
                    dataType: "json",
                    url: flatsomeVars.ajaxurl,
                    data: {
                        'action': 'child_variation_change',
                        'productId': productId,
						'variations': variations,
                    },
                    success: function (data) {                       
                        product_card.find('.ajax_add_to_cart').removeClass('disabled');					
						product_card.find('.ajax_add_to_cart').attr('data-variation_id', data.variation_id);
						product_card.find('.box-image > .image-none > a').html(data.image);
						product_card.find('.price-wrapper .price').html(data.price);						
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log(errorThrown);
						$('.message p').text('Error! Please, contact the support. ' + errorThrown);
						$('.message').addClass('error').removeClass('updated');
						$('.export-orders.disabled').removeClass('disabled');
					}
                });
		}
});
    function addVariationToCart() {
        off_canvas = 0;
        $('.product-type-variable .ajax_add_to_cart').click(function (e) {
            e.preventDefault();
            var is_disabled = $(this).hasClass('disabled');
			if (is_disabled) {
				alert('Bitte wählen Sie alle Varianten aus.');
				return false;
			}
			var variation_id = $(this).data('variation_id'),
                button = $(this);
		
            if (variation_id) {
                button.addClass("disabled loading");
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: flatsomeVars.ajaxurl,
                    data: {
                        'action': 'add_variation_to_cart_ajax',                        
						'variationId': variation_id,                        
                    },
                    success: function (data) {
                        if (off_canvas === 0) {
                            $(document).on('wc_fragments_refreshed', function () {
                                $('.header-cart-link.off-canvas-toggle').click();
                                off_canvas = 1;

                            });
                        }
                        $(document.body).trigger('wc_fragment_refresh');
                        button.removeClass("disabled loading");

                    },
                    error: function (error) {
                        console.log("AJAX Call Error: " + error);
                    }
                });

            }
        });
    }
    addVariationToCart();
});
