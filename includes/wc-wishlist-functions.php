<?php
/**
 * General plugin functions
 * @version     1.0
 */

/**
 * Get template part function that will get the required templates files
 * @param  [string] $slug [the file slug]
 * @param  string $name [the name of the tempate]
 * @version     1.0
 */

function qs_get_template_part( $slug, $name = '' ) {
    $template = '';

    if ( $name  ) {
        $template = locate_template( array( "{$slug}-{$name}.php", QS_WL_TEMPLATE_PATH . "{$slug}-{$name}.php" ) );
    }

    if ( ! $template && $name && file_exists( QS_WL_TEMPLATE_PATH . "/woocommerce/{$slug}-{$name}.php" ) ) {
        $template = QS_WL_TEMPLATE_PATH . "/woocommerce/{$slug}-{$name}.php";
    }

    if ( ! $template && $name && file_exists( QS_WL_PLUGIN_PATH . "/templates/{$slug}-{$name}.php" ) ) {
        $template = QS_WL_PLUGIN_PATH . "/templates/{$slug}-{$name}.php";
    }

    if ( ! $template ) {
        $template = locate_template( array( "{$slug}.php", QS_WL_TEMPLATE_PATH . "{$slug}.php" ) );
    }

    $template = apply_filters( 'qs_get_template_part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Checks if the product is in the wishlist
 * @param  string  $product_id [the id of the product]
 * @return boolean             [true/false]
 * @version     1.0
 */
function qs_ws_is_on_wishlist( $product_id = "" ){
    global $product;

    if( ! $product_id && ! $product ){
        return false;
    }elseif( $product ){
        $product_id = $product->get_id();
    }


    $is_on_wishlist = WC_qs_wishlist_handler::is_on_wishlist( $product_id );

    return $is_on_wishlist;
}

/**
 * Adds/updates an item to the wishlist
 * @param string  $product_id
 * @param integer $quantity   [how many items to add to the wishlist]
 * @version     1.0
 */
function qs_add_to_wishlist( $product_id = "" , $quantity = 1 ){
    global $product;
    $added = false;

    if( ! $product_id && ! $product && (isset( $_POST['productid'] ) && $_POST['productid'] ) ){
        return false;
    }elseif( $product ){
        $product_id = $product->get_id();
    }elseif( isset( $_POST['productid'] ) && $_POST['productid'] ){
        $product_id = sanitize_text_field( $_POST['productid'] );
    }

    WC_qs_wishlist_handler::add_to_wishlist( $product_id , $quantity );

    $added = true;

    return $added;
}
/**
 * Remove an item from the wishlist
 * @param  string $product_id [description]
 * @return [type]             [description]
 * @version     1.0
 */
function qs_remove_from_wishlist(  $product_id = "" ){
    global $product;
    $added = false;

    if( ! $product_id && ! $product && ( isset( $_POST['productid'] ) && $_POST['productid'] ) ){
        return false;
    }elseif( $product ){
        $product_id = $product->get_id();
    }elseif( isset( $_POST['productid'] ) && $_POST['productid'] ){
        $product_id = sanitize_text_field( $_POST['productid'] );
    }


    if( qs_ws_is_on_wishlist( $product_id ) ){

        WC_qs_wishlist_handler::remove_from_wishlist( $product_id );

        $added = true;
    }

    return $added;
}

/**
 * Returns a boolean rather to show or hide the wishlist
 * @return [type] [description]
 * @version     1.0
 */
function qs_show_wishlist(){
    return apply_filters( 'qs_show_wishlist' , WC_qs_wishlist_handler::show_wishlist() );
}
/**
 * Returns the users wishlist
 * @return [type] [description]
 * @version     1.0
 */
function qs_ws_get_user_wishlist(){
    return apply_filters( 'qs_ws_get_user_wishlist' , WC_qs_wishlist_handler::get_user_wishlist_items() );
}

/**
 * Returns the url of the wishlist page
 * @return [type] [description]
 * @version     1.0
 */
function qs_ws_get_wishlist_page_link(){
    $permalink = "";

    if( $page_id = qs_ws_get_wishlist_page_id() ){
        $permalink = get_permalink( $page_id );
    }

    return apply_filters( 'qs_ws_get_wishlist_page_link' , $permalink );
}

/**
 * Returnes the id of the wishlist page
 * @return [type] [description]
 * @version     1.0
 */
function qs_ws_get_wishlist_page_id(){
    return WC_qs_wishlist_handler::get_wishlist_page_id();
}

/**
 * returns a boolean on rather to allow or disallow adding to cart from the wishlist page
 * [qs_ws_allow_add_to_cart description]
 * @version     1.0
 */
function qs_ws_allow_add_to_cart(){
    return apply_filters( 'wl_wc_allow_add_to_cart' , qs_wl_get_plugin_options( "qswishlist_allow_add_to_cart" ) );
}

/**
 * returns a permalink to the login page
 * @return [type] [description]
 * @version     1.0
 */
function qs_ws_get_login_url(){
    if( qs_wl_get_plugin_options( "qswishlist_login_page" ) ){
        $link = get_permalink( qs_wl_get_plugin_options( "qswishlist_login_page" ) );
    }
    return apply_filters( 'wc_wl_get_login_url' , $link );
}
/**
 * Check if to enable wishlist
 * functionality for logged in users only
 * @return boolean [description]
 */
function qs_wl_is_logged_in_users_only(){
    return apply_filters( 'qs_wl_is_logged_in_users_only' , qs_wl_get_plugin_options( 'qs-wishlist_allow_logged_in_only' ) );
}
/**
 * A helper function to get the wishlist from a cookir
 * @param  [type] $wishlist_name [description]
 * @return [type]                [description]
 */
function qs_get_wishlist_from_cookie( $wishlist_name ){

    $wishlist = isset( $_COOKIE[ $wishlist_name ] ) && $_COOKIE[ $wishlist_name ] ? $_COOKIE[ $wishlist_name ] : '';

    return apply_filters( 'qs_get_wishlist_from_cookie' , maybe_unserialize( $wishlist ) );
}
/**
 * A helper function to get users wishlist items
 * @return [type] [description]
 */
function qs_get_wishlist_items(){

    return WC_qs_wishlist_handler::get_user_wishlist_items();
}
/**
 * A helper function to get plugin options
 */
function qs_wl_get_plugin_options( $option_name ){

    $language = qs_wl_get_language();
    return get_option( $option_name . $language );

}
/**
 * Get language to support diffrent option pages languages
 */
function qs_wl_get_language(){
    return defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'en';
}
