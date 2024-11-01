<?php

// read options
$afs_click = !get_option('afs_clicks') ? '' : stripslashes_deep( get_option('afs_clicks') ); // sanitize
$afs_pixels_array = !get_option('afs_pixels') ? '' : json_decode( get_option('afs_pixels'), true );

// check options
if( !empty( $afs_click ) && !filter_var( $afs_click, FILTER_VALIDATE_URL ) ) { $afs_click = ''; }
if( json_last_error() !== JSON_ERROR_NONE ) { $afs_pixels_array = ''; } // valid json
if( empty( $afs_pixels_array ) ) { $afs_pixels_array = array( '' => '' ); } // show empty field
	
echo '
<div class="wrap afs_tracking_class">
<h1 class="wp-heading-inline">Tracking code for Woocommerce</h1>
<hr class="wp-header-end">
<p>
Add here your conversion tracking code, it will be automatically called in your Woocommerce\'s thank you page. You can also add multiple tracking codes.
<br>
This free plugin was created for <a href="https://www.affiliationsoftware.network" target="_blank">AffiliationSoftware</a>\'s users but you can use it with any tracking code you want.
</p>
<form method="POST">
<h2 class="title">Conversion tracking</h2>
<div id="afs_dynamic_rows">
';

$afs_fields = 0; // counter
foreach( $afs_pixels_array AS $afs_type => $afs_url ) {
	$afs_url = stripslashes_deep( $afs_url ); // sanitize
	if( !empty( $afs_url ) && !filter_var( $afs_url, FILTER_VALIDATE_URL ) ) { $afs_url = ''; } // check field
	echo "
	<select id='afs_type$afs_fields' name='afs_type[$afs_fields]'>
	<option value=''>Select type</option>
	<option value='img' ".( $afs_type == 'img' ? 'selected' : '' ).">Image</option>
	<option value='js' ". ( $afs_type == 'js' ? 'selected' : '' ).">Javascript</option>
	<option value='ifr' ".( $afs_type == 'ifr' ? 'selected' : '' ).">Iframe</option>
	</select>
	<input id='afs_url$afs_fields' name='afs_url[$afs_fields]' type='url' placeholder='https://example.com/pixel.php?amount=[order_total]' value='$afs_url'>
	<br><br>
	";
	$afs_fields++;
}

echo "
</div>
<input type='button' class='button button-secondary' value='Add another' onClick='afs_add_field()'>
<br><br>
<hr>
<h2 class='title'>Click tracking (optional)</h2>
<select id='af_clicks_type' name='af_clicks_type'>
<option value=''>Select type</option>
<option value='js' ".( $afs_click ? 'selected' : '' ).">Javascript</option>
</select>
<input id='afs_click' name='afs_click' type='url' placeholder='https://example.com/click-tracking' value='$afs_click'>
<br><br>
<input name='afs_save' type='submit' class='button button-primary' value='Save changes'>
";

wp_nonce_field('affiliationsoftware-woocommerce', 'affiliationsoftware-woocommerce_nonce');

echo "
</form>
<br>
<hr>
<h2 class='title'>Parameters</h2>
<p>
You can use the following parameters to dynamically add the order's data in your conversion tracking URLs.
</p>
<details>
<summary><strong>Show available parameters</strong></summary>
<ul>
<li><strong>[order_id]</strong> Number of the order</li>
<li><strong>[order_total]</strong> Total amount of the order</li>
<li><strong>[order_subtotal]</strong> Products' total cost</li>
<li><strong>[order_shipping]</strong> Shipping total cost</li>
<li><strong>[order_tax]</strong> Taxes total cost</li>
<li><strong>[order_discount]</strong> Discount total amount</li>
<li><strong>[order_coupon]</strong> Coupon name (if any)</li>
<li><strong>[order_currency]</strong> Currency used</li>
<li><strong>[order_ip]</strong> User's IP address</li>
<li><strong>[customer_id]</strong> Customer's ID</li>
<li><strong>[customer_email]</strong> Customer's email'</li>
<li><strong>[customer_first_name]</strong> Customer's first name</li>
<li><strong>[customer_last_name]</strong> Customer's last name</li>
<li><strong>[customer_country]</strong> Customer's country</li>
<li><strong>[payment_method]</strong> Payment method</li>
</ul>
</details>
<br>
<hr>
<p class='footer-thankyou'>
Free plugin powered by <a href='https://www.affiliationsoftware.network' taget='_blank'>AffiliationSoftware</a>
</p>
</div>

<script>var afs_fields = $afs_fields;</script>
";