function aaptc_add_product_to_cart($item_key, $product_id) {
    $target_product_id    = 445;
    if (!is_admin() && ($product_id == $target_product_id)) {
        $free_product_id = 205646;  // Product Id of the free product which will get added to cart
        $found      = false;
        //check if product already in cart
        if (sizeof(WC()->cart->get_cart()) > 0) {
            foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                $_product = $values['data'];
                if ($_product->get_id() == $free_product_id)
                    $found = true;
                $free_prod_key = $cart_item_key;
            }
            // if product not found, add it
            if (!$found)
                WC()->cart->add_to_cart($free_product_id);
        }
    }
}
add_action('woocommerce_add_to_cart', 'aaptc_add_product_to_cart', 10, 2);
function remove_item_on_remove($cart_item_key, $cart) {
    $free_prod_key;
    if ($cart->cart_contents[$cart_item_key]['product_id'] == 445) {

        $product_id = $cart->cart_contents[$cart_item_key]['product_id'];
        foreach ($cart->cart_contents as $cart_item) {
            if ($cart_item['product_id'] == 205646) {
                $free_prod_key = $cart_item['key'];
                $product_name = get_the_title(445);
                $free_product_name = get_the_title(205646);
                WC()->cart->remove_cart_item($free_prod_key);
                wc_add_notice(__('"' . $product_name . '"" and "' . $free_product_name . '"" were removed from cart.', 'slyrs'), 'notice');
            }
        }
    }
};
add_action('woocommerce_remove_cart_item', 'remove_item_on_remove', 10, 2);

// Promotional Product add to cart
add_action('woocommerce_before_calculate_totals', 'adding_promotional_product', 10, 1);
function adding_promotional_product($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    if (did_action('woocommerce_before_calculate_totals') >= 2) {
        return;
    }
    $promo_id               = 1839; // <=== <=== <=== Set HERE the ID of your promotional product
    $targeted_cart_subtotal = 85; // <=== Set HERE the target cart subtotal
    $has_promo              = false;
    $subtotal               = 0;
    $target_product_id    = 445; //slyrs 12y
    $free_product_id = 205646; //Ostergeschenk
    if (!$cart->is_empty()) {
        // Iterating through each item in cart
        foreach ($cart->get_cart() as $item_key => $cart_item) {
            $product_id = version_compare(WC_VERSION, '3.0', '<') ? $cart_item['data']->id : $cart_item['data']->get_id();
            // If Promo product is in cart
            if ($product_id == $promo_id) {
                $has_promo = true;
                $promo_key = $item_key;
            } else {
                // Adding subtotal item to global subtotal
                $subtotal += $cart_item['line_subtotal'];
            }
            //update quantity egg liquire on target product
            if ($product_id == $target_product_id) {
                $target_product_quantity = $cart_item['quantity'];
            }
            if ($product_id == $free_product_id) {
                $cart->set_quantity($item_key, $target_product_quantity); // Change quantity
            }
            //END OF update quantity egg liquire on target product
        }
        // If Promo product is NOT in cart and target subtotal reached, we add it.
        if (!$has_promo && $subtotal >= $targeted_cart_subtotal) {
            $cart->add_to_cart($promo_id);
            // echo 'add';
            // If Promo product is in cart and target subtotal is not reached, we remove it.
        } elseif ($has_promo && $subtotal < $targeted_cart_subtotal) {
            $cart->remove_cart_item($promo_key);
        }
    }
}
