<?php
/*
Plugin Name: PickPlugins Download Monitor for WooCommerce
Plugin URI: https://www.pickplugins.com/product/woocommerce-download-monitor/
Description: Awesome WooCommerce tool to monitor all Downloads by your customer
Version: 1.0.6
Text Domain: woo-download-monitor
Author: pickplugins
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class WocDownloadMonitor{

    /**
     * WocDownloadMonitor constructor.
     */
    public function __construct(){
	
		$this->wdm_define_constants();
		$this->wdm_declare_classes();
		
		$this->wdm_loading_script();
		$this->wdm_loading_functions();
		
		register_activation_hook( __FILE__, array( $this, 'wdm_activation' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ));
		
		add_action( 'admin_notices', array( $this, 'wdm_check_WooCommerce_enabled_function' ) );
	}
	
	public function wdm_check_WooCommerce_enabled_function(){
	
		if( ! class_exists( 'WooCommerce' ) ){
			
			echo "<div class='notice notice-error is-dismissible'><p><strong>WooCommerce</strong> is required for <strong>".WDM_PLUGIN_NAME."</strong></p></div>";
		}
	}
	
	
	public function wdm_activation() {
		
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS ".WDM_TABLE_NAME." (
			id int(100) NOT NULL AUTO_INCREMENT,
			download_id varchar(255) NOT NULL,
			product_id bigint(20) NOT NULL,
			order_id bigint(20) NOT NULL,
			order_key varchar(255) NOT NULL,
			user_id bigint(20) NOT NULL,
			datetime DATETIME NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	public function load_textdomain() {

		
		load_plugin_textdomain( WDM_TEXTDOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' ); 
	}

	public function wdm_loading_functions() {
		
		require_once( WDM_PLUGIN_DIR . 'includes/functions.php');
	}
	
	public function wdm_loading_script() {
	
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
		add_action( 'wp_enqueue_scripts', array( $this, 'wdm_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wdm_admin_scripts' ) );
	}
	
	public function wdm_declare_classes() {
		
		require_once( WDM_PLUGIN_DIR . 'includes/classes/class-functions.php');	
		require_once( WDM_PLUGIN_DIR . 'includes/classes/class-settings.php');	
		require_once( WDM_PLUGIN_DIR . 'includes/classes/class-woo-meta.php');	
		require_once( WDM_PLUGIN_DIR . 'includes/classes/class-shortcodes.php');	
		require_once( WDM_PLUGIN_DIR . 'includes/classes/class-actions.php');	
	}
	
	public function wdm_define_constants() {
		
		global $wpdb;
		$this->wdm_define( 'WDM_PLUGIN_URL', plugins_url('/', __FILE__)  );
		$this->wdm_define( 'WDM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->wdm_define( 'WDM_TEXTDOMAIN', 'woo-download-monitor' );
		$this->wdm_define( 'WDM_PLUGIN_NAME', __('WooCommerce Download Monitor', WDM_TEXTDOMAIN) );
		$this->wdm_define( 'WDM_PLUGIN_SUPPORT', 'http://www.pickplugins.com/questions/'  );
		$this->wdm_define( 'WDM_TABLE_NAME', $wpdb->prefix .'wdm_download_records' );
	}
	
	private function wdm_define( $name, $value ) {
		if( $name && $value )
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
		
	public function wdm_front_scripts(){
		
		wp_enqueue_script('jquery');
		wp_enqueue_style('dashicons');

		wp_enqueue_script( 'wdm_front_js', plugins_url( '/assets/front/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script('wdm_front_js', 'wdm_ajax', array( 'wdm_ajaxurl' => admin_url( 'admin-ajax.php')));
		
		wp_enqueue_style('wdm_style', WDM_PLUGIN_URL.'assets/front/css/style.css');	
	}

	public function wdm_admin_scripts(){
		
		wp_enqueue_script('jquery');
		wp_enqueue_style('dashicons');

		wp_enqueue_script('wdm_admin_js', plugins_url( '/assets/admin/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script( 'wdm_admin_js', 'wdm_ajax', array( 'wdm_ajaxurl' => admin_url( 'admin-ajax.php')));
		
		wp_enqueue_style('wdm_admin_style', WDM_PLUGIN_URL.'assets/admin/css/style.css');
		
		wp_enqueue_script('wdm_ParaAdmin', plugins_url( '/assets/admin/ParaAdmin/js/ParaAdmin.js' , __FILE__ ) , array( 'jquery' ));		
		wp_enqueue_style('wdm_paraAdmin', WDM_PLUGIN_URL.'assets/admin/ParaAdmin/css/ParaAdmin.css');
		
	}
} new WocDownloadMonitor();