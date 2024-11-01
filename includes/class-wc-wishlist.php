<?php
/**
 * the main plugin class
 * @var [type]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_qs_wishlist{
    /**
	 * The plugin identifier.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name unique plugin id.
	 */
	protected $plugin_name;

    /**
	 * save the instance of the plugin for static actions.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $instance    an instance of the class.
	 */
    public static $instance;

    /**
     * a reference to the admin class.
     *
     * @since    1.0.0
     * @access   protected
     * @var      object
     */
    public $admin;

	/**
     * a reference to the plugin status .
     *
     * @since    1.0.0
     * @access   protected
     * @var      object    $admin    an instance of the admin class.
     */
    private $woocommerce_is_active;
    /**
	 * Define the plugin functionality.
	 *
	 * set plugin name and version , and load dependencies
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = 'wc-qs-wishlist';
		$this->version = '1.0.0';

		$this->load_dependencies();

        /**
         * Create an instance of the admin class
         * @var WC_qs_wishlist_admin
         */
        $this->admin = new WC_qs_wishlist_admin();
		$this->admin->plugin_name = $this->plugin_name;

        //Stop here if woocommerce is not installed and active
        $this->woocommerce_is_active = $this->admin->wc_is_active;
        /**
         * save the instance for static actions
         *
         */
        self::$instance = $this;

	}
	/**
	 * Initialize plugin
	 * @return [type] [description]
	 */
    public function init(){
        if( ! $this->woocommerce_is_active ){
            return false;
        }

        add_action( 'wp_enqueue_scripts', array( $this , 'load_frontend_scripts' ) );
        add_action( 'plugins_loaded', array( $this , 'load_wishlist_handler' ) );
		add_action( 'plugin_action_links_' . $this->plugin_basename , array( $this , 'link_to_settings_page' ) );


    }

	/**
	 * Load the frontend plugin handler
	 * @return [type] [description]
	 */
    public function load_wishlist_handler(){
		/* This plugin will be on for logged on users only */
		if( qs_wl_get_plugin_options('qswishlist_hide_for_non_logged_in') == 'yes' && ! is_user_logged_in() ){

		}else{
			$this->handler = new WC_qs_wishlist_handler();
		}

    }
    /**
     * Get the current plugin instance
     * @return [type] [description]
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	/**
	 * Register plugin frontened scripts and styles
	 * @return [type] [description]
	 */
    public function load_frontend_scripts(){

		wp_enqueue_style( 'wc-'.$this->plugin_name.'-css', QS_WL_FRONTEND_CSS_URL . '/'.$this->plugin_name.'-front-css.css' );
        wp_enqueue_script( 'wc-'.$this->plugin_name.'-script', QS_WL_FRONTEND_JS_URL . '/'.$this->plugin_name.'-front-script.js' , array( 'jquery' ) , NULL , true );
        wp_localize_script( 'wc-'.$this->plugin_name.'-script', 'qs_args', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }
    /**
     * update the version of the plugin to the options table for future data changes.
     *
     */
    public static function activation_handler() {
        $instance = self::get_instance();

        update_option( $instance->plugin_name.'_ver' , $instance->version );

		$instance->generate_wishlist_page();

    }
	/**
	 * Creates a page that will handle the wishlist page
	 */
	public function generate_wishlist_page(){

		if( ! qs_wl_get_plugin_options( 'qs-wishlist_wishlist_page_created' ) ){


			$args = array(
				'post_content' => '[qs_wishlist]',
				'post_title'   => __( 'Wishlist' , 'qs-wishlist' ),
				'post_type'	   => 'page',
				'post_status'  => 'publish'
			);

			$wishlist_page_id = wp_insert_post( $args );

			update_option( 'qs-wishlist_wishlist_page_created' , $wishlist_page_id );
		}
	}

    /**
     * Actions to perform when plugin is uninstalled.
     *
     */
    public static function deactivation_handler() {

    }

	/**
	 * Link to the settings page
	 */
	 public function link_to_settings_page( $links ) {

	 	$links = array_merge( array(
	 		'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=qswishlist' ) ) . '">' . __( 'Settings', 'qs-wishlist' ) . '</a>'
	 	), $links );

	 	return $links;
 	}

    /**
     * Load the required dependencies for this plugin.
     *
     * - wc-boxit-functions.php. General global plugin functions.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * General global plugin functions
       */
        require_once QS_WL_INCLUDES_PATH . 'wc-wishlist-functions.php';
        /**
         * admin class
       */
        require_once QS_WL_INCLUDES_PATH . 'class-wc-wishlist-admin.php';
        /**
         * Global actions and filters
         */
        require_once QS_WL_INCLUDES_PATH . 'wc-wishlist-hooks.php';

        /**
         * Woocommerce options page
         */
        require_once QS_WL_INCLUDES_PATH . 'class-wc-settings-page.php';

        /**
         * Woocommerce options page
         */
        require_once QS_WL_INCLUDES_PATH . 'class-wc-wishlist-handler.php';
        /**
         * Woocommerce options page
         */
        require_once QS_WL_INCLUDES_PATH . 'shortcodes/wishlist-page.php';

    }


}
