<?php
/**
 * WooCommerce Notices template hooks.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce\Modules;

use Razzi\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of WooCommerce Notices
 */

class Notices {
    /**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    /**
     * Instantiate the object.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        // Popup add to cart HTML
        add_action( 'wp_footer', array( $this, 'popup_add_to_cart' ), 50 );

        // Popup add to cart Template
        add_action( 'wc_ajax_razzi_product_popup_recommended', array( $this, 'product_template_recommended' ) );

        add_action( 'razzi_product_popup_atc_recommendation', array(
            $this,
            'products_recommendation'
        ), 5 );

        add_action( 'woocommerce_widget_shopping_cart_total', array(
			$this,
			'widget_shopping_cart_count_notice'
		), 5 );

        add_filter( 'razzi_wp_script_data', array(
            $this,
            'notices_script_data'
        ), 30 );
   }

   /**
    * Get popup add to cart
    *
    * @since 1.0.0
    *
    * @return void
    */
   public function popup_add_to_cart() {
        if ( is_404() ) {
            return;
        }

        if ( Helper::get_option( 'added_to_cart_notice' ) != 'popup' ) {
            return;
        }
        ?>

        <div id="rz-popup-add-to-cart" class="rz-popup-add-to-cart rz-modal woocommerce">
            <div class="off-modal-layer"></div>
            <div class="modal-content container woocommerce">
                <div class="button-close">
                    <?php echo \Razzi\Icon::get_svg( 'close' ) ?>
                </div>
                <div class="product-modal-content">
                <div class="rz-product-popup-atc__notice">
                    <?php esc_html_e( 'Successfully added to your cart.', 'razzi' ) ?>
                </div>
               <div class="widget_shopping_cart_content"></div>
               <?php do_action( 'razzi_product_popup_atc_recommendation' ); ?>
                </div>

                <div class="razzi-posts__loading">
                    <div class="razzi-loading"></div>
                </div>
            </div>
        </div>
        <?php
    }

