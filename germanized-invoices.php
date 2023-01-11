<?php 

use Vendidero\StoreaBill\WooCommerce\Helper;
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
//end of adding invoice

/* Query all invoices */
$invoices = sab_get_invoices(array(
            'limit' => -1,
            'orderby'        => 'date_created',
            'order'          => 'DESC',
            'type' => 'invoice',
            'return' => 'ID',
            'date_created' => $date['start'] . '...' . $date['end']
        )); //grab all invoices

$invoice = sab_get_invoice($invoice_id); //grab invoice by its id
$order_id = $invoice->get_order_id($invoice); //grab order id by invoice
$inv_date = $invoice->get_date_created()->date('d-m-Y H:i:s');
$timezone_string = get_option('timezone_string');
$inv_date = DateTime::createFromFormat(
                    'd-m-Y H:i:s',
                    $inv_date,
                    new DateTimeZone($timezone_string)
                );
$invoice_ids[] = $invoice->get_formatted_number() ? $invoice->get_formatted_number() : $invoice->get_parent_formatted_number();

/* grab invoices by order */
$invoices = wc_gzdp_get_invoices_by_order($order, 'simple');//grab invoices by order
