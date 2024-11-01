<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class WDM_Functions{
	
	public function wdm_settings_data($options = array()){
		
		$section_options = array(
			'wdm_downloads_on_single_product'=>array(
				'title'=>__('Show Downloads - Single Product',WDM_TEXTDOMAIN),
				'option_details'=>__('Do you want to show downloads list in single product page?', WDM_TEXTDOMAIN),						
				'input_type'=>'select', 
				'input_args'=>array('yes'=>__('Yes',WDM_TEXTDOMAIN),'no'=>__('No',WDM_TEXTDOMAIN),),
			),
            'wdm_display_file_extension'=>array(
                'title'=>__('Display file extension',WDM_TEXTDOMAIN),
                'option_details'=>__('If you want to display file extension on single product page?', WDM_TEXTDOMAIN),
                'input_type'=>'select',
                'input_args'=>array('yes'=>__('Yes',WDM_TEXTDOMAIN),'no'=>__('No',WDM_TEXTDOMAIN),),
            ),
            'wdm_item_per_page'=>array(
                'title'=>__('Item per page',WDM_TEXTDOMAIN),
                'option_details'=>__('Display item per page on stats page?', WDM_TEXTDOMAIN),
                'input_type'=>'text',
                'input_values'=>'10',
            ),

		);
		$options['<span class="dashicons dashicons-admin-generic"></span> '.__('Options', WDM_TEXTDOMAIN)] = apply_filters( 'wdm_settings_section_options', $section_options );
		
		$section_options = array(
			'woocommerce_downloads_require_login'=>array(
				'title'=>__('Downloads require login - WooCommerce Setting',WDM_TEXTDOMAIN),
				'option_details'=>__('This setting does not apply to guest purchases - WooCommerce Setting', WDM_TEXTDOMAIN),						
				'input_type'=>'select', 
				'input_args'=>array('yes'=>__('Yes',WDM_TEXTDOMAIN),'no'=>__('No',WDM_TEXTDOMAIN),),
			),

            

		);
		$options['<span class="dashicons dashicons-lock"></span> '.__('Permissions', WDM_TEXTDOMAIN)] = apply_filters( 'wdm_settings_section_permissions', $section_options );
		
	
		return apply_filters( 'wdm_filter_settings_options', $options );
	}
	
	public function wdm_settings_html( $OPTIONS_DATA = array(), $HTML = "" ){
			
		if( empty( $OPTIONS_DATA ) ) return "";
		
		$HTML.= '<div class="para-settings qa-settings">';			
		$html_nav = '';
		$html_box = '';
		$i=1;
		foreach($OPTIONS_DATA as $key=>$options){
			
			if( $i == 1 ) $html_nav.= '<li nav="'.$i.'" class="nav'.$i.' active">'.$key.'</li>';				
			else $html_nav.= '<li nav="'.$i.'" class="nav'.$i.'">'.$key.'</li>';
			if( $i == 1 ) $html_box.= '<li style="display: block;" class="box'.$i.' tab-box active">';				
			else $html_box.= '<li style="display: none;" class="box'.$i.' tab-box">';
			$single_html_box = '';
			foreach( $options as $option_key => $option_info ){
				$option_value =  get_option( "$option_key", '' );				
				if( empty( $option_value ) )
				$option_value = isset( $option_info['input_values'] ) ? $option_info['input_values'] : '';
				$placeholder = isset( $option_info['placeholder'] ) ? $option_info['placeholder'] : ''; 
				$single_html_box.= "<div class='option-box $option_key'>";
				$single_html_box.= '<p class="option-title">'.$option_info['title'].'</p>';
				$single_html_box.= '<p class="option-info">'.$option_info['option_details'].'</p>';
				if($option_info['input_type'] == 'text')
				$single_html_box.= '<input type="text" id="'.$option_key.'" placeholder="'.$placeholder.'" name="'.$option_key.'" value="'.$option_value.'" /> ';					
				elseif( $option_info['input_type'] == 'text-multi' ) {
					
					$input_values = $option_value;
					$option_id = $option_key;
					$single_html_box.= '<div class="repatble">';
					$single_html_box.= '<div class="repatble-items">';
					if(!empty($input_values)){
						if(is_array($input_values)){
							foreach($input_values as $key=>$value){
								$single_html_box.= '<div class="single">';
								$single_html_box.= '<input type="text" name="'.$option_id.'['.$key.']" value="'.$input_values[$key].'" />';
								$single_html_box.= '<input class="remove-field button" type="button" value="'.__('Remove').'" />';	
								$single_html_box.= '</div>';
							}
						} else {
							$single_html_box.= '<input type="text" name="'.$option_id.'[]" value="'.$input_values.'" /> ';
							$single_html_box.= '<input class="remove-field button" type="button" value="'.__('Remove').'" />';
							}
					} else {
						$single_html_box.= '<input type="text" name="'.$option_id.'[]" value="'.$input_values.'" /> ';
						$single_html_box.= '<input class="remove-field button" type="button" value="'.__('Remove').'" />';
					}
					$single_html_box.= '</div>';
					$single_html_box.= '<input  class="add-field button" option-id="'.$option_id.'" type="button" value="'.__('Add more').'" /> ';
					$single_html_box.= '</div>';
				} elseif($option_info['input_type'] == 'textarea')
					$single_html_box.= '<textarea placeholder="'.$placeholder.'" name="'.$option_key.'" >'.$option_value.'</textarea> ';
				elseif( $option_info['input_type'] == 'radio' ) {
					$input_args = $option_info['input_args'];
					foreach( $input_args as $input_args_key => $input_args_values ) {
						$checked = ( $input_args_key == $option_value ) ? $checked = 'checked' : '';
						$html_box.= '<label><input class="'.$option_key.'" type="radio" '.$checked.' value="'.$input_args_key.'" name="'.$option_key.'"   >'.$input_args_values.'</label><br/>';
					}
				} elseif( $option_info['input_type'] == 'select' ) {
					$input_args = $option_info['input_args'];
					$single_html_box 	.= '<select name="'.$option_key.'" >';
					foreach( $input_args as $input_args_key => $input_args_values ) {
						$selected = ( $input_args_key == $option_value ) ? 'selected' : '';
						$single_html_box.= '<option '.$selected.' value="'.$input_args_key.'">'.$input_args_values.'</option>';
					}
					$single_html_box.= '</select>';
				} elseif( $option_info['input_type'] == 'selectmultiple' ) {
					$input_args = $option_info['input_args'];
					$single_html_box.= '<select multiple="multiple" size="6" name="'.$option_key.'[]" >';
					foreach($input_args as $input_args_key=>$input_args_values){
						$selected = in_array( $input_args_key, $option_value ) ? 'selected' : '';
						$single_html_box.= '<option '.$selected.' value="'.$input_args_key.'">'.$input_args_values.'</option>';
					}
					$single_html_box.= '</select>';
				} elseif( $option_info['input_type'] == 'checkbox' ) {
					foreach($option_info['input_args'] as $input_args_key=>$input_args_values){
						$checked = in_array( $input_args_key, $option_value ) ? 'checked' : '';
						$single_html_box.= '<label><input '.$checked.' value="'.$input_args_key.'" name="'.$option_key.'['.$input_args_key.']"  type="checkbox" >'.$input_args_values.'</label><br/>';
					}
				} elseif( $option_info['input_type'] == 'file' ){
					$single_html_box.= '<input type="text" id="file_'.$option_key.'" name="'.$option_key.'" value="'.$option_value.'" /><br />';
					$single_html_box.= '<input id="upload_button_'.$option_key.'" class="upload_button_'.$option_key.' button" type="button" value="Upload File" />';					
					$single_html_box.= '<br /><br /><div style="overflow:hidden;max-height:150px;max-width:150px;" class="logo-preview"><img style=" width:100%;" src="'.$option_value.'" /></div>';
					$single_html_box.= '
					<script>jQuery(document).ready(function($){
					var custom_uploader; 
					jQuery("#upload_button_'.$option_key.'").click(function(e) {
						e.preventDefault();
						if (custom_uploader) {
							custom_uploader.open();
							return;
						}
						custom_uploader = wp.media.frames.file_frame = wp.media({
							title: "Choose File",
							button: { text: "'.__('Choose File', WDM_TEXTDOMAIN).'" },
							multiple: false
						});
						custom_uploader.on("select", function() {
							attachment = custom_uploader.state().get("selection").first().toJSON();
							jQuery("#file_'.$option_key.'").val(attachment.url);
							jQuery(".logo-preview img").attr("src",attachment.url);											
						});
						custom_uploader.open();
					});
					})
				</script>';					
				}
				$single_html_box.= '</div>';
			}
			$html_box .= $single_html_box;
			$html_box.= '</li>';
			$i++;
		}
		$HTML.= '<ul class="tab-nav">';
		$HTML.= $html_nav;			
		$HTML.= '</ul>';
		$HTML.= '<ul class="box">';
		$HTML.= $html_box;
		$HTML.= '</ul>';		
		$HTML.= '</div>';			
		return $HTML;
	}
	
} new WDM_Functions();