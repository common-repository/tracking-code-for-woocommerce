<?php
/*
Plugin Name: Tracking code for Woocommerce
Plugin URI: https://www.affiliationsoftware.network/woocommerce-affiliate-software
Description: Easily add your conversion tracking code in woocommerce. This free plugin was created for AffiliationSoftware's users but you can use it with any tracking code you want.
Version: 1.0.5
Requires PHP: 5.6
Author: AffiliationSotware
Author URI: https://www.affiliationsoftware.network
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 2.3
WC tested up to: 6.3
*/

/**
 * Copyright (c) 2021 AffiliationSoftware. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// show notice if woocomemrce is not active
add_action( 'admin_notices', 'afs_check_woocommerce' );
function afs_check_woocommerce (){
	$plugin = 'woocommerce/woocommerce.php';
	if( !in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || is_plugin_active_for_network( $plugin ) ) {
		echo '<div class="error notice is-dismissible"><p>';
		echo __( '<b>Tracking code for Woocommerce</b> won\'t work without <a target="_blank" href="https://wordpress.org/plugins/woocommerce/">Woocommerce</a>', 'affiliationsoftware-woocommerce' ); 
		echo '</p></div>';
	}
}

// create data in wp_options on activation
register_activation_hook( __FILE__, 'afs_activation' );
function afs_activation () {
	global $wpdb; 
	if( !$wpdb->query("SELECT * FROM ". $wpdb->prefix . "options WHERE option_name ='afs_pixels' LIMIT 1") ) { // check exist
		add_option( 'afs_pixels', '', '', 'yes' );
	}
	if( !$wpdb->query("SELECT * FROM ". $wpdb->prefix . "options WHERE option_name ='afs_clicks' LIMIT 1") ) { // check exist
		add_option( 'afs_clicks', '', '', 'yes' );
	}
}

// delete wp_options on uninstall 
// register_deactivation_hook( __FILE__, 'afs_deactivation' );
register_uninstall_hook(__FILE__,'afs_uninstall');
function afs_uninstall(){
	delete_option( 'afs_pixels' );
	delete_option( 'afs_clicks' );  
}

// add settings link in plugin page
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'afs_plugin_settings_link' );
function afs_plugin_settings_link( array $links ) {
    $settings_link = '<a href="'.get_admin_url().'admin.php?page=affiliationsoftware-woocommerce">Settings</a>'; 
    array_unshift($links, $settings_link); 
    return $links;
}

// add link in dashboard woocommerce menu
add_action('admin_menu', 'afs_init_admin',99);
function afs_init_admin(){
    add_submenu_page( 'woocommerce', 'Tracking code', 'Tracking code', 'manage_options', 'affiliationsoftware-woocommerce', 'afs_admin_page' ); 
}

// show page in admin dashboard
function afs_admin_page(){
	wp_register_style('afs_admin_css', plugins_url('css/style.css',__FILE__ ));
    wp_enqueue_style('afs_admin_css');
    wp_register_script( 'afs_admin_js', plugins_url('js/script.js',__FILE__ ));
    wp_enqueue_script('afs_admin_js');
	require_once( plugin_dir_path( __FILE__ ) . 'inc/admin_validation.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'inc/admin_page.php' );
}

// print pixel on all pages (clicks)
add_action('wp_footer', 'afs_print_clicks', 99); 
function afs_print_clicks() { 
	$afs_clicks = get_option('afs_clicks') ? stripslashes_deep( get_option('afs_clicks') ) : ''; // sanitize
	if( !empty( $afs_clicks ) && filter_var( $afs_clicks, FILTER_VALIDATE_URL ) ) {
		echo "<script type=\"text/javascript\" src=\"$afs_clicks\"></script>";
	}
}

// print pixel on woocommerce thank you page (conversions)
add_action( 'woocommerce_thankyou_order_received_text', 'afs_print_pixels', 20, 2 );
function afs_print_pixels( $thank_you_text, $order ) {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/thankyou_page.php' );
	$afs_output = '';
	$afs_pixels_array = get_option('afs_pixels') ? json_decode( get_option('afs_pixels'), true ) : '';
	if( !empty( $afs_pixels_array ) && json_last_error() === JSON_ERROR_NONE ) {
		foreach( $afs_pixels_array AS $afs_type => $afs_url ) {
			$afs_url = stripslashes_deep( $afs_url ); // sanitize
			$afs_url = str_replace( array_keys( $afs_vars ), array_values( $afs_vars ), $afs_url ); // variables
			if( !empty( $afs_url ) && filter_var( $afs_url, FILTER_VALIDATE_URL ) ) {
				if( $afs_type == 'img' ) { $afs_output .= "<img src=\"$afs_url\" width=\"1\" height=\"1\" border=\"0\" alt=\"\" style=\"height:1px\" />\n"; }
				elseif( $afs_type == 'js' ) { $afs_output .= "<script type=\"text/javascript\" src=\"$afs_url\"></script>\n"; }
				elseif( $afs_type == 'ifr' ) { $afs_output .= "<iframe src=\"$afs_url\" width=\"1\" height=\"1\" frameborder=\"0\" ></iframe>\n"; }
			}
		}
	}
	if( $afs_output ) { echo "<div style=\"margin-top:-1.4em;height:1px\">$afs_output</div><p style=\"margin:0\">"; }
	return $thank_you_text; 
}

?>