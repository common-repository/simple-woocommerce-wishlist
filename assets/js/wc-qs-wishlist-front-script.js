
var qs_wishlist;

var QS_Wishlist = (function(){
    var qs_WishlistObj = function(){ return constructor.apply(this,arguments); };
    function constructor(parent,elem_id,elem_val){

        this.initSelectors();

        this.initEventListeners();

        this.init();
    }
    return qs_WishlistObj;
})();

QS_Wishlist.prototype = {
    init: function(){
        
    },

    initSelectors : function(){
        this.addTowishlistButton = '.qs-add-to-wishlist';
        this.removeFromWishlistButton = '.qs-remove-from-wishlist';
        this.removeFromWishlistCartButton = '.qs-remove-from-wishlist-cart';
        this.loader = '.qs-loader';
        this.sizeSelector = '.woocommerce-wishlist .size_inner';
        this.colorSelector = '.woocommerce-wishlist .product-color-wrap select';
        this.addTocartButton = '.qs-add-to-cart';
        this.quantityBox = '.woocommerce-wishlist .quantity input';
    },

    initEventListeners: function(){
        //Add to wishlist button clicked
        jQuery( document.body ).on( 'click' , this.addTowishlistButton , this.onAddToWishlistClick.bind( this ) );
        //remove from wishlist button clicked
        jQuery( document.body ).on( 'click' , this.removeFromWishlistButton , this.onRemoveFromListClick.bind( this ) );
        //remove from wishlist on cart page was clicked
        jQuery( document.body ).on( 'click' , this.removeFromWishlistCartButton , this.onRemoveFromListCartClick.bind( this ) );

        //add to cart button was clicked
        jQuery( document.body ).on( 'click' , this.addTocartButton , this.onAddedToCart.bind( this ) );
        //quantity on wishlist changed
        jQuery( document.body ).on( 'change' , this.quantityBox , this.onQuantityChange.bind( this ) );


    },
    onQuantityChange: function( e ){
        var changedQuantity = jQuery( e.currentTarget );

        var product_id = changedQuantity.parents("tr").data("productid");

        var quantity = changedQuantity.val();

        this.addToWishlist( product_id , quantity );
    },
    onAddedToCart: function( e ){
        e.preventDefault();

        var clickedButton = jQuery( e.currentTarget );

        var product_id = clickedButton.parents("tr").data("productid");
        var qty = clickedButton.parents("tr").find('input.qty').val();

        if( product_id && qty ){
            this.showLoader( product_id );

            this.addToCart( product_id ,"" , qty);
        }
    },
    addToCart: function( product_id ,variation_id , qty ){
        var _this = this;
        jQuery.post(
        	qs_args.ajaxurl,
        	{
        		action : 'wishlist-add-to-cart',
        		productid : product_id,
                variation_id : variation_id,
                qty: qty
        	},
        	function( response ) {
                if( typeof response.data.productid != 'undefined' && response.success ){
                    _this.hideLoader( response.data.productid );
                    _this.removeWishlistRow( response.data.productid );
                    _this.removeFromWishlist( product_id );

                    jQuery( document.body ).trigger('added-to-cart' , response.data );
                }

        	},
            'json'
        );
    },
    removeWishlistRow: function( product_id ){
        jQuery("[data-productid="+ product_id +"]").fadeOut(function(){
            jQuery(this).remove();
        });
    },
    onRemoveFromListCartClick: function( e ){
        e.preventDefault();

        var clickedButton = jQuery( e.currentTarget );

        var product_id = clickedButton.parents('.cart_item').data( 'productid' );

        if( product_id ){
            this.showLoader( product_id );

            this.removeFromWishlist( product_id );
        }
    },
    onRemoveFromListClick: function( e ){

        var clickedButton = jQuery( e.currentTarget );

        var product_id = clickedButton.parents('.qs-add-to-wishlist-wrap').data( 'productid' );

        if( product_id ){
            this.showLoader( product_id );

            this.removeFromWishlist( product_id );
        }

    },

    removeFromWishlist: function( product_id ){

        var _this = this;
        jQuery.post(
        	qs_args.ajaxurl,
        	{
        		action : 'remove-from-wishlist',
        		productid : product_id
        	},
        	function( response ) {
                if( typeof response.data.productid != 'undefined' ){
                    _this.hideLoader( response.data.productid );
                    _this.removedFromWishlist( response.data.productid );
                }

                jQuery( document.body ).trigger('removed-from-wishlist' , response.data );
        	},
            'json'
        );

    },

    onAddToWishlistClick:function( e ){
        var clickedButton = jQuery( e.currentTarget );

        var product_id = clickedButton.parents('.qs-add-to-wishlist-wrap').data( 'productid' );

        var quantity = clickedButton.parents('.qs-add-to-wishlist-wrap').find( '.qty' ).val();

        if( product_id ){
            this.showLoader( product_id );

            this.addToWishlist( product_id , quantity );
        }
    },

    showLoader: function( product_id ){
        jQuery( '[data-productid='+ product_id +'] ' + this.loader).addClass( 'show' );
    },

    hideLoader: function( product_id ){
        jQuery( '[data-productid='+ product_id +'] ' + this.loader ).removeClass( 'show' );
    },

    addToWishlist: function( product_id , quantity ){
        if( typeof quantity == 'undefined' ){
            quantity = 1;
        }
        var _this = this;
        jQuery.post(
        	qs_args.ajaxurl,
        	{
        		action : 'add-to-wishlist',
        		productid : product_id,
                quantity: quantity
        	},
        	function( response ) {
                _this.hideLoader( response.data.productid );
                if( typeof response.data.productid != 'undefined' && response.success){
                    _this.addedToWishlist( response.data.productid );
                }else if( typeof response.data.action_required != 'undefined' && response.data.action_required === 'login'){
                    jQuery( document.body ).trigger('added-to-wishlist-failed' , response.data );
                    jQuery( document.body ).trigger('login-required' , response.data );

                    if( typeof response.data.login_url != 'undefined' && response.data.login_url ){
                        window.location = response.data.login_url;
                    }
                }else{
                    jQuery( document.body ).trigger('added-to-wishlist-failed' , response.data );
                }

        	},
            'json'
        );
    },

    addedToWishlist: function( product_id ){

        jQuery( '[data-productid='+ product_id +']' ).addClass( 'in-wishlist' );

    },

    removedFromWishlist: function( product_id ){
        //remove row from wishlist
        jQuery( 'tr[data-productid='+ product_id +']' ).slideUp();
        jQuery( '[data-productid='+ product_id +']' ).removeClass( 'in-wishlist' );

    }
};

jQuery(document).ready(function(){
    qs_wishlist = new QS_Wishlist();
});
