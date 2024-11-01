<?php	
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

$wdm_item_per_page = get_option('wdm_item_per_page', 20);


global $wpdb;

$paged 		= isset( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : 1;
$counter 	= 0;
$PER_PAGE 	= $wdm_item_per_page;
$OFFSET 	= ($paged - 1) * $PER_PAGE ;
$wdm_query 	= "SELECT * FROM ".WDM_TABLE_NAME." ORDER BY id DESC LIMIT $PER_PAGE OFFSET $OFFSET";

$wdm_downloads = $wpdb->get_results( $wdm_query, OBJECT );
if( empty( $wdm_downloads ) ) $wdm_downloads = array();

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2>WooCommerce Download Monitor - Download Records</h2><br><br>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
            <th id="" class="manage-column column-cb check-column" scope="col"> </th>
            <th width="100" id="" class="thumb column-thumb" scope="col">Thumbnail</th>
            <th id="" class="" scope="col">Product</th>
            <th id="" class="" scope="col">File</th>
            <th id="" class="" scope="col">Price</th>
            <th id="" class="s" scope="col">Order</th>
            <th id="" class="" scope="col">User</th>
            <th id="" class="" scope="col">Date & Time</th>
        </tr>
        </thead>


        <tbody id="the-list">

        <?php

        if(!empty($wdm_downloads)):
        foreach( $wdm_downloads as $dl_record ):



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

            <tr id="" class="">
                <td id="" class="manage-column column-cb check-column" scope="col"> </td>
                <td class="" data-colname="thumb column-thumb"><img width="40" src="<?php echo $thumb_url; ?>" /></td>
                <td class="" data-colname="Product">
                    <strong><a href="<?php echo $product_admin_url; ?>" target="_blank">
                        <?php echo get_the_title( $dl_record->product_id ); ?>

                    </a></strong>
                    <div class="row-actions">

                        <span class="id">ID: <?php echo $dl_record->product_id; ?> | </span><span class="edit"><a href="<?php echo $product_admin_url; ?>" aria-label="">Edit</a> | </span><span class="inline hide-if-no-js"><a href="<?php echo get_permalink( $dl_record->product_id ); ?>" class="editinline" aria-label="” inline">View</a> </span>


                    </div>
                </td>
                <td class="" data-colname="File">
                    <a href="<?php echo $product_admin_url; ?>#woocommerce-product-data" target="_blank">

                        <?php echo empty( $dl_file_name ) ? "" : $dl_file_name; ?>
                    </a>
                </td>


                <td class="" data-colname="Price"><?php echo $price ." ". $currency; ?></td>
                <td class="" data-colname="Order">

                    <?php if( !empty( $dl_record->order_id ) ): ?>

                            <a href="post.php?post=<?php echo $dl_record->order_id; ?>&action=edit" target="_blank">
                                <?php echo "#".$dl_record->order_id;?>
                            </a>
                            <a href="post.php?post=<?php echo $dl_record->order_id; ?>&action=edit" target="_blank">
                                <?php //echo get_the_title( $dl_record->order_id );?>
                            </a>

                    <?php else: ?> <span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span>
                    <?php endif; ?>

                </td>
                <td class="" data-colname="User">

                    <?php if( !empty( $dl_record->user_id ) ): ?>
                        <?php $userdata = get_userdata( $dl_record->user_id );

                        ?>

                            <a href="user-edit.php?user_id=<?php echo $dl_record->user_id; ?>" target="_blank">
                                <?php //echo get_avatar($dl_record->user_id, 30); ?>
                                <?php echo $userdata->display_name;?>
                            </a>

                    <?php else: ?>
                        <span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span>
                    <?php endif; ?>

                </td>
                <td class="" data-colname="Date">

                    <?php if( !empty( $dl_record->datetime ) ):

                        $date=date_create($dl_record->datetime);


                        ?>

                            <?php
                            //$time_ago = human_time_diff( date("U", strtotime( $dl_record->datetime )), date("U", strtotime( current_time('mysql') )) ); ?>
                            <span class="dl_time"><?php echo date_format($date,"M d, Y h:i:s A"); //echo $dl_record->datetime; ?></span>
                            <p class="dl_time_ago"><?php //echo __('Last Download',WDM_TEXTDOMAIN); ?> <?php //echo $time_ago; ?> <?php //echo __(' ago',WDM_TEXTDOMAIN); ?></p>

                    <?php else: ?> <span class="wdm_no_data"><?php echo __('No Data',WDM_TEXTDOMAIN); ?></span>
                    <?php endif; ?>

                </td>

            </tr>


            <?php

        endforeach;

        else:

            ?>
            <tr>
                <th id="" class="manage-column column-cb check-column" scope="col"> </th>
                <td colspan="7">No download yet.</td>

            </tr>
        <?php



        endif;

        ?>

        </tbody>

        <tfoot>
        <tr>
            <th id="" class="manage-column column-cb check-column" scope="col"> </th>
            <th id="" class="manage-column thumb column-thumb" scope="col">Thumbnail</th>
            <th id="" class="" scope="col">Product</th>
            <th id="" class="" scope="col">File</th>
            <th id="" class="" scope="col">Price</th>
            <th id="" class="s" scope="col">Order</th>
            <th id="" class="" scope="col">User</th>
            <th id="" class="" scope="col">Date & Time</th>
        </tr>
        </tfoot>

    </table>




		<?php 

		$num_rows_query = $wpdb->get_results("SELECT * FROM ".WDM_TABLE_NAME." ORDER BY id DESC");
		$big = 999999999;
		$paginate_links = paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, $paged ),
			'prev_text'          => '«',
			'next_text'          => '»',
			'total' => (int)ceil($wpdb->num_rows / $PER_PAGE)
		) );
		
		//echo "<div class='paginate'>$paginate_links</div> ";
		//var_dump($paginate_links);
		?>

        <div class="tablenav bottom">

            <div class="tablenav-pages">

                <span class="pagination-links">

                    <?php
                    echo $paginate_links;
                    ?>

            </div>
            <br class="clear">
        </div>
	

	
	
</div>
