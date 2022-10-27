function child_variations_card()
{
	global $product;
	$id                           = $product->get_id();
	$variation_ids = $product->get_children();
	$availability = $product->get_stock_status();
	if ($variation_ids && ($product->is_purchasable() || $availability === 'instock')) {
		echo '<div class="variations-wrapper">
		<div class="row">';
		
		$attributes = $product->get_variation_attributes();
		foreach ($attributes as $taxonomy_slug => $variation_slug) {				
 			$terms     = wc_get_product_terms($product->get_id(), $taxonomy_slug, array('fields' => 'all'));
			$taxonomy_label = (get_taxonomy($taxonomy_slug)->labels->singular_name ? get_taxonomy($taxonomy_slug)->labels->singular_name : $taxonomy_slug);
			//echo tdebug(get_taxonomy($taxonomy_slug));
			echo '<div class="col large-6 variation-buttons">';
			echo '<h5>'.$taxonomy_label.'</h5>';
			echo '<div class="select-dropdown-wrapper"><a class="selected-variation">Bitte wählen <i class="far fa-chevron-down"></i></a>
				<ul class="select-dropdown">';	
			if ($terms) {				
				foreach ($terms as $term) {
					$color_hex  = get_woocommerce_term_meta($term->term_id, 'ux_color', true);
					if ($color_hex) {
						echo '<li class="single-variation" data-term-id="'.$term->term_id.'" data-attr-name="attribute_' . $taxonomy_slug . '" data-attr-slug="' . $term->slug . '">
						<label>'. $term->name .'</label> 
						<div class="circle" style="background:' . $color_hex . ' ">&nbsp;</div>
						</li>';
					} else {
						echo '<li class="single-variation" data-attr-name="attribute_' . $taxonomy_slug . '" data-attr-slug="' . $term->slug . '"><label>' . $term->name . '</label></li>';
					}
				}			
			} else {
				foreach ($variation_slug as $slug) {
					$taxonomy_slug = sanitize_title($taxonomy_slug);
					$term_obj = get_term_by('slug', $slug, 'pa_'.$taxonomy_slug);
					$name     = ($term_obj->name ? $term_obj->name : $slug);
					echo '<li class="single-variation" data-attr-name="' . $taxonomy_slug . '" data-attr-slug="' . $slug . '"><label>' . $name . '</label></li>';
				}
			}
			echo '</ul></div>';
			

			echo '</div>';
		}
		echo '</div>';
		        echo '<a href="" class="button primary ajax_add_to_cart add-to-cart disabled" data-quantity="1" data-product_id="'.$id.'" data-variation_id="">In den Warenkorb</a></div>';
	}
}

add_action( 'woocommerce_after_shop_loop_item','child_variations_card' );

function child_variation_change() {
	$product = wc_get_product($_POST['productId']);
	$js_variations = $_POST['variations'];
	$variations = array();
	//we must format it to: attribute_pa_farbe => red
	foreach ($js_variations as $key => $variation) {		
		$variations[$variation['key']] = $variation['value'];
	}
	
	$variation_id = child_get_variation_id($product, $variations);
	
    if ($variation_id) {       
        $variation = wc_get_product($variation_id);
		$variation_img = $variation->get_image('medium');
		$variation_price = wc_price($variation->get_price());
		$msg = 'success';
        echo json_encode(array('image' => $variation_img, 'price' => $variation_price, 'variation_id' => $variation_id, 'message' => $msg));
    }
    wp_die();
}
add_action('wp_ajax_child_variation_change', 'child_variation_change');
add_action('wp_ajax_nopriv_child_variation_change', 'child_variation_change');

function add_variation_to_cart_ajax() {
	$variation_id = $_POST['variationId'];
    if ($variation_id) {       
		$quantity = 1;
        WC()->cart->add_to_cart($variation_id, $quantity);
        echo json_encode('success');
    }
    wp_die();
}
add_action('wp_ajax_add_variation_to_cart_ajax', 'add_variation_to_cart_ajax');
add_action('wp_ajax_nopriv_add_variation_to_cart_ajax', 'add_variation_to_cart_ajax');
