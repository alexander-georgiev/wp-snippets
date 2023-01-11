<?php 

function trigger_invoice_on_completed_orders($order_id)
{
    $order = wc_get_order($order_id);
    $allowed_payments = get_option('storeabill_invoice_woo_order_auto_payment_gateways');
    if ($order->get_status() === 'completed' && in_array($order->get_payment_method(), $allowed_payments) && $order->get_total() > 0) {
        $gzd_order = Helper::get_order($order_id);
        $gzd_order->sync_order(true, array('created_via' => 'child_theme')); //creates invoice as draft
        $gzd_order->finalize(); //publish the invoice
    }
}
add_action('woocommerce_order_status_completed', 'trigger_invoice_on_completed_orders');
