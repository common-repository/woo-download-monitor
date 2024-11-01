<?php	
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	if ( get_query_var('paged') ) { $paged = get_query_var('paged');} 
	elseif ( get_query_var('page') ) { $paged = get_query_var('page'); } 
	else { $paged = 1; }
	
	
	
	$meta_query = array();		
	$meta_query[] = array(
		'key'     => '_downloadable',
		'value'   => 'yes',
		'compare' => '=',
	);
	
	$WDM_Product_query = new WP_Query( array (
		'post_type' => array( 'product', 'product_variation' ),
		'post_status' => array( 'publish' ),
		'meta_query' => $meta_query,
		'posts_per_page' => -1,
		'paged' => $paged,
	) );
	
	global $wpdb;
	$counter = 0;
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2>WooCommerce Download Monitor - <?php echo __('Statistics', WDM_TEXTDOMAIN); ?></h2><br><br>
	
	<div class="wdm_stat_container">
	
		<div class="wdm_stat_header">
			<div class="dl_meta wdm_counter"><?php echo __('Serial', WDM_TEXTDOMAIN ); ?></div>
			<div class="dl_meta wdm_product_img"><span><?php echo __('Thumbnail', WDM_TEXTDOMAIN ); ?></span></div>
			<div class="dl_meta wdm_product_name"><span><?php echo __('Product Name', WDM_TEXTDOMAIN ); ?></span></div>
			<div class="dl_meta wdm_product_price"><span><?php echo __('Product Price', WDM_TEXTDOMAIN ); ?></span></div>
			<div class="dl_meta wdm_order_data"><span><?php echo __('Order Data', WDM_TEXTDOMAIN ); ?></span></div>
			<div class="dl_meta wdm_user_data"><span><?php echo __('User Data', WDM_TEXTDOMAIN ); ?></span></div>
			<div class="dl_meta wdm_dl_count"><span><?php echo __('Download Count', WDM_TEXTDOMAIN ); ?></span></div>
			<div class="dl_meta wdm_dl_time"><span><?php echo __('Download Time', WDM_TEXTDOMAIN ); ?></span></div>
		</div>
		
		<div class="wdm_stat_list">
		<?php
			if ( $WDM_Product_query->have_posts() ) : 
				while ( $WDM_Product_query->have_posts() ) : $WDM_Product_query->the_post();
					include WDM_PLUGIN_DIR . 'templates/single-stat.php';
				endwhile;

				$big = 999999999;
				$paginate_links = paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, $paged ),
					'total' => $WDM_Product_query->max_num_pages
				) );
				echo "<div class='paginate'>$paginate_links</div> ";
				wp_reset_query();
			endif;
		?>
		</div>
	
	</div>
	
	
</div>
