<?php
/**
 * The template being used to display the add to wishlist cart
 * You can override this template by creating a file on your theme under the path yourtheme/templates/wishlist-page.php
 *
 *
 * @author 		QS Inc
 * @package 	WooCommerce/qs/Wishlist
 * @version     1.0
 */?>

<div class="woocommerce woocommerce-cart woocommerce-wishlist">
    <div class="large-12 columns col_9">

        <?php if( qs_show_wishlist() ): ?>

            <?php if( $wishlist = qs_get_wishlist_items() ):?>

                    <?php //do_action( 'wc_wl_before_cart' , $wishlist ); ?>

                    <table class="shop_table shop_table_responsive cart" cellspacing="0">
                        <thead>
                            <tr>
                                <!-- <th class="product-thumbnail">&nbsp;</th> -->
                                <th class="product-thumbnail"><?php _e( 'Product Thumbnail', 'qs-wishlist' ); ?></th>
                                <th class="product-name"><?php _e( 'Product name', 'qs-wishlist' ); ?></th>
                                <th class="product-stock"><?php _e( 'Stock status', 'qs-wishlist' ); ?></th>
                                <th class="product-quantity"><?php _e( 'Quantity', 'qs-wishlist' ); ?></th>
                                <th class="product-price"><?php _e( 'Price', 'qs-wishlist' ); ?></th>
                                <th class="product-remove"><?php _e( 'Actions', 'qs-wishlist' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ( $wishlist as $product_id => $params ){
                                $_product = $params['product'];
                                if ( $_product && $_product->exists() ){
                                    $product_permalink = $_product->is_visible() ? $_product->get_permalink() : '';
                                    ?>
                                    <tr class="cart_item" data-productid="<?php echo $product_id;?>">

                                        <td class="product-thumbnail">
                							<?php
                								$thumbnail = apply_filters( 'wc_wl_cart_item_thumbnail', $_product->get_image());

                								if ( ! $product_permalink ) {
                									echo $thumbnail;
                								} else {
                									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                								}
                							?>
                						</td>

                                        <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'qs-wishlist' ); ?>">
                							<?php
                								if ( ! $product_permalink ) {
                									echo $_product->get_name() . '&nbsp;';
                								} else {
                									echo sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() );
                								}

                								// Backorder notification
                								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                									echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'qs-wishlist' ) . '</p>';
                								}
                							?>
                						</td>

                                        <td class="stock-status" data-title="<?php _e( 'Stock status', 'qs-wishlist' ); ?>">
                                            <?php if( $_product->is_in_stock() ):?>
                                                <span class='instock'><?php _e('In Stock' , 'qs-wishlist' ); ?></span>
                                            <?php else:?>
                                                <span class='out-of-stock'><?php _e('Out of Stock' , 'qs-wishlist' ); ?></span>
                                            <?php endif;?>
                                        </td>

                                        <td class="product-quantity" data-title="<?php _e( 'Quantity', 'qs-wishlist' ); ?>">

                                            <?php
                                                if ( $_product->is_sold_individually() ) {
                                                    $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $_product->id );
                                                } else {
                                                    $product_quantity = woocommerce_quantity_input( array(
                                                        'input_name'  => "cart[{$product_id}][qty]",
                                                        'input_value' => $params['quantity'],
                                                        'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                                        'min_value'   => '0'
                                                    ), $_product, false );
                                                }
                                                echo $product_quantity;
                                            ?>

                                            <div class="clearfix"></div>
                                        </td>


                                        <td class="product-price" data-title="<?php _e( 'Price', 'qs-wishlist' ); ?>">
                                            <?php echo $_product->get_price_html(); ?>
                                            <div class="clearfix"></div>
                                        </td>

                                        <td class="product-remove qs-wishlist-actions-column" data-title="<?php _e( 'Remove', 'qs-wishlist' ); ?>">

                                            <?php
                                            $label_add_to_cart = qs_wl_get_plugin_options('qswishlist_wishlist_page_add_to_cart_button_label') ? qs_wl_get_plugin_options('qswishlist_wishlist_page_add_to_cart_button_label') : __('Add to cart' , 'qs-wishlist');

                                            $label_select_options = qs_wl_get_plugin_options('qswishlist_wishlist_page_select_options_button_label') ? qs_wl_get_plugin_options('qswishlist_wishlist_page_select_options_button_label') : __('Select options' , 'qs-wishlist');

                                            $label_remove_from_wishlist = qs_wl_get_plugin_options('qswishlist_wishlist_page_remove_from_wishlist_button_label') ? qs_wl_get_plugin_options('qswishlist_wishlist_page_remove_from_wishlist_button_label') : __('Remove' , 'qs-wishlist');
                                            ?>

                                            <a href="#" class="qs-remove-from-wishlist-cart qs-button-alert" title="<?php echo $label_remove_from_wishlist;?>">
                                                <?php echo $label_remove_from_wishlist; ?>
                                            </a>

                                            <?php if( qs_ws_allow_add_to_cart() && $_product->is_in_stock() ):?>
                                                <?php if( $_product->is_type( 'variable' ) ):?>
                                                    <a href="<?php echo $product_permalink;?>" class="add qs-button-info" title="<?php echo $label_select_options; ?>">
                                                        <?php echo $label_select_options; ?>
                                                    </a>
                                                <?php else:?>
                                                    <a href="#" class="add qs-add-to-cart qs-button-info" title="<?php echo $label_add_to_cart; ?>">
                                                        <?php echo $label_add_to_cart; ?>
                                                    </a>
                                                <?php endif;?>
                                            <?php endif;?>

                                            <span class='qs-loader'></span>
                                        </td>

                                    </tr>
                                    <?php
                                } //end if
                            } //end foreach

                            ?>

                        </tbody>
                    </table>

                    <?php do_action( 'wc_wl_after_cart' , $wishlist ); ?>
                <?php else:?>
                    <?php _e("No items currently in your wishlist." ,"qs-wishlist"); ?>
                <?php endif;?>
            <?php else:?>

                <?php _e("Wishlist is available for logged in users only." ,"qs-wishlist"); ?>

            <?php endif;?>
    </div>
</div>
