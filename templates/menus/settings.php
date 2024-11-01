<?php	
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

$WDM_Functions = new WDM_Functions();
$wdm_settings_data = $WDM_Functions->wdm_settings_data(); 

$wdm_hidden = isset( $_POST['wdm_hidden'] ) ? sanitize_text_field( $_POST['wdm_hidden'] ) : '';

if( ! empty( $wdm_hidden ) && $wdm_hidden == 'Y' ):
	foreach( $wdm_settings_data as $key=>$options ): foreach( $options as $option_key => $option_info ):

		${$option_key} = stripslashes_deep($_POST[$option_key]);
		update_option($option_key, ${$option_key});
		
	endforeach; endforeach;
	echo "<div class='updated'><p><strong>".__('Changes Saved', WDM_TEXTDOMAIN )."</strong></p></div>";
endif;
?>





<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2>WooCommerce Download Monitor - Settings</h2><br><br>
	<form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="wdm_hidden" value="Y">
		<?php 
		settings_fields( 'wdm_plugin_options' );
		do_settings_sections( 'wdm_plugin_options' );
		
		echo $WDM_Functions->wdm_settings_html($wdm_settings_data); 
		
		// echo "<pre>"; print_r( $wdm_settings_data ); echo "</pre>";
		
		?>
		<p class="submit"><input class="button button-primary" type="submit" name="submit" value="<?php _e('Save Changes',WDM_TEXTDOMAIN ); ?>" /></p>
	</form>
</div>
