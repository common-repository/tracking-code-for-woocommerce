<?php

function afs_validate_url ( $url ) {
	if( substr( $url, 0, 4 ) != 'http' ) { return false; } // start with http
	if( preg_match( "/(<|>|'|\")/i", $url ) ) { return false; } // disable tags or quotes
	if( !filter_var( $url, FILTER_VALIDATE_URL ) ) { return false; } 
	if( !wp_http_validate_url( $url ) ) { return false; } 
	return true;
}

function afs_sanitize_url ( $url ) {
	$url = strip_tags( $url );
	$url = trim( $url );
	return $url;
}

// start validation
if( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['afs_save'] ) ) {

	$afs_data = array(); // valid data
	
	// check permissions
	if ( !current_user_can( 'unfiltered_html' ) ) {
		$afs_error = 'Sorry, you are not allowed to edit this page';

	// check nonce
	} elseif ( !isset( $_POST[ 'affiliationsoftware-woocommerce_nonce' ] ) 
		|| !wp_verify_nonce( $_POST[ 'affiliationsoftware-woocommerce_nonce' ], 'affiliationsoftware-woocommerce' ) ) {
		$afs_error = 'Missing or Invalid nonce field';

	// check fields
	} elseif( !isset( $_POST['afs_url'] ) || !is_array( $_POST['afs_url'] ) 
	  || !isset( $_POST['afs_type'] ) || !is_array( $_POST['afs_type'] ) 
	  || !isset( $_POST['afs_click'] ) ) {
		$afs_error = 'Something went wrong, please retry';

	// validate click
	} elseif( !empty( $_POST['afs_click'] ) && !afs_validate_url( $_POST['afs_click'] ) ) {
		$afs_error = "Please enter a valid URL for CLICK TRACKING"; 

	// validate types & URLs
	} else {
		foreach( $_POST['afs_url'] AS $afs_num => $afs_val ) {
			if( !isset( $_POST['afs_url'][$afs_num] ) || !isset( $_POST['afs_type'][$afs_num] ) ) {
				$afs_error = 'Something went wrong, please retry';
				break;
			} elseif( !empty( $_POST['afs_url'][$afs_num] ) && !in_array( $_POST['afs_type'][$afs_num], array( 'img', 'js', 'ifr' ) ) ) {
				$afs_error = "Please select a valid TYPE for row ".($afs_num+1);
				break;
			} elseif( !empty( $_POST['afs_url'][$afs_num] ) && !afs_validate_url( $_POST['afs_url'][$afs_num] ) ) {
				$afs_error = "Please enter a valid URL for row ".($afs_num+1);
				break;

			// sanitize data
			} else {
				if( !empty( $_POST['afs_url'][$afs_num] ) ) {
					$afs_data[ $_POST['afs_type'][$afs_num] ] = afs_sanitize_url( $_POST['afs_url'][$afs_num] );
					/*$afs_data[ $afs_num ] = array( 'cat' => 'conversion', 'type' => $_POST['afs_type'][$afs_num], 'url' => afs_sanitize_url( $_POST['afs_url'][$afs_num] ), ); // TODO */
				}
			}
		}
	}

	// print error
	if( isset( $afs_error ) && !empty( $afs_error ) ) {
		echo '<br><div class="notice notice-error is-dismissible"><p>';
		echo __( "<b>$afs_error</b>", 'affiliationsoftware-woocommerce' ); 
		echo '</p></div>';

	// save data	
	} else {
		update_option('afs_pixels', empty($afs_data) ? '' : wp_json_encode( $afs_data ) ); // escaped
		update_option('afs_clicks', afs_sanitize_url( $_POST['afs_click'] ) ); // escaped

		echo '<br><div class="notice notice-success is-dismissible"><p>';
		echo __( '<b>Settings Saved.</b>', 'affiliationsoftware-woocommerce' ); 
		echo '</p></div>';
	}
}