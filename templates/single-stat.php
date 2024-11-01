<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	$counter ++;
	
	$currency 	= get_option( 'woocommerce_currency' );
	$price 		= get_post_meta( get_the_ID(), '_price', true );
	$thumb_id 	= get_post_meta( get_the_ID(), '_thumbnail_id', true );
	$thumb_url 	= wp_get_attachment_thumb_url( $thumb_id );
	
	$dl_record 	= $wpdb->get_row( "SELECT * FROM ".WDM_TABLE_NAME." WHERE product_id = " . get_the_ID() );
	$dl_count 	= $wpdb->get_var( "SELECT COUNT(*) FROM ".WDM_TABLE_NAME." WHERE product_id = " . get_the_ID() );
	
	// echo "<pre>"; print_r( $dl_record ); echo "</pre>";
?>
<div class="wdm_single_stat">

	<div class="dl_meta wdm_counter"><?php echo $counter; ?></div>
	<div class="dl_meta wdm_product_img"><img src="<?php echo $thumb_url; ?>" /></div>
	<div class="dl_meta wdm_product_name">
		<a href="post.php?post=<?php echo get_the_ID(); ?>&action=edit" target="_blank"><?php echo get_the_title(); ?></a>
		<p><a href="<?php echo get_the_permalink(); ?>" target="_blank"><?php echo __('Front View',WDM_TEXTDOMAIN); ?></a></p>
	</div>
	<div class="dl_meta wdm_product_price"><?php echo $price ." ". $currency; ?></div>
	
	
	<?php if( !empty( $dl_record->order_id ) ): ?>
	<div class="dl_meta wdm_order_data">
		<a href="<?php echo get_the_permalink( $dl_record->order_id ); ?>" href="_blank">
			<?php echo "#".$dl_record->order_id;?>
		</a>
		<a href="<?php echo get_the_permalink( $dl_record->order_id ); ?>" href="_blank">
			<?php echo get_the_title( $dl_record->order_id );?>
		</a>
	</div>
	<?php else: ?> <div class="dl_meta dl_meta_no_data wdm_order_data"><span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span></div>
	<?php endif; ?>
	
	
	
	<?php if( !empty( $dl_record->user_id ) ): ?>
	<?php $userdata = get_userdata( $dl_record->user_id ); ?>
	<div class="dl_meta wdm_user_data">
		<a href="user-edit.php?user_id=<?php echo $dl_record->user_id; ?>" href="_blank">
			<?php echo $userdata->display_name;?>
		</a>
		<p class="user_email"> <?php echo $dl_record->user_email; ?></p>
	</div>
	<?php else: ?> <div class="dl_meta dl_meta_no_data wdm_user_data"><span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span></div>
	<?php endif; ?>
	
	
	
	<div class="dl_meta wdm_dl_count">
		<span class="dl_count"><?php echo empty($dl_count) ? 0 : $dl_count; ?></span>
		<span class="dl_text"> <?php echo __('time(s)',WDM_TEXTDOMAIN); ?></span>
	</div>
	
	
	
	<?php if( !empty( $dl_record->datetime ) ): ?>
	<div class="dl_meta wdm_dl_time">
		<?php $time_ago = human_time_diff( $dl_record->datetime, current_time('mysql') ) . " ago"; ?>
		<span class="dl_time"><?php echo $dl_record->datetime; ?></span>
		<p class="dl_time_ago"><?php echo $time_ago; ?></p>
	</div>
	<?php else: ?> <div class="dl_meta dl_meta_no_data wdm_dl_time"><span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span></div>
	<?php endif; ?>
	

</div>
