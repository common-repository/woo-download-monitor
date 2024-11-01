<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	global $woocommerce, $post, $thepostid, $product_object;
	
	$_wdm_restrict_specific_user = get_post_meta( $thepostid, '_wdm_restrict_specific_user', true );
	
	$wdm_editable_roles = array( 
		"" 		=> __( 'Select a Role', WDM_TEXTDOMAIN ),
		"any" 	=> __( 'Any Role', WDM_TEXTDOMAIN ),
	);
	foreach( get_editable_roles() as $role_key => $role ):
		$wdm_editable_roles[$role_key] = $role['name'];
	endforeach;
	
	// echo "<pre>"; print_r( $user_data ); echo "</pre>";
	
?>
<div id="wdm_download_monitor_data" class="panel woocommerce_options_panel">
	
	<div class="options_group">
	<?php
		woocommerce_wp_checkbox( array( 
			'id'            => '_wdm_allow_download_public', 
			'label'         => __( 'Allow Download - Public', WDM_TEXTDOMAIN ),
			'description'   => __( 'Allow public/visitors to download files without even login to the website', WDM_TEXTDOMAIN ),
			'default'  		=> '0',
			'desc_tip'    	=> true,
		) );

		woocommerce_wp_select( array(
			'id' 			=> '_wdm_allow_specific_role',
			'label' 		=> __( 'Allow Download - Role', WDM_TEXTDOMAIN ),
			'description'   => __( 'Allow a specific Role to download files without purchasing this product', WDM_TEXTDOMAIN ),
			'desc_tip'    	=> true,
			'options' 		=> $wdm_editable_roles
		) );
	?> 
	</div>
	
	<div class="options_group">

		<p class="form-field">
			<label for="_wdm_restrict_specific_user"><?php _e( 'Restrict a Specific User', WDM_TEXTDOMAIN ); ?></label>
			<select class="wc-customer-search"  multiple="multiple" style="width: 50%;" id="_wdm_restrict_specific_user" name="_wdm_restrict_specific_user[]" data-placeholder="<?php esc_attr_e( 'Search for an user&hellip;', WDM_TEXTDOMAIN ); ?>" data-action="wdm_json_search_user" data-minimum_input_length="3">
			<?php
				foreach( $_wdm_restrict_specific_user as $key => $user_id ) :
					$user_data = get_userdata( $user_id );
					echo '<option value="' . esc_attr( $user_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $user_data->display_name ) . '</option>';
				endforeach;
			?>
			</select> 
			<?php echo wc_help_tip( __( 'Upsells are products which you recommend instead of the currently viewed product, for example, products that are more profitable or better quality or more expensive.', WDM_TEXTDOMAIN ) ); ?>
		</p>
		
	</div>
	
	
	
</div>