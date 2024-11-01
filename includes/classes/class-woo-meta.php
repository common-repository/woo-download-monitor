<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class WDM_Woo_meta{
	
	public function __construct(){
		
		add_action( 'woocommerce_product_data_tabs', array( $this, 'wdm_woocommerce_product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'wdm_woocommerce_product_data_panels' ) );
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'wdm_woocommerce_admin_process_product_object' ) );
    }
	
	public function wdm_woocommerce_product_data_tabs( $tabs ){
		
		$tabs['wdm'] = array(
			'label' 	=> __( 'WC Download Motinor', WDM_TEXTDOMAIN ),
			'priority' 	=> 80,
			'target' 	=> 'wdm_download_monitor_data',
			'class'		=> array(),
		);
		
		return $tabs;
	}
	
	public function wdm_woocommerce_product_data_panels( $OPTIONS_DATA = array(), $HTML = "" ){
			
		include( WDM_PLUGIN_DIR . 'templates/product-meta-data.php');
	}
	
	public function wdm_woocommerce_admin_process_product_object( $product ){
		
		$allow_download_public 	= isset( $_POST['_wdm_allow_download_public'] ) ? $_POST['_wdm_allow_download_public'] : '';
		$allow_specific_role 	= isset( $_POST['_wdm_allow_specific_role'] ) ? $_POST['_wdm_allow_specific_role'] : '';
		$restrict_specific_user = isset( $_POST['_wdm_restrict_specific_user'] ) ? $_POST['_wdm_restrict_specific_user'] : '';
		
		
		update_post_meta( $product->id, '_wdm_allow_download_public', $allow_download_public );
		update_post_meta( $product->id, '_wdm_allow_specific_role', $allow_specific_role );
		update_post_meta( $product->id, '_wdm_restrict_specific_user', $restrict_specific_user );
	}
	
} new WDM_Woo_meta();