<?php 
function disable_recipient_processing_orders($recipient, $order) {
    //basic logic - disable CC
    $static_payments = array('bacs', 'invoice');
    if (!in_array($order->get_payment_method(), $static_payments) && !$order->is_paid()) {
        $recipient = '';
    }
    //transition status
    if ($order instanceof WC_Order && $order->get_status() === 'processing' && $order->get_status_previous() === 'on-hold') {
        return '';
    }
    return $recipient;
}
add_filter('woocommerce_email_recipient_customer_processing_order' . 'disable_recipient_processing_orders', 10, 2);
function disable_pending_email_for_cc_payments($enabled, $order) {
    $static_payments = array('bacs', 'invoice');
    if ($order instanceof WC_Order && !in_array($order->get_payment_method(), $static_payments) && !$order->is_paid()) {
        $enabled = false;
    }
    return $enabled;
}
add_filter('woocommerce_email_enabled_customer_processing_order', 'disable_pending_email_for_cc_payments', 10, 2);
