<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class class_wdm_settings  {
	
	public function __construct(){

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
    }
	
	public function admin_menu() {
		
		add_menu_page( __( 'WC Download Monitor', WDM_TEXTDOMAIN ), __( 'WC Download Monitor', WDM_TEXTDOMAIN ), 'manage_options', 'wdm-downloads', array( $this, 'wdm_downloads' ), 'dashicons-analytics', 58 );
		//add_submenu_page( 'wdm-downloads', __( 'Full Statistics', WDM_TEXTDOMAIN ), __( 'Full Statistics', WDM_TEXTDOMAIN ), 'manage_options', 'wdm-statistics', array( $this, 'wdm_statistics' ) );
		add_submenu_page( 'wdm-downloads', __( 'Settings', WDM_TEXTDOMAIN ), __( 'Settings', WDM_TEXTDOMAIN ), 'manage_options', 'wdm-settings', array( $this, 'wdm_settings' ) );
	}
	
	public function wdm_downloads(){
		include( WDM_PLUGIN_DIR. 'templates/menus/downloads.php' );
	}	
	
	public function wdm_statistics(){
		include( WDM_PLUGIN_DIR. 'templates/menus/statistics.php' );
	}	
	
	public function wdm_settings(){
		include( WDM_PLUGIN_DIR. 'templates/menus/settings.php' );
	}	
	
} new class_wdm_settings();

