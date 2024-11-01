<?php

/**
 * WC Wishlist
 *
 *
 * @link
 * @since             1.0.0
 * @package           wc_qs_wishlist
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Woocommerce Wishlist
 * Plugin URI: 		  http://querysol.com/product/woocommerce-simple-wishlist/
 * Description:       Adds a wishlist functionality to your woocommerce based shop. By Query Solutions.
 * Version:           1.0.0
 * Author:            Query Solutions LTD
 * Author URI: 		  http://www.querysol.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       qs-wishlist
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Load plugin dependencies
 */

define( 'QS_WL_PLUGIN_PATH' , plugin_dir_path( __FILE__ ) );
define( 'QS_WL_INCLUDES_PATH' , plugin_dir_path( __FILE__ ). 'includes/' );
define( 'QS_WL_TEMPLATE_PATH' , get_template_directory() );
define( 'QS_WL_ADMIN_JS_URL' , plugin_dir_url( __FILE__ ). 'admin/js/' );
define( 'QS_WL_ADMIN_CSS_URL' , plugin_dir_url( __FILE__ ). 'admin/css/' );
define( 'QS_WL_FRONTEND_JS_URL' , plugin_dir_url( __FILE__ ). 'assets/js/' );
define( 'QS_WL_FRONTEND_CSS_URL' , plugin_dir_url( __FILE__ ). 'assets/css/' );
define( 'QS_WL_IMAGES_URL' , plugin_dir_url( __FILE__ ). 'assets/css/' );
/**
 * The core plugin class
 */
require_once QS_WL_INCLUDES_PATH . 'class-wc-wishlist.php';

/**
 * Activation and deactivation hooks
 *
 */
register_activation_hook( __FILE__ , array( 'WC_qs_wishlist' , 'activation_handler' ) );
register_deactivation_hook( __FILE__ , array( 'WC_qs_wishlist' , 'deactivation_handler' ) );

/**
 * Begins execution of the plugin.
 *
 * Init the plugin process
 *
 * @since    1.0.0
 */
function init_wc_wishlist() {
    global $wc_wishlist;

	$wc_wishlist = new WC_qs_wishlist();
	$wc_wishlist->plugin_basename = plugin_basename( __FILE__ );

	$wc_wishlist->init();

}

init_wc_wishlist();
