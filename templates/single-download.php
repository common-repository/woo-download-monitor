<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	$counter ++;
	
	$currency 	= get_option( 'woocommerce_currency' );
	$price 		= get_post_meta( $dl_record->product_id, '_price', true );
	$thumb_id 	= get_post_meta( $dl_record->product_id, '_thumbnail_id', true );
	$thumb_url 	= wp_get_attachment_thumb_url( $thumb_id );
	
	$wc_get_product = wc_get_product($dl_record->product_id);
	$arr_downloads 	= $wc_get_product->get_downloads();
	
	$dl_file_name	= isset( $arr_downloads[ $dl_record->download_id ]['name'] ) ? $arr_downloads[ $dl_record->download_id ]['name'] : '';

	$product_admin_url = "post.php?post=$dl_record->product_id&action=edit";
	if( get_post_type( $dl_record->product_id ) == 'product_variation' ){
		
		$post_parent_id = wp_get_post_parent_id( $dl_record->product_id );

		if( $post_parent_id != 0 )
		$product_admin_url = "post.php?post=$post_parent_id&action=edit";
	}

?>
<div class="wdm_single_stat">

	<div class="dl_meta wdm_counter"><?php echo $counter; ?></div>
	<div class="dl_meta wdm_product_img"><img src="<?php echo $thumb_url; ?>" /></div>
	<div class="dl_meta wdm_product_name">
		<a href="<?php echo $product_admin_url; ?>" target="_blank">
			<?php echo get_the_title( $dl_record->product_id ); ?>
			<?php echo empty( $dl_file_name ) ? "" : " - <i>$dl_file_name</i>"; ?>
		</a>
		<p><a href="<?php echo get_the_permalink( $dl_record->product_id ); ?>" target="_blank"><?php __('Front View',WDM_TEXTDOMAIN); ?></a></p>
	</div>
	<div class="dl_meta wdm_product_price"><?php echo $price ." ". $currency; ?></div>
	
	
	<?php if( !empty( $dl_record->order_id ) ): ?>
	<div class="dl_meta wdm_order_data">
		<a href="post.php?post=<?php echo $dl_record->order_id; ?>&action=edit" target="_blank">
			<?php echo "#".$dl_record->order_id;?>
		</a>
		<a href="post.php?post=<?php echo $dl_record->order_id; ?>&action=edit" target="_blank">
			<?php echo get_the_title( $dl_record->order_id );?>
		</a>
	</div>
	<?php else: ?> <div class="dl_meta dl_meta_no_data wdm_order_data"><span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span></div>
	<?php endif; ?>
	
	
	<?php if( !empty( $dl_record->user_id ) ): ?>
	<?php $userdata = get_userdata( $dl_record->user_id ); ?>
	<div class="dl_meta wdm_user_data">
		<a href="user-edit.php?user_id=<?php echo $dl_record->user_id; ?>" target="_blank">
			<?php echo $userdata->display_name;?>
		</a>
	</div>
	<?php else: ?> <div class="dl_meta dl_meta_no_data wdm_user_data"><span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span></div>
	<?php endif; ?>
	

	<?php if( !empty( $dl_record->datetime ) ): ?>
	<div class="dl_meta wdm_dl_time">
		<?php 
		$time_ago = human_time_diff( date("U", strtotime( $dl_record->datetime )), date("U", strtotime( current_time('mysql') )) ); ?>
		<span class="dl_time"><?php echo $dl_record->datetime; ?></span>
		<p class="dl_time_ago"><?php echo __('Last Download',WDM_TEXTDOMAIN); ?>: <?php echo $time_ago; ?> <?php echo __(' ago',WDM_TEXTDOMAIN); ?></p>
	</div>
	<?php else: ?> <div class="dl_meta dl_meta_no_data wdm_dl_time"><span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span></div>
	<?php endif; ?>
	

</div>
