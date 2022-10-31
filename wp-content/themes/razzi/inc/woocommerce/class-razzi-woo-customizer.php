<?php
/**
 * WooCommerce Customizer functions and definitions.
 *
 * @package Razzi
 */

namespace Razzi\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The Razzi WooCommerce Customizer class
 */
class Customizer {
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
		add_filter( 'razzi_customize_panels', array( $this, 'get_customize_panels' ) );
		add_filter( 'razzi_customize_sections', array( $this, 'get_customize_sections' ) );
		add_filter( 'razzi_customize_fields', array( $this, 'get_customize_fields' ) );
	}

	/**
	 * Adds theme options panels of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $panels Theme options panels.
	 *
	 * @return array
	 */
	public function get_customize_panels( $panels ) {
		$panels['woocommerce'] = array(
			'priority' => 50,
			'title'    => esc_html__( 'Woocommerce', 'razzi' ),
		);

		$panels['product_catalog'] = array(
			'priority' => 50,
			'title'    => esc_html__( 'Product Catalog', 'razzi' ),
		);

		$panels['shop_product'] = array(
			'priority' => 50,
			'title'    => esc_html__( 'Single Product', 'razzi' ),
		);

		return $panels;
	}

	/**
	 * Adds theme options sections of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Theme options sections.
	 *
	 * @return array
	 */
	public function get_customize_sections( $sections ) {
		// Page Cart
		$sections = array_merge( $sections, array(
			'woocommerce_cart' => array(
				'title'    => esc_html__( 'Cart', 'razzi' ),
				'priority' => 60,
				'panel'    => 'woocommerce',
			),
		) );

		// Product Loop
		$sections = array_merge( $sections, array(
			'product_loop' => array(
				'title'    => esc_html__( 'Product Loop', 'razzi' ),
				'priority' => 60,
				'panel'    => 'woocommerce',
			),
		) );

		// Product Notification
		$sections = array_merge( $sections, array(
			'product_notifications' => array(
				'title'    => esc_html__( 'Product Notifications', 'razzi' ),
				'priority' => 60,
				'panel'    => 'woocommerce',
			),
		) );

		// Badges
		$sections = array_merge( $sections, array(
			'shop_badges' => array(
				'title'    => esc_html__( 'Badges', 'razzi' ),
				'priority' => 60,
				'panel'    => 'woocommerce',
			),
		) );

		// Badges
		$sections = array_merge( $sections, array(
			'product_qty' => array(
				'title'    => esc_html__( 'Product Qty', 'razzi' ),
				'priority' => 60,
				'panel'    => 'woocommerce',
			),
		) );

		// cross sells
		$sections = array_merge( $sections, array(
			'product_cross_sells' => array(
				'title'    => esc_html__( 'Cross Sells Products', 'razzi' ),
				'priority' => 60,
				'panel'    => 'woocommerce',
			),
		) );

		// Product Page
		$sections = array_merge( $sections, array(
			'single_product_layout'  => array(
				'title'    => esc_html__( 'Product Layout', 'razzi' ),
				'priority' => 10,
				'panel'    => 'shop_product',
			),
			'sticky_add_to_cart'     => array(
				'title'    => esc_html__( 'Sticky Add To Cart', 'razzi' ),
				'priority' => 15,
				'panel'    => 'shop_product',
			),
			'single_product_related' => array(
				'title'    => esc_html__( 'Related Products', 'razzi' ),
				'priority' => 20,
				'panel'    => 'shop_product',
			),
			'single_product_upsells' => array(
				'title'    => esc_html__( 'Upsells Products', 'razzi' ),
				'priority' => 30,
				'panel'    => 'shop_product',
			),
			'single_product_sharing' => array(
				'title'    => esc_html__( 'Product Sharing', 'razzi' ),
				'priority' => 40,
				'panel'    => 'shop_product',
			),
		) );

		// Catalog Page
		$sections = array_merge( $sections, array(
			'catalog_layout' => array(
				'title' => esc_html__( 'Catalog Layout', 'razzi' ),
				'panel' => 'product_catalog',
			),

			'catalog_page_header' => array(
				'title' => esc_html__( 'Page Header', 'razzi' ),
				'panel' => 'product_catalog',
			),


			'taxonomy_description' => array(
				'title' => esc_html__( 'Taxonomy Description', 'razzi' ),
				'panel' => 'product_catalog',
			),

			'shop_banners' => array(
				'title' => esc_html__( 'Banners', 'razzi' ),
				'panel' => 'product_catalog',
			),

			'catalog_categories' => array(
				'title' => esc_html__( 'Top Categories', 'razzi' ),
				'panel' => 'product_catalog',
			),

			'catalog_toolbar' => array(
				'title' => esc_html__( 'Catalog Toolbar', 'razzi' ),
				'panel' => 'product_catalog',
			),

			'shop_quick_view' => array(
				'title' => esc_html__( 'Quick View', 'razzi' ),
				'panel' => 'product_catalog',
			),
		) );


		return $sections;
	}

	/**
	 * Adds theme options of WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Theme options fields.
	 *
	 * @return array
	 */
	public function get_customize_fields( $fields ) {
		// WooCommerce settings.
		$fields = array_merge(
			$fields, array(

				// Shop product catalog
				'product_loop_layout'                 => array(
					'type'     => 'select',
					'label'    => esc_html__( 'Product Loop Layout', 'razzi' ),
					'default'  => '1',
					'section'  => 'product_loop',
					'priority' => 10,
					'choices'  => array(
						'1' => esc_html__( 'Icons over thumbnail on hover', 'razzi' ),
						'2' => esc_html__( 'Icons & Quick view button', 'razzi' ),
						'3' => esc_html__( 'Icons & Add to cart button', 'razzi' ),
						'4' => esc_html__( 'Icons on the bottom', 'razzi' ),
						'5' => esc_html__( 'Simple', 'razzi' ),
						'6' => esc_html__( 'Standard button', 'razzi' ),
						'7' => esc_html__( 'Info on hover', 'razzi' ),
						'8' => esc_html__( 'Icons & Add to cart text', 'razzi' ),
						'9' => esc_html__( 'Quick Shop button', 'razzi' ),
					),
				),
				'product_loop_hover'                  => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Product Loop Hover', 'razzi' ),
					'description'     => esc_html__( 'Product hover animation.', 'razzi' ),
					'default'         => 'classic',
					'section'         => 'product_loop',
					'priority'        => 10,
					'choices'         => array(
						'classic' => esc_html__( 'Classic', 'razzi' ),
						'slider'  => esc_html__( 'Slider', 'razzi' ),
						'fadein'  => esc_html__( 'Fadein', 'razzi' ),
						'zoom'    => esc_html__( 'Zoom', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '!=',
							'value'    => '7',
						),
					),
				),
				'product_loop_featured_icons_custom'  => array(
					'type'            => 'custom',
					'section'         => 'product_loop',
					'priority'        => 10,
					'default'         => '<hr/>',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '!=',
							'value'    => '5',
						),
					),
				),
				'product_loop_featured_icons'         => array(
					'type'            => 'multicheck',
					'label'           => esc_html__( 'Featured Icons', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => array( 'cart', 'qview', 'wlist' ),
					'priority'        => 10,
					'choices'         => array(
						'cart'  => esc_html__( 'Cart', 'razzi' ),
						'qview' => esc_html__( 'Quick View', 'razzi' ),
						'wlist' => esc_html__( 'Wishlist', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '!=',
							'value'    => '5',
						),
					),
				),
				'product_loop_wishlist'               => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Always Display Wishlist', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 1,
					'priority'        => 10,
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '2', '3', '9' ),
						),
					),
				),
				'product_loop_attributes_custom'      => array(
					'type'     => 'custom',
					'section'  => 'product_loop',
					'priority' => 10,
					'default'  => '<hr/>',
				),
				'product_loop_attributes'             => array(
					'type'     => 'multicheck',
					'label'    => esc_html__( 'Attributes', 'razzi' ),
					'section'  => 'product_loop',
					'default'  => array( 'taxonomy' ),
					'priority' => 10,
					'choices'  => array(
						'taxonomy' => esc_html__( 'Taxonomy', 'razzi' ),
						'rating'   => esc_html__( 'Rating', 'razzi' ),
					),
				),
				'product_loop_taxonomy'               => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Product Taxonomy', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 'product_cat',
					'priority'        => 10,
					'choices'         => array(
						'product_cat'   => esc_html__( 'Category', 'razzi' ),
						'product_brand' => esc_html__( 'Brand', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_attributes',
							'operator' => 'in',
							'value'    => 'taxonomy',
						),
					),
				),
				'product_loop_variation_custom'       => array(
					'type'            => 'custom',
					'section'         => 'product_loop',
					'priority'        => 10,
					'default'         => '<hr/>',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '8', '9' ),
						),
					),
				),
				'product_loop_variation'              => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Show Variations', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 1,
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '8', '9' ),
						),
					),
				),
				'product_loop_variation_ajax'         => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Show Variations With AJAX', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 1,
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '9' ),
						),
						array(
							'setting'  => 'product_loop_variation',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'product_loop_desc_custom'            => array(
					'type'            => 'custom',
					'section'         => 'product_loop',
					'priority'        => 10,
					'default'         => '<hr/>',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
				),
				'product_loop_desc'                   => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Show Description', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 1,
					'priority'        => 10,
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
				),
				'product_loop_desc_length'            => array(
					'type'            => 'slider',
					'label'           => esc_html__( 'Description Length', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 10,
					'choices'         => array(
						'min' => 1,
						'max' => 200,
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
				),
				'product_loop_custom_button_color_br' => array(
					'type'            => 'custom',
					'section'         => 'product_loop',
					'priority'        => 10,
					'default'         => '<hr/>',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
				),
				'product_loop_custom_button_color'    => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Custom Button Color', 'razzi' ),
					'section'         => 'product_loop',
					'default'         => 0,
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
				),
				'product_loop_button_bg_color'        => array(
					'label'           => esc_html__( 'Background Color', 'razzi' ),
					'type'            => 'color',
					'default'         => '',
					'section'         => 'product_loop',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_custom_button_color',
							'operator' => '==',
							'value'    => '1',
						),
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
					'js_vars'         => array(
						array(
							'element'  => 'ul.products.product-loop-layout-6 li.product .ajax_add_to_cart',
							'property' => 'background-color',
						),
					),
				),
				'product_loop_button_text_color'      => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Text Color', 'razzi' ),
					'transport'       => 'postMessage',
					'section'         => 'product_loop',
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_custom_button_color',
							'operator' => '==',
							'value'    => '1',
						),
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
					'js_vars'         => array(
						array(
							'element'  => 'ul.products.product-loop-layout-6 li.product .ajax_add_to_cart',
							'property' => 'color',
						),
					),
				),
				'product_loop_button_border_color'    => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Border Color', 'razzi' ),
					'section'         => 'product_loop',
					'transport'       => 'postMessage',
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_custom_button_color',
							'operator' => '==',
							'value'    => '1',
						),
						array(
							'setting'  => 'product_loop_layout',
							'operator' => '==',
							'value'    => '6',
						),
					),
					'js_vars'         => array(
						array(
							'element'  => 'ul.products.product-loop-layout-6 li.product .ajax_add_to_cart',
							'property' => '--rz-border-color-primary',
						),
					),
				),

				// Added to cart Notice
				'added_to_cart_notice'                => array(
					'type'        => 'select',
					'label'       => esc_html__( 'Added to Cart Notice', 'razzi' ),
					'description' => esc_html__( 'Display a notification when a product is added to cart.', 'razzi' ),
					'default'     => 'panel',
					'section'     => 'product_notifications',
					'choices'     => array(
						'panel'  => esc_html__( 'Open mini cart panel', 'razzi' ),
						'popup'  => esc_html__( 'Open cart popup', 'razzi' ),
						'simple' => esc_html__( 'Simple', 'razzi' ),
						'none'   => esc_html__( 'None', 'razzi' ),
					),
				),

				'added_to_cart_notice_products'       => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Recommended Products', 'razzi' ),
					'description'     => esc_html__( 'Display recommended products on the cart popup', 'razzi' ),
					'default'         => 'related_products',
					'section'         => 'product_notifications',
					'choices'         => array(
						'none'                  => esc_html__( 'None', 'razzi' ),
						'best_selling_products' => esc_html__( 'Best selling products', 'razzi' ),
						'featured_products'     => esc_html__( 'Featured products', 'razzi' ),
						'recent_products'       => esc_html__( 'Recent products', 'razzi' ),
						'sale_products'         => esc_html__( 'Sale products', 'razzi' ),
						'top_rated_products'    => esc_html__( 'Top rated products', 'razzi' ),
						'related_products'      => esc_html__( 'Related products', 'razzi' ),
						'upsells_products'      => esc_html__( 'Upsells products', 'razzi' ),

					),
					'active_callback' => array(
						array(
							'setting'  => 'added_to_cart_notice',
							'operator' => '==',
							'value'    => 'popup',
						),
					),
				),
				'added_to_cart_notice_products_title' => array(
					'type'            => 'text',
					'description'     => esc_html__( 'Title', 'razzi' ),
					'default'         => esc_html__( 'You may also like', 'razzi' ),
					'section'         => 'product_notifications',
					'active_callback' => array(
						array(
							'setting'  => 'added_to_cart_notice',
							'operator' => '==',
							'value'    => 'popup',
						),
					),
				),
				'added_to_cart_notice_products_limit' => array(
					'type'            => 'number',
					'description'     => esc_html__( 'Number of products', 'razzi' ),
					'section'         => 'product_notifications',
					'default'         => 4,
					'active_callback' => array(
						array(
							'setting'  => 'added_to_cart_notice',
							'operator' => '==',
							'value'    => 'popup',
						),
					),
				),

				'added_to_cart_notice_auto_hide' => array(
					'type'            => 'number',
					'label'           => esc_html__( 'Cart Notification Auto Hide', 'razzi' ),
					'description'     => esc_html__( 'How many seconds you want to hide the notification.', 'razzi' ),
					'section'         => 'product_notifications',
					'active_callback' => array(
						array(
							'setting'  => 'added_to_cart_notice',
							'operator' => '==',
							'value'    => 'simple',
						),
					),
					'default'         => 3,
				),

				'cart_notice_auto_hide_custom' => array(
					'type'    => 'custom',
					'section' => 'product_notifications',
					'default' => '<hr>',
				),

				'added_to_wishlist_notice' => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Added to Wishlist Notification', 'razzi' ),
					'description' => esc_html__( 'Display a notification when a product is added to wishlist', 'razzi' ),
					'section'     => 'product_notifications',
					'default'     => 0,
				),

				'wishlist_notice_auto_hide'   => array(
					'type'            => 'number',
					'label'           => esc_html__( 'Wishlist Notification Auto Hide', 'razzi' ),
					'description'     => esc_html__( 'How many seconds you want to hide the notification.', 'razzi' ),
					'section'         => 'product_notifications',
					'active_callback' => array(
						array(
							'setting'  => 'added_to_wishlist_notice',
							'operator' => '==',
							'value'    => 1,
						),
					),
					'default'         => 3,
				),

				// Page Cart
				'update_cart_page_auto'       => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Update Cart Automatically', 'razzi' ),
					'description' => esc_html__( 'Check this option to update cart page automatically', 'razzi' ),
					'default'     => 0,
					'section'     => 'woocommerce_cart',
				),
				'product_hr_1'                => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'woocommerce_cart',
				),
				// Cross Sells
				'product_cross_sells'         => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Show Cross-Sells Products', 'razzi' ),
					'section'     => 'woocommerce_cart',
					'description' => esc_html__( 'Check this option to show cross-sells products in product cart page', 'razzi' ),
					'default'     => 1,
				),
				'product_cross_sells_title'   => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Cross-Sells Products Title', 'razzi' ),
					'section'         => 'woocommerce_cart',
					'default'         => esc_html__( 'You may also like', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'product_cross_sells',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),
				'product_cross_sells_numbers' => array(
					'type'            => 'number',
					'label'           => esc_html__( 'Cross-Sells Products Numbers', 'razzi' ),
					'section'         => 'woocommerce_cart',
					'default'         => 6,
					'description'     => esc_html__( 'Specify how many numbers of Cross-Sells products you want to show on product cart page', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'product_cross_sells',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),
				'checkout_product_thumbnail'     => array(
					'type'            => 'toggle',
					'section'         => 'woocommerce_checkout',
					'label'           => esc_html__( 'Display the product thumbnail', 'razzi' ),
					'default'         => false,
				),

			)
		);

		// Product Page
		$fields = array_merge(
			$fields, array(
				'product_layout'           => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Product Layout', 'razzi' ),
					'default' => 'v1',
					'section' => 'single_product_layout',
					'choices' => array(
						'v1' => esc_html__( 'Layout 1', 'razzi' ),
						'v2' => esc_html__( 'Layout 2', 'razzi' ),
						'v3' => esc_html__( 'Layout 3', 'razzi' ),
						'v4' => esc_html__( 'Layout 4', 'razzi' ),
						'v5' => esc_html__( 'Layout 5', 'razzi' ),
					),
				),
				'product_hr_3'             => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'single_product_layout',
				),
				'single_product_page_header'       => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Breadcrumb', 'razzi' ),
					'section'     => 'single_product_layout',
					'description' => esc_html__( 'Display breadcrumb on top of product page', 'razzi' ),
					'default'     => true,
				),
				'product_add_to_cart_ajax' => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Add to cart with AJAX', 'razzi' ),
					'section'     => 'single_product_layout',
					'default'     => 1,
					'description' => esc_html__( 'Check this option to enable add to cart with AJAX on the product page.', 'razzi' ),
				),
				'product_taxonomy'         => array(
					'type'        => 'select',
					'label'       => esc_html__( 'Product Taxonomy', 'razzi' ),
					'section'     => 'single_product_layout',
					'description' => esc_html__( 'Show a taxonomy above the product title', 'razzi' ),
					'default'     => 'product_cat',
					'choices'     => array(
						'product_cat'   => esc_html__( 'Category', 'razzi' ),
						'product_brand' => esc_html__( 'Brand', 'razzi' ),
						''              => esc_html__( 'None', 'razzi' ),
					),
				),
				'product_brand_type'         => array(
					'type'        => 'select',
					'label'       => esc_html__( 'Product Brand', 'razzi' ),
					'section'     => 'single_product_layout',
					'default'     => 'title',
					'choices'     => array(
						'title'   => esc_html__( 'Title', 'razzi' ),
						'logo' => esc_html__( 'Logo', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_taxonomy',
							'operator' => '==',
							'value'    => 'product_brand',
						),
					),
				),
				'product_wishlist_button'  => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Wishlist button', 'razzi' ),
					'section' => 'single_product_layout',
					'default' => 'icon',
					'choices' => array(
						'none'  => esc_html__( 'None', 'razzi' ),
						'icon'  => esc_html__( 'Icon', 'razzi' ),
						'title' => esc_html__( 'Icon & Title', 'razzi' ),
					),
				),
				'product_hr_4'             => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'single_product_layout',
				),
				'product_image_zoom'        => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Image Zoom', 'razzi' ),
					'description' => esc_html__( 'Zooms in where your cursor is on the image', 'razzi' ),
					'default'     => false,
					'section'     => 'single_product_layout',
				),
				'product_image_lightbox'    => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Image Lightbox', 'razzi' ),
					'description' => esc_html__( 'Opens your images against a dark backdrop', 'razzi' ),
					'default'     => true,
					'section'     => 'single_product_layout',
				),
				'product_thumbnail_numbers' => array(
					'type'            => 'number',
					'label'           => esc_html__( 'Thumbnail Numbers', 'razzi' ),
					'default'         => 5,
					'section'         => 'single_product_layout',
					'active_callback' => array(
						array(
							'setting'  => 'product_layout',
							'operator' => 'in',
							'value'    => array( 'v1', 'v2' ),
						),
					),
				),
				'product_play_video'  => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Play video', 'razzi' ),
					'section' => 'single_product_layout',
					'default' => 'load',
					'choices' => array(
						'load'  => esc_html__( 'From Page', 'razzi' ),
						'popup'  => esc_html__( 'In Popup', 'razzi' ),
					),
				),
				'product_hr_6' => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'single_product_layout',
				),
				'product_meta' => array(
					'type'     => 'multicheck',
					'label'    => esc_html__( 'Product Meta', 'razzi' ),
					'section'  => 'single_product_layout',
					'default'  => array( 'sku', 'category', 'tags' ),
					'priority' => 10,
					'choices'  => array(
						'sku'      => esc_html__( 'Sku', 'razzi' ),
						'tags'     => esc_html__( 'Tags', 'razzi' ),
						'category' => esc_html__( 'Category', 'razzi' ),
					),
				),
				'product_hr_7' => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'single_product_layout',
				),
				'product_tabs_position'           => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Product Tabs Position', 'razzi' ),
					'default' => 'default',
					'section' => 'single_product_layout',
					'choices' => array(
						'default' => esc_html__( 'Under Product Gallery', 'razzi' ),
						'under_summary' => esc_html__( 'Under Product Summary', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_layout',
							'operator' => 'in',
							'value'    => array( 'v1', 'v2', 'v3', 'v4' ),
						),
					),
				),
				'product_tabs_status'           => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Product Tabs Status', 'razzi' ),
					'default' => 'close',
					'section' => 'single_product_layout',
					'choices' => array(
						'close' => esc_html__( 'Close all tabs', 'razzi' ),
						'first' => esc_html__( 'Open first tab', 'razzi' ),
					),
					'active_callback' => function() {
						$product_layout = get_theme_mod( 'product_layout', 'v1' );
						$product_tabs_position    = get_theme_mod( 'product_tabs_position', 'default' );
						if ( $product_layout == 'v5') {
							return true;
						} elseif ( in_array($product_layout, array('v1', 'v2', 'v3', 'v4') ) &&  $product_tabs_position == 'under_summary') {
							return true;
						}
						return false;
					},
				),

				'product_add_to_cart_sticky' => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Sticky Add To Cart', 'razzi' ),
					'section'     => 'sticky_add_to_cart',
					'default'     => 0,
					'description' => esc_html__( 'A small content bar at the top of the browser window which includes relevant product information and an add-to-cart button. It slides into view once the standard add-to-cart button has scrolled out of view.', 'razzi' ),
				),

				'product_add_to_cart_sticky_position' => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Sticky Bar Position', 'razzi' ),
					'default'         => 'top',
					'section'         => 'sticky_add_to_cart',
					'choices'         => array(
						'top'    => esc_html__( 'Top', 'razzi' ),
						'bottom' => esc_html__( 'Bottom', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_add_to_cart_sticky',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),
				'product_atc_variable'                    => array(
					'type'        => 'select',
					'label'       => esc_html__( 'Product Variable Style', 'razzi' ),
					'section'     => 'sticky_add_to_cart',
					'default'     => 'form',
					'priority'    => 40,
					'choices'         => array(
						'button'    => esc_html__( 'Button Only', 'razzi' ),
						'form' => esc_html__( 'Add To Cart Form', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_add_to_cart_sticky',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),

				'product_upsells'                    => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Show Upsells Products', 'razzi' ),
					'section'     => 'single_product_upsells',
					'description' => esc_html__( 'Check this option to show up-sells products in single product page', 'razzi' ),
					'default'     => 1,
					'priority'    => 40,
				),
				'product_upsells_title'              => array(
					'type'     => 'text',
					'label'    => esc_html__( 'Up-sells Products Title', 'razzi' ),
					'section'  => 'single_product_upsells',
					'default'  => esc_html__( 'You may also like', 'razzi' ),
					'priority' => 40,
				),
				'product_upsells_numbers'            => array(
					'type'        => 'number',
					'label'       => esc_html__( 'Up-sells Products Numbers', 'razzi' ),
					'section'     => 'single_product_upsells',
					'default'     => 6,
					'priority'    => 40,
					'description' => esc_html__( 'Specify how many numbers of up-sells products you want to show on single product page', 'razzi' ),
				),
				'product_sharing'                 => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Product Sharing', 'razzi' ),
					'default' => true,
					'section' => 'single_product_sharing',
				),
				'product_sharing_socials'         => array(
					'type'            => 'multicheck',
					'description'     => esc_html__( 'Select social media for sharing products', 'razzi' ),
					'section'         => 'single_product_sharing',
					'default'         => array(
						'facebook',
						'twitter',
						'pinterest',
					),
					'choices'         => array(
						'facebook'   => esc_html__( 'Facebook', 'razzi' ),
						'twitter'    => esc_html__( 'Twitter', 'razzi' ),
						'googleplus' => esc_html__( 'Google Plus', 'razzi' ),
						'pinterest'  => esc_html__( 'Pinterest', 'razzi' ),
						'tumblr'     => esc_html__( 'Tumblr', 'razzi' ),
						'telegram'   => esc_html__( 'Telegram', 'razzi' ),
						'whatsapp'   => esc_html__( 'WhatsApp', 'razzi' ),
						'email'      => esc_html__( 'Email', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_sharing',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				'product_sharing_whatsapp_number' => array(
					'type'            => 'text',
					'description'     => esc_html__( 'WhatsApp Phone Number', 'razzi' ),
					'section'         => 'single_product_sharing',
					'active_callback' => array(
						array(
							'setting'  => 'product_sharing',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'product_sharing_socials',
							'operator' => 'contains',
							'value'    => 'whatsapp',
						),
					),
				),
			)
		);

		// Badges
		$fields = array_merge(
			$fields, array(
				'product_catalog_badges' => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Catalog Badges', 'razzi' ),
					'description' => esc_html__( 'Display the badges in the catalog page', 'razzi' ),
					'default'     => true,
					'section'     => 'shop_badges',
				),

				'single_product_badges' => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Product Badges', 'razzi' ),
					'description' => esc_html__( 'Display the badges in the single page', 'razzi' ),
					'default'     => true,
					'section'     => 'shop_badges',
				),

				'product_catalog_badges_layout' => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Badges Layout', 'razzi' ),
					'section'         => 'shop_badges',
					'default'         => 'dark',
					'choices'         => array(
						'layout-1' => esc_html__( 'Layout 1', 'razzi' ),
						'layout-2' => esc_html__( 'Layout 2', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'product_catalog_badges',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),

				// badges
				'product_hr_sale'               => array(
					'type'    => 'custom',
					'section' => 'shop_badges',
					'default' => '<hr/><h3>' . esc_html__( 'Sale Badge', 'razzi' ) . '</h3>',
				),
				'shop_badge_sale'               => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Enalble', 'razzi' ),
					'description' => esc_html__( 'Display a badge for sale products.', 'razzi' ),
					'default'     => true,
					'section'     => 'shop_badges',
				),
				'shop_badge_sale_type'          => array(
					'type'            => 'radio',
					'label'           => esc_html__( 'Type', 'razzi' ),
					'default'         => 'text',
					'choices'         => array(
						'percent' => esc_html__( 'Percentage', 'razzi' ),
						'text'    => esc_html__( 'Text', 'razzi' ),
						'both'    => esc_html__( 'Both', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_sale',
							'operator' => '=',
							'value'    => true,
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_sale_text'          => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Text', 'razzi' ),
					'tooltip'         => esc_html__( 'Use {%} to display discount percentages, {$} to display discount amount.', 'razzi' ),
					'default'         => esc_html__( 'Sale', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_sale',
							'operator' => '=',
							'value'    => true,
						),
						array(
							'setting'  => 'shop_badge_sale_type',
							'operator' => 'in',
							'value'    => array( 'text', 'both' ),
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_sale_color'         => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Color', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_sale',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.onsale',
							'property' => 'color',
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_sale_bg'            => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Background', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_sale',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.onsale',
							'property' => 'background-color',
						),
					),
					'section'         => 'shop_badges',
				),

				'product_hr_new' => array(
					'type'    => 'custom',
					'section' => 'shop_badges',
					'default' => '<hr/><h3>' . esc_html__( 'New Badge', 'razzi' ) . '</h3>',
				),

				'shop_badge_new'       => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Enable', 'razzi' ),
					'description' => esc_html__( 'Display a badge for new products.', 'razzi' ),
					'default'     => true,
					'section'     => 'shop_badges',
				),
				'shop_badge_new_text'  => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Text', 'razzi' ),
					'default'         => esc_html__( 'New', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_new',
							'operator' => '=',
							'value'    => true,
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_newness'   => array(
					'type'            => 'number',
					'label'           => esc_html__('Product Newness', 'razzi'),
					'description'     => esc_html__( 'Display the "New" badge for how many days?', 'razzi' ),
					'tooltip'         => esc_html__( 'You can also add the NEW badge to each product in the Advanced setting tab of them.', 'razzi' ),
					'default'         => 3,
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_new',
							'operator' => '=',
							'value'    => true,
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_new_color' => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Color', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_new',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.new',
							'property' => 'color',
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_new_bg'    => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Background', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_new',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.new',
							'property' => 'background-color',
						),
					),
					'section'         => 'shop_badges',
				),

				'product_hr_featured' => array(
					'type'    => 'custom',
					'section' => 'shop_badges',
					'default' => '<hr/><h3>' . esc_html__( 'Featured Badge', 'razzi' ) . '</h3>',
				),

				'shop_badge_featured'       => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Enable', 'razzi' ),
					'description' => esc_html__( 'Display a badge for featured products.', 'razzi' ),
					'default'     => true,
					'section'     => 'shop_badges',
				),
				'shop_badge_featured_text'  => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Text', 'razzi' ),
					'default'         => esc_html__( 'Hot', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_featured',
							'operator' => '=',
							'value'    => true,
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_featured_color' => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Color', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_featured',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.featured',
							'property' => 'color',
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_featured_bg'    => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Background', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_featured',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.featured',
							'property' => 'background-color',
						),
					),
					'section'         => 'shop_badges',
				),

				'product_hr_soldout'       => array(
					'type'    => 'custom',
					'section' => 'shop_badges',
					'default' => '<hr/><h3>' . esc_html__( 'Sold Out Badge', 'razzi' ) . '</h3>',
				),
				'shop_badge_soldout'       => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Enable', 'razzi' ),
					'description' => esc_html__( 'Display a badge for out of stock products.', 'razzi' ),
					'default'     => false,
					'section'     => 'shop_badges',
				),
				'shop_badge_soldout_text'  => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Text', 'razzi' ),
					'default'         => esc_html__( 'Sold Out', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_soldout',
							'operator' => '=',
							'value'    => true,
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_soldout_color' => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Color', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_soldout',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.sold-out',
							'property' => 'color',
						),
					),
					'section'         => 'shop_badges',
				),
				'shop_badge_soldout_bg'    => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Background', 'razzi' ),
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'shop_badge_soldout',
							'operator' => '=',
							'value'    => true,
						),
					),
					'transport'       => 'postMessage',
					'js_vars'         => array(
						array(
							'element'  => '.woocommerce-badge.sold-out',
							'property' => 'background-color',
						),
					),
					'section'         => 'shop_badges',
				),
			)
		);

		// Product Qty
		$fields = array_merge(
			$fields, array(
				'product_qty_input' => array(
					'type'        => 'radio',
					'label'       => esc_html__( 'Qty Input', 'razzi' ),
					'default'     => 'incremental',
					'section'     => 'product_qty',
					'choices' => array(
						'dropdown'    => esc_html__( 'Dropdown', 'razzi' ),
						'incremental' => esc_html__( 'Incremental', 'razzi' ),
					),
				),
			)
		);

		// Catalog page.
		$fields = array_merge(
			$fields, array(
				// Shop product catalog
				'shop_catalog_layout' => array(
					'type'    => 'radio',
					'label'   => esc_html__( 'Catalog Layout', 'razzi' ),
					'default' => 'grid',
					'choices' => array(
						'grid'    => esc_html__( 'Grid', 'razzi' ),
						'masonry' => esc_html__( 'Masonry', 'razzi' ),
					),
					'section' => 'catalog_layout',
				),

				'catalog_sidebar' => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Sidebar', 'razzi' ),
					'tooltip'         => esc_html__( 'Go to appearance > widgets find to catalog sidebar to edit your sidebar', 'razzi' ),
					'default'         => 'full-content',
					'choices'         => array(
						'content-sidebar' => esc_html__( 'Right Sidebar', 'razzi' ),
						'sidebar-content' => esc_html__( 'Left Sidebar', 'razzi' ),
						'full-content'    => esc_html__( 'No Sidebar', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'shop_catalog_layout',
							'operator' => '==',
							'value'    => 'grid',
						)
					),
					'section'         => 'catalog_layout',
				),

				'catalog_widget_collapse_content' => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Collapse Widget', 'razzi' ),
					'default'         => 1,
					'active_callback' => array(
						array(
							'setting'  => 'catalog_sidebar',
							'operator' => '!=',
							'value'    => 'full-content',
						),
						array(
							'setting'  => 'shop_catalog_layout',
							'operator' => '==',
							'value'    => 'grid',
						),

					),
					'section'         => 'catalog_layout',
				),

				'catalog_widget_collapse_content_status' => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Collapse Widget Status', 'razzi' ),
					'default'         => 'rz-show',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_sidebar',
							'operator' => '!=',
							'value'    => 'full-content',
						),
						array(
							'setting'  => 'shop_catalog_layout',
							'operator' => '==',
							'value'    => 'grid',
						),
						array(
							'setting'  => 'catalog_widget_collapse_content',
							'operator' => '==',
							'value'    => '1',
						),

					),
					'choices'         => array(
						'show' => esc_html__( 'Show the content', 'razzi' ),
						'hide' => esc_html__( 'Hide the content', 'razzi' ),
					),
					'section'         => 'catalog_layout',
				),

				'catalog_sticky_sidebar_custom' => array(
					'type'    => 'custom',
					'section' => 'catalog_layout',
					'default' => '<hr/>',
					'active_callback' => array(
						array(
							'setting'  => 'shop_catalog_layout',
							'operator' => '==',
							'value'    => 'grid',
						)
					),
				),
				'catalog_sticky_sidebar'        => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Sticky Sidebar', 'razzi' ),
					'description' => esc_html__( 'Attachs the sidebar to the page when the user scrolls', 'razzi' ),
					'default' => true,
					'section' => 'catalog_layout',
					'active_callback' => array(
						array(
							'setting'  => 'shop_catalog_layout',
							'operator' => '==',
							'value'    => 'grid',
						),
						array(
							'setting'  => 'catalog_sidebar',
							'operator' => '!=',
							'value'    => 'full-content',
						),
					),
				),

				'shop_products_hr_1' => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'catalog_layout',
				),

				'catalog_toolbar_filtered' => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Enable Active Product Filters', 'razzi' ),
					'section' => 'catalog_layout',
					'default' => 1,
				),
				'shop_products_hr_10' => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'catalog_layout',
				),
				'catalog_product_filter_sidebar'  => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Close Filter Sidebar before filtering', 'razzi' ),
					'default'         => 1,
					'section'         => 'catalog_layout',
				),
				'shop_products_hr_2' => array(
					'type'    => 'custom',
					'default' => '<hr>',
					'section' => 'catalog_layout',
				),

				'product_catalog_navigation' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Navigation Type', 'razzi' ),
					'default' => 'loadmore',
					'choices' => array(
						'numeric'  => esc_html__( 'Numeric', 'razzi' ),
						'loadmore' => esc_html__( 'Load More', 'razzi' ),
						'infinite' => esc_html__( 'Infinite Scroll', 'razzi' ),
					),
					'section' => 'catalog_layout',
				),

				'shop_products_hr_3' => array(
					'type'            => 'custom',
					'default'         => '<hr>',
					'section'         => 'catalog_layout',
					'active_callback' => array(
						array(
							'setting'  => 'shop_catalog_layout',
							'operator' => '==',
							'value'    => 'grid',
						)
					),
				),

			)
		);

		// Catalog page.
		$fields = array_merge(
			$fields, array(
				'catalog_page_header' => array(
					'type'    => 'radio',
					'label'   => esc_html__( 'Layout', 'razzi' ),
					'default' => '',
					'section' => 'catalog_page_header',
					'choices' => array(
						''         => esc_html__( 'None', 'razzi' ),
						'layout-1' => esc_html__( 'Layout 1', 'razzi' ),
						'layout-2' => esc_html__( 'Layout 2', 'razzi' ),
						'template' => esc_html__( 'Page Template', 'razzi' ),
					),
				),

				'catalog_page_header_image' => array(
					'type'            => 'image',
					'label'           => esc_html__( 'Image', 'razzi' ),
					'default'         => '',
					'section'         => 'catalog_page_header',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => '==',
							'value'    => 'layout-2',
						),
					),
				),
				'catalog_page_header_els'   => array(
					'type'            => 'multicheck',
					'label'           => esc_html__( 'Elements', 'razzi' ),
					'section'         => 'catalog_page_header',
					'default'         => array( 'breadcrumb', 'title' ),
					'priority'        => 10,
					'choices'         => array(
						'breadcrumb' => esc_html__( 'BreadCrumb', 'razzi' ),
						'title'      => esc_html__( 'Title', 'razzi' ),
					),
					'description'     => esc_html__( 'Select which elements you want to show.', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => 'in',
							'value'    => array('layout-2', 'layout-1'),
						),
					),
				),

				'catalog_page_header_custom_field_1' => array(
					'type'            => 'custom',
					'section'         => 'catalog_page_header',
					'default'         => '<hr/><h3>' . esc_html__( 'Custom', 'razzi' ) . '</h3>',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => 'in',
							'value'    => array('layout-2', 'layout-1'),
						),
					),
				),

				'catalog_page_header_padding_top' => array(
					'type'            => 'slider',
					'label'           => esc_html__( 'Padding Top', 'razzi' ),
					'transport'       => 'postMessage',
					'section'         => 'catalog_page_header',
					'default'         => '0',
					'priority'        => 20,
					'choices'         => array(
						'min' => 0,
						'max' => 700,
					),
					'js_vars'         => array(
						array(
							'element'  => '.razzi-catalog-page .catalog-page-header--layout-1 .page-header__title',
							'property' => 'padding-top',
							'units'    => 'px',
						),
						array(
							'element'  => '.razzi-catalog-page .catalog-page-header--layout-2',
							'property' => 'padding-top',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => 'in',
							'value'    => array('layout-2', 'layout-1'),
						),
					),
				),

				'catalog_page_header_padding_bottom' => array(
					'type'            => 'slider',
					'label'           => esc_html__( 'Padding Bottom', 'razzi' ),
					'transport'       => 'postMessage',
					'section'         => 'catalog_page_header',
					'default'         => '0',
					'priority'        => 20,
					'choices'         => array(
						'min' => 0,
						'max' => 700,
					),
					'js_vars'         => array(
						array(
							'element'  => '.razzi-catalog-page .catalog-page-header--layout-1 .page-header__title',
							'property' => 'padding-bottom',
							'units'    => 'px',
						),
						array(
							'element'  => '.razzi-catalog-page .catalog-page-header--layout-2',
							'property' => 'padding-bottom',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => 'in',
							'value'    => array('layout-2', 'layout-1'),
						),
					),
				),

				'catalog_page_header_text_color' => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Title Color', 'razzi' ),
					'transport'       => 'postMessage',
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => '==',
							'value'    => 'layout-2',
						),
						array(
							'setting'  => 'catalog_page_header_els',
							'operator' => 'in',
							'value'    => 'title',
						),
					),
					'js_vars'         => array(
						array(
							'element'  => '.catalog-page-header--layout-2 .page-header',
							'property' => '--rz-color-dark',
						),
					),
					'section'         => 'catalog_page_header',

				),

				'catalog_page_header_bread_color' => array(
					'type'            => 'color',
					'label'           => esc_html__( 'Breadcrumb Color', 'razzi' ),
					'transport'       => 'postMessage',
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => '==',
							'value'    => 'layout-2',
						),
						array(
							'setting'  => 'catalog_page_header_els',
							'operator' => 'in',
							'value'    => 'breadcrumb',
						),
					),
					'js_vars'         => array(
						array(
							'element'  => '.catalog-page-header--layout-2 .page-header .site-breadcrumb',
							'property' => 'color',
						),
					),
					'section'         => 'catalog_page_header',
				),
				'shop_header_template_id'                       => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Template', 'razzi' ),
					'section' => 'catalog_page_header',
					'default' => 'homepage-mobile',
					'choices' => class_exists( 'Kirki_Helper' ) && is_admin() ? \Kirki_Helper::get_posts( array(
						'posts_per_page' => - 1,
						'post_type'      => 'elementor_library',
					) ) : '',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_page_header',
							'operator' => '==',
							'value'    => 'template',
						),
					),
				),

				// Banners
				'shop_page_banners'               => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show on Shop Page', 'razzi' ),
					'section' => 'shop_banners',
					'default' => false,
				),

				'category_page_banners' => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show on Category Page', 'razzi' ),
					'section' => 'shop_banners',
					'default' => false,
				),
				'shop_banners_images'   => array(
					'type'            => 'repeater',
					'label'           => esc_html__( 'Images', 'razzi' ),
					'section'         => 'shop_banners',
					'row_label'       => array(
						'type'  => 'text',
						'value' => esc_html__( 'Image', 'razzi' ),
					),
					'fields'          => array(
						'image' => array(
							'type'    => 'image',
							'label'   => esc_html__( 'Image', 'razzi' ),
							'default' => '',
						),
						'link'  => array(
							'type'    => 'text',
							'label'   => esc_html__( 'Link', 'razzi' ),
							'default' => '',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'shop_banners',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),

				'taxonomy_description_position'      => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Taxonomy Description Position', 'razzi' ),
					'description' => esc_html__('This option works with the taxonomy such as product category, tag, brand...', 'razzi'),
					'default' => 'above',
					'section' => 'taxonomy_description',
					'choices' => array(
						'above' => esc_html__( 'Above the Products', 'razzi' ),
						'below' => esc_html__( 'Below the Products', 'razzi' ),
					),
				),

				// Catalog Toolbar
				'catalog_toolbar'       => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Catalog Toolbar', 'razzi' ),
					'default' => true,
					'section' => 'catalog_toolbar',
				),

				'catalog_toolbar_layout'      => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Toolbar Layout', 'razzi' ),
					'default' => 'v1',
					'section' => 'catalog_toolbar',
					'choices' => array(
						'v1' => esc_html__( 'Layout V1', 'razzi' ),
						'v2' => esc_html__( 'Layout V2', 'razzi' ),
						'v3' => esc_html__( 'Layout V3', 'razzi' ),
					),
				),
				'catalog_toolbar_layout_1'    => array(
					'type'            => 'custom',
					'default'         => '',
					'section'         => 'catalog_toolbar',
					'description'     => esc_html__( 'Add Razzi - Product filter widget in Appearance > Widgets > Catalog Filters sidebar.', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '!=',
							'value'    => 'v1',
						),
					),
				),

				'catalog_toolbar_els' => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Toolbar Left', 'razzi' ),
					'default'         => 'page_header',
					'section'         => 'catalog_toolbar',
					'choices'         => array(
						'page_header' => esc_html__( 'Page Header', 'razzi' ),
						'result'      => esc_html__( 'Showing Result', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v1',
						),
					),
				),

				'catalog_toolbar_hr_1' => array(
					'type'            => 'custom',
					'default'         => '<hr>',
					'section'         => 'catalog_toolbar',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),

				'catalog_toolbar_tabs'               => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Products Tabs', 'razzi' ),
					'default'         => 'category',
					'section'         => 'catalog_toolbar',
					'choices'         => array(
						'group'    => esc_html__( 'Groups', 'razzi' ),
						'category' => esc_html__( 'Categories', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),
				'catalog_toolbar_tabs_groups'        => array(
					'type'            => 'multicheck',
					'default'         => array( 'best_sellers', 'new', 'sale' ),
					'section'         => 'catalog_toolbar',
					'choices'         => array(
						'best_sellers' => esc_html__( 'Best Sellers', 'razzi' ),
						'featured'     => esc_html__( 'Hot Products', 'razzi' ),
						'new'          => esc_html__( 'New Products', 'razzi' ),
						'sale'         => esc_html__( 'Sale Products', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_tabs',
							'operator' => '==',
							'value'    => 'group',
						),
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),
				'catalog_toolbar_tabs_categories'    => array(
					'type'            => 'text',
					'description'     => esc_html__( 'Product categories. Enter category names, separate by commas. Leave empty to get all categories. Enter a number to get limit number of top categories.', 'razzi' ),
					'default'         => 3,
					'section'         => 'catalog_toolbar',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_tabs',
							'operator' => '==',
							'value'    => 'category',
						),
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),
				'catalog_toolbar_tabs_subcategories' => array(
					'type'            => 'checkbox',
					'label'           => esc_html__( 'Replace by sub-categories', 'razzi' ),
					'default'         => false,
					'section'         => 'catalog_toolbar',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_tabs',
							'operator' => '==',
							'value'    => 'category',
						),
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),

				'catalog_toolbar_hr_2' => array(
					'type'            => 'custom',
					'default'         => '<hr>',
					'section'         => 'catalog_toolbar',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),

				'catalog_toolbar_products_filter'          => array(
					'type'            => 'radio',
					'label'           => esc_html__( 'Products Filter', 'razzi' ),
					'tooltip'         => esc_html__( 'Add Razzi - Product filter widget in Appearance > Widgets > Catalog Filters sidebar.', 'razzi' ),
					'default'         => 'dropdown',
					'choices'         => array(
						'modal'    => esc_html__( 'Open filters on side', 'razzi' ),
						'dropdown' => esc_html__( 'Open filters bellow', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
					'section'         => 'catalog_toolbar',
				),
				'catalog_filters_sidebar_collapse_content' => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Collapse Widget', 'razzi' ),
					'default'         => 1,
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
						array(
							'setting'  => 'catalog_toolbar_products_filter',
							'operator' => '==',
							'value'    => 'modal',
						),

					),
					'section'         => 'catalog_toolbar',
				),
				'catalog_filters_sidebar_collapse_status'  => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Collapse Widget Status', 'razzi' ),
					'default'         => 'show',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
						array(
							'setting'  => 'catalog_toolbar_products_filter',
							'operator' => '==',
							'value'    => 'modal',
						),
						array(
							'setting'  => 'catalog_filters_sidebar_collapse_content',
							'operator' => '==',
							'value'    => '1',
						),

					),
					'choices'         => array(
						'show' => esc_html__( 'Show the content', 'razzi' ),
						'hide' => esc_html__( 'Hide the content', 'razzi' ),
					),
					'section'         => 'catalog_toolbar',
				),
				'catalog_toolbar_hr_3' => array(
					'type'            => 'custom',
					'default'         => '<hr>',
					'section'         => 'catalog_toolbar',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),
				'catalog_toolbar_products_sorting'       => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Product Sorting', 'razzi' ),
					'default' => true,
					'section' => 'catalog_toolbar',
					'active_callback' => array(
						array(
							'setting'  => 'catalog_toolbar_layout',
							'operator' => '==',
							'value'    => 'v3',
						),
					),
				),

				// Categories
				'top_categories_shop_page'                 => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show on Shop Page', 'razzi' ),
					'section' => 'catalog_categories',
					'default' => false,
				),

				'top_categories_category_page' => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show on Category Page', 'razzi' ),
					'section' => 'catalog_categories',
					'default' => false,
				),

				'catalog_top_categories_subcategories' => array(
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Replace by sub-categories', 'razzi' ),
					'default' => false,
					'section' => 'catalog_categories',
				),

				'catalog_top_categories_count' => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Products Count', 'razzi' ),
					'section' => 'catalog_categories',
					'default' => 1,
				),

				'catalog_top_categories_limit' => array(
					'type'    => 'number',
					'label'   => esc_html__( 'Limit', 'razzi' ),
					'section' => 'catalog_categories',
					'default' => 10,
				),

				'catalog_top_categories_orderby' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Order By', 'razzi' ),
					'section' => 'catalog_categories',
					'default' => 'order',
					'choices' => array(
						'order' => esc_html__( 'Category Order', 'razzi' ),
						'name'  => esc_html__( 'Category Name', 'razzi' ),
						'id'    => esc_html__( 'Category ID', 'razzi' ),
						'count' => esc_html__( 'Product Counts', 'razzi' ),
					),
				),

				'top_categories_ajax_filter' => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Filter With AJAX', 'razzi' ),
					'section' => 'catalog_categories',
					'default' => true,
				),
			)
		);


		return $fields;
	}
}
