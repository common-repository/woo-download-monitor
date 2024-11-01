<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


function wdm_ajax_get_downloads_for_variable_products(){
	
	$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
	$attributes = isset( $_POST['attributes'] ) ? stripslashes_deep( $_POST['attributes'] ) : array();
	
	$arr_attributes = array();
	foreach( $attributes as $attribute ){
		
		$attribute_name = isset( $attribute['attribute_name'] ) ? sanitize_text_field( $attribute['attribute_name'] ) : '';
		$attribute_value = isset( $attribute['attribute_value'] ) ? sanitize_text_field( $attribute['attribute_value'] ) : '';
		
		if( empty( $attribute_name ) || empty( $attribute_value ) ) continue; 		
		$arr_attributes[$attribute_name] = $attribute_value;
	}
	
	$wc_get_product = wc_get_product( $product_id );
	$available_variations = $wc_get_product->get_available_variations();
	
	foreach( $available_variations as $variation ){
		
		$variation_id 	= isset( $variation['variation_id'] ) ? $variation['variation_id'] : '';
		$_attributes 	= isset( $variation['attributes'] ) ? $variation['attributes'] : array();
		
		if( serialize( $_attributes ) == serialize( $arr_attributes ) ){
			
			$_arr_downloads = get_post_meta( $variation_id, '_downloadable_files', true );
			echo wdm_get_downloadable_files_html( $variation_id, $_arr_downloads );
			break;
		}	
	}

	die();
}
add_action('wp_ajax_wdm_ajax_get_downloads_for_variable_products', 'wdm_ajax_get_downloads_for_variable_products');
add_action('wp_ajax_nopriv_wdm_ajax_get_downloads_for_variable_products', 'wdm_ajax_get_downloads_for_variable_products');

function wdm_woocommerce_after_add_to_cart_form_function() {

    $wdm_downloads_on_single_product = get_option('wdm_downloads_on_single_product');

    if($wdm_downloads_on_single_product=='yes'){

        $html 			= '';
        $wc_get_product = wc_get_product(get_the_ID());
        $downloads 		= $wc_get_product->get_downloads();

        echo wdm_get_downloadable_files_html( get_the_ID(), $downloads );
    }
}

add_action( 'woocommerce_after_single_product_summary' , 'wdm_woocommerce_after_add_to_cart_form_function' );

