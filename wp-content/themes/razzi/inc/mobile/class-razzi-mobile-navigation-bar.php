<?php
/**
 * Navigation Bar Mobile functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Mobile;
use Razzi\Helper;
use WeDevs\WeMail\Rest\Help\Help;

class Navigation_Bar {
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
	function __construct() {
		add_filter( 'razzi_site_footer_class', array( $this, 'footer_class' ) );

		add_filter( 'wp_footer', array( $this, 'navigation_bar_contents' ) );


		add_action( 'wp_footer', array( $this, 'category_menu_modal' ) );


		add_action( 'razzi_navigation_bar_before_atc_template', array(
			$this,
			'open_wrapper_product_type_variable'
		), 10 );
		add_action( 'razzi_navigation_bar_after_atc_template', array(
			$this,
			'close_wrapper_product_type_variable'
		), 30 );

		add_action( 'razzi_navigation_bar_content_atc_template', array( $this, 'get_content_navigation' ), 30 );
	}

	/**
	 * Add 'navigation bar' class to the body tag.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function footer_class( $classes ) {
		if ( $this->get_items_count() > 1 ) {
			$classes .= ' navigation-bar-standard';
		} else {
			$classes .= ' navigation-bar-simple';
		}

		return $classes;
	}

	/**
	 * Get navigation bar items
     *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_items() {
		global $product;

		if ( isset( $this->items ) ) {
			return $this->items;
		}

		$this->items    = array();
		$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );
		$classes = '';
		if ( Helper::is_catalog() ) {
			if ( in_array( $navigation_bar, array( 'simple_adoptive', 'standard_adoptive' ) ) ) {
				$this->items['els']   = array( 'filter' );
				$classes = 'rz-navigation-bar__simple';
			}
		} elseif ( is_singular( 'product' ) ) {
			if ( in_array( $navigation_bar, array( 'simple_adoptive' ) ) ) {
				$this->items['els'] 	= array( '', 'add-to-cart' );
				$classes 	= 'rz-navigation-bar__simple';
			} elseif ( in_array( $navigation_bar, array( 'standard_adoptive' ) ) ) {
				$this->items['els'] 	= array( '', 'add-to-cart' );
				$classes 	= 'rz-navigation-bar__sticky-atc rz-navigation-bar__standard';
			}

			if ( $product->is_type( 'variable' ) ) {
				$classes 	.= ' rz-navigation-bar__type-variable';
			}

			if ( ! $product->is_in_stock() ) {
				$classes 	.= ' rz-navigation-bar__type-out-of-stock';
			}


			if ( in_array( $navigation_bar, array( 'simple_adoptive', 'standard_adoptive' ) ) && Helper::get_option('mobile_floating_action_button') ) {
				$classes 	.= ' rz-navigation-bar--floating-button';
			}

		} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
			if ( WC()->cart->get_cart_contents_count() != 0 ) {
				if ( in_array( $navigation_bar, array( 'simple_adoptive' ) ) ) {
					$this->items['els']   = array('', 'add-to-checkout' );
					$classes = 'rz-navigation-bar__simple';
				} elseif ( in_array( $navigation_bar, array( 'standard_adoptive' ) ) ) {
					$this->items['els']   = array('', 'add-to-checkout' );
					$classes = 'rz-navigation-bar__standard rz-navigation-bar__sticky-atc';
				}

				if ( in_array( $navigation_bar, array( 'simple_adoptive', 'standard_adoptive' ) ) && Helper::get_option('mobile_floating_action_button') ) {
					$classes 	.= ' rz-navigation-bar--floating-button';
				}
			}
		} elseif ( function_exists( 'is_checkout' ) && is_checkout() && ( ! is_wc_endpoint_url( 'order-pay' ) ) && ( ! is_wc_endpoint_url( 'order-received' ) )) {
			if ( in_array( $navigation_bar, array( 'simple_adoptive', 'standard_adoptive' ) ) ) {
				$this->items['els']   = array( '','add-to-order' );
				$classes = 'rz-navigation-bar__standard rz-navigation-bar__add-to-order';
			}
		}

		if ( ! $this->items ) {
			if ( in_array( $navigation_bar, array( 'simple', 'simple_adoptive' ) ) ) {
				$this->items['els']   = (array) Helper::get_option( 'mobile_navigation_bar_item' );
				$classes = 'rz-navigation-bar__simple rz-navigation-bar_items-' . Helper::get_option( 'mobile_navigation_bar_item_align' );
			} else {
				$this->items['els']   = (array) Helper::get_option( 'mobile_navigation_bar_items' );
				$classes = 'rz-navigation-bar__standard';
			}
		}

		$this->items['class'] = $classes;

		return $this->items;
	}

	/**
	 * Get navigation bar items
     *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_items_count() {
		$items = $this->get_items();

		if ( $items && isset( $items['els'] ) ) {
			return count( $items['els'] );
		}

		return 0;

	}

	/**
	 * Display navigation bar items
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function navigation_bar_contents() {
		global $product;
		$items = $this->get_items();

		if ( ! $items || ! isset( $items['els'] ) ) {
			return;
		}

		?>
		<?php if ( is_singular( 'product' ) && $product->is_type( 'variable' ) ) : ?>
            <div class="rz-navigation-bar__off-layer"></div>
		<?php endif; ?>
        <div id="rz-navigation-bar" class="rz-navigation-bar <?php echo esc_attr( $items['class'] ); ?>">
			<?php $this->navigation_bar_template_item( $items['els'] ); ?>
        </div>
		<?php
	}

	/**
	 * Display header items
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function navigation_bar_template_item( $items ) {
		foreach ( $items as $item ) {

		    if( empty($item) ) {
		        continue;
            }

			$template_file = $item;

			switch ( $item ) {
				case 'cart':
					if ( ! class_exists( 'WooCommerce' ) ) {
						$template_file = '';
						break;
					}

					\Razzi\Theme::instance()->set_prop( 'modals', $item );
					break;

				case 'search':
					\Razzi\Theme::instance()->set_prop( 'modals', $item );
					break;

				case 'account':
					\Razzi\Theme::instance()->set_prop( 'modals', $item );
					break;

				case 'filter':
					\Razzi\Theme::instance()->set_prop( 'modals', $item );
					break;

				case 'add-to-cart':
					\Razzi\Theme::instance()->set_prop( 'modals', $item );
					break;

				case 'add-to-checkout':
					\Razzi\Theme::instance()->set_prop( 'modals', $item );
					break;

			}

			if ( $template_file ) {
				get_template_part( 'template-parts/navigation-bar/' . $template_file );
			}
		}
	}

	/**
	 * Check has menu
     *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_menu() {
		$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );
		if ( $navigation_bar == 'none' ) {
			return false;
		}

		if ( in_array( $navigation_bar, array( 'standard', 'standard_adoptive' ) ) ) {
			if ( ! in_array( 'menu', (array) Helper::get_option( 'mobile_navigation_bar_items' ) ) ) {
				return false;
			}
		} else {
			if ( 'menu' != Helper::get_option( 'mobile_navigation_bar_item' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Add menu mobile modal
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function category_menu_modal() {
		if ( ! $this->has_menu() ) {
			return;
		}
		?>
        <div id="mobile-category-menu-modal"
             class="mobile-menu rz-modal ra-menu-mobile-modal ra-hamburger-modal <?php echo esc_attr( Helper::get_option( 'mobile_navigation_bar_menu_side_type' ) == 'side-left' ? 'side-left' : '' ) ?>"
             tabindex="-1" role="dialog">
            <div class="off-modal-layer"></div>
            <div class="menu-mobile-panel-content panel-content">
                <div class="modal-header">
                    <div class="mobile-logo">
						<?php if ( Helper::get_option( 'mobile_panel_custom_logo' ) ) : ?>
							<?php get_template_part( 'template-parts/mobile/header-panel-logo' ); ?>
						<?php else : ?>
							<?php get_template_part( 'template-parts/headers/logo' ); ?>
						<?php endif; ?>
                    </div>
                    <a href="#"
                       class="close-account-panel button-close"><?php echo \Razzi\Icon::get_svg( 'close' ); ?></a>
                </div>
                <div class="modal-content">
                    <nav class="hamburger-navigation menu-mobile-navigation">
						<?php
						$class = array(
							'nav-menu',
							'menu',
							Helper::get_option( 'mobile_navigation_bar_menu_click_item' )
						);

						$classes = implode( ' ', $class );

						$menu_slug = Helper::get_option( 'mobile_navigation_bar_menu_item' );
						if( ! empty($menu_slug) ) {
							wp_nav_menu( array(
								'theme_location' => '__no_such_location',
								'menu'           => $menu_slug,
								'container'      => null,
								'fallback_cb'    => 'wp_page_menu',
								'menu_class'     => $classes,
							) );
						}

						?>
                    </nav>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Open wrapper product type vatiable
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_wrapper_product_type_variable() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );

		if ( in_array( $navigation_bar, array( 'standard_adoptive' ) ) ) {
			echo '<div class="rz-navigation-bar__type-variable--header">';
		}
	}

	/**
	 * Close wrapper product type vatiable
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_wrapper_product_type_variable() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );

		if ( in_array( $navigation_bar, array( 'standard_adoptive' ) ) ) {
			echo '</div>';
		}

		echo '<div class="rz-navigation-bar__type-variable--content product">';

		echo \Razzi\Icon::get_svg( 'close', 'close rz-navigation-bar__btn-close active' );
		\Razzi\Theme::instance()->get( 'woocommerce' )->get_template( 'product_loop' )->display_variation_dropdown();

		echo '</div>';
	}

	/**
	 * Get content navigation
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_content_navigation() {
		global $product;

		$navigation_bar = Helper::get_option( 'mobile_navigation_bar' );
		if ( in_array( $navigation_bar, array( 'standard_adoptive' ) ) && ! Helper::get_option( 'mobile_floating_action_button' ) ) {
			echo sprintf(
				'<div class="price">%s</div>',
				wp_kses_post( $product->get_price_html() )
			);
		}


		if ( $product->is_type( 'variable' ) ) {
			echo sprintf(
				'<a href="#" class="add_to_cart_button rz-loop_atc_button">%s<span class="add-to-cart-text loop_button-text">%s</span></a>',
				\Razzi\Icon::get_svg( 'cart', 'cart', 'shop' ),
				esc_html__('Add to Cart', 'razzi')
			);
		} else {
			if ( $product->is_in_stock() ) {
				woocommerce_template_loop_add_to_cart();
			}
		}

	}

}