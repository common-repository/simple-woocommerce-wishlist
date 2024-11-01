<?php
/**
 * The plugins backend/Admin handler
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_qs_wishlist_admin{
    /**
     * Holds an array of notices to be displayed
     * @var [type]
     */
    private $notices;

    /**
     * Holds the plugin options
     * @var [type]
     */
    private $options;

    private $options_name;

    public function __construct(){

        $this->options_name = 'wc_wishlist_options';

        $this->options = $this->get_plugin_options();

        $this->init();

    }
    /**
	 * Initiallize required actions the plugin .
	 *
	 * set plugin name and version , and load dependencies
	 *
	 * @since    1.0.0
	 */
	private function init() {
        $this->verify_woocommerce();

        if( $this->wc_is_active ){

            $this->register_hooks();

            $this->add_woocommerce_settings_page();
        }
	}
    private function add_woocommerce_settings_page(){

        $this->woocommerce_settings_page = new WC_qs_settings();

    }
    /**
     * get the plugin admin options
     * @return [type] [description]
     */
    private function get_plugin_options(){

        return get_option( $this->options_name );

    }
    /**
     * save the plugin admin options
     * @return [type] [description]
     */
    private function update_plugin_options(){

        update_option( $this->options_name , $this->options );

    }

    /**
     * Register the plugin hooks and actions
     * @return [type] [description]
     */
    public function register_hooks(){

        /**
         * display notices hook
         */
        add_action( 'admin_notices' , array( $this , 'admin_notices' ) );
        /**
         * enqueue admin scripts and styles
         */
        add_action( 'admin_enqueue_scripts', array( $this , 'load_admin_scripts' ) );
        /**
         * catch dismiss notice action and add it to the dismissed notices array
         */
        add_action( 'wp_ajax_wc_boxit_dismiss_notices' , array( $this , 'wc_boxit_dismiss_notices' ) );


    }
	/**
	 * Register plugin admin scripts and styles
	 * @return [type] [description]
	 */
    public function load_admin_scripts(){

		wp_enqueue_style( 'wc-'.$this->plugin_name.'-css', QS_WL_FRONTEND_CSS_URL . '/'.$this->plugin_name.'-admin-css.css' );
        wp_enqueue_script( 'wc-'.$this->plugin_name.'-script', QS_WL_FRONTEND_JS_URL . '/'.$this->plugin_name.'-admin-script.js' , array( 'jquery' ) , NULL , true );

    }
    /**
     * display the notices that resides in the notices collection
     * @return [type] [description]
     */
    public function admin_notices(){
	
        if( $this->notices ){
            foreach( $this->notices as $admin_notice ){
                /**
                 * only disply the notice if it wasnt dismiised in the past
                 */
                $id = $admin_notice['id'];
                if( ! isset( $this->options['dismiss_notices'][$id] ) || ! $this->options['dismiss_notices'][$id] ){
                    echo "<div id='{$admin_notice['id']}' class='notice notice-{$admin_notice['type']} is-dismissible wc-wishlist-dismiss-notice-forever'>
                         <p>{$admin_notice['notice']}</p>
                     </div>";
                }

            }
        }
    }

    /**
     * adds notices to the class notices collection
     * @param array $notice an array of notice message and notice type
     * Types available are "error" "warning" "success" "info"
     */
    public function wp_add_notice( $notice = "" ){

        if( $notice ){
            $this->notices[] = array(
                'id'     => $notice['id'],
                'notice' => $notice['notice'],
                'type'   => isset( $type['type'] ) ? $type['type'] : 'warning'
            );
        }

    }

    /**
     * Verify that woocommerce is installed
     * Add notice in case woocommerce is not available
     * @return null
     */
    private function verify_woocommerce(){
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
           $this->wc_is_active = true;
        } else {
            // you don't appear to have WooCommerce activated
            $this->wc_is_active = false;

            $notice = array(
                'id'     => 'woocommerce-not-installed',
                'notice' => __( 'Boxit shipping plugin needs WooCommerce to be installed and activated' ),
                'type'   => 'warning'
            );

            $this->wp_add_notice( $notice );

        }

    }
}
