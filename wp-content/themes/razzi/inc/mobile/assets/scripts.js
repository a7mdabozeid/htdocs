(function ($) {
    'use strict';

    var razzi = razzi || {};
    razzi.init = function () {
        razzi.$body = $(document.body),
            razzi.$window = $(window),
            razzi.$header = $('#site-header');

        this.navigationBarDropdown();
        this.navigationBarClick();
        this.navigationBarOrderClick();

        this.updateCartTotal();

    };

    /**
     * Navigation Bar Dropdown
     */
    razzi.navigationBarDropdown = function () {
        if ( ! razzi.$body.hasClass( 'single-product' ) ) {
            return;
        }

        if ( ! $( '.rz-navigation-bar' ).hasClass( 'rz-navigation-bar__type-variable' ) ) {
            return;
        }

        var $wrapper = $('.rz-navigation-bar__type-variable');

        $wrapper.on("click", ".add_to_cart_button", function (e) {
            e.preventDefault();

            $wrapper.find(".add_to_cart_button").removeClass("active");
            $wrapper.find(".rz-navigation-bar__type-variable--content").slideUp();
            $(this).addClass("active");
            $(this).closest(".rz-navigation-bar__type-variable").find(".rz-navigation-bar__type-variable--content").stop().slideDown(300);
            razzi.$body.css({'overflow' : 'hidden'});
            razzi.$body.find('.rz-navigation-bar__off-layer').addClass("active");
        });

        razzi.$body.on("click", ".rz-navigation-bar__off-layer, .rz-navigation-bar__btn-close", function (e) {
            e.preventDefault();

            $wrapper.find(".rz-navigation-bar__type-variable--header .add_to_cart_button").removeClass("active");
            $wrapper.find(".rz-navigation-bar__type-variable--content").stop().slideUp(300);
            razzi.$body.find('.rz-navigation-bar__off-layer').removeClass("active");
            razzi.$body.removeAttr( 'style' );
        });
    };


    /**
     * Navigation Bar Dropdown
     */
    razzi.navigationBarClick = function () {
        var $btn = $( '.rz-navigation-bar__sticky-atc .rz-loop_button' ),
            $offset = 0,
            $heightSticky = razzi.$body.find( '.site-header' ).height();

        if ( $btn.hasClass( 'ajax_add_to_cart' ) ) {
            return;
        }

        if ( razzi.$body.hasClass( 'header-sticky' ) ) {
            $offset = $heightSticky;
        }

        $btn.on('click', function (event) {
            event.preventDefault();

            $('html,body').stop().animate({
                    scrollTop: $("form.cart").offset().top - $offset
                },
                'slow');
        });
    };

    /**
     * Navigation Bar Order Click
     */
    razzi.navigationBarOrderClick = function () {
        if ( ! razzi.$body.hasClass( 'woocommerce-checkout' ) ) {
            return;
        }

        razzi.$body.on( 'click', '.rz-navigation-bar__btn-place-order', function(e) {
            e.preventDefault();
            $('.woocommerce-checkout .woocommerce-checkout-payment #place_order').trigger('click');
        })
    };

    /**
     * Update cart total
     */
    razzi.updateCartTotal = function () {
        if ( ! razzi.$body.hasClass( 'woocommerce-cart' ) ) {
            return;
        }

        razzi.$body.on( 'updated_cart_totals', function() {
            var $order_total = $( '.cart_totals .order-total > td' ).html();

            if($( '.cart_totals .order-total').length) {}

            $('#rz-navigation-bar .order-total').html($order_total);
        })

        razzi.$body.on( 'wc_cart_emptied', function() {
            $('#rz-navigation-bar').hide();
        })
    };

    /**
     * Document ready
     */
    $(function () {
        razzi.init();
    });

})(jQuery);