function wdm_woocommerce_download_file_function(){

    $product_id     = isset( $_GET['wdm_download'] ) ? sanitize_text_field( $_GET['wdm_download'] ) : '';
    $file_id        = isset( $_GET['file_id'] ) ? sanitize_text_field( $_GET['file_id'] ) : '';

    if( empty( $product_id ) || empty( $file_id ) ) return;

    $current_user   = wp_get_current_user();
    $product        = wc_get_product( $product_id );
    $data_store     = WC_Data_Store::load( 'customer-download' );

	$user_id 		= get_current_user_id();
	$user_email 	= get_user_meta( $user_id, 'billing_email', true );
    $data_store     = WC_Data_Store::load( 'customer-download' );
    $can_download	= true;

	if( empty( $user_email ) ){
		$user_email = $current_user->user_email;
	}

    $download_ids = $data_store->get_downloads( array(
        'user_email'  => sanitize_email( str_replace( ' ', '+', $user_email ) ),
        'product_id'  => $product_id,
        'download_id' => wc_clean( preg_replace( '/\s+/', ' ', $file_id ) ),
        'orderby'     => 'downloads_remaining',
        'order'       => 'DESC',
        'limit'       => 1,
        'return'      => 'ids',
    ) );
	
    if( empty( $download_ids ) ) $can_download = false;

    $download   = new WC_Customer_Download( current( $download_ids ) );
    $order      = new WC_Order($download->get_order_id());

    if ( $download->get_order_id() && ( $order = wc_get_order( $download->get_order_id() ) ) && ! $order->is_download_permitted() ) {
		$can_download = false;
    }
    if ( '' !== $download->get_downloads_remaining() && 0 >= $download->get_downloads_remaining() ) {
        $can_download = false;
    }
    if ( ! is_null( $download->get_access_expires() ) && $download->get_access_expires()->getTimestamp() < strtotime( 'midnight', current_time( 'timestamp', true ) ) ) {
        $can_download = false;
    }
    if ( $download->get_user_id() && 'yes' === get_option( 'woocommerce_downloads_require_login' ) ) {
        if (!is_user_logged_in()) {
            $can_download = false;
        } elseif (!current_user_can('download_file', $download)) {
            $can_download = false;
        }
        else {}
    }
	
	$_download_id	= $download->get_download_id();
	$_product_id	= $download->get_product_id();
	
	$args 			= array();
	$user_data 		= get_userdata( get_current_user_id() );
	$user_roles 	= empty( $user_data->roles ) ? array() : $user_data->roles;
	
	$allow_download_public	= get_post_meta( $product_id, '_wdm_allow_download_public', true );
	$allow_specific_role 	= get_post_meta( $product_id, '_wdm_allow_specific_role', true );
	$restricted_users 		= get_post_meta( $product_id, '_wdm_restrict_specific_user', true );
	
	if( empty( $allow_download_public ) ) $allow_download_public = 'no';
	if( empty( $allow_specific_role ) ) $allow_specific_role = '';
	if( empty( $restricted_users ) ) $restricted_users = array();


	
	if( $allow_download_public == 'yes' ){
		
		$args['can_download'] = true;
		$args['error_message'] = "";
		$args['takeover_priority'] = true;
	}
	elseif( in_array( get_current_user_id(), $restricted_users ) ){
		
		$args['can_download'] = false;
		$args['error_message'] = __( 'You are restricted from downloading this File', WDM_TEXTDOMAIN );
		$args['takeover_priority'] = true;
	}
	elseif( in_array( $allow_specific_role, $user_roles ) ){
		
		$args['can_download'] = true;
		$args['error_message'] = "";
		$args['takeover_priority'] = true;
	}

	$check_external_permission = apply_filters( 'wdm_filter_check_external_permission', $args, $product_id, $download );

	if(  isset($check_external_permission['takeover_priority']) && $check_external_permission['takeover_priority'] ) {
		
		$can_download 	= true;
		$_download_id	= $file_id;
		$_product_id	= $product_id;
	}
	if( isset($check_external_permission['can_download']) && ! $check_external_permission['can_download'] ) {
		echo $check_external_permission['error_message'];
		die();
	}
	
	if( ! $can_download ) return;
	
    @$WC_Download_Handler = new WC_Download_Handler();

    $count     = $download->get_download_count();
    $remaining = $download->get_downloads_remaining();
    $download->set_download_count( $count + 1 );
    if ( '' !== $remaining ) {
        $download->set_downloads_remaining( $remaining - 1 );
    }
    $download->save();

	do_action(
		'wdm_action_on_download_file',
		$download->get_user_email(),
		$download->get_order_key(),
		! empty( $download->get_product_id() ) ? $download->get_product_id() : $_product_id,
		get_current_user_id(),
		! empty( $download->get_download_id() ) ? $download->get_download_id() : $_download_id,
		$download->get_order_id()
	);

    @$WC_Download_Handler::download( $product->get_file_download_path( $_download_id ), $product_id );

}
add_action( 'init' , 'wdm_woocommerce_download_file_function' );

