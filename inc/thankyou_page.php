<?php

$afs_vars = array(
	'[order_id]' => $order->get_order_number(),
	'[order_total]' => $order->get_total(),
	'[order_subtotal]' => $order->get_subtotal(),
	'[order_shipping]' => $order->get_shipping_total(),
	'[order_tax]' => $order->get_total_tax(),
	'[order_discount]' => $order->get_discount_total(),
	'[order_currency]' => $order->get_currency(),
	'[order_ip]' => $order->get_customer_ip_address(),
	'[customer_id]' => $order->get_user_id(),
	'[customer_email]' => $order->get_billing_email(),
	'[customer_first_name]' => $order->get_billing_first_name(),
	'[customer_last_name]' => $order->get_billing_last_name(),
	'[customer_country]' => $order->get_billing_country(),
	'[payment_method]' => $order->get_payment_method(),
	'[order_coupon]' => '',
);

foreach( $order->get_coupon_codes() as $coupon_code ) {
	$afs_vars['[order_coupon]'] = $coupon_code; break;
}

/*
do_action( 'woocommerce_thankyou', $order_get_id ); 
// define the woocommerce_thankyou callback 
function action_woocommerce_thankyou( $order_get_id ) { 
	// make action magic happen here... 
};        
// add the action 
add_action( 'woocommerce_thankyou', 'action_woocommerce_thankyou', 10, 1 ); 
*/

?>