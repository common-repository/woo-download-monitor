<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

if( $product_id == 0 || empty( $arr_downloads ) ): ?>
<div class='woocommerce wdm_file_container'></div>
<?php else: ?>

<div class='woocommerce wdm_file_container'>

	<div class='wdm_file_container_header'>
		<?php echo __( "Available downloadable files", WDM_TEXTDOMAIN ); ?>
	
		<?php if( !empty( $attributes ) ):
			?><div class='wdm_attribute_list'>
			<?php foreach( $attributes as $attr_name => $atrr_value ): ?>
				<div class='wdm_attribute_inline'>
					<span class='wdm_attribute_name'><?php echo $attr_name; ?> : </span>
					<span class='wdm_attribute_value'><?php echo ucfirst($atrr_value) ?></span>
					<?php if( end($attributes) !== $atrr_value ) ?><span>, </span>
				</div>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	
	
	<?php foreach( $arr_downloads as $key => $single_download ){
		
		$file_id 	= isset( $single_download['id'] ) ? $single_download['id'] : '';
		$file_path 	= isset( $single_download['file'] ) ? $single_download['file'] : '';
		$file_name 	= isset( $single_download['name'] ) ? $single_download['name'] : __('No Name', WDM_TEXTDOMAIN);
		
		if( empty( $file_path ) ) continue;
		
		$path_info 	= pathinfo($file_path);
		$extension 	= isset( $path_info['extension'] ) ? $path_info['extension'] : '';
		$download_url = get_site_url()."?wdm_download=$product_id&file_id=$file_id";
		
		?> 
		<div class='wdm_single_file'>
			<div class='wdm_single_file_meta wdm_file_icon'>
			<?php if( $extension == 'zip' ) { ?>
				<span class="dashicons dashicons-media-archive"></span>
			<?php } elseif( $extension == 'pdf' ) { ?>
				<span class="dashicons dashicons-media-document"></span>
			<?php } elseif( $extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' ){ ?>
				<span class="dashicons dashicons-media-interactive"></span>
			<?php } else { ?>
				<span class="dashicons dashicons-media-default"></span>
			<?php } ?>
			</div>

			<?php if($wdm_display_file_extension=='yes') $file_name = $file_name.'.'.$extension; ?>
			
			<div class='wdm_single_file_meta wdm_file_name'><?php echo $file_name; ?></div>
		
			<a class='wdm_single_file_meta wdm_file_download button' href='<?php echo $download_url; ?>' target='_blank'><?php echo __("Download",WDM_TEXTDOMAIN); ?></a>
			
			<?php do_action( 'wdm_acton_inside_single_download', $single_download  ); ?>
			
			
		</div>
		
	<?php } ?>
	
</div>
	
<?php endif; ?>
	