function wdm_ajax_check_download_validity(){

    $product_id     = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
    $download_id    = isset( $_POST['download_id'] ) ? sanitize_text_field( $_POST['download_id'] ) : '';
    $current_user   = wp_get_current_user();
	$user_id 		= get_current_user_id();
	$user_email 	= get_user_meta( $user_id, 'billing_email', true );
    $data_store     = WC_Data_Store::load( 'customer-download' );
    $wdm_error		= array();

    if( empty( $product_id ) || empty( $download_id) ) {
        echo __('Invalid Product', WDM_TEXTDOMAIN);
        die();
    }
	
	if( empty( $user_email ) ){
		$user_email = $current_user->user_email;
	}
	
	if ( 'yes' === get_option( 'woocommerce_downloads_require_login', 'yes' ) ) {
        if ( ! is_user_logged_in() ) {
            $wdm_error[] = __( 'You must be logged in to download files', WDM_TEXTDOMAIN );
        }
    }
	
    $download_ids = $data_store->get_downloads( array(
        'user_email'  => sanitize_email( str_replace( ' ', '+', $user_email ) ),
        'product_id'  => $product_id,
        'download_id' => wc_clean( preg_replace( '/\s+/', ' ', $download_id ) ),
        'orderby'     => 'downloads_remaining',
        'order'       => 'DESC',
        'limit'       => 1,
        'return'      => 'ids',
    ) );
	
    if( empty( $download_ids ) ) {
       $wdm_error[] = __( "You are not allowed to download", WDM_TEXTDOMAIN );
    }

    $download   = new WC_Customer_Download( current( $download_ids ) );
    $order      = new WC_Order($download->get_order_id());

	
    if ( $download->get_order_id() && ( $order = wc_get_order( $download->get_order_id() ) ) && ! $order->is_download_permitted() ) {
        $wdm_error[] = __( "Invalid Order", WDM_TEXTDOMAIN );
    }
    if ( '' !== $download->get_downloads_remaining() && 0 >= $download->get_downloads_remaining() ) {
        $wdm_error[] = __( 'Sorry, you have reached your download limit for this file', WDM_TEXTDOMAIN );
    }
    if ( ! is_null( $download->get_access_expires() ) && $download->get_access_expires()->getTimestamp() < strtotime( 'midnight', current_time( 'timestamp', true ) ) ) {
        $wdm_error[] = __( 'Sorry, this download has expired', WDM_TEXTDOMAIN );
    }
	if ( 'yes' === get_option( 'woocommerce_downloads_require_login' ) ) {
        if ( ! current_user_can( 'download_file', $download ) ) {
           $wdm_error[] = __( 'This is not your download link', WDM_TEXTDOMAIN );
        }
    }
	
	
	
	$args 		= array();
	$user_data 	= get_userdata( get_current_user_id() );
	$allow_download_public	= get_post_meta( $product_id, '_wdm_allow_download_public', true );
	$allow_specific_role 	= get_post_meta( $product_id, '_wdm_allow_specific_role', true );
	$restricted_users 		= get_post_meta( $product_id, '_wdm_restrict_specific_user', true );
	
	if( empty( $allow_download_public ) ) $allow_download_public = 'no';
	if( empty( $allow_specific_role ) ) $allow_specific_role = '';
	if( empty( $restricted_users ) ) $restricted_users = array();
	
	
	if( $allow_download_public == 'yes' ){
		
		$args['can_download'] = true;
		$args['error_message'] = "";
		$args['takeover_priority'] = true;
	}
	elseif( in_array( get_current_user_id(), $restricted_users ) ){
		
		$args['can_download'] = false;
		$args['error_message'] = __( 'You are restricted from downloading this File', WDM_TEXTDOMAIN );
		$args['takeover_priority'] = true;
	}
	elseif( in_array( $allow_specific_role, $user_data->roles ) ){
		
		$args['can_download'] = true;
		$args['error_message'] = "";
		$args['takeover_priority'] = true;
	}
	
	$check_external_permission = apply_filters( 'wdm_filter_check_external_permission', $args, $product_id, $download );
	
	if( isset($check_external_permission['takeover_priority']) && $check_external_permission['takeover_priority'] ) {
		$wdm_error = array();
	}
	if( isset($check_external_permission['can_download']) && ! $check_external_permission['can_download'] ) {
		$wdm_error[] = $check_external_permission['error_message'];
	}

    if( empty( $wdm_error ) ) {
        echo "";
        die();
    }
	
	$wdm_error_html = "<p>";
	$wdm_error_html.= implode( "</p><p>", $wdm_error );
	$wdm_error_html.= "</p>";
	
    echo "<div class='wdm_download_error'>$wdm_error_html</div>";
    die();
}
add_action('wp_ajax_wdm_ajax_check_download_validity', 'wdm_ajax_check_download_validity');
add_action('wp_ajax_nopriv_wdm_ajax_check_download_validity', 'wdm_ajax_check_download_validity');

