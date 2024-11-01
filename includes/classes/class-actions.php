<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class WDM_Actions{
	
	public function __construct(){

		add_action( 'wdm_acton_inside_single_download', array( $this, 'wdm_acton_display_user_download_list' ), 10, 1  );
    }
	
	function wdm_acton_display_user_download_list( $single_download ){
		
		require( WDM_PLUGIN_DIR . 'templates/actions/user-download-list.php');	
	}
	
	
} new WDM_Actions();