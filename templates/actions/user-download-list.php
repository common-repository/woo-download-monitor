<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	global $wpdb;
	
	$MAX_USER_DOWNLOAD 	= get_option( 'wdm_max_user_download_show', 5 );
	$MAX_USER_DOWNLOAD 	= empty( $MAX_USER_DOWNLOAD ) ? 5 : $MAX_USER_DOWNLOAD;
	$DOWNLOAD_ID 		= isset( $single_download['id'] ) ? $single_download['id'] : '';
			
	// echo "<pre>"; print_r( $wdm_query ); echo "</pre>";
		
?>

<div class="user_download_list">
	<span class="user_download_title"><?php echo __( 'Downloaded by:', WDM_TEXTDOMAIN ); ?> </span>
	
	<?php if( empty( $DOWNLOAD_ID ) ): ?>
		
		<span class="user_download_show_error"><?php echo __( 'Invalid download ID !', WDM_TEXTDOMAIN ); ?></span>
	
	<?php else:
	
		$wdm_query 	= "SELECT * FROM ".WDM_TABLE_NAME." WHERE download_id='$DOWNLOAD_ID' ORDER BY id DESC LIMIT $MAX_USER_DOWNLOAD";
		$wdm_downloads = $wpdb->get_results( $wdm_query, OBJECT );

		if( empty( $wdm_downloads ) ) { $wdm_downloads = array(); ?>
			<span class="user_download_show_error"><?php echo __( 'No download found !', WDM_TEXTDOMAIN ); ?></span>
		<?php } ?>
		
		<span class="user_download_avatars">
			<?php foreach( $wdm_downloads as $dl_record ): ?>
			<span class="wdm_tooltip user_download_avatar">
				<?php echo get_avatar( $dl_record->user_id,18 ); ?>
				<div class="ttt"> <?php 
					$time_ago = human_time_diff( date("U", strtotime( $dl_record->datetime )), date("U", strtotime( current_time('mysql') )) );
					$user_data = get_userdata( $dl_record->user_id );
					
					if( !empty( $user_data->display_name ) ) echo "<span>{$user_data->display_name}</span><br>"; else echo __('Guest',WDM_TEXTDOMAIN ).'<br>';
					echo "<span class='corange'>Downloaded <i>$time_ago</i> ago</span>";
				?> </div>
			</span>
			<?php endforeach; ?>
			
			<?php //if( count( $wdm_downloads ) >= $MAX_USER_DOWNLOAD ) :?>
				<!-- <a class="wdm_tooltip user_download_view_more dashicons dashicons-plus" href=""><span class="ttt">View More</span></a> -->
			<?php //endif; ?>
			
		</span>
		
	<?php endif; ?>
	
</div>