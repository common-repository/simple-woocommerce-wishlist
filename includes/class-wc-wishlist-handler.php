<?php
/**
 * The plugins front-end handler
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_qs_wishlist_handler{
	/**
	 * Saves an instance of the class for static calls
	 * @version     1.0
	 */
    public static $instance;
	/**
	 * Set the name foe the wishlist
	 * @var [type]
	 */
	private $wishlist_name = 'wishlist-items';
	/**
	 * The constructor
	 * @version     1.0
	 */
    public function __construct(){

        $this->register_actions();

        $this->get_user_wishlist();

        self::$instance = $this;

    }

	/**
	 * gets the users wishlist from the update_user_meta
	 * @return array [an array that holds the raw wishlist]
	 * @version     1.0
	 */
    public function get_user_wishlist(){
        $this->wishlist = array();

        if( is_user_logged_in() ){

            $this->wishlist = get_user_meta( get_current_user_id() , $this->wishlist_name , true );

        }elseif( ! qs_wl_is_logged_in_users_only()  ){

			$this->wishlist = qs_get_wishlist_from_cookie( $this->wishlist_name );

		}

        return $this->wishlist;
    }
	/**
	 * get the user wishlist static function
	 * @return [type] [description]
	 * @version     1.0
	 */
	public static function get_wishlist(){
        $instance = self::get_instance();

		$wishlist = $instance->get_user_wishlist();

        return $wishlist;
    }
	/**
	 * Extract products and quanaities from the user wishlist
	 * @return [type] [description]
	 * @version     1.0
	 */
    public static function get_user_wishlist_items(){
        $instance = self::get_instance();

		$temp_wishlist = array();

        if( isset( $instance->wishlist ) && $instance->wishlist ){
            foreach( $instance->wishlist as $item_id => $params ){
                if( $item_id ){
                    $temp_wishlist[$item_id] = array(
						"product" => wc_get_product( $item_id ),
						"quantity" => $params['quantity']
					);
                }
            }

        }

        return $temp_wishlist;
    }
	/**
	 * egister ajax/woocommerce hooks
	 * @return [type] [description]
	 * @version     1.0
	 */
    public function register_actions(){

        add_action( 'woocommerce_after_shop_loop_item' , array( $this , 'add_to_wishlist_button' ) );
        add_action( 'woocommerce_variable_add_to_cart' , array( $this , 'add_to_wishlist_button' ) , 900 );
        add_action( 'woocommerce_simple_add_to_cart' , array( $this , 'add_to_wishlist_button' ) , 900 );

        add_action( 'wp_ajax_nopriv_add-to-wishlist', array( $this , 'ajax_add_to_wishlist') );
        add_action( 'wp_ajax_add-to-wishlist', array( $this , 'ajax_add_to_wishlist') );

        add_action( 'wp_ajax_nopriv_remove-from-wishlist', array( $this , 'ajax_remove_from_wishlist') );
        add_action( 'wp_ajax_remove-from-wishlist', array( $this , 'ajax_remove_from_wishlist') );

		add_action( 'wp_ajax_nopriv_wishlist-add-to-cart', array( $this , 'ajax_wishlist_add_to_cart') );
        add_action( 'wp_ajax_wishlist-add-to-cart', array( $this , 'ajax_wishlist_add_to_cart') );

    }
	/**
	 * [add to wishlist ajax handler]
	 * @version     1.0
	 */
	public function ajax_wishlist_add_to_cart(){
		global $woocommerce;

		if( isset( $_POST['productid'] ) && $_POST['productid'] ){
            $product_id = sanitize_text_field( $_POST['productid'] );
        }

		if( isset( $_POST['qty'] ) && $_POST['qty'] ){
			$qty = sanitize_text_field( $_POST['qty'] );
		}

		if( isset( $_POST['variation_id'] ) && $_POST['variation_id'] ){
			$variation_id = sanitize_text_field( $_POST['variation_id'] );
		}

		if( ! $qty || ! $product_id ){
			$response = array(
	            'success' => false,
	            'data'   => array(
	                'action' => 'added-to-cart',
	                'productid' => $product_id
	            )
	        );
		}else{
			$woocommerce->cart->add_to_cart( $product_id , $qty , $variation_id );

			$response = array(
	            'success' => true,
	            'data'   => array(
	                'action' => 'added-to-cart',
	                'productid' => $product_id
	            )
	        );
		}

		wp_send_json( $response );
	}
	/**
	 * [reomve from wishlist ajax handler]
	 * @return [type] [description]
	 * @version     1.0
	 */
    public function ajax_remove_from_wishlist(){
        if( isset( $_POST['productid'] ) && $_POST['productid'] ){
            $product_id = sanitize_text_field( $_POST['productid'] );
        }

        qs_remove_from_wishlist( $product_id );

        $response = array(
            'success' => true,
            'data'   => array(
                'action' => 'remove-from-wishlist',
                'productid' => $product_id
            )
        );

        wp_send_json( $response );
    }
	/**
	 * remove from wishlist static function
	 * @version     1.0
	 */
    public static function remove_from_wishlist( $product_id ){
        $instance = self::get_instance();

        if( qs_ws_is_on_wishlist( $product_id ) ){
            unset( $instance->wishlist[$product_id] );

            $instance->save_wishlist();
		}

    }
	/**
	 * Add to wishlist ajax handler
	 * @version     1.0
	 */
    public function ajax_add_to_wishlist(){
        if( isset( $_POST['productid'] ) && $_POST['productid'] ){
            $product_id = sanitize_text_field( $_POST['productid'] );
        }

		if( isset( $_POST['quantity'] ) && $_POST['quantity'] ){
            $quantity = sanitize_text_field( $_POST['quantity'] );
        }else{
			$quantity = 1;
		}


		if( ! is_user_logged_in() && qs_wl_is_logged_in_users_only() ){
			$response = array(
				'success' => false,
				'data'   => array(
					'action' => 'add-to-wishlist',
					'productid' => $product_id,
					'action_required' => 'login',
					'login_url' => qs_ws_get_login_url()
				)
			);
		}else{
			qs_add_to_wishlist( $product_id , $quantity );

	        $response = array(
	            'success' => true,
	            'data'   => array(
	                'action' => 'add-to-wishlist',
	                'productid' => $product_id
	            )
	        );
		}


        wp_send_json( $response );
    }
	/**
	 * gets the add to wishlist button template
	 * @version     1.0
	 */
    public function add_to_wishlist_button(){

        qs_get_template_part( 'wishlist' , 'add-button' );

    }
	/**
	 * Check if the product is no the wishlist
	 * @param  [int]  $product_id [the id of the product]
	 * @return boolean             [description]
	 * @version     1.0
	 */
    public static function is_on_wishlist( $product_id ){
        $instance = self::get_instance();

        return isset( $instance->wishlist[$product_id] );
    }

    /**
     * Get the current plugin instance
     * @return [type] [description]
     * @version     1.0
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	/**
	 * Add an item to the wishlist handler
	 * @param [type]  $product_id [the id of the product]
	 * @param integer $quantity   [how many to add]
	 * @version     1.0
	 */
    public static function add_to_wishlist( $product_id , $quantity = 1 ){
        $instance = self::get_instance();

        $instance->wishlist[$product_id] = array(
			'quantity' => $quantity
		);

        $instance->save_wishlist();

    }
	/**
	 * Saves the wishlist to the users meta
	 * @return [type] [description]
	 * @version     1.0
	 */
    public function save_wishlist(){
        $instance = self::get_instance();

		if( is_user_logged_in() ){
			update_user_meta( get_current_user_id() , $this->wishlist_name , $instance->wishlist );
		}elseif( ! qs_wl_is_logged_in_users_only() ){
			/* SET COOKIE EXPIRATION IN DAYS */
			$cookie_expiration = apply_filters( 'cookie_expiration' , 30 );

			setcookie( $this->wishlist_name , serialize( $instance->wishlist ) , time() + $cookie_expiration * DAY_IN_SECONDS , '/' );
		}
    }
	/**
	 * Gets the wishlist page template
	 * @return [type] [description]
	 * @version     1.0
	 */
    public static function get_wishlist_page_template(){
        qs_get_template_part( 'wishlist' , 'page' );
    }

	/**
	 * a filter to show or hide the wishlist
	 * @return [type] [description]
	 * @version     1.0
	 */
	public static function show_wishlist(){

		$show_wishlist = ! qs_wl_is_logged_in_users_only()|| is_user_logged_in();

		return apply_filters( 'qs_show_wishlist' , $show_wishlist );
	}

	/**
	 * Get the id of the wishlist page , can be set on the settings page
	 * @return [int] [page_id]
	 */
	public static function get_wishlist_page_id(){
		
		return apply_filters( 'qs_get_wishlist_page_id' , qs_wl_get_plugin_options( 'qswishlist_wishlist_page_created' ) );
	}

}
