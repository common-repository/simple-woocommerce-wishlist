<?php
/**
 * Woocommerce settings API
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_qs_settings{

    public function __construct(){

        $this->id = 'qswishlist';

		$this->language_code = qs_wl_get_language();

        add_filter( 'woocommerce_get_sections_products', array( $this , 'wishlist_settings_page') );

        add_filter( 'woocommerce_get_settings_products', array( $this , 'wishlist_settings') , 10, 2 );

    }
	/**
	 * Add the link to the products tab
	 * @param  [type] $sections [description]
	 * @return [type]           [description]
	 */
    public function wishlist_settings_page( $sections ){

        $sections[ $this->id ] = __( 'WC Wishlist', 'qs-wishlist' );

    	return $sections;
    }

	/**
	 * Create the required settings page fields
	 * @param  [type] $settings        [description]
	 * @param  [type] $current_section [description]
	 * @return [type]                  [description]
	 * @version     1.0
	 */
    public function wishlist_settings( $settings, $current_section ){

        	/**
        	 * Check the current section is what we want
        	 **/
        	if ( $current_section == $this->id ) {
				// get a list of pages that will be the source for the settings fields
				$pages = $this->get_pages_array();

        		$settings_slider = array();

        		// Add Title to the Settings
        		$settings_slider[] = array(
                    'name' => __( 'Wishlist Settings', 'qs-wishlist' ),
                    'type' => 'title',
                    'desc' => __( 'The following options are used to configure the Wishlist',
                    'qs-wishlist' ),
                    'id' => 'wcslider'
                );
        		// Add first checkbox option
        		$settings_slider[] = array(
        			'name'     => __( 'Wishlist page', 'qs-wishlist' ),
        			'desc_tip' => __( 'This is not a mandatory field, it will be used for the auto wishlist link purposes', 'qs-wishlist' ),
        			'id'       => $this->id . '_wishlist_page_created' . $this->language_code,
        			'type'     => 'select',
					'options'  => $pages,
        		);

				// Allow adding items to cart from the wishlist page
        		$settings_slider[] = array(
        			'name'     => __( 'Allow add to cart', 'qs-wishlist' ),
        			'desc_tip' => __( 'Allow users to add items to cart from the wishlist page', 'qs-wishlist' ),
        			'id'       => $this->id . '_allow_add_to_cart' . $this->language_code,
        			'type'     => 'checkbox'
        		);

				// Logged in users are only allowed to add items to the wishlist
				$settings_slider[] = array(
					'name'     => __( 'Allow only for logged in users', 'qs-wishlist' ),
					'desc_tip' => __( 'Allow only logged in users to add items to the wishlist', 'qs-wishlist' ),
					'id'       => $this->id . '_allow_logged_in_only' . $this->language_code,
					'type'     => 'checkbox'
				);
				// Disable wishlist for non logged in users
        		$settings_slider[] = array(
        			'name'     => __( 'Hide for non logged in users', 'qs-wishlist' ),
        			'desc_tip' => __( 'Wishlist will be availble for logged in users only.', 'qs-wishlist' ),
        			'id'       => $this->id . '_hide_for_non_logged_in' . $this->language_code,
        			'type'     => 'checkbox'
        		);

				// Redirect non logged in to login/register page
				$settings_slider[] = array(
					'name'     => __( 'Login page', 'qs-wishlist' ),
					'desc_tip' => __( 'Non logged in users will be redirected to this page if they try to add an item to the wishlist (applicable if Hide for non logged in users is not set).', 'qs-wishlist' ),
					'id'       => $this->id . '_login_page' . $this->language_code,
					'type'     => 'select',
					'options'  => $pages,
				);

				// Add to Wishlist button
				$settings_slider[] = array(
					'name'     => __( 'Add to wishlist button', 'qs-wishlist' ),
					'desc_tip' => __( 'Add to wishlist button title.', 'qs-wishlist' ),
					'id'       => $this->id . '_add_to_wishlist_button_label' . $this->language_code,
					'type'     => 'text',
					'default'  => __( 'Add to wishlist' , 'qs-wishlist' )
				);
				// Remove from Wishlist button
				$settings_slider[] = array(
					'name'     => __( 'Remove from wishlist button', 'qs-wishlist' ),
					'desc_tip' => __( 'Remove from wishlist button title.', 'qs-wishlist' ),
					'id'       => $this->id . '_remove_from_wishlist_button_label' . $this->language_code,
					'type'     => 'text',
					'default'  => __( 'Remove from wishlist' , 'qs-wishlist' )
				);
				// View Wishlist button
				$settings_slider[] = array(
					'name'     => __( 'View Wishlist button', 'qs-wishlist' ),
					'desc_tip' => __( 'View Wishlist button title.', 'qs-wishlist' ),
					'id'       => $this->id . '_view_wishlist_button_label' . $this->language_code,
					'type'     => 'text',
					'default'  => __( 'View wishlist' , 'qs-wishlist' )
				);
				// Wishlist page - Add to cart button
				$settings_slider[] = array(
					'name'     => __( 'Wishlist Page: Add to cart button', 'qs-wishlist' ),
					'desc_tip' => __( 'Wishlist Page: Add to cart button title.', 'qs-wishlist' ),
					'id'       => $this->id . '_wishlist_page_add_to_cart_button_label' . $this->language_code,
					'type'     => 'text',
					'default'  => __( 'Add to cart' , 'qs-wishlist' )
				);
				// Wishlist page - Select options button
				$settings_slider[] = array(
					'name'     => __( 'Wishlist Page: Select options button', 'qs-wishlist' ),
					'desc_tip' => __( 'Wishlist Page: Select options button title.', 'qs-wishlist' ),
					'id'       => $this->id . '_wishlist_page_select_options_button_label' . $this->language_code,
					'type'     => 'text',
					'default'  => __( 'Select options' , 'qs-wishlist' )
				);
				// Wishlist page - Remove from Wishlist button
				$settings_slider[] = array(
					'name'     => __( 'Wishlist Page: Remove from Wishlist button', 'qs-wishlist' ),
					'desc_tip' => __( 'Wishlist Page: Remove from Wishlist button title.', 'qs-wishlist' ),
					'id'       => $this->id . '_wishlist_page_remove_from_wishlist_button_label' . $this->language_code,
					'type'     => 'text',
					'default'  => __( 'Remove from wishlist' , 'qs-wishlist' )
				);

        		$settings_slider[] = array( 'type' => 'sectionend', 'id' => $this->id );

        		return $settings_slider;

        	/**
        	 * If not, return the standard settings
        	 **/
        	} else {
        		return $settings;
        	}
    }

	/**
	 * Get a list of pages to display on the select fields
	 * @return [type] [description]
	 */
	private function get_pages_array(){
		$args = array(
			'posts_per_page' => -1,
			'post_status'	 => 'publish',
			'post_type'		 => 'page'
		);

		$pages = get_posts( $args );

		$pages_array = array();
		if( $pages ){
			$pages_array[] = __( 'Select a page' , 'qs-wishlist');
			foreach( $pages as $page ){
				$pages_array[ $page->ID ] = $page->post_title;
			}
		}

		return $pages_array;
	}
}