function wdm_get_downloadable_files_html( $product_id = 0, $arr_downloads = array(), $attributes = array() ){

    $wdm_display_file_extension = get_option('wdm_display_file_extension');
	
	if( empty( $arr_downloads ) ){
		
		
		$wc_get_product 	= wc_get_product( $product_id );
		$default_attributes	= $wc_get_product->get_default_attributes();
		
		foreach( $wc_get_product->get_available_variations() as $variation ){
				
			$variation_id 	= isset( $variation['variation_id'] ) ? $variation['variation_id'] : '';
			$_attributes 	= isset( $variation['attributes'] ) ? $variation['attributes'] : array();

			foreach( $default_attributes as $key => $value ) {
				
				if( strpos( $key, 'attribute_' ) === 0 ) continue;
				
				unset( $default_attributes[ $key ] );
				$default_attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
			}
	
			if( serialize( $_attributes ) == serialize( $default_attributes ) ){
					
				$arr_downloads 	= get_post_meta( $variation_id, '_downloadable_files', true );
				$product_id		= $variation_id;
				break;
			}	
		}
	}
	
	ob_start();
	require_once( WDM_PLUGIN_DIR . 'templates/downloadable-files.php');		
	return ob_get_clean();
}

function wdm_action_on_download_file_function( $user_email, $order_key, $product_id, $user_id, $download_id, $order_id ){
	
	$gmt_offset = get_option('gmt_offset');	
	global $wpdb;
	
	$ret = $wpdb->insert(
		WDM_TABLE_NAME, 
		array( 
			'download_id' => $download_id, 
			'product_id' => $product_id,
			'order_id' => $order_id,
			'order_key' => $order_key,
			'user_id' => $user_id,
			'datetime' => date('Y-m-d H:i:s ', strtotime('+'.$gmt_offset.' hour')),
		)
	);
 
}
add_action( 'wdm_action_on_download_file', 'wdm_action_on_download_file_function', 10, 6 );


// Download action from WooCommerce
function woocommerce_download_product_function($user_email, $order_key, $product_id, $user_id, $download_id, $order_id ){
	
	do_action( 'wdm_action_on_download_file', $user_email, $order_key, $product_id, $user_id, $download_id, $order_id );
}
add_action( 'woocommerce_download_product', 'woocommerce_download_product_function', 10, 6 );

/* 
function wdm_filter_check_external_permission_function($args, $product_id, $download){
	
	$allow_download_public	= get_post_meta( $product_id, '_wdm_allow_download_public', true );
	$allow_specific_role 	= get_post_meta( $product_id, '_wdm_allow_specific_role', true );
	$restricted_users 		= get_post_meta( $product_id, '_wdm_restrict_specific_user', true );
	
	if( empty( $allow_download_public ) ) $allow_download_public = 'no';
	if( empty( $allow_specific_role ) ) $allow_specific_role = '';
	if( empty( $restricted_users ) ) $restricted_users = array();
	
	if( $allow_download_public == 'yes' ){
		
		$args['can_download'] = true;
		$args['error_message'] = "";
		$args['takeover_priority'] = true;
		
		return $args;
	}
	
	$user_data = get_userdata( get_current_user_id() );
	
	if( in_array( get_current_user_id(), $restricted_users ) ){
		
		$args['can_download'] = false;
		$args['error_message'] = __( 'You are restricted from downloading this File', WDM_TEXTDOMAIN );
		$args['takeover_priority'] = true;
		
		return $args;
	}
	
	if( in_array( $allow_specific_role, $user_data->roles ) ){
		
		$args['can_download'] = true;
		$args['error_message'] = "";
		$args['takeover_priority'] = true;
		
		return $args;
	}
	
	return $args;
}
add_filter( 'wdm_filter_check_external_permission', 'wdm_filter_check_external_permission_function', 10, 3 ); */