   /**
    * Get product recommended
    *
    * @since 1.0.0
    *
    * @return void
    */
    public function product_template_recommended() {
        ob_start();

        if ( isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] )  ) {
            $product_id      = $_POST['product_id'];
            $product = wc_get_product( $product_id );

            $limit = Helper::get_option( 'added_to_cart_notice_products_limit' );
            $type  = Helper::get_option( 'added_to_cart_notice_products' );

             $query = new \stdClass();
            if ( 'related_products' == $type ) {
                $related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product_id, $limit, $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
                $related_products = wc_products_array_orderby( $related_products, 'rand', 'desc' );

                $query->posts = $related_products;
            } elseif ( 'upsells_products' == $type ) {
                $upsells = wc_products_array_orderby( array_filter( array_map( 'wc_get_product', $product->get_upsell_ids() ), 'wc_products_array_filter_visible' ), 'rand', 'desc' );

                $query->posts = $upsells;
            }

             if( count($query->posts) ) {
                 $this->products_recommended_content($query->posts);
             }

        }

        $output = ob_get_clean();
        wp_send_json_success( $output );
        die();
    }

    /**
    * Get products recommended
    *
    * @since 1.0.0
    *
    * @return void
    */
    public function products_recommendation() {
        if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
            return;
        }

        $limit = Helper::get_option( 'added_to_cart_notice_products_limit' );
        $type  = Helper::get_option( 'added_to_cart_notice_products' );

        if('none' == $type){
            return;
        }

        if('related_products' == $type || 'upsells_products' == $type ) {
            echo '<div class="rz-product-popup-atc__recommendation"></div>';
            return;
        }

        $atts = array(
            'per_page'     => intval( $limit ),
            'category'     => '',
            'cat_operator' => 'IN',
        );

        switch ( $type ) {
            case 'sale_products':
            case 'top_rated_products':
                $atts = array_merge( array(
                    'orderby' => 'title',
                    'order'   => 'ASC',
                ), $atts );
                break;

            case 'recent_products':
                $atts = array_merge( array(
                    'orderby' => 'date',
                    'order'   => 'DESC',
                ), $atts );
                break;

            case 'featured_products':
                $atts = array_merge( array(
                    'orderby' => 'date',
                    'order'   => 'DESC',
                ), $atts );
                break;
        }

        $args  = new \WC_Shortcode_Products( $atts, $type );
        $args  = $args->get_query_args();
        $query = new \WP_Query( $args );

        if( !count($query->posts) ) {
            return;
        }

        echo '<div class="rz-product-popup-atc__recommendation loaded">';
        $this->products_recommended_content($query->posts);
        wp_reset_postdata();
        echo '</div>';

    }

    /**
    * Get products recommended content
    *
    * @since 1.0.0
    *
    * @param $query_posts
    *
    * @return void
    */
    public function products_recommended_content($query_posts) {
        ?>
        <div class="recommendation-heading">
            <h2 class="product-heading"> <?php echo esc_html( Helper::get_option( 'added_to_cart_notice_products_title' ) );?> </h2>
            <div class="rz-swiper-buttons">
                  <?php echo \Razzi\Icon::get_svg('chevron-left', 'rz-swiper-button-prev'); ?>
                   <?php echo \Razzi\Icon::get_svg('chevron-right', 'rz-swiper-button-next'); ?>
            </div>
        </div>
        <div class="swiper-container recommendation-products-carousel">
            <ul class="product-items swiper-wrapper">
            <?php
            foreach ( $query_posts as $product_id ) {
                $_product = is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id;
                ?>

                <li class="product-item">
                    <a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
                        <?php echo wp_kses_post( $_product->get_image( 'woocommerce_thumbnail' ) ); ?>
                        <span class="product-title"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
                        <span class="product-price"><?php echo wp_kses_post( $_product->get_price_html() ); ?></span>
                    </a>
                </li>

                <?php
            }

            echo '	</ul>';
        echo '</div>';

    }

    /**
    * Get notices script data
    *
    * @since 1.0.0
    *
    * @param $data
    *
    * @return array
    */
    public function notices_script_data( $data ) {
        if ( Helper::get_option( 'added_to_cart_notice' ) == 'simple' ) {
            $data['added_to_cart_notice'] = array(
                'added_to_cart_text'              => esc_html__( 'has been added to your cart.', 'razzi' ),
                'successfully_added_to_cart_text' => esc_html__( 'Successfully added to your cart.', 'razzi' ),
                'cart_view_text'                  => esc_html__( 'View Cart', 'razzi' ),
                'cart_view_link'                  => function_exists( 'wc_get_cart_url' ) ? esc_url( wc_get_cart_url() ) : '',
                'cart_notice_auto_hide'           => intval( Helper::get_option( 'added_to_cart_notice_auto_hide' ) ) > 0 ? intval( Helper::get_option( 'added_to_cart_notice_auto_hide' ) ) * 1000 : 0,
            );
        }

        if ( Helper::get_option( 'added_to_cart_notice' ) != 'none' ) {
            $data['added_to_cart_notice']['added_to_cart_notice_layout'] = Helper::get_option( 'added_to_cart_notice' );
        }

        if ( intval( Helper::get_option( 'added_to_wishlist_notice' ) ) && defined( 'YITH_WCWL' ) ) {
            $data['added_to_wishlist_notice'] = array(
                'added_to_wishlist_text'    => esc_html__( 'has been added to your wishlist.', 'razzi' ),
                'added_to_wishlist_texts'   => esc_html__( 'have been added to your wishlist.', 'razzi' ),
                'wishlist_view_text'        => esc_html__( 'View Wishlist', 'razzi' ),
                'wishlist_view_link'        => esc_url( get_permalink( get_option( 'yith_wcwl_wishlist_page_id' ) ) ),
                'wishlist_notice_auto_hide' => intval( Helper::get_option( 'wishlist_notice_auto_hide' ) ) > 0 ? intval( Helper::get_option( 'wishlist_notice_auto_hide' ) ) * 1000 : 0,
            );
        }

        return $data;
    }

    /**
	 * Cart count notice
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function widget_shopping_cart_count_notice() {
		echo '<span class="woocommerce-mini-cart__count_notice hidden">';
		$count = WC()->cart->get_cart_contents_count();
        if ( $count > 1 ) {
            echo sprintf( __('There are %s items in your cart', 'razzi'), $count );
        } else {
            echo sprintf( __('There is %s item in your cart', 'razzi'), $count );
        }
		echo '</span>';
	}
}
