<?php
/**
 * The template being used to display the add to wishlist button
 * You can override this template by creating a file on your theme under the path yourtheme/templates/wishlist-add-button.php
 *
 *
 * @author 		QS Inc
 * @package 	WooCommerce/qs/Wishlist
 * @version     1.0
 */?>

<?php
    global $product;

    $is_on_wishlist = qs_ws_is_on_wishlist( $product->get_id() );

    $label = qs_wl_get_plugin_options("qswishlist_add_to_wishlist_button_label") ? qs_wl_get_plugin_options("qswishlist_add_to_wishlist_button_label") : __("Add to wishlist" ,"qs-wishlist");

    $label_remove = qs_wl_get_plugin_options("qswishlist_remove_from_wishlist_button_label") ? qs_wl_get_plugin_options("qswishlist_remove_from_wishlist_button_label") : __("Remove from wishlist" ,"qs-wishlist");

    $label_view_wishlist = qs_wl_get_plugin_options("qswishlist_view_wishlist_button_label") ? qs_wl_get_plugin_options("qswishlist_view_wishlist_button_label") : __('View wishlist', 'qs-wishlist');
?>
<div class="qs-add-to-wishlist-wrap <?php echo $is_on_wishlist ? 'in-wishlist' : '';?> " data-productid="<?php echo $product->get_id();?>">
    <button type="button" name="add-to-wishlist" class="button qs-add-to-wishlist">
        <span>
            <?php _e( $label , "qs-wishlist" ); ?>
        </span>
    </button>
    <button type="button" name="remove-from-wishlist" class="button qs-remove-from-wishlist">
        <span>
            <?php _e( $label_remove , "qs-wishlist" ); ?>
        </span>
    </button>
    <?php if( $wishlist_link = qs_ws_get_wishlist_page_link() ):?>
        <small class="to-wishlist-link">
            <a href='<?php echo $wishlist_link;?>'><?php echo _e( $label_view_wishlist , "qs-wishlist" ); ?></a>
        </small>
    <?php endif;?>
    <span class='qs-loader'></span>
</div>
