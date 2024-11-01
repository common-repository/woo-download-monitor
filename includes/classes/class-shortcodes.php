<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class class_wdm_shortcodes  {
	
	public function __construct(){

		add_shortcode( 'wdm_downloadable_files', array( $this, 'wdm_shortcode_downloadable_files_function' ) );
    }
	
	public function wdm_shortcode_downloadable_files_function($atts, $content = null) {
		
		$output 	= "";
		$wdm_error 	= new WP_Error();
		$atts 		= shortcode_atts( array( 'id' => '' ), $atts);
		$product_id = isset( $atts['id'] ) ? sanitize_text_field( $atts['id'] ) : 0;
		
		if( $product_id == 0 )
			$product_id = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : 0;
		if( $product_id == 0 )
			$wdm_error->add( 'no_product_id', __('No Product ID found', WDM_TEXTDOMAIN ) );
		
		$product	= wc_get_product( $product_id );
		$output	   .= '<div class="wdm_product_title"><a target="_blank" href="'.get_the_permalink( $product_id ).'">'.get_the_title( $product_id ).'</a></div>';
		
		if( $product->is_type( 'variable' ) ):
			
			$available_variations = $product->get_available_variations();
			$available_attributes = array();
			
			foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {
				$attr_name = strtolower( str_replace( 'attribute_', '', $attribute_name ) );
				$available_attributes["attribute_$attr_name"] = array(
					'name'   	=> ucwords( str_replace( 'attribute_', '', str_replace( 'pa_', '', $attribute_name ) ) ),
					'option' 	=> $attribute,
				);
			}
		
			foreach( $available_variations as $variation ):
		
				$variation_id 	= isset( $variation['variation_id'] ) ? $variation['variation_id'] : '';
				$arr_attributes = isset( $variation['attributes'] ) ? $variation['attributes'] : array();
				$attributes		= array();

				foreach( $arr_attributes as $attr_name => $atrr_value ):
					$attribute_name = isset( $available_attributes[$attr_name]['name'] ) ? $available_attributes[$attr_name]['name'] : '';
					if( empty( $attribute_name ) ) continue;
					$attributes[$attribute_name] = ucfirst($atrr_value);
				endforeach;
				
				$_arr_downloads = get_post_meta( $variation_id, '_downloadable_files', true );
				
				$output    .= wdm_get_downloadable_files_html( $variation_id, $_arr_downloads, $attributes );
				
			endforeach;
	
		else:
			
			$downloads 	= $product->get_downloads();
			$output    .= wdm_get_downloadable_files_html( $product_id, $downloads );
		
			
		endif;
		
		
		ob_start();
		
		if ( is_wp_error( $wdm_error ) ) {
			foreach ( $wdm_error->get_error_messages() as $error ) {
				echo "<div class='wdm_error'><strong>ERROR:</strong> $error</div>";
			}
		}
		
		echo $output;
		
		return ob_get_clean();
	}
	
		
	
} new class_wdm_shortcodes();

