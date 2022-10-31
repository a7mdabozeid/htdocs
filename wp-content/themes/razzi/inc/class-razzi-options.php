<?php

/**
 * Theme Options
 *
 * @package Razzi
 */

namespace Razzi;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class Options {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * $razzi_customize
	 *
	 * @var $razzi_customize
	 */
	protected static $razzi_customize = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self:: $instance;
	}

	/**
	 * The class constructor
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {
		add_filter('razzi_customize_config', array($this, 'customize_settings'));
		self::$razzi_customize = Theme::instance()->get('customizer');
	}


	/**
	 * This is a short hand function for getting setting value from customizer
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 *
	 * @return bool|string
	 */
	public function get_option($name) {
		if (is_object(self::$razzi_customize)) {
			$value = self::$razzi_customize->get_option($name);
		} elseif (false !== get_theme_mod($name)) {
			$value = get_theme_mod($name);
		} else {
			$value = $this->get_option_default($name);
		}

		return apply_filters('razzi_get_option', $value, $name);
	}

	/**
	 * Get default option values
	 *
	 * @since 1.0.0
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function get_option_default($name) {
		if (empty(self::$razzi_customize)) {
			return false;
		}

		return self:: $razzi_customize->get_option_default($name);
	}

	/**
	 * Get categories
	 *
	 * @since 1.0.0
	 *
	 * @param $taxonomies
	 * @param $default
	 *
	 * @return array
	 */
	public function get_categories($taxonomies, $default = false) {
		if (!taxonomy_exists($taxonomies)) {
			return [];
		}

		if (!is_admin()) {
			return [];
		}

		$output = [];

		if ($default) {
			$output[0] = esc_html__('Select Category', 'razzi');
		}

		global $wpdb;
		$post_meta_infos = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.term_id AS id, b.name as name, b.slug AS slug
						FROM {$wpdb->term_taxonomy} AS a
						INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
						WHERE a.taxonomy                            = '%s'",
				$taxonomies
			),
			ARRAY_A
		);

		if (is_array($post_meta_infos) && !empty($post_meta_infos)) {
			foreach ($post_meta_infos as $value) {
				$output[$value['slug']] = $value['name'];
			}
		}

		return $output;
	}

	/**
	 * Options of footer items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function footer_items_option() {
		return apply_filters('razzi_footer_items_option', array(
			'copyright' => esc_html__('Copyright', 'razzi'),
			'menu'      => esc_html__('Menu', 'razzi'),
			'text'      => esc_html__('Custom text', 'razzi'),
			'payment'   => esc_html__('Payments', 'razzi'),
			'social'    => esc_html__('Socials', 'razzi'),
			'logo'      => esc_html__('Logo', 'razzi'),
		));
	}

	/**
	 * Options of header items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function header_items_option() {
		return apply_filters('razzi_header_items_option', array(
			'0'              => esc_html__('Select a item', 'razzi'),
			'logo'           => esc_html__('Logo', 'razzi'),
			'menu-primary'   => esc_html__('Primary Menu', 'razzi'),
			'menu-secondary' => esc_html__('Secondary Menu', 'razzi'),
			'hamburger'      => esc_html__('Hamburger Icon', 'razzi'),
			'search'         => esc_html__('Search Icon', 'razzi'),
			'cart'           => esc_html__('Cart Icon', 'razzi'),
			'wishlist'       => esc_html__('Wishlist Icon', 'razzi'),
			'account'        => esc_html__('Account Icon', 'razzi'),
			'languages'      => esc_html__('Languages', 'razzi'),
			'currencies'     => esc_html__('Currencies', 'razzi'),
			'department'     => esc_html__('Department', 'razzi'),
			'socials'        => esc_html__('Socials', 'razzi'),
		));
	}

	/**
	 * Options of topbar items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function topbar_items_option() {
		return apply_filters('razzi_topbar_items_option', array(
			'menu'     => esc_html__('Menu', 'razzi'),
			'currency' => esc_html__('Currency Switcher', 'razzi'),
			'language' => esc_html__('Language Switcher', 'razzi'),
			'social'   => esc_html__('Socials', 'razzi'),
			'text'     => esc_html__('Custom Text', 'razzi'),
			'close'    => esc_html__('Close Icon', 'razzi'),
		));
	}

		/**
	 * Options of navigation bar items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function navigation_bar_items_option() {
		return apply_filters( 'razzi_navigation_bar_items_option', array(
			'home'     => esc_html__( 'Home', 'razzi' ),
			'menu'     => esc_html__( 'Menu', 'razzi' ),
			'search'   => esc_html__( 'Search', 'razzi' ),
			'cart'     => esc_html__( 'Cart', 'razzi' ),
			'wishlist' => esc_html__( 'Wishlist', 'razzi' ),
			'account'  => esc_html__( 'Account', 'razzi' ),
		) );
	}


	/**
	 * Options of mobile header icons
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function mobile_header_icons_option() {
		return apply_filters( 'razzi_mobile_header_icons_option', array(
			'cart'     => esc_html__( 'Cart Icon', 'razzi' ),
			'wishlist' => esc_html__( 'Wishlist Icon', 'razzi' ),
			'account'  => esc_html__( 'Account Icon', 'razzi' ),
			'menu'     => esc_html__( 'Menu Icon', 'razzi' ),
			'search'   => esc_html__( 'Search Icon', 'razzi' ),
		) );
	}

	/**
	 * Get customize settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function customize_settings() {
		$settings = array(
			'theme' => 'razzi',
		);

		$panels = array(
			'general' => array(
				'priority' => 10,
				'title'    => esc_html__('General', 'razzi'),
			),

			// Typography
			'typography' => array(
				'priority' => 30,
				'title'    => esc_html__( 'Typography', 'razzi' ),
			),

			// Header
			'header'  => array(
				'title'      => esc_html__('Header', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 30,
			),

			'page'   => array(
				'title'      => esc_html__('Page', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 40,
			),

			// Blog
			'blog'   => array(
				'title'      => esc_html__('Blog', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 50,
			),

			// Footer
			'footer' => array(
				'title'      => esc_html__('Footer', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 60,
			),
			// Footer
			'mobile' => array(
				'title'      => esc_html__('Mobile', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 60,
			),

		);

		$sections = array(
			// Maintenance
			'maintenance'  => array(
				'title'      => esc_html__('Maintenance', 'razzi'),
				'priority'   => 10,
				'capability' => 'edit_theme_options',
			),
			// Boxed
			'boxed_layout' => array(
				'title'       => esc_html__('Boxed Layout', 'razzi'),
				'description' => '',
				'priority'    => 20,
				'capability'  => 'edit_theme_options',
				'panel'       => 'general',
			),

			'general_backtotop' => array(
				'title'       => esc_html__('Back To Top', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'general',
				'priority'    => 20,
			),
			'colors'            => array(
				'title'    => esc_html__( 'Colors', 'razzi' ),
				'priority' => 20,
			),

			// Typography
			'typo_main'         => array(
				'title'    => esc_html__( 'Main', 'razzi' ),
				'panel'    => 'typography',
			),
			'typo_headings'     => array(
				'title'    => esc_html__( 'Headings', 'razzi' ),
				'panel'    => 'typography',
			),
			'typo_header'       => array(
				'title'    => esc_html__( 'Header', 'razzi' ),
				'panel'    => 'typography',
			),
			'typo_page'         => array(
				'title'    => esc_html__( 'Page', 'razzi' ),
				'panel'    => 'typography',
			),
			'typo_posts'        => array(
				'title'    => esc_html__( 'Blog', 'razzi' ),
				'panel'    => 'typography',
			),
			'typo_widget'       => array(
				'title'    => esc_html__( 'Widgets', 'razzi' ),
				'panel'    => 'typography',
			),
			'typo_footer'       => array(
				'title'    => esc_html__( 'Footer', 'razzi' ),
				'panel'    => 'typography',
			),

			// Newsletter
			'newsletter_popup'  => array(
				'title'      => esc_html__('Newsletter Popup', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 20,
			),

			'preloader'         => array(
				'title'    => esc_html__( 'Preloader', 'razzi' ),
				'priority' => 20,
				'panel'    => 'general',
			),

			// Header
			'header_top'        => array(
				'title' => esc_html__('Topbar', 'razzi'),
				'panel' => 'header',
			),
			'header_topbar_bg'  => array(
				'title' => esc_html__('Topbar Background', 'razzi'),
				'panel' => 'header',
			),
			'header_layout'     => array(
				'title' => esc_html__('Header Layout', 'razzi'),
				'panel' => 'header',
			),
			'header_main'       => array(
				'title' => esc_html__('Header Main', 'razzi'),
				'panel' => 'header',
			),
			'header_bottom'     => array(
				'title' => esc_html__('Header Bottom', 'razzi'),
				'panel' => 'header',
			),
			'header_background' => array(
				'title' => esc_html__('Header Background', 'razzi'),
				'panel' => 'header',
			),
			'header_campaign'   => array(
				'title' => esc_html__('Campaign Bar', 'razzi'),
				'panel' => 'header',
			),
			'header_logo'       => array(
				'title' => esc_html__('Logo', 'razzi'),
				'panel' => 'header',
			),
			'header_search'     => array(
				'title' => esc_html__('Search', 'razzi'),
				'panel' => 'header',
			),
			'header_account'    => array(
				'title' => esc_html__('Account', 'razzi'),
				'panel' => 'header',
			),
			'header_wishlist'   => array(
				'title' => esc_html__('Wishlist', 'razzi'),
				'panel' => 'header',
			),
			'header_cart'       => array(
				'title' => esc_html__('Cart', 'razzi'),
				'panel' => 'header',
			),
			'header_primary_menu'  => array(
				'title' => esc_html__('Primary Menu', 'razzi'),
				'panel' => 'header',
			),
			'header_hamburger'  => array(
				'title' => esc_html__('Hamburger Menu', 'razzi'),
				'panel' => 'header',
			),
			'header_department' => array(
				'title' => esc_html__('Department', 'razzi'),
				'panel' => 'header',
			),

			// Page
			'page_header'       => array(
				'title'       => esc_html__('Page Header', 'razzi'),
				'description' => '',
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'panel'       => 'page',
			),

			// Blog
			'blog_page'         => array(
				'title'       => esc_html__('Blog Page', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'blog',
			),

			'page_header_blog'  => array(
				'title'       => esc_html__('Blog Page Header', 'razzi'),
				'description' => '',
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'panel'       => 'blog',
			),

			// Single Post
			'single_post'       => array(
				'title'       => esc_html__('Single Post', 'razzi'),
				'description' => '',
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'panel'       => 'blog',
			),

			// Footer
			'footer_layout'     => array(
				'title'       => esc_html__('Footer Layout', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_newsletter' => array(
				'title'       => esc_html__('Footer Newsletter', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_widget'		=> array(
				'title'       => esc_html__('Footer Widget', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_extra'      => array(
				'title'       => esc_html__('Footer Extra', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_main'       => array(
				'title'       => esc_html__('Footer Main', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_background' => array(
				'title'       => esc_html__('Footer Background', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_copyright'  => array(
				'title'       => esc_html__('Copyright', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_menu'  => array(
				'title'       => esc_html__('Menu', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_text'       => array(
				'title'       => esc_html__('Custom Text', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_payment'    => array(
				'title'       => esc_html__('Payments', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),
			'footer_logo'       => array(
				'title'       => esc_html__('Logo', 'razzi'),
				'description' => '',
				'capability'  => 'edit_theme_options',
				'panel'       => 'footer',
			),

			'recently_viewed'  => array(
				'title'      => esc_html__('Recently Viewed', 'razzi'),
				'capability' => 'edit_theme_options',
				'priority'   => 50,
			),
			// Mobile
			'mobile_newsletter_popup' => array(
				'title' => esc_html__( 'Newsletter Popup', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_topbar'           => array(
				'title' => esc_html__( 'Topbar', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_header'           => array(
				'title' => esc_html__( 'Header', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_header_campaign'  => array(
				'title' => esc_html__( 'Campaign Bar', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_footer'           => array(
				'title' => esc_html__( 'Footer', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_panel'           => array(
				'title' => esc_html__( 'Panel', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_page'             => array(
				'title' => esc_html__( 'Page', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_blog'             => array(
				'title' => esc_html__( 'Blog', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_single_blog'             => array(
				'title' => esc_html__( 'Single Blog', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_product_catalog'  => array(
				'title' => esc_html__( 'Product Catalog', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_single_product'  => array(
				'title' => esc_html__( 'Single Product', 'razzi' ),
				'panel' => 'mobile',
			),
			'mobile_version'          => array(
				'priority' => 50,
				'title'    => esc_html__( 'Mobile Version', 'razzi' ),
				'panel'    => 'mobile',
			)

		);

		$fields = array(
			'color_scheme_title'  => array(
				'type'  => 'custom',
				'section'     => 'colors',
				'label' => esc_html__( 'Color Scheme', 'razzi' ),
			),
			'color_scheme'        => array(
				'type'            => 'color-palette',
				'default'         => '#ff6F61',
				'choices'         => array(
					'colors' => array(
						'#ff6F61',
						'#053399',
						'#3f51b5',
						'#7b1fa2',
						'#009688',
						'#388e3c',
						'#e64a19',
						'#b8a08d',
					),
					'style'  => 'round',
				),
				'section'     => 'colors',
				'active_callback' => array(
					array(
						'setting'  => 'color_scheme_custom',
						'operator' => '!=',
						'value'    => true,
					),
				),
			),
			'color_scheme_custom' => array(
				'type'      => 'checkbox',
				'label'     => esc_html__( 'Pick my favorite color', 'razzi' ),
				'default'   => false,
				'section'     => 'colors',
			),
			'color_scheme_color'  => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Custom Color', 'razzi' ),
				'default'         => '#161619',
				'section'     => 'colors',
				'active_callback' => array(
					array(
						'setting'  => 'color_scheme_custom',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'preloader_enable'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable Preloader', 'razzi' ),
				'description' => esc_html__( 'Show a waiting screen when page is loading', 'razzi' ),
				'default'     => false,
				'section'     => 'preloader',
				'transport'   => 'postMessage',
			),
			'preloader_background_color' => array(
				'type'            => 'color',
				'label'           => esc_html__( 'Background Color', 'razzi' ),
				'default'         => 'rgba(255,255,255,1)',
				'section'     => 'preloader',
				'choices'         => array(
					'alpha' => true,
				),
				'active_callback' => array(
					array(
						'setting'  => 'preloader_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport'   => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '#preloader',
						'property' => 'background-color',
					),
				),
			),
			'preloader'                  => array(
				'type'            => 'radio',
				'label'           => esc_html__( 'Preloader', 'razzi' ),
				'default'         => 'default',
				'section'     => 'preloader',
				'choices'         => array(
					'default'  => esc_attr__( 'Default Icon', 'razzi' ),
					'image'    => esc_attr__( 'Upload custom image', 'razzi' ),
					'external' => esc_attr__( 'External image URL', 'razzi' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'preloader_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
			),
			'preloader_image'            => array(
				'type'            => 'image',
				'description'     => esc_html__( 'Preloader Image', 'razzi' ),
				'section'     => 'preloader',
				'active_callback' => array(
					array(
						'setting'  => 'preloader_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'preloader',
						'operator' => '==',
						'value'    => 'image',
					),
				),
				'transport'       => 'postMessage',
			),
			'preloader_url'              => array(
				'type'            => 'text',
				'description'     => esc_html__( 'Preloader URL', 'razzi' ),
				'choices'         => array(
					'default'  => esc_attr__( 'Default Icon', 'razzi' ),
					'image'    => esc_attr__( 'Upload custom image', 'razzi' ),
					'external' => esc_attr__( 'External image URL', 'razzi' ),
				),
				'section'     => 'preloader',
				'active_callback' => array(
					array(
						'setting'  => 'preloader_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'preloader',
						'operator' => '==',
						'value'    => 'external',
					),
				),
				'transport'       => 'postMessage',
			),
			// Popup
			'newsletter_popup_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable Popup', 'razzi'),
				'description' => esc_html__('Show a newsletter popup after website loaded.', 'razzi'),
				'section'     => 'newsletter_popup',
				'default'     => false,
				'transport'   => 'postMessage',
			),

			'newsletter_popup_layout' => array(
				'type'      => 'radio-buttonset',
				'label'     => esc_html__('Popup Layout', 'razzi'),
				'default'   => '2-columns',
				'transport' => 'postMessage',
				'choices'   => array(
					'1-column'  => esc_attr__('1 Column', 'razzi'),
					'2-columns' => esc_attr__('2 Columns', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'newsletter_popup',
			),

			'newsletter_popup_image' => array(
				'type'            => 'image',
				'label'           => esc_html__('Image', 'razzi'),
				'description'     => esc_html__('This image will be used as background of the popup if the layout is 1 Column', 'razzi'),
				'transport'       => 'postMessage',
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'newsletter_popup',
			),

			'newsletter_popup_content' => array(
				'type'            => 'editor',
				'label'           => esc_html__('Popup Content', 'razzi'),
				'description'     => esc_html__('Enter popup content. HTML and shortcodes are allowed.', 'razzi'),
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',
				'section' => 'newsletter_popup',
			),

			'newsletter_popup_form' => array(
				'type'            => 'textarea',
				'label'           => esc_html__('NewsLetter Form', 'razzi'),
				'default'         => '',
				'description'     => sprintf(wp_kses_post('Enter the shortcode of MailChimp form . You can edit your sign - up form in the <a href= "%s" > MailChimp for WordPress form settings </a>.', 'razzi'), admin_url('admin.php?page=mailchimp-for-wp-forms')),
				'section'         => 'newsletter_popup',
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport'       => 'postMessage',

			),

			'newsletter_popup_frequency' => array(
				'type'        => 'number',
				'label'       => esc_html__('Frequency', 'razzi'),
				'description' => esc_html__('Do NOT show the popup to the same visitor again until this much days has passed.', 'razzi'),
				'default'     => 1,
				'choices'     => array(
					'min'  => 0,
					'step' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport' => 'postMessage',
				'section'   => 'newsletter_popup',
			),

			'newsletter_popup_visible' => array(
				'type'        => 'select',
				'label'       => esc_html__('Popup Visible', 'razzi'),
				'description' => esc_html__('Select when the popup appear', 'razzi'),
				'default'     => 'loaded',
				'choices'     => array(
					'loaded' => esc_html__('Right after page loads', 'razzi'),
					'delay'  => esc_html__('Wait for seconds', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'transport' => 'postMessage',
				'section'   => 'newsletter_popup',
			),

			'newsletter_popup_visible_delay' => array(
				'type'        => 'number',
				'label'       => esc_html__('Delay Time', 'razzi'),
				'description' => esc_html__('The time (in seconds) before the popup is displayed, after the page loaded.', 'razzi'),
				'default'     => 5,
				'choices'     => array(
					'min'  => 0,
					'step' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'newsletter_popup_enable',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => 'newsletter_popup_visible',
						'operator' => '==',
						'value'    => 'delay',
					),
				),
				'transport' => 'postMessage',
				'section'   => 'newsletter_popup',
			),

			// Maintenance
			'maintenance_enable'             => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable Maintenance Mode', 'razzi'),
				'description' => esc_html__('Put your site into maintenance mode', 'razzi'),
				'default'     => false,
				'section'     => 'maintenance',
			),
			'maintenance_mode'               => array(
				'type'        => 'radio',
				'label'       => esc_html__('Mode', 'razzi'),
				'description' => esc_html__('Select the correct mode for your site', 'razzi'),
				'tooltip'     => wp_kses_post(sprintf(__('If you are putting your site into maintenance mode for a longer perior of time, you should set this to "Coming Soon". Maintenance will return HTTP 503, Comming Soon will set HTTP to 200. <a href="%s" target="_blank">Learn more</a>', 'razzi'), 'https://yoast.com/http-503-site-maintenance-seo/')),
				'default'     => 'maintenance',
				'choices'     => array(
					'maintenance' => esc_attr__('Maintenance', 'razzi'),
					'coming_soon' => esc_attr__('Coming Soon', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'maintenance_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'maintenance',
			),
			'maintenance_page'               => array(
				'type'            => 'dropdown-pages',
				'label'           => esc_html__('Maintenance Page', 'razzi'),
				'default'         => 0,
				'active_callback' => array(
					array(
						'setting'  => 'maintenance_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'maintenance',
			),

			// Typography
			'typo_body'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Body', 'razzi' ),
				'description' => esc_html__( 'Customize the body font', 'razzi' ),
				'section' 	  => 'typo_main',
				'default'     => array(
					'font-family' => '',
					'variant'     => '400',
					'font-size'   => '16px',
					'line-height' => '1.5',
					'color'       => '#525252',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'body',
					),
				),
			),

			'typo_h1'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 1', 'razzi' ),
				'description' => esc_html__( 'Customize the H1 font', 'razzi' ),
				'section' 	  => 'typo_headings',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '60px',
					'line-height'    => '1.33',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h1, .h1',
					),
				),
			),
			'typo_h2'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 2', 'razzi' ),
				'description' => esc_html__( 'Customize the H2 font', 'razzi' ),
				'section' 	  => 'typo_headings',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '40px',
					'line-height'    => '1.33',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h2, .h2',
					),
				),
			),
			'typo_h3'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 3', 'razzi' ),
				'description' => esc_html__( 'Customize the H3 font', 'razzi' ),
				'section' 	  => 'typo_headings',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '32px',
					'line-height'    => '1.33',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h3, .h3',
					),
				),
			),
			'typo_h4'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 4', 'razzi' ),
				'description' => esc_html__( 'Customize the H4 font', 'razzi' ),
				'section' 	  => 'typo_headings',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '24px',
					'line-height'    => '1.33',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h4, .h4',
					),
				),
			),
			'typo_h5'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 5', 'razzi' ),
				'description' => esc_html__( 'Customize the H5 font', 'razzi' ),
				'section' 	  => 'typo_headings',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '20px',
					'line-height'    => '1.33',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h5, .h5',
					),
				),
			),
			'typo_h6'                        => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Heading 6', 'razzi' ),
				'description' => esc_html__( 'Customize the H6 font', 'razzi' ),
				'section' 	  => 'typo_headings',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '16px',
					'line-height'    => '1.33',
					'color'          => '#111',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => 'h6, .h6',
					),
				),
			),

			'typo_menu'                      => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Menu', 'razzi' ),
				'description' => esc_html__( 'Customize the menu font', 'razzi' ),
				'section' 	  => 'typo_header',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '16px',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.main-navigation a, .hamburger-navigation a',
					),
				),
			),
			'typo_submenu'                   => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Sub-Menu', 'razzi' ),
				'description' => esc_html__( 'Customize the sub-menu font', 'razzi' ),
				'section'     => 'typo_header',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '15px',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.main-navigation ul ul a, .hamburger-navigation ul ul a',
					),
				),
			),

			'typo_page_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Page Title', 'razzi' ),
				'description' => esc_html__( 'Customize the page title font', 'razzi' ),
				'section'     => 'typo_page',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '32px',
					'line-height'    => '1.33',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.page-header__title',
					),
				),
			),

			'typo_blog_header_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Header Title', 'razzi' ),
				'description' => esc_html__( 'Customize the font of blog header', 'razzi' ),
				'section'     => 'typo_posts',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '32px',
					'line-height'    => '1.33',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.blog .page-header__title',
					),
				),
			),
			'typo_blog_post_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Post Title', 'razzi' ),
				'description' => esc_html__( 'Customize the font of blog post title', 'razzi' ),
				'section'     => 'typo_posts',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '500',
					'font-size'      => '24px',
					'line-height'    => '1.33',
					'text-transform' => 'none',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.hentry .entry-title a',
					),
				),
			),
			'typo_blog_post_excerpt'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Blog Post Excerpt', 'razzi' ),
				'description' => esc_html__( 'Customize the font of blog post excerpt', 'razzi' ),
				'section'     => 'typo_posts',
				'default'     => array(
					'font-family'    => '',
					'variant'        => 'regular',
					'font-size'      => '16px',
					'line-height'    => '1.5',
					'text-transform' => 'none',
					'color'          => '#525252',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.blog-wrapper .entry-content',
					),
				),
			),

			'typo_widget_title'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Widget Title', 'razzi' ),
				'description' => esc_html__( 'Customize the widget title font', 'razzi' ),
				'section'     => 'typo_widget',
				'default'     => array(
					'font-family'    => '',
					'variant'        => '600',
					'font-size'      => '16px',
					'text-transform' => 'uppercase',
					'color'          => '#111',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.widget-title, .footer-widgets .widget-title',
					),
				),
			),

			'typo_footer_extra'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Footer Extra', 'razzi' ),
				'description' => esc_html__( 'Customize the font of footer extra', 'razzi' ),
				'section'     => 'typo_widget',
				'default'     => array(
					'font-family'    => '',
					'variant'        => 'regular',
					'font-size'      => '16px',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.footer-extra',
					),
				),
			),
			'typo_footer_widgets'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Footer Widgets', 'razzi' ),
				'description' => esc_html__( 'Customize the font of footer widgets', 'razzi' ),
				'section'     => 'typo_widget',
				'default'     => array(
					'font-family'    => '',
					'variant'        => 'regular',
					'font-size'      => '16px',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.footer-widgets',
					),
				),
			),
			'typo_footer_main'              => array(
				'type'        => 'typography',
				'label'       => esc_html__( 'Footer Main', 'razzi' ),
				'description' => esc_html__( 'Customize the font of footer main', 'razzi' ),
				'section'     => 'typo_widget',
				'default'     => array(
					'font-family'    => '',
					'variant'        => 'regular',
					'font-size'      => '14px',
					'text-transform' => 'none',
					'subsets'        => array( 'latin-ext' ),
				),
				'transport' => 'postMessage',
				'js_vars'      => array(
					array(
						'element' => '.footer-main',
					),
				),
			),

			// topbar
			'topbar'                         => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Topbar', 'razzi'),
				'default'     => '0',
				'section'     => 'header_top',
				'description' => esc_html__('Check this option to enable a topbar above the site header', 'razzi'),
			),

			'topbar_custom_field_1' => array(
				'type'    => 'custom',
				'section' => 'header_top',
				'default' => '<hr/>',
			),

			'topbar_height' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Height', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '45',
				'choices'   => array(
					'min' => 0,
					'max' => 240,
				),
				'js_vars'   => array(
					array(
						'element'  => '.topbar',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'section' => 'header_top',
			),

			'topbar_custom_field_2' => array(
				'type'    => 'custom',
				'section' => 'header_top',
				'default' => '<hr/>',
			),

			'topbar_left'  => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Left Items', 'razzi'),
				'description' => esc_html__('Control items on the left side of the topbar', 'razzi'),
				'transport'   => 'postMessage',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'      => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'section' => 'header_top',
			),
			'topbar_right' => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Right Items', 'razzi'),
				'description' => esc_html__('Control items on the right side of the topbar', 'razzi'),
				'transport'   => 'postMessage',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'      => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'section' => 'header_top',
			),

			'topbar_custom_field_5' => array(
				'type'    => 'custom',
				'section' => 'header_top',
				'default' => '<hr/>',
			),

			'topbar_menu_item'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Menu', 'razzi' ),
				'section'         => 'header_top',
				'default'         => '',
				'choices'         => $this->get_navigation_bar_get_menus(),

			),

			'topbar_custom_field_4' => array(
				'type'    => 'custom',
				'section' => 'header_top',
				'default' => '<hr/>',
			),

			'topbar_svg_code' => array(
				'type'              => 'textarea',
				'label'             => esc_html__('Custom Text SVG', 'razzi'),
				'description'       => esc_html__('The SVG before your text', 'razzi'),
				'default'           => '',
				'sanitize_callback' => '\Razzi\Icon::sanitize_svg',
				'output'            => array(
					array(
						'element' => '.razzi-topbar__text .razzi-svg-icon',
					),
				),
				'section' => 'header_top',
			),

			'topbar_text'             => array(
				'type'        => 'editor',
				'label'       => esc_html__('Custom Text', 'razzi'),
				'description' => esc_html__('The content of Custom Text item', 'razzi'),
				'default'     => '',
				'section'     => 'header_top',
			),


			// topbar bg
			'topbar_bg_custom_color'  => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Custom Color', 'razzi'),
				'section' => 'header_topbar_bg',
				'default' => 0,
			),
			'topbar_bg_color'         => array(
				'label'           => esc_html__('Background Color', 'razzi'),
				'type'            => 'color',
				'default'         => '',
				'transport'       => 'postMessage',
				'active_callback' => array(
					array(
						'setting'  => 'topbar_bg_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.topbar',
						'property' => 'background-color',
					),
				),
				'section' => 'header_topbar_bg',
			),
			'topbar_text_color'       => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Color', 'razzi'),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'topbar_bg_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.topbar',
						'property' => '--rz-color-dark',
					),
					array(
						'element'  => '.topbar',
						'property' => '--rz-icon-color',
					)
				),
				'section' => 'header_topbar_bg',
			),
			'topbar_text_color_hover' => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Hover Color', 'razzi'),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'topbar_bg_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.topbar',
						'property' => '--rz-color-primary',
					),
				),
				'section' => 'header_topbar_bg',
			),
			'topbar_bg_border_custom_field_1'              => array(
				'type'    => 'custom',
				'section' => 'header_topbar_bg',
				'default' => '<hr/>',
			),
			'topbar_bg_border'  => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Border Line', 'razzi'),
				'section' => 'header_topbar_bg',
				'default' => 0,
			),
			'topbar_bg_border_color' => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color', 'razzi'),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'topbar_bg_border',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.topbar',
						'property' => 'border-color',
					),
				),
				'section' => 'header_topbar_bg',
			),

			// Header Layout
			'header_type'             => array(
				'type'        => 'radio',
				'label'       => esc_html__('Header Type', 'razzi'),
				'description' => esc_html__('Select a default header or custom header', 'razzi'),
				'section'     => 'header_layout',
				'default'     => 'default',
				'choices'     => array(
					'default' => esc_html__('Use pre-build header', 'razzi'),
					'custom'  => esc_html__('Build my own', 'razzi'),
				),
			),
			'header_layout'           => array(
				'type'    => 'select',
				'label'   => esc_html__('Header Layout', 'razzi'),
				'section' => 'header_layout',
				'default' => 'v1',
				'choices' => array(
					'v1' => esc_html__('Header v1', 'razzi'),
					'v2' => esc_html__('Header v2', 'razzi'),
					'v3' => esc_html__('Header v3', 'razzi'),
					'v4' => esc_html__('Header v4', 'razzi'),
					'v5' => esc_html__('Header v5', 'razzi'),
					'v6' => esc_html__('Header v6', 'razzi'),
					'v7' => esc_html__('Header v7', 'razzi'),
					'v8' => esc_html__('Header v8', 'razzi'),
					'v9' => esc_html__('Header v9', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'default',
					),
				),
			),

			'header_layout_custom_field_1'              => array(
				'type'    => 'custom',
				'section' => 'header_layout',
				'default' => '<hr/>',
			),

			// Header Sticky
			'header_sticky'                             => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Header Sticky', 'razzi'),
				'default' => 0,
				'section' => 'header_layout',
			),
			'header_width'	=> array(
				'type'    => 'select',
				'label'   => esc_html__('Header Width', 'razzi'),
				'section' => 'header_layout',
				'default' => '',
				'choices' => array(
					''      => esc_html__('Normal', 'razzi'),
					'large' => esc_html__('Large', 'razzi'),
				),
			),
			'header_sticky_el'                          => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Header Sticky Elements', 'razzi'),
				'section'  => 'header_layout',
				'default'  => array('header_main'),
				'priority' => 10,
				'choices'  => array(
					'header_main'   => esc_html__('Header Main', 'razzi'),
					'header_bottom' => esc_html__('Header Bottom', 'razzi'),
				),
				'active_callback' => function() {
					return $this->display_header_sticky();
				},
			),

			// Header main
			'header_main_left'                          => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Left Items', 'razzi'),
				'description' => esc_html__('Control items on the left side of header main', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_main',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_main_center'                        => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Center Items', 'razzi'),
				'description' => esc_html__('Control items at the center of header main', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_main',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_main_right'                         => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Right Items', 'razzi'),
				'description' => esc_html__('Control items on the right of header main', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_main',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_main_hr'                            => array(
				'type'    => 'custom',
				'section' => 'header_main',
				'default' => '<hr>',
			),
			'header_main_height'                        => array(
				'type'      => 'slider',
				'label'     => esc_html__('Header Height', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'header_main',
				'default'   => '90',
				'choices'   => array(
					'min' => 50,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.header-main',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
			'sticky_header_main_height'                 => array(
				'type'        => 'slider',
				'label'       => esc_html__('Header Sticky Height', 'razzi'),
				'description' => esc_html__('Adjust Header Main height when Header Sticky is enable', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_main',
				'default'     => '90',
				'choices'     => array(
					'min' => 50,
					'max' => 500,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-sticky .site-header.minimized .header-main',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),

			// Header Bottom
			'header_bottom_left'                        => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Left Items', 'razzi'),
				'description' => esc_html__('Control items on the left side of header bottom', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_bottom',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_bottom_center'                      => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Center Items', 'razzi'),
				'description' => esc_html__('Control items at the center of header bottom', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_bottom',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_bottom_right'                       => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Right Items', 'razzi'),
				'description' => esc_html__('Control items on the right of header bottom', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_bottom',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->header_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_bottom_hr'                          => array(
				'type'    => 'custom',
				'section' => 'header_bottom',
				'default' => '<hr>',
			),
			'header_bottom_height'                      => array(
				'type'      => 'slider',
				'label'     => esc_html__('Header Height', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'header_bottom',
				'default'   => '50',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.header-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
			'sticky_header_bottom_height'               => array(
				'type'        => 'slider',
				'label'       => esc_html__('Header Sticky Height', 'razzi'),
				'description' => esc_html__('Adjust Header Bottom height when Header Sticky is enable', 'razzi'),
				'transport'   => 'postMessage',
				'section'     => 'header_bottom',
				'default'     => '50',
				'choices'     => array(
					'min' => 0,
					'max' => 500,
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_sticky',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-sticky .site-header.minimized .header-bottom',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),

			// Header Backgound
			'header_main_background'                    => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Header Main Custom Color', 'razzi'),
				'section' => 'header_background',
				'default' => 0,
			),
			'header_main_background_color'              => array(
				'type'            => 'color',
				'label'           => esc_html__('Background Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_main_background',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-main, .site-header .header-mobile',
						'property' => 'background-color',
					),
					array(
						'element'  => '.header-v6 .header-main .razzi-header-container',
						'property' => '--rz-background-color-light',
					),
				),
			),
			'header_main_background_text_color'         => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_main_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-main, .site-header .header-mobile',
						'property' => '--rz-header-color-dark',
					),
					array(
						'element'  => '.site-header .header-main, .site-header .header-mobile',
						'property' => '--rz-stroke-svg-dark',
					),
				),
			),
			'header_main_background_text_color_hover'   => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Hover Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_main_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-main',
						'property' => '--rz-color-hover-primary',
					),
				),
			),
			'header_main_background_border_color'       => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_main_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-main',
						'property' => 'border-color',
					),
				),
			),
			'header_background_hr'                      => array(
				'type'    => 'custom',
				'section' => 'header_background',
				'default' => '<hr>',
			),
			'header_bottom_background'                  => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Header Bottom Custom Color', 'razzi'),
				'section' => 'header_background',
				'default' => 0,
			),
			'header_bottom_background_color'            => array(
				'type'            => 'color',
				'label'           => esc_html__('Background Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_bottom_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-bottom',
						'property' => 'background-color',
					),
				),
			),
			'header_bottom_background_text_color'       => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_bottom_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-bottom',
						'property' => '--rz-header-color-dark',
					),
					array(
						'element'  => '.header-v3 .header-bottom .main-navigation > ul > li > a',
						'property' => '--rz-header-color-dark',
					),
					array(
						'element'  => '.site-header .header-bottom',
						'property' => '--rz-stroke-svg-dark',
					),
				),
			),
			'header_bottom_background_text_color_hover' => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Hover Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_bottom_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-bottom',
						'property' => '--rz-color-hover-primary',
					),
				),
			),
			'header_bottom_background_border_color'     => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_background',
				'active_callback' => array(
					array(
						'setting'  => 'header_bottom_background',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.site-header .header-bottom',
						'property' => 'border-color',
					),
				),
			),
			'header_menu_hr'                      => array(
				'type'    => 'custom',
				'section' => 'header_background',
				'default' => '<hr>',
			),

			'header_active_primary_menu_color'                  => array(
				'type'    => 'select',
				'label'   => esc_html__('Active Primary Menu Color', 'razzi'),
				'section' => 'header_background',
				'default' => 'primary',
				'choices' => array(
					'primary'  => esc_html__('Primary Color', 'razzi'),
					'current' => esc_html__('Current Color', 'razzi'),
				),
			),

			// Header Campain
			'campaign_bar'                              => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Campaign Bar', 'razzi'),
				'section'     => 'header_campaign',
				'description' => esc_html__('Display a bar bellow site header. This bar will be hidden when the header background is transparent.', 'razzi'),
				'default'     => false,
			),
			'campaign_bar_position'                     => array(
				'type'    => 'select',
				'label'   => esc_html__('Position', 'razzi'),
				'section' => 'header_campaign',
				'default' => 'after',
				'choices' => array(
					'after'  => esc_html__('After Header', 'razzi'),
					'before' => esc_html__('Before Header', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'campaign_bar_height'                       => array(
				'type'      => 'slider',
				'label'     => esc_html__('Height', 'razzi'),
				'section'   => 'header_campaign',
				'default'   => '50',
				'transport' => 'postMessage',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '#campaign-bar',
						'property' => 'height',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'campaign_bar_custom_field_1'               => array(
				'type'            => 'custom',
				'section'         => 'header_campaign',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'campaign_items'                            => array(
				'type'      => 'repeater',
				'label'     => esc_html__('Campaigns', 'razzi'),
				'section'   => 'header_campaign',
				'transport' => 'postMessage',
				'default'   => array(),
				'row_label' => array(
					'type'  => 'field',
					'value' => esc_attr__('Campaign', 'razzi'),
				),
				'fields'          => array(
					'text'    => array(
						'type'  => 'textarea',
						'label' => esc_html__('Text', 'razzi'),
					),
					'image'   => array(
						'type'  => 'image',
						'label' => esc_html__('Background Image', 'razzi'),
					),
					'bgcolor' => array(
						'type'  => 'color',
						'label' => esc_html__('Background Color', 'razzi'),
					),
					'color'   => array(
						'type'  => 'color',
						'label' => esc_html__('Color', 'razzi'),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'campaign_bar',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			// Logo
			'logo_type'                                 => array(
				'type'    => 'radio',
				'label'   => esc_html__('Logo Type', 'razzi'),
				'default' => 'text',
				'section' => 'header_logo',
				'choices' => array(
					'image' => esc_html__('Image', 'razzi'),
					'svg'   => esc_html__('SVG', 'razzi'),
					'text'  => esc_html__('Text', 'razzi'),
				),
			),
			'logo_svg'                                  => array(
				'type'              => 'textarea',
				'label'             => esc_html__('Logo SVG', 'razzi'),
				'section'           => 'header_logo',
				'description'       => esc_html__('Paste SVG code of your logo here', 'razzi'),
				'sanitize_callback' => '\Razzi\Icon::sanitize_svg',
				'output'            => array(
					array(
						'element' => '.site-branding .logo',
					),
				),
				'active_callback'   => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'logo'                                      => array(
				'type'            => 'image',
				'label'           => esc_html__('Logo', 'razzi'),
				'default'         => '',
				'section'         => 'header_logo',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'logo_svg_light'                            => array(
				'type'              => 'textarea',
				'label'             => esc_html__('Logo Light SVG', 'razzi'),
				'section'           => 'header_logo',
				'description'       => esc_html__('Paste SVG code of your logo here', 'razzi'),
				'sanitize_callback' => '\Razzi\Icon::sanitize_svg',
				'output'            => array(
					array(
						'element' => '.site-branding .logo',
					),
				),
				'active_callback'   => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'logo_light'                                => array(
				'type'            => 'image',
				'label'           => esc_html__('Logo Light', 'razzi'),
				'default'         => '',
				'section'         => 'header_logo',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'logo_text'                                 => array(
				'type'    => 'textarea',
				'label'   => esc_html__('Logo Text', 'razzi'),
				'default' => 'Razzi.',
				'section' => 'header_logo',
				'output'  => array(
					array(
						'element' => '.site-branding .logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'logo_dimension'                            => array(
				'type'    => 'dimensions',
				'label'   => esc_html__('Logo Dimension', 'razzi'),
				'default' => array(
					'width'  => '',
					'height' => '',
				),
				'section'         => 'header_logo',
				'active_callback' => array(
					array(
						'setting'  => 'logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),

			// Header Search
			'header_search_style'                       => array(
				'type'    => 'select',
				'label'   => esc_html__('Search Layout', 'razzi'),
				'default' => 'icon',
				'section' => 'header_search',
				'choices' => array(
					'form-cat' => esc_html__('Icon and categories', 'razzi'),
					'form'     => esc_html__('Icon and search field', 'razzi'),
					'icon'     => esc_html__('Icon only', 'razzi'),
				),
			),
			'header_search_form_style'                  => array(
				'type'    => 'select',
				'label'   => esc_html__('Search Style', 'razzi'),
				'default' => 'boxed',
				'section' => 'header_search',
				'choices' => array(
					'boxed'      => esc_html__('Boxed', 'razzi'),
					'full-width' => esc_html__('Full Width', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'form',
					),
				),
			),
			'header_search_form_skin'                  => array(
				'type'    => 'select',
				'label'   => esc_html__('Search Skin', 'razzi'),
				'default' => 'dark',
				'section' => 'header_search',
				'choices' => array(
					'dark' 		=> esc_html__('Dark', 'razzi'),
					'light'      => esc_html__('Light', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'form',
					),
					array(
						'setting'  => 'header_search_form_style',
						'operator' => '==',
						'value'    => 'boxed',
					),
					array(
						'setting'  => 'header_type',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'header_search_type'                        => array(
				'type'    => 'select',
				'label'   => esc_html__('Search For', 'razzi'),
				'default' => '',
				'section' => 'header_search',
				'choices' => array(
					''        => esc_html__('Search for everything', 'razzi'),
					'product' => esc_html__('Search for products', 'razzi'),
				),
			),
			'header_search_custom_field_1'              => array(
				'type'            => 'custom',
				'section'         => 'header_search',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'icon',
					),
				),
			),
			'header_search_text'                        => array(
				'type'            => 'text',
				'label'           => esc_html__('Panel Search Title', 'razzi'),
				'section'         => 'header_search',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'icon',
					),
				),
			),
			'header_search_custom_field_2'              => array(
				'type'    => 'custom',
				'section' => 'header_search',
				'default' => '<hr/>',
			),
			'header_search_placeholder'                 => array(
				'type'    => 'text',
				'label'   => esc_html__('Placeholder', 'razzi'),
				'section' => 'header_search',
				'default' => '',
			),
			'header_search_custom_field_3'              => array(
				'type'            => 'custom',
				'section'         => 'header_search',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'icon',
					),
				),
			),
			'header_search_ajax'                        => array(
				'type'        => 'toggle',
				'label'       => esc_html__('AJAX Search', 'razzi'),
				'section'     => 'header_search',
				'default'     => 0,
				'description' => esc_html__('Check this option to enable AJAX search in the header', 'razzi'),
			),
			'header_search_number'                      => array(
				'type'            => 'number',
				'label'           => esc_html__('AJAX Product Numbers', 'razzi'),
				'default'         => 3,
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_ajax',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'header_search_custom_field_5'              => array(
				'type'            => 'custom',
				'section'         => 'header_search',
				'default'         => '<hr/>',
			),
			'header_search_quick_links' => array(
				'type'            => 'toggle',
				'section'         => 'header_search',
				'label'           => esc_html__( 'Quick links', 'razzi' ),
				'description'     => esc_html__( 'Display a list of links bellow the search field', 'razzi' ),
				'default'         => false,
			),
			'header_search_links'       => array(
				'type'            => 'repeater',
				'section'         => 'header_search',
				'label'           => esc_html__( 'Links', 'razzi' ),
				'description'     => esc_html__( 'Add custom links of the quick links popup', 'razzi' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_attr__( 'Link', 'razzi' ),
					'field' => 'text',
				),
				'fields'          => array(
					'text' => array(
						'type'  => 'text',
						'label' => esc_html__( 'Text', 'razzi' ),
					),
					'url'  => array(
						'type'  => 'text',
						'label' => esc_html__( 'URL', 'razzi' ),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'header_search_quick_links',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'header_search_custom_field_4'              => array(
				'type'            => 'custom',
				'section'         => 'header_search',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'form',
					),
					array(
						'setting'  => 'header_search_form_style',
						'operator' => '==',
						'value'    => 'boxed',
					),
				),
			),
			'header_search_custom_color'                => array(
				'type'            => 'toggle',
				'label'           => esc_html__('Custom Color', 'razzi'),
				'section'         => 'header_search',
				'default'         => 0,
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '!=',
						'value'    => 'icon',
					),
				),
			),
			'header_search_background_color'            => array(
				'type'            => 'color',
				'label'           => esc_html__('Background Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '!=',
						'value'    => 'icon',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-search.search-form-type .search-fields',
						'property' => 'background-color',
					),
					array(
						'element'  => '.header-search.search-form-type .product-cat-label',
						'property' => 'background-color',
					),
					array(
						'element'  => '#site-header .header-search .form-search .search-field',
						'property' => 'background-color',
					),
				),
			),
			'header_search_text_color'                  => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '!=',
						'value'    => 'icon',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '#site-header .header-search .form-search .search-field',
						'property' => '--rz-header-color-light',
					),
					array(
						'element'  => '#site-header .header-search .form-search .search-field::placeholder',
						'property' => '--rz-color-placeholder',
					),
					array(
						'element'  => '#site-header .header-search .form-search .search-field',
						'property' => '--rz-header-color-darker',
					),
					array(
						'element'  => '.header-search.search-type-form-cat .product-cat-label',
						'property' => '--rz-header-text-color-gray',
					),
				),
			),
			'header_search_button_color'                => array(
				'type'            => 'color',
				'label'           => esc_html__('Icon Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '!=',
						'value'    => 'icon',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '#site-header .header-search .form-search .search-submit',
						'property' => 'color',
					),
					array(
						'element'  => '#site-header .header-search .form-search .search-submit .razzi-svg-icon',
						'property' => 'color',
					),
				),
			),
			'header_search_border_color'                => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '!=',
						'value'    => 'icon',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-search .form-search',
						'property' => 'border-color',
					),
					array(
						'element'  => '.header-search .form-search',
						'property' => '--rz-border-color-light',
					),
					array(
						'element'  => '.header-search .form-search',
						'property' => '--rz-border-color-dark',
					),
					array(
						'element'  => '#site-header .header-search .form-search .search-field',
						'property' => 'border-color',
					),
				),
			),
			'header_search_border_color_hover'          => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color Hover', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'form-cat',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '#site-header .header-search .form-search .search-field:focus',
						'property' => 'border-color',
					),
					array(
						'element'  => '#site-header .header-search .form-search .search-field:focus',
						'property' => '--rz-border-color-dark',
					),
					array(
						'element'  => '.header-search .border-color-dark',
						'property' => '--rz-border-color-dark',
					),
				),
			),
			'header_search_bg_button_color'             => array(
				'type'            => 'color',
				'label'           => esc_html__('Button Background Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'form-cat',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '#site-header .search-type-form-cat .form-search .search-submit',
						'property' => 'background-color',
					),
				),
			),
			'header_search_border_button_color'         => array(
				'type'            => 'color',
				'label'           => esc_html__('Button Border Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_search',
				'active_callback' => array(
					array(
						'setting'  => 'header_search_style',
						'operator' => '==',
						'value'    => 'form-cat',
					),
					array(
						'setting'  => 'header_search_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '#site-header .search-type-form-cat .form-search .search-submit',
						'property' => '--rz-border-color-light',
					),
				),
			),

			// Header Account
			'header_account_behaviour'                  => array(
				'type'    => 'radio',
				'label'   => esc_html__('Account Icon Behaviour', 'razzi'),
				'default' => 'panel',
				'section' => 'header_account',
				'choices' => array(
					'panel' => esc_attr__('Open the account panel', 'razzi'),
					'link'  => esc_attr__('Open the account page', 'razzi'),
				),
			),

			// Header Wishlist
			'header_wishlist_link'                      => array(
				'type'    => 'text',
				'label'   => esc_html__('Custom Wishlist Link', 'razzi'),
				'section' => 'header_wishlist',
				'default' => '',
			),

			// Header Cart
			'header_cart_behaviour'                     => array(
				'type'    => 'radio',
				'label'   => esc_html__('Cart Icon Behaviour', 'razzi'),
				'default' => 'panel',
				'section' => 'header_cart',
				'choices' => array(
					'panel' => esc_attr__('Open the cart panel', 'razzi'),
					'link'  => esc_attr__('Open the cart page', 'razzi'),
				),
			),
			'header_cart_custom_field_1'                => array(
				'type'    => 'custom',
				'section' => 'header_cart',
				'default' => '<hr/>',
			),
			'header_cart_custom_color'                  => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Custom Color Counter', 'razzi'),
				'section'     => 'header_cart',
				'default'     => 0,
				'description' => esc_html__('Change color button counter cart', 'razzi'),
			),
			'header_cart_background_color'              => array(
				'type'            => 'color',
				'label'           => esc_html__('Background Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_cart',
				'active_callback' => array(
					array(
						'setting'  => 'header_cart_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-cart .counter',
						'property' => '--rz-background-color-primary',
					),
				),
			),
			'header_cart_text_color'                    => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_cart',
				'active_callback' => array(
					array(
						'setting'  => 'header_cart_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-cart .counter',
						'property' => '--rz-background-text-color-primary',
					),
				),
			),
			'header_cart_border_color'                  => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_cart',
				'active_callback' => array(
					array(
						'setting'  => 'header_cart_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-cart .counter',
						'property' => '--rz-border-color-lighter',
					),
				),
			),
			'header_cart_custom_field_2'                => array(
				'type'    => 'custom',
				'section' => 'header_cart',
				'default' => '<hr/>',
			),
			'header_mini_cart_buttons'              => array(
				'type'            => 'multicheck',
				'label'           => esc_html__('Mini Cart Buttons', 'razzi'),
				'default'         => array('cart', 'checkout'),
				'section'         => 'header_cart',
				'choices' => array(
					'cart' => esc_attr__('Cart Button', 'razzi'),
					'checkout'  => esc_attr__('Checkout Button', 'razzi'),
				),
			),

			'primary_menu_show_arrow'                      => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Show Arrow Icon', 'razzi'),
				'default'     => 1,
				'section'     => 'header_primary_menu',
			),

			// Header Menu Hamburger
			'hamburger_side_type'                       => array(
				'type'    => 'select',
				'label'   => esc_html__('Show Menu', 'razzi'),
				'section' => 'header_hamburger',
				'default' => 'side-right',
				'choices' => array(
					'side-left'  => esc_html__('Side to right', 'razzi'),
					'side-right' => esc_html__('Side to left', 'razzi'),
				),
			),
			'hamburger_click_item'                      => array(
				'type'    => 'select',
				'label'   => esc_html__('Show Sub-Menus', 'razzi'),
				'section' => 'header_hamburger',
				'default' => 'click-item',
				'choices' => array(
					'click-item' => esc_html__('Click to item', 'razzi'),
					'click-icon' => esc_html__('Click to icon', 'razzi'),
				),
			),
			'hamburger_show_arrow'                      => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Show Arrow Icon', 'razzi'),
				'default'     => '0',
				'section'     => 'header_hamburger',
			),
			'header_hamburger_custom_field_1'           => array(
				'type'    => 'custom',
				'section' => 'header_hamburger',
				'default' => '<hr/>',
			),
			'hamburger_show_socials'                    => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Show Socials', 'razzi'),
				'default'     => '0',
				'section'     => 'header_hamburger',
				'description' => esc_html__('Display social menu', 'razzi'),
			),
			'header_hamburger_custom_field_2'           => array(
				'type'    => 'custom',
				'section' => 'header_hamburger',
				'default' => '<hr/>',
			),
			'hamburger_show_copyright'                  => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Show Copyright', 'razzi'),
				'default'     => '0',
				'section'     => 'header_hamburger',
				'description' => esc_html__('Display copyright', 'razzi'),
			),

			// Header Department
			'header_department_hr'                      => array(
				'type'        => 'custom',
				'section'     => 'header_department',
				'default'     => '<hr>',
				'description' => esc_html__('Go to Appearance > Menus > create a new menu and check to Department Menu location', 'razzi'),
			),
			'header_department_text'                    => array(
				'type'    => 'text',
				'label'   => esc_html__('Text', 'razzi'),
				'section' => 'header_department',
				'default' => '',
			),
			'header_department_custom_field_1'          => array(
				'type'    => 'custom',
				'section' => 'header_department',
				'default' => '<hr/>',
			),
			'header_department_custom_color'            => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Custom Color', 'razzi'),
				'section'     => 'header_department',
				'default'     => 0,
				'description' => esc_html__('Change color header department', 'razzi'),
			),
			'header_department_background_color'        => array(
				'type'            => 'color',
				'label'           => esc_html__('Background Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_department',
				'active_callback' => array(
					array(
						'setting'  => 'header_department_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-department',
						'property' => '--rz-header-background-color-dark',
					),
				),
			),
			'header_department_text_color'              => array(
				'type'            => 'color',
				'label'           => esc_html__('Text Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_department',
				'active_callback' => array(
					array(
						'setting'  => 'header_department_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-department',
						'property' => '--rz-header-color-light',
					),
				),
			),
			'header_department_border_color'            => array(
				'type'            => 'color',
				'label'           => esc_html__('Border Color', 'razzi'),
				'default'         => '',
				'transport'       => 'postMessage',
				'section'         => 'header_department',
				'active_callback' => array(
					array(
						'setting'  => 'header_department_custom_color',
						'operator' => '==',
						'value'    => '1',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.header-department',
						'property' => 'border-color',
					),
				),
			),

			// Blog
			'blog_type'                                 => array(
				'type'        => 'radio',
				'label'       => esc_html__('Blog Type', 'razzi'),
				'description' => esc_html__('The layout of blog posts', 'razzi'),
				'default'     => 'listing',
				'choices'     => array(
					'listing' => esc_attr__('Listing', 'razzi'),
					'grid'    => esc_attr__('Grid', 'razzi'),
				),
				'section' => 'blog_page',
			),

			'blog_columns' => array(
				'type'     => 'select',
				'label'    => esc_html__('Columns', 'razzi'),
				'section'  => 'blog_page',
				'default'  => '3',
				'priority' => 10,
				'choices'  => array(
					'2' => esc_html__('2 Columns', 'razzi'),
					'3' => esc_html__('3 Columns', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'blog_type',
						'operator' => '==',
						'value'    => 'grid',
					),
				),
			),

			'blog_layout' => array(
				'type'        => 'select',
				'label'       => esc_html__('Blog Layout', 'razzi'),
				'section'     => 'blog_page',
				'default'     => 'content-sidebar',
				'tooltip'     => esc_html__('Go to appearance > widgets find to blog sidebar to edit your sidebar', 'razzi'),
				'priority'    => 10,
				'description' => esc_html__('Select default sidebar for the single post page.', 'razzi'),
				'choices'     => array(
					'content-sidebar' => esc_html__('Right Sidebar', 'razzi'),
					'sidebar-content' => esc_html__('Left Sidebar', 'razzi'),
					'full-content'    => esc_html__('Full Content', 'razzi'),
				),
			),

			'excerpt_length' => array(
				'type'     => 'number',
				'label'    => esc_html__('Excerpt Length', 'razzi'),
				'section'  => 'blog_page',
				'default'  => 20,
				'priority' => 10,
			),

			'blog_view_more_text'                   => array(
				'type'     => 'text',
				'label'    => esc_html__('Loading Text', 'razzi'),
				'section'  => 'blog_page',
				'default'  => esc_html__('Load More', 'razzi'),
				'priority' => 10,
			),

			// Categories Filter
			'blog_categories_filter_custom_field_2' => array(
				'type'    => 'custom',
				'section' => 'blog_page',
				'default' => '<hr/><h2>' . esc_html__('Categories Filter', 'razzi') . '</h2>',
			),
			'show_blog_cats'                        => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable', 'razzi'),
				'section'     => 'blog_page',
				'default'     => 0,
				'description' => esc_html__('Display categories list above posts list', 'razzi'),
				'priority'    => 10,
			),

			'custom_blog_cats' => array(
				'type'     => 'toggle',
				'label'    => esc_html__('Custom Categories List', 'razzi'),
				'section'  => 'blog_page',
				'default'  => 0,
				'priority' => 10,
			),
			'blog_cats_slug'   => array(
				'type'            => 'select',
				'section'         => 'blog_page',
				'label'           => esc_html__('Custom Categories', 'razzi'),
				'description'     => esc_html__('Select product categories you want to show.', 'razzi'),
				'default'         => '',
				'multiple'        => 999,
				'priority'        => 10,
				'choices'         => $this->get_categories('category'),
				'active_callback' => array(
					array(
						'setting'  => 'custom_blog_cats',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'blog_cats_view_all'     => array(
				'type'     => 'text',
				'label'    => esc_html__('View All Text', 'razzi'),
				'section'  => 'blog_page',
				'default'  => esc_html__('All', 'razzi'),
				'priority' => 10,
			),
			'blog_cats_orderby'      => array(
				'type'     => 'select',
				'label'    => esc_html__('Categories Orderby', 'razzi'),
				'section'  => 'blog_page',
				'default'  => 'count',
				'priority' => 10,
				'choices'  => array(
					'count' => esc_html__('Count', 'razzi'),
					'title' => esc_html__('Title', 'razzi'),
					'id'    => esc_html__('ID', 'razzi'),
				),
			),
			'blog_cats_order'        => array(
				'type'     => 'select',
				'label'    => esc_html__('Categories Order', 'razzi'),
				'section'  => 'blog_page',
				'default'  => 'DESC',
				'priority' => 10,
				'choices'  => array(
					'DESC' => esc_html__('DESC', 'razzi'),
					'ASC'  => esc_html__('ASC', 'razzi'),
				),
			),
			'blog_cats_number'       => array(
				'type'     => 'number',
				'label'    => esc_html__('Categories Number', 'razzi'),
				'section'  => 'blog_page',
				'default'  => 6,
				'priority' => 10,
			),

			// Single Post
			'single_post_breadcrumb' => array(
				'type'     => 'toggle',
				'default'  => 1,
				'label'    => esc_html__('Enable breadcrumb', 'razzi'),
				'section'  => 'single_post',
				'priority' => 10,
			),

			'single_post_featured' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable featured image', 'razzi'),
				'default'     => '1',
				'section'     => 'single_post',
				'description' => esc_html__('Display the featured image on the post', 'razzi'),
			),

			'single_post_layout' => array(
				'type'        => 'select',
				'label'       => esc_html__('Layout', 'razzi'),
				'section'     => 'single_post',
				'default'     => 'full-content',
				'tooltip'     => esc_html__('Go to appearance > widgets find to blog sidebar to edit your sidebar', 'razzi'),
				'priority'    => 10,
				'description' => esc_html__('Select default sidebar for the single post page.', 'razzi'),
				'choices'     => array(
					'content-sidebar' => esc_html__('Right Sidebar', 'razzi'),
					'sidebar-content' => esc_html__('Left Sidebar', 'razzi'),
					'full-content'    => esc_html__('Full Content', 'razzi'),
				),
			),

			'post_custom_field_2' => array(
				'type'    => 'custom',
				'section' => 'single_post',
				'default' => '<hr/>',
			),

			'post_socials_toggle' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Socials Share', 'razzi'),
				'description' => esc_html__('Check this option to show socials share in the single post.', 'razzi'),
				'section'     => 'single_post',
				'default'     => 0,
				'priority'    => 10,
			),

			'post_socials_share'           => array(
				'type'    => 'multicheck',
				'label'   => esc_html__('Socials List', 'razzi'),
				'section' => 'single_post',
				'default' => array('facebook', 'twitter', 'googleplus', 'tumblr'),
				'choices' => array(
					'facebook'   => esc_html__('Facebook', 'razzi'),
					'twitter'    => esc_html__('Twitter', 'razzi'),
					'googleplus' => esc_html__('Google Plus', 'razzi'),
					'tumblr'     => esc_html__('Tumblr', 'razzi'),
					'pinterest'  => esc_html__('Pinterest', 'razzi'),
					'linkedin'   => esc_html__('Linkedin', 'razzi'),
					'reddit'     => esc_html__('Reddit', 'razzi'),
					'telegram'   => esc_html__('Telegram', 'razzi'),
					'pocket'     => esc_html__('Pocket', 'razzi'),
					'email'      => esc_html__('Email', 'razzi'),
				),
				'priority'        => 10,
				'active_callback' => array(
					array(
						'setting'  => 'post_socials_toggle',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			// Related Posts
			'related_posts_custom_field_1' => array(
				'type'    => 'custom',
				'section' => 'single_post',
				'default' => '<hr/><h2>' . esc_html__('Related Posts', 'razzi') . '</h2>',
			),

			'related_posts'             => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Enable', 'razzi'),
				'description' => esc_html__('Check this option to show related posts.', 'razzi'),
				'section'     => 'single_post',
				'default'     => 0,
				'priority'    => 10,
			),
			'related_posts_title'       => array(
				'type'     => 'text',
				'label'    => esc_html__('Title', 'razzi'),
				'section'  => 'single_post',
				'default'  => esc_html__('Related Posts', 'razzi'),
				'priority' => 10,

			),
			'related_posts_numbers'     => array(
				'type'        => 'number',
				'label'       => esc_html__('Numbers', 'razzi'),
				'description' => esc_html__('How many related posts would you like to show', 'razzi'),
				'section'     => 'single_post',
				'default'     => 3,
				'priority'    => 10,

			),
			'related_posts_columns'     => array(
				'type'    => 'select',
				'label'   => esc_html__('Columns', 'razzi'),
				'section' => 'single_post',
				'default' => '3',
				'choices' => array(
					'4' => esc_html__('4 Columns', 'razzi'),
					'3' => esc_html__('3 Columns', 'razzi'),
					'2' => esc_html__('2 Columns', 'razzi'),
				),
				'priority' => 10,

			),

			// Footer Layout
			'footer_sections'           => array(
				'type'        => 'sortable',
				'label'       => esc_html__('Footer Sections', 'razzi'),
				'description' => esc_html__('Select and order footer contents', 'razzi'),
				'default'     => '',
				'choices'     => array(
					'newsletter' => esc_attr__('Newsletter', 'razzi'),
					'extra'      => esc_attr__('Extra Content', 'razzi'),
					'widgets'    => esc_attr__('Footer Widgets', 'razzi'),
					'main'       => esc_attr__('Footer Main', 'razzi'),
				),
				'section' => 'footer_layout',
			),
			'footer_layout_custom_hr_1' => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'section' => 'footer_layout',
			),
			'footer_container'          => array(
				'type'        => 'select',
				'label'       => esc_html__('Footer Width', 'razzi'),
				'description' => esc_html__('Select the width of footer container', 'razzi'),
				'default'     => 'container',
				'choices'     => array(
					'container'       => esc_attr__('Normal', 'razzi'),
					'razzi-container' => esc_attr__('Large', 'razzi'),
				),
				'section' => 'footer_layout',
			),
			'footer_layout_custom_hr_2' => array(
				'type'    => 'custom',
				'default' => '<hr/>',
				'section' => 'footer_layout',
			),
			'footer_section_border_top'             => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Border Top', 'razzi'),
				'description' => esc_html__('Display a divide line on top', 'razzi'),
				'section'     => 'footer_layout',
				'default'     => '',
			),
			'footer_section_border_color' => array(
				'label'     => esc_html__('Border Color', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '.site-footer.has-divider',
						'property' => 'border-color',
					),
				),
				'section' => 'footer_layout',
				'active_callback' => array(
					array(
						'setting'  => 'footer_section_border',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			// Footer Widget
			'footer_widgets_layout'     => array(
				'type'        => 'select',
				'tooltip'     => esc_html__('Go to appearance > widgets find to footer sidebar to edit your sidebar', 'razzi'),
				'label'       => esc_html__('Layout', 'razzi'),
				'description' => esc_html__('Select number of columns for displaying widgets', 'razzi'),
				'default'     => '4-columns',
				'choices'     => array(
					'2-columns'      => esc_html__('2 Columns', 'razzi'),
					'3-columns'      => esc_html__('3 Columns', 'razzi'),
					'4-columns'      => esc_html__('4 Columns', 'razzi'),
					'5-columns'      => esc_html__('5 Columns', 'razzi'),
				),
				'section' => 'footer_widget',
			),

			'footer_widget_custom_field_1' => array(
				'type'    => 'custom',
				'section' => 'footer_widget',
				'default' => '<hr/>',
			),

			'footer_widget_border'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Border Line', 'razzi'),
				'description' => esc_html__('Display a divide line on top', 'razzi'),
				'default'     => true,
				'section'     => 'footer_widget',
			),
			'footer_widget_border_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Border Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '.footer-widgets.has-divider',
						'property' => '--rz-footer-widget-border-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'footer_widget_border',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'footer_widget',
			),

			'footer_widget_custom_field_2' => array(
				'type'    => 'custom',
				'section' => 'footer_widget',
				'default' => '<hr/><h2>' . esc_html__('Custom Padding', 'razzi') . '</h2>',
			),

			'footer_widget_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Top', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_widget',
				'default'   => '64',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.footer-widgets',
						'property' => '--rz-footer-widget-top-spacing',
						'units'    => 'px',
					),
				),
			),

			'footer_widget_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Bottom', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_widget',
				'default'   => '64',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.footer-widgets',
						'property' => '--rz-footer-widget-bottom-spacing',
						'units'    => 'px',
					),
				),
			),

			// Footer Main
			'footer_main_left'             => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Left Items', 'razzi'),
				'description' => esc_html__('Control left items of the footer', 'razzi'),
				'transport'   => 'postMessage',
				'default'     => array(array('item' => 'copyright')),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->footer_items_option(),
					),
				),
				'section' => 'footer_main',
			),
			'footer_main_center'           => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Center Items', 'razzi'),
				'description' => esc_html__('Control center items of the footer', 'razzi'),
				'transport'   => 'postMessage',
				'default'     => array(),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->footer_items_option(),
					),
				),
				'section' => 'footer_main',
			),
			'footer_main_right'            => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Right Items', 'razzi'),
				'description' => esc_html__('Control right items of the footer', 'razzi'),
				'transport'   => 'postMessage',
				'default'     => array(array('item' => 'menu')),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'default' => 'copyright',
						'choices' => $this->footer_items_option(),
					),
				),
				'section' => 'footer_main',
			),
			'footer_main_divide_1'         => array(
				'type'    => 'custom',
				'default' => '<hr>',
				'section' => 'footer_main',
			),
			'footer_main_border'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Border Line', 'razzi'),
				'description' => esc_html__('Display a divide line on top', 'razzi'),
				'default'     => true,
				'section'     => 'footer_main',
			),
			'footer_main_border_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Border Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '.footer-main.has-divider',
						'property' => '--rz-footer-main-border-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'footer_main_border',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'footer_main',
			),

			'footer_main_custom_field_1' => array(
				'type'    => 'custom',
				'section' => 'footer_main',
				'default' => '<hr/><h2>' . esc_html__('Custom Padding', 'razzi') . '</h2>',
			),

			'footer_main_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Top', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_main',
				'default'   => '22',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.footer-main',
						'property' => '--rz-footer-main-top-spacing',
						'units'    => 'px',
					),
				),
			),

			'footer_main_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Bottom', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_main',
				'default'   => '22',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.footer-main',
						'property' => '--rz-footer-main-bottom-spacing',
						'units'    => 'px',
					),
				),
			),

			// Footer Item
			'footer_copyright'           => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Footer Copyright', 'razzi'),
				'description' => esc_html__('Display copyright info on the left side of footer', 'razzi'),
				'default'     => sprintf('%s %s ' . esc_html__('All rights reserved', 'razzi'), '&copy;' . date('Y'), get_bloginfo('name')),
				'section'     => 'footer_copyright',
			),

			'footer_menu'       => array(
				'type'            => 'select',
				'label'           => esc_html__( 'Menu', 'razzi' ),
				'section'         => 'footer_menu',
				'default'         => '',
				'choices'         => $this->get_navigation_bar_get_menus(),

			),

			'footer_main_text' => array(
				'type'        => 'textarea',
				'label'       => esc_html__('Custom Text', 'razzi'),
				'description' => esc_html__('The content of the Custom Text item', 'razzi'),
				'section'     => 'footer_text',
			),

			'footer_main_payment_images' => array(
				'type'      => 'repeater',
				'label'     => esc_html__('Payment Images', 'razzi'),
				'section'   => 'footer_payment',
				'row_label' => array(
					'type'  => 'text',
					'value' => esc_html__('Image', 'razzi'),
				),
				'fields'    => array(
					'image' => array(
						'type'    => 'image',
						'label'   => esc_html__('Image', 'razzi'),
						'default' => '',
					),
					'link'  => array(
						'type'    => 'text',
						'label'   => esc_html__('Link', 'razzi'),
						'default' => '',
					),
				),
			),
			'footer_logo_type'       => array(
				'type'    => 'radio',
				'label'   => esc_html__('Logo Type', 'razzi'),
				'default' => 'text',
				'section' => 'footer_logo',
				'choices' => array(
					'image' => esc_html__('Image', 'razzi'),
					'svg'   => esc_html__('SVG', 'razzi'),
					'text'  => esc_html__('Text', 'razzi'),
				),
			),

			'footer_logo_svg'        => array(
				'type'              => 'textarea',
				'label'             => esc_html__('Logo SVG', 'razzi'),
				'section'           => 'footer_logo',
				'description'       => esc_html__('Paste SVG code of your logo here', 'razzi'),
				'sanitize_callback' => '\Razzi\Icon::sanitize_svg',
				'output'            => array(
					array(
						'element' => '.footer-branding .logo',
					),
				),
				'active_callback'   => array(
					array(
						'setting'  => 'footer_logo_type',
						'operator' => '==',
						'value'    => 'svg',
					),
				),
			),
			'footer_logo'            => array(
				'type'            => 'image',
				'label'           => esc_html__('Logo', 'razzi'),
				'default'         => '',
				'section'         => 'footer_logo',
				'active_callback' => array(
					array(
						'setting'  => 'footer_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),
			'footer_logo_text'       => array(
				'type'    => 'textarea',
				'label'   => esc_html__('Logo Text', 'razzi'),
				'section' => 'footer_logo',
				'output'  => array(
					array(
						'element' => '.footer-branding .logo',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'footer_logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
			),
			'footer_logo_text_color' => array(
				'type'            => 'color',
				'label'           => esc_html__('Color', 'razzi'),
				'transport'       => 'postMessage',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'footer_logo_type',
						'operator' => '==',
						'value'    => 'text',
					),
				),
				'js_vars'         => array(
					array(
						'element'  => '.footer-branding',
						'property' => '--rz-color-dark',
					),
				),
				'section' => 'footer_logo',
			),
			'footer_logo_dimension'  => array(
				'type'    => 'dimensions',
				'label'   => esc_html__('Logo Dimension', 'razzi'),
				'default' => array(
					'width'  => '',
					'height' => '',
				),
				'section'         => 'footer_logo',
				'active_callback' => array(
					array(
						'setting'  => 'footer_logo_type',
						'operator' => '==',
						'value'    => 'image',
					),
				),
			),

			'general_backtotop'    => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Back To Top', 'razzi'),
				'section'     => 'general_backtotop',
				'description' => esc_html__('Check this to show back to top.', 'razzi'),
				'default'     => true,
			),

			// Footer Extra
			'footer_extra_content' => array(
				'type'        => 'repeater',
				'label'       => esc_html__('Items', 'razzi'),
				'description' => esc_html__('Control items of the extra footer', 'razzi'),
				'transport'   => 'postMessage',
				'default'     => array(array('item' => 'copyright')),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__('Item', 'razzi'),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->footer_items_option(),
					),
				),
				'section' => 'footer_extra',
			),

			'footer_extra_custom_field_1' => array(
				'type'    => 'custom',
				'section' => 'footer_extra',
				'default' => '<hr/>',
			),

			'footer_extra_border'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Border Line', 'razzi'),
				'description' => esc_html__('Display a divide line on top', 'razzi'),
				'default'     => false,
				'section'     => 'footer_extra',
			),
			'footer_extra_border_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Border Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '.footer-extra.has-divider',
						'property' => '--rz-footer-extra-border-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'footer_extra_border',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'footer_extra',
			),

			'footer_extra_custom_field_2' => array(
				'type'    => 'custom',
				'section' => 'footer_extra',
				'default' => '<hr/><h2>' . esc_html__('Custom Padding', 'razzi') . '</h2>',
			),

			'footer_extra_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Top', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_extra',
				'default'   => '105',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.footer-extra',
						'property' => '--rz-footer-extra-top-spacing',
						'units'    => 'px',
					),
				),
			),

			'footer_extra_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Bottom', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_extra',
				'default'   => '112',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'   => array(
					array(
						'element'  => '.footer-extra',
						'property' => '--rz-footer-extra-bottom-spacing',
						'units'    => 'px',
					),
				),
			),
			// Newsletter
			'footer_newsletter_layout'       => array(
				'type'    => 'radio',
				'label'   => esc_html__('Layout', 'razzi'),
				'default' => 'v1',
				'section' => 'footer_newsletter',
				'choices' => array(
					'v1' => esc_html__('Layout 1', 'razzi'),
					'v2' => esc_html__('Layout 2', 'razzi'),
				),
			),

			'footer_newsletter_text' => array(
				'type'            => 'text',
				'label'           => esc_html__('Title', 'razzi'),
				'section'         => 'footer_newsletter',
				'default'         => '',
			),

			'footer_newsletter_form' => array(
				'type'            => 'textarea',
				'label'           => esc_html__('Form', 'razzi'),
				'description'     => esc_html__('Enter the shortcode of MailChimp form', 'razzi'),
				'section'         => 'footer_newsletter',
				'default'         => '',
			),

			'custom_newsletter_link_to_form' => array(
				'type'            => 'custom',
				'section'         => 'footer_newsletter',
				'default'         => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=mailchimp-for-wp-forms'), esc_html__('Go to MailChimp form', 'razzi')),
			),

			'footer_newsletter_custom_field_1' => array(
				'type'    => 'custom',
				'section' => 'footer_newsletter',
				'default' => '<hr/>',
			),

			'footer_newsletter_border'           => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Border Line', 'razzi'),
				'description' => esc_html__('Display a divide line on top', 'razzi'),
				'default'     => false,
				'section'     => 'footer_newsletter',
			),
			'footer_newsletter_border_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Border Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '.footer-newsletter.has-divider',
						'property' => '--rz-footer-newsletter-border-color',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'footer_newsletter_border',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section' => 'footer_newsletter',
			),

			'footer_newsletter_custom_field_2' => array(
				'type'            => 'custom',
				'section'         => 'footer_newsletter',
				'default'         => '<hr/><h2>' . esc_html__('Custom Padding', 'razzi') . '</h2>',
			),

			'footer_newsletter_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Top', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_newsletter',
				'default'   => '110',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '.footer-newsletter',
						'property' => 'padding-top',
						'units'    => 'px',
					),
				),
			),

			'footer_newsletter_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Bottom', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'footer_newsletter',
				'default'   => '41',
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '.footer-newsletter',
						'property' => 'padding-bottom',
						'units'    => 'px',
					),
				),
			),
			// Background
			'footer_background_scheme'       => array(
				'type'    => 'select',
				'label'   => esc_html__('Background Scheme', 'razzi'),
				'default' => 'dark',
				'section' => 'footer_background',
				'choices' => array(
					'dark'   => esc_html__('Dark', 'razzi'),
					'light'  => esc_html__('Light', 'razzi'),
					'gray'   => esc_html__('Gray', 'razzi'),
					'custom' => esc_html__('Custom', 'razzi'),
				),
			),
			'footer_bg'                        => array(
				'type'    => 'image',
				'label'   => esc_html__('Background Image', 'razzi'),
				'default' => '',
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_bg_color' => array(
				'label'     => esc_html__('Background Color', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer',
						'property' => 'background-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_bg_heading_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Heading Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .newsletter-title, #site-footer .widget-title, #site-footer .logo .logo-text',
						'property' => '--rz-color-lighter',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_bg_text_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer',
						'property' => '--rz-text-color-gray',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_bg_text_color_hover' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Hover Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer',
						'property' => '--rz-text-color-hover',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_bg_custom_field_2' => array(
				'type'    => 'custom',
				'section' => 'footer_background',
				'default' => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_extra_bg_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('FOOTER EXTRA', 'razzi'),
				'section'     => 'footer_background',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_extra_bg' => array(
				'type'    => 'image',
				'label'   => esc_html__('Background Image', 'razzi'),
				'default' => '',
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_extra_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_extra_bg_color' => array(
				'label'     => esc_html__('Background Color', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-extra',
						'property' => 'background-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_extra_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_extra_text_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-extra',
						'property' => '--rz-text-color-gray',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_extra_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_extra_text_color_hover' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Hover Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-extra',
						'property' => '--rz-text-color-hover',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_extra_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_custom_field_10' => array(
				'type'    => 'custom',
				'section' => 'footer_background',
				'label'   => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_newsletter_bg_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('FOOTER NEWSLETTER', 'razzi'),
				'section'     => 'footer_background',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_newsletter_bg' => array(
				'type'    => 'image',
				'label'   => esc_html__('Background Image', 'razzi'),
				'default' => '',
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_bg_color' => array(
				'label'     => esc_html__('Background Color', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter',
						'property' => 'background-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_heading_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Heading Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .newsletter-title',
						'property' => '--rz-color-lighter',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_form_border_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Border Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter.layout-v2',
						'property' => '--rz-textbox-bg-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_newsletter_layout',
						'operator' => '==',
						'value'    => 'v2',
					),
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_text_field_bgcolor' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Field Background Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter.layout-v1',
						'property' => '--rz-textbox-bg-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_newsletter_layout',
						'operator' => '==',
						'value'    => 'v1',
					),
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_text_field_placeholder_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Field Placehoder Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter .mc4wp-form-fields',
						'property' => '--rz-textbox-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_text_field_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Field Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter',
						'property' => '--rz-textbox-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_submit_bg_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Button Background Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter.layout-v1',
						'property' => '--rz-button-bg-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_newsletter_layout',
						'operator' => '==',
						'value'    => 'v1',
					),
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_newsletter_submit_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Button Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-newsletter',
						'property' => '--rz-button-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_newsletter_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),
			'footer_widgets_custom_field_10' => array(
				'type'    => 'custom',
				'section' => 'footer_background',
				'label'   => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_widgets_bg_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('FOOTER WIDGETS', 'razzi'),
				'section'     => 'footer_background',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'footer_widget_bg' => array(
				'type'    => 'image',
				'label'   => esc_html__('Background Image', 'razzi'),
				'default' => '',
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_widgets_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_widget_bg_color' => array(
				'label'     => esc_html__('Background Color', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-widgets',
						'property' => 'background-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_widgets_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_widget_dropdown_border_color' => array(
				'label'     => esc_html__('Border Color Dropdown', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-widgets .widget.dropdown',
						'property' => 'border-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_widgets_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_widget_heading_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Heading Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-widgets .widget-title, #site-footer .footer-widgets .logo-text',
						'property' => '--rz-color-lighter',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_widgets_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_widget_text_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-widgets',
						'property' => '--rz-text-color-gray',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_widgets_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_widget_text_color_hover' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Hover Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-widgets',
						'property' => '--rz-text-color-hover',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_widgets_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_main_custom_field_10' => array(
				'type'    => 'custom',
				'section' => 'footer_background',
				'label'   => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),

			'footer_main_bg_enable' => array(
				'type'        => 'toggle',
				'label'       => esc_html__('FOOTER MAIN', 'razzi'),
				'section'     => 'footer_background',
				'default'     => false,
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'footer_main_bg' => array(
				'type'    => 'image',
				'label'   => esc_html__('Background Image', 'razzi'),
				'default' => '',
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_main_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_main_bg_color' => array(
				'label'     => esc_html__('Background Color', 'razzi'),
				'type'      => 'color',
				'default'   => '',
				'transport' => 'postMessage',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-main',
						'property' => 'background-color',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_main_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_main_text_color' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-main',
						'property' => '--rz-text-color-gray',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_main_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			'footer_main_text_color_hover' => array(
				'type'      => 'color',
				'label'     => esc_html__('Text Hover Color', 'razzi'),
				'transport' => 'postMessage',
				'default'   => '',
				'js_vars'   => array(
					array(
						'element'  => '#site-footer .footer-main',
						'property' => '--rz-text-color-hover',
					),
				),
				'section' => 'footer_background',
				'active_callback' => array(
					array(
						'setting'  => 'footer_background_scheme',
						'operator' => '==',
						'value'    => 'custom',
					),
					array(
						'setting'  => 'footer_main_bg_enable',
						'operator' => '==',
						'value'    => true,
					),
				),
			),


			'page_header'             => array(
				'type'        => 'toggle',
				'default'     => 1,
				'label'       => esc_html__('Enable Page Header', 'razzi'),
				'section'     => 'page_header',
				'description' => esc_html__('Enable to show a page header for the page below the site header', 'razzi'),
				'priority'    => 10,
			),

			'page_header_custom_field_1' => array(
				'type'            => 'custom',
				'section'         => 'page_header',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),

			'page_header_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Page Header Elements', 'razzi'),
				'section'  => 'page_header',
				'default'  => array('breadcrumb', 'title'),
				'priority' => 10,
				'choices'  => array(
					'breadcrumb' => esc_html__('BreadCrumb', 'razzi'),
					'title'      => esc_html__('Title', 'razzi'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'razzi'),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'page_header_custom_field_2' => array(
				'type'            => 'custom',
				'section'         => 'page_header',
				'default'         => '<hr/><h3>' . esc_html__('Custom Title', 'razzi') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
			),

			'page_header_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'page_header',
				'default'   => 50,
				'priority'  => 20,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '#page-header .page-header__title.custom-spacing',
						'property' => 'padding-top',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
			),

			'page_header_padding_bottom'  => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'page_header',
				'default'   => 50,
				'priority'  => 20,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '#page-header .page-header__title.custom-spacing',
						'property' => 'padding-bottom',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'page_header_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
			),

			// Boxed Layout
			'boxed_layout'                => array(
				'type'     => 'toggle',
				'label'    => esc_html__('Boxed Layout', 'razzi'),
				'section'  => 'boxed_layout',
				'default'  => 0,
				'priority' => 10,
			),
			'boxed_background_color'      => array(
				'type'            => 'color',
				'label'           => esc_html__('Background Color', 'razzi'),
				'default'         => '',
				'section'         => 'boxed_layout',
				'priority'        => 10,
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),
			'boxed_background_image'      => array(
				'type'            => 'image',
				'label'           => esc_html__('Background Image', 'razzi'),
				'default'         => '',
				'section'         => 'boxed_layout',
				'priority'        => 10,
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),
			'boxed_background_horizontal' => array(
				'type'     => 'select',
				'label'    => esc_html__('Background Horizontal', 'razzi'),
				'section'  => 'boxed_layout',
				'default'  => '',
				'priority' => 10,
				'choices'  => array(
					''       => esc_html__('None', 'razzi'),
					'left'   => esc_html__('Left', 'razzi'),
					'center' => esc_html__('Center', 'razzi'),
					'right'  => esc_html__('Right', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),
			'boxed_background_vertical'   => array(
				'type'     => 'select',
				'label'    => esc_html__('Background Vertical', 'razzi'),
				'section'  => 'boxed_layout',
				'default'  => '',
				'priority' => 10,
				'choices'  => array(
					''       => esc_html__('None', 'razzi'),
					'top'    => esc_html__('Top', 'razzi'),
					'center' => esc_html__('Center', 'razzi'),
					'bottom' => esc_html__('Bottom', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),
			'boxed_background_repeat'     => array(
				'type'     => 'select',
				'label'    => esc_html__('Background Repeat', 'razzi'),
				'section'  => 'boxed_layout',
				'default'  => '',
				'priority' => 10,
				'choices'  => array(
					''          => esc_html__('None', 'razzi'),
					'no-repeat' => esc_html__('No Repeat', 'razzi'),
					'repeat'    => esc_html__('Repeat', 'razzi'),
					'repeat-y'  => esc_html__('Repeat Vertical', 'razzi'),
					'repeat-x'  => esc_html__('Repeat Horizontal', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),
			'boxed_background_attachment' => array(
				'type'     => 'select',
				'label'    => esc_html__('Background Attachment', 'razzi'),
				'section'  => 'boxed_layout',
				'default'  => '',
				'priority' => 10,
				'choices'  => array(
					''       => esc_html__('None', 'razzi'),
					'scroll' => esc_html__('Scroll', 'razzi'),
					'fixed'  => esc_html__('Fixed', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),
			'boxed_background_size'       => array(
				'type'     => 'select',
				'label'    => esc_html__('Background Size', 'razzi'),
				'section'  => 'boxed_layout',
				'default'  => '',
				'priority' => 10,
				'choices'  => array(
					''        => esc_html__('None', 'razzi'),
					'auto'    => esc_html__('Auto', 'razzi'),
					'cover'   => esc_html__('Cover', 'razzi'),
					'contain' => esc_html__('Contain', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'boxed_layout',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			// Blog
			'page_header_blog'            => array(
				'type'        => 'toggle',
				'default'     => 1,
				'label'       => esc_html__('Enable Page Header', 'razzi'),
				'section'     => 'page_header_blog',
				'description' => esc_html__('Enable to show a page header for the page below the site header', 'razzi'),
				'priority'    => 10,
			),

			'page_header_blog_custom_field_1' => array(
				'type'            => 'custom',
				'section'         => 'page_header_blog',
				'default'         => '<hr/>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header_blog',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),

			'page_header_blog_els' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Page Header Elements', 'razzi'),
				'section'  => 'page_header_blog',
				'default'  => array('breadcrumb', 'title'),
				'priority' => 10,
				'choices'  => array(
					'breadcrumb' => esc_html__('BreadCrumb', 'razzi'),
					'title'      => esc_html__('Title', 'razzi'),
				),
				'description'     => esc_html__('Select which elements you want to show.', 'razzi'),
				'active_callback' => array(
					array(
						'setting'  => 'page_header_blog',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'page_header_blog_custom_field_2' => array(
				'type'            => 'custom',
				'section'         => 'page_header_blog',
				'default'         => '<hr/><h3>' . esc_html__('Custom Title', 'razzi') . '</h3>',
				'active_callback' => array(
					array(
						'setting'  => 'page_header_blog',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'page_header_blog_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
			),

			'page_header_blog_padding_top' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Top', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'page_header_blog',
				'default'   => 50,
				'priority'  => 20,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '.razzi-blog-page #page-header .page-header__title',
						'property' => 'padding-top',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header_blog',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'page_header_blog_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
			),

			'page_header_blog_padding_bottom' => array(
				'type'      => 'slider',
				'label'     => esc_html__('Padding Bottom', 'razzi'),
				'transport' => 'postMessage',
				'section'   => 'page_header_blog',
				'default'   => 50,
				'priority'  => 20,
				'choices'   => array(
					'min' => 0,
					'max' => 500,
				),
				'js_vars'         => array(
					array(
						'element'  => '.razzi-blog-page #page-header .page-header__title',
						'property' => 'padding-bottom',
						'units'    => 'px',
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'page_header_blog',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'page_header_blog_els',
						'operator' => 'in',
						'value'    => 'title',
					),
				),
			),

			// Recently viewed
			'recently_viewed_enable'          => array(
				'type'        => 'toggle',
				'label'       => esc_html__('Recently Viewed', 'razzi'),
				'section'     => 'recently_viewed',
				'default'     => 0,
				'description' => esc_html__('Check this option to show the recently viewed products above the footer.', 'razzi'),
			),

			'recently_viewed_ajax' => array(
				'type'    => 'toggle',
				'label'   => esc_html__('Load With Ajax', 'razzi'),
				'section' => 'recently_viewed',
				'default' => 0,
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),

			'recently_viewed_empty' => array(
				'type'            => 'toggle',
				'label'           => esc_html__('Hide Empty Products', 'razzi'),
				'section'         => 'recently_viewed',
				'default'         => 1,
				'description'     => esc_html__('Check this option to hide the recently viewed products when empty.', 'razzi'),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),

			'recently_viewed_display_page' => array(
				'type'     => 'multicheck',
				'label'    => esc_html__('Display On Page', 'razzi'),
				'section'  => 'recently_viewed',
				'default'  => array('single'),
				'priority' => 10,
				'choices'  => array(
					'single'   => esc_html__('Single Product', 'razzi'),
					'catalog'  => esc_html__('Catalog Page', 'razzi'),
					'cart'     => esc_html__('Cart Page', 'razzi'),
					'checkout' => esc_html__('Checkout Page', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'recently_viewed_custom' => array(
				'type'    => 'custom',
				'section' => 'recently_viewed',
				'default' => '<hr/>',
			),

			'recently_viewed_layout' => array(
				'type'    => 'select',
				'label'   => esc_html__('Layout', 'razzi'),
				'section' => 'recently_viewed',
				'default' => 'default',
				'choices' => array(
					'default' => esc_html__('Default', 'razzi'),
					'effect'  => esc_html__('Effect Hover', 'razzi'),
				),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'recently_viewed_title' => array(
				'type'            => 'text',
				'label'           => esc_html__('Title', 'razzi'),
				'section'         => 'recently_viewed',
				'default'         => esc_html__('Recently Viewed', 'razzi'),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'recently_viewed_button_text' => array(
				'type'            => 'text',
				'label'           => esc_html__('Button Text', 'razzi'),
				'section'         => 'recently_viewed',
				'default'         => esc_html__('View All', 'razzi'),
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'recently_viewed_button_link' => array(
				'type'            => 'text',
				'label'           => esc_html__('Button Link', 'razzi'),
				'section'         => 'recently_viewed',
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
			),

			'recently_viewed_columns' => array(
				'type'            => 'number',
				'label'           => esc_html__('Columns', 'razzi'),
				'section'         => 'recently_viewed',
				'default'         => 4,
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'description' => esc_html__('Specify how many numbers of recently viewed products you want to show on page', 'razzi'),
			),

			'recently_viewed_numbers' => array(
				'type'            => 'number',
				'label'           => esc_html__('Numbers', 'razzi'),
				'section'         => 'recently_viewed',
				'default'         => 6,
				'active_callback' => array(
					array(
						'setting'  => 'recently_viewed_enable',
						'operator' => '==',
						'value'    => 1,
					),
				),
				'description' => esc_html__('Specify how many numbers of recently viewed products you want to show on page', 'razzi'),
			),
			// Topbar Mobile
			'mobile_topbar' => array(
				'type'        => 'toggle',
				'label'       => esc_html__( 'Topbar', 'razzi' ),
				'description' => esc_html__( 'Display topbar on mobile', 'razzi' ),
				'default'     => false,
				'section'     => 'mobile_topbar',
			),

			'mobile_topbar_items'               => array(
				'type'            => 'repeater',
				'label'           => esc_html__( 'List Items', 'razzi' ),
				'description'     => esc_html__( 'Control items on the topbar mobile', 'razzi' ),
				'transport'       => 'postMessage',
				'default'         => array(),
				'section'         => 'mobile_topbar',
				'row_label'       => array(
					'type'  => 'field',
					'value' => esc_attr__( 'Item', 'razzi' ),
					'field' => 'item',
				),
				'fields'          => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->topbar_items_option(),
					),
				),
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'mobile_topbar_custom_field_1'      => array(
				'type'    => 'custom',
				'section' => 'mobile_topbar',
				'default' => '<hr/>',
			),
			'mobile_topbar_svg_code'            => array(
				'type'              => 'textarea',
				'label'             => esc_html__( 'SVG code', 'razzi' ),
				'section'           => 'mobile_topbar',
				'description'       => esc_html__( 'The SVG before your text', 'razzi' ),
				'default'           => '',
				'sanitize_callback' => '\Razzi\Icon::sanitize_svg',
				'output'            => array(
					array(
						'element' => '.razzi-topbar__text .razzi-svg-icon',
					),
				),
				'active_callback'   => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'mobile_topbar_text'                => array(
				'type'            => 'editor',
				'label'           => esc_html__( 'Custom Text', 'razzi' ),
				'section'         => 'mobile_topbar',
				'description'     => esc_html__( 'The content of Custom Text item', 'razzi' ),
				'default'         => '',
				'active_callback' => array(
					array(
						'setting'  => 'mobile_topbar',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'mobile_header_height'              => array(
				'type'      => 'slider',
				'label'     => esc_html__( 'Header Height', 'razzi' ),
				'section'   => 'mobile_header',
				'transport' => 'postMessage',
				'default'   => '60',
				'choices'   => array(
					'min' => 40,
					'max' => 300,
				),
				'js_vars'   => array(
					array(
						'element'  => '.header-mobile',
						'property' => 'height',
						'units'    => 'px',
					),
				),
			),
			'mobile_header_custom_field_2'      => array(
				'type'    => 'custom',
				'section' => 'mobile_header',
				'default' => '<hr/>',
			),
			'mobile_header_icons'               => array(
				'type'        => 'repeater',
				'label'       => esc_html__( 'Header Icons', 'razzi' ),
				'section'     => 'mobile_header',
				'description' => esc_html__( 'Control icons on the right side of mobile header', 'razzi' ),
				'transport'   => 'postMessage',
				'default'     => array( array( 'item' => 'search' ), array( 'item' => 'cart' ) ),
				'row_label'   => array(
					'type'  => 'field',
					'value' => esc_attr__( 'Item', 'razzi' ),
					'field' => 'item',
				),
				'fields'      => array(
					'item' => array(
						'type'    => 'select',
						'choices' => $this->mobile_header_icons_option(),
					),
				),
			),
			'mobile_logo_custom_field_1'        => array(
				'type'    => 'custom',
				'section' => 'mobile_header',
					'default' => '<hr/>',
				),

				// Page
				'mobile_page_header_hr'             => array(
					'type'    => 'custom',
					'section' => 'mobile_page',
					'default' => '<hr/><h2>' . esc_html__( 'Page Header', 'razzi' ) . '</h2>',
				),
				'mobile_page_header'                => array(
					'type'        => 'toggle',
					'default'     => 1,
					'label'       => esc_html__( 'Enable Page Header', 'razzi' ),
					'section'     => 'mobile_page',
					'description' => esc_html__( 'Enable to show a page header for the page below the site header', 'razzi' ),
				),
				'mobile_page_header_els'            => array(
					'type'            => 'multicheck',
					'label'           => esc_html__( 'Page Header Elements', 'razzi' ),
					'section'         => 'mobile_page',
					'default'         => array( 'breadcrumb', 'title' ),
					'priority'        => 10,
					'choices'         => array(
						'breadcrumb' => esc_html__( 'BreadCrumb', 'razzi' ),
						'title'      => esc_html__( 'Title', 'razzi' ),
					),
					'description'     => esc_html__( 'Select which elements you want to show.', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_page_header',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),

				// Blog
				'mobile_blog_page_header_hr'        => array(
					'type'    => 'custom',
					'section' => 'mobile_blog',
					'default' => '<hr/><h2>' . esc_html__( 'Page Header', 'razzi' ) . '</h2>',
				),
				'mobile_blog_page_header'           => array(
					'type'        => 'toggle',
					'default'     => 1,
					'label'       => esc_html__( 'Enable Page Header', 'razzi' ),
					'section'     => 'mobile_blog',
					'description' => esc_html__( 'Enable to show a page header for the page below the site header', 'razzi' ),
				),
				'mobile_blog_page_header_els'       => array(
					'type'            => 'multicheck',
					'label'           => esc_html__( 'Page Header Elements', 'razzi' ),
					'section'         => 'mobile_blog',
					'default'         => array( 'breadcrumb', 'title' ),
					'priority'        => 10,
					'choices'         => array(
						'breadcrumb' => esc_html__( 'BreadCrumb', 'razzi' ),
						'title'      => esc_html__( 'Title', 'razzi' ),
					),
					'description'     => esc_html__( 'Select which elements you want to show.', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_page_header',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),

				// Single Post
				'mobile_single_post_breadcrumb'    => array(
					'type'    => 'toggle',
					'default' => 1,
					'label'   => esc_html__( 'Enable breadcrumb', 'razzi' ),
					'section' => 'mobile_single_blog',
				),

				// Mobile Footer
				'mobile_footer_newsletter'          => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Footer Newsletter', 'razzi' ),
					'description' => esc_html__( 'Display footer newsletter on mobile', 'razzi' ),
					'default'     => true,
					'section'     => 'mobile_footer',
				),
				'mobile_footer_newsletter_padding_top' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Top', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 30,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-newsletter',
							'property' => '--rz-footer-newsletter-top-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_newsletter',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'mobile_footer_newsletter_padding_bottom' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Bottom', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 40,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-newsletter',
							'property' => '--rz-footer-newsletter-bottom-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_newsletter',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),

				'mobile_footer_custom_field_1' => array(
					'type'            => 'custom',
					'section'         => 'mobile_footer',
					'default' 		  => '<hr/>',
				),

				'mobile_footer_widget'              => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Footer Widget', 'razzi' ),
					'description' => esc_html__( 'Display footer widget on mobile', 'razzi' ),
					'default'     => true,
					'section'     => 'mobile_footer',
				),
				'mobile_footer_widget_padding_top' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Top', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 30,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-widgets',
							'property' => '--rz-footer-widget-top-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_widget',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'mobile_footer_widget_padding_bottom' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Bottom', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 40,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-widgets',
							'property' => '--rz-footer-widget-bottom-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_widget',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),

				'mobile_footer_custom_field_2' => array(
					'type'            => 'custom',
					'section'         => 'mobile_footer',
					'default' 		  => '<hr/>',
				),

				'mobile_footer_main'                => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Footer Main', 'razzi' ),
					'description' => esc_html__( 'Display footer main on mobile', 'razzi' ),
					'default'     => true,
					'section'     => 'mobile_footer',
				),
				'mobile_footer_main_padding_top' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Top', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 30,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-main',
							'property' => '--rz-footer-main-top-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_main',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'mobile_footer_main_padding_bottom' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Bottom', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 40,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-main',
							'property' => '--rz-footer-main-bottom-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_main',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),

				'mobile_footer_custom_field_3' => array(
					'type'            => 'custom',
					'section'         => 'mobile_footer',
					'default' 		  => '<hr/>',
				),

				'mobile_footer_extra'               => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Footer Extra', 'razzi' ),
					'description' => esc_html__( 'Display footer extra on mobile', 'razzi' ),
					'default'     => true,
					'section'     => 'mobile_footer',
				),
				'mobile_footer_extra_padding_top' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Top', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 30,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-extra',
							'property' => '--rz-footer-extra-top-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_extra',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'mobile_footer_extra_padding_bottom' => array(
					'type'      => 'slider',
					'label'     => esc_html__('Padding Bottom', 'razzi'),
					'transport' => 'postMessage',
					'section'   => 'mobile_footer',
					'default'   => 40,
					'choices'   => array(
						'min' => 0,
						'max' => 500,
					),
					'js_vars'         => array(
						array(
							'element'  => '.site-footer .footer-extra',
							'property' => '--rz-footer-extra-bottom-spacing',
							'units'    => 'px',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_footer_extra',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),

				// Mobile Logo
				'mobile_menu_left'                  => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Show Menu Left', 'razzi' ),
					'section'     => 'mobile_header',
					'default'     => true,
				),
				'mobile_header_history_back'            => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Back to history', 'razzi' ),
					'section' => 'mobile_header',
					'description' => esc_html__( 'Show back icon in the inner pages', 'razzi' ),
					'default' => 0,
				),
				'mobile_logo_custom_field_20'        => array(
					'type'    => 'custom',
					'section' => 'mobile_header',
					'default' => '<hr/>',
				),
				'mobile_custom_logo'                => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Mobile Logo', 'razzi' ),
					'section'     => 'mobile_header',
					'description' => esc_html__( 'Use a different logo on mobile', 'razzi' ),
					'default'     => false,
				),
				'mobile_logo'                       => array(
					'type'            => 'image',
					'section'         => 'mobile_header',
					'active_callback' => array(
						array(
							'setting'  => 'mobile_custom_logo',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				'mobile_logo_dimension'                            => array(
					'type'    => 'dimensions',
					'label'   => esc_html__('Logo Dimension', 'razzi'),
					'default' => array(
						'width'  => '',
						'height' => '',
					),
					'section'         => 'mobile_header',
					'active_callback' => array(
						array(
							'setting'  => 'mobile_custom_logo',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				'mobile_logo_custom_field_2'        => array(
					'type'    => 'custom',
					'section' => 'mobile_header',
					'default' => '<hr/>',
				),
				// Mobile Menu
				'mobile_menu_custom_field_1'        => array(
					'type'    => 'custom',
					'section' => 'mobile_header',
					'default' => '<h2>' . esc_html__( 'Header Menu Panel', 'razzi' ) . '</h2>',
				),
				'mobile_menu_click_item'            => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Show Sub-Menus', 'razzi' ),
					'section' => 'mobile_header',
					'default' => 'click-item',
					'choices' => array(
						'click-item' => esc_html__( 'Click to item', 'razzi' ),
						'click-icon' => esc_html__( 'Click to icon', 'razzi' ),
					),
				),
				'mobile_menu_show_socials'          => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show Socials', 'razzi' ),
					'default' => '0',
					'section' => 'mobile_header',
				),
				'mobile_menu_show_copyright'        => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show Copyright', 'razzi' ),
					'default' => '0',
					'section' => 'mobile_header',
				),
				'mobile_campaign_bar'            => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Campaign Bar', 'razzi' ),
					'section' => 'mobile_header_campaign',
					'default' => false,
				),
				'mobile_newsletter_popup'        => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Newsletter_Popup', 'razzi' ),
					'section' => 'mobile_newsletter_popup',
					'default' => true,
				),
				// Panel
				'mobile_panel_custom_logo'                => array(
					'type'        => 'toggle',
					'label'       => esc_html__( 'Custom Logo', 'razzi' ),
					'section'     => 'mobile_panel',
					'description' => esc_html__( 'Use a different logo on mobile panel', 'razzi' ),
					'default'     => false,
				),
				'mobile_panel_logo'                       => array(
					'type'            => 'image',
					'section'         => 'mobile_panel',
					'active_callback' => array(
						array(
							'setting'  => 'mobile_panel_custom_logo',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				'mobile_panel_logo_dimension'                            => array(
					'type'    => 'dimensions',
					'label'   => esc_html__('Logo Dimension', 'razzi'),
					'default' => array(
						'width'  => '',
						'height' => '',
					),
					'section'         => 'mobile_panel',
					'active_callback' => array(
						array(
							'setting'  => 'mobile_panel_custom_logo',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				// Catalog
				'mobile_catalog_product_loop_hr' => array(
					'type'            => 'custom',
					'section'         => 'mobile_product_catalog',
					'default'         => '<hr/><h2>' . esc_html__( 'Product Loop', 'razzi' ) . '</h2>',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '1', '2', '3', '4', '6', '7', '8', '9' ),
						),
					),
				),
				'mobile_product_featured_icons'  => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Always Show Featured Icons', 'razzi' ),
					'default'         => '0',
					'section'         => 'mobile_product_catalog',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '1', '2', '3', '4', '6', '7', '8', '9' ),
						),
					),
				),
				'mobile_product_loop_atc'        => array(
					'type'            => 'toggle',
					'label'           => esc_html__( 'Show Add To Cart Button', 'razzi' ),
					'default'         => '0',
					'section'         => 'mobile_product_catalog',
					'active_callback' => array(
						array(
							'setting'  => 'product_loop_layout',
							'operator' => 'in',
							'value'    => array( '1', '2', '4', '7' ),
						),
					),
				),

				'shop_products_hr_4' => array(
					'type'    => 'custom',
					'default' => '<hr/><h2>' . esc_html__( 'Product Columns', 'razzi' ) . '</h2>',
					'section' => 'mobile_product_catalog',
				),

				'mobile_landscape_product_columns'     => array(
					'label'   => esc_html__( 'Mobile Landscape(767px)', 'razzi' ),
					'section' => 'mobile_product_catalog',
					'type'    => 'select',
					'default' => '3',
					'choices' => array(
						'1' => esc_attr__( '1 Column', 'razzi' ),
						'2' => esc_attr__( '2 Columns', 'razzi' ),
						'3' => esc_attr__( '3 Columns', 'razzi' ),
					),
				),
				'mobile_portrait_product_columns'      => array(
					'label'   => esc_html__( 'Mobile Portrait(479px)', 'razzi' ),
					'section' => 'mobile_product_catalog',
					'type'    => 'select',
					'default' => '2',
					'choices' => array(
						'1' => esc_attr__( '1 Column', 'razzi' ),
						'2' => esc_attr__( '2 Columns', 'razzi' ),
						'3' => esc_attr__( '3 Columns', 'razzi' ),
					),
				),
				'mobile_catalog_page_header_hr'        => array(
					'type'    => 'custom',
					'section' => 'mobile_product_catalog',
					'default' => '<hr/><h2>' . esc_html__( 'Page Header', 'razzi' ) . '</h2>',
				),
				'mobile_catalog_page_header'           => array(
					'type'        => 'toggle',
					'default'     => 1,
					'label'       => esc_html__( 'Enable Page Header', 'razzi' ),
					'section'     => 'mobile_product_catalog',
					'description' => esc_html__( 'Enable to show a page header for the shop page below the site header', 'razzi' ),
				),
				'mobile_catalog_page_header_els'       => array(
					'type'            => 'multicheck',
					'label'           => esc_html__( 'Page Header Elements', 'razzi' ),
					'section'         => 'mobile_product_catalog',
					'default'         => array( 'breadcrumb', 'title' ),
					'priority'        => 10,
					'choices'         => array(
						'breadcrumb' => esc_html__( 'BreadCrumb', 'razzi' ),
						'title'      => esc_html__( 'Title', 'razzi' ),
					),
					'description'     => esc_html__( 'Select which elements you want to show.', 'razzi' ),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_catalog_page_header',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),
				// Single Product
				'mobile_single_product_breadcrumb'    => array(
					'type'        => 'toggle',
					'default'     => 1,
					'label'       => esc_html__( 'Enable Breadcrumb', 'razzi' ),
					'section'     => 'mobile_single_product',
					'description' => esc_html__( 'Enable to show a page header for the single product page below the site header', 'razzi' ),
				),

				'mobile_version'                        => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Mobile Version', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 'yes',
				),
				'mobile_version_custom_1'               => array(
					'type'    => 'custom',
					'section' => 'mobile_version',
					'default' => '<hr/>',
				),
				'custom_mobile_homepage'                       => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Custom Homepage', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 0,
				),
				'mobile_homepage_id'                       => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Homepage', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 'homepage-mobile',
					'choices' => class_exists( 'Kirki_Helper' ) && is_admin() ? \Kirki_Helper::get_posts( array(
						'posts_per_page' => - 1,
						'post_type'      => 'page',
					) ) : '',
					'active_callback' => array(
						array(
							'setting'  => 'custom_mobile_homepage',
							'operator' => '==',
							'value'    => 1,
						),
					),
				),
				'mobile_version_custom_2'               => array(
					'type'    => 'custom',
					'section' => 'mobile_version',
					'default' => '<hr/>',
				),
				'mobile_navigation_bar'                 => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Navigation Bar', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 'none',
					'choices' => array(
						'none'              => esc_html__( 'None', 'razzi' ),
						'simple'            => esc_html__( 'Simple', 'razzi' ),
						'simple_adoptive'   => esc_html__( 'Simple Adaptive', 'razzi' ),
						'standard'          => esc_html__( 'Standard', 'razzi' ),
						'standard_adoptive' => esc_html__( 'Standard Adaptive', 'razzi' ),
					),
				),
				'mobile_navigation_bar_items'           => array(
					'type'            => 'sortable',
					'label'           => esc_html__( 'Items', 'razzi' ),
					'section'         => 'mobile_version',
					'default'         => array( 'home', 'menu', 'search', 'account' ),
					'choices'         => $this->navigation_bar_items_option(),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_navigation_bar',
							'operator' => 'in',
							'value'    => array( 'standard', 'standard_adoptive' ),
						),
					),

				),
				'mobile_navigation_bar_item'            => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Item', 'razzi' ),
					'section'         => 'mobile_version',
					'default'         => 'menu',
					'choices'         => $this->navigation_bar_items_option(),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_navigation_bar',
							'operator' => 'in',
							'value'    => array( 'simple', 'simple_adoptive' ),
						),
					),

				),
				'mobile_navigation_bar_item_align'      => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Align Item', 'razzi' ),
					'section'         => 'mobile_version',
					'default'         => 'right',
					'choices'         => array(
						'left'   => esc_html__( 'Left', 'razzi' ),
						'right'  => esc_html__( 'Right', 'razzi' ),
						'center' => esc_html__( 'Center', 'razzi' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_navigation_bar',
							'operator' => 'in',
							'value'    => array( 'simple', 'simple_adoptive' ),
						),
					),
				),
				'mobile_navigation_bar_menu_item'       => array(
					'type'            => 'select',
					'label'           => esc_html__( 'Menu', 'razzi' ),
					'section'         => 'mobile_version',
					'default'         => '',
					'choices'         => $this->get_navigation_bar_get_menus(),

				),
				'mobile_navigation_bar_menu_side_type'  => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Show Menu', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 'side-left',
					'choices' => array(
						'side-left'  => esc_html__( 'Side to right', 'razzi' ),
						'side-right' => esc_html__( 'Side to left', 'razzi' ),
					),
				),
				'mobile_navigation_bar_menu_click_item' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Show Sub-Menus', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 'click-item',
					'choices' => array(
						'click-item' => esc_html__( 'Click to item', 'razzi' ),
						'click-icon' => esc_html__( 'Click to icon', 'razzi' ),
					),
				),
				'mobile_floating_action_button' => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Floating Action Button', 'razzi' ),
					'section' => 'mobile_version',
					'default' => 0,
					'active_callback' => array(
						array(
							'setting'  => 'mobile_navigation_bar',
							'operator' => 'in',
							'value'    => array( 'standard_adoptive', 'simple_adoptive' ),
						),
					),
				),
		);

		$settings['panels']   = apply_filters('razzi_customize_panels', $panels);
		$settings['sections'] = apply_filters('razzi_customize_sections', $sections);
		$settings['fields']   = apply_filters('razzi_customize_fields', $fields);

		return $settings;
	}

	/**
	 * Get nav menus
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_navigation_bar_get_menus() {
		if ( ! is_admin() ) {
			return [];
		}

		$menus = wp_get_nav_menus();
		if ( ! $menus ) {
			return [];
		}

		$output = array(
			0 => esc_html__( 'Select Menu', 'razzi' ),
		);
		foreach ( $menus as $menu ) {
			$output[ $menu->slug ] = $menu->name;
		}

		return $output;
	}

	/**
	 * Display header sticky
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_header_sticky() {
		if( empty(get_theme_mod( 'header_sticky' )) ) {
			return false;
		}

		if ( 'default' == get_theme_mod( 'header_type' ) ) {
			if( ! in_array( get_theme_mod( 'header_layout' ), array( 'v3', 'v4', 'v9' ) ) ) {
				return false;
			}

			return true;
		} else {
			return true;
		}
	}
}
