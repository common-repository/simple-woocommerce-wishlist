<?php
/**
 * The plugins shortcodes
 * @version     1.0
 */

add_shortcode( 'qs_wishlist', 'qs_wishlist_page_view' );


/**
 * Display the wishlist
 * @return [type] [description]
 */
function qs_wishlist_page_view(){
    ob_start();

    WC_qs_wishlist_handler::get_wishlist_page_template();

    return ob_get_clean();
}
