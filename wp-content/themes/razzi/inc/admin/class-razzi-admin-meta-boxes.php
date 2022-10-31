<?php
/**
 * Meta boxes functions
 *
 * @package Razzi
 */

namespace Razzi\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Meta boxes initial
 *
 */
class Meta_Boxes {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_scripts' ) );
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function meta_box_scripts( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			wp_enqueue_script( 'razzi-meta-boxes', get_template_directory_uri() . '/assets/js/backend/meta-boxes.js', array( 'jquery' ), '20201012', true );
		}
	}

	/**
	 * Registering meta boxes
	 *
	 * @since 1.0.0
	 *
	 * Using Meta Box plugin: http://www.deluxeblogtips.com/meta-box/
	 *
	 * @see http://www.deluxeblogtips.com/meta-box/docs/define-meta-boxes
	 *
	 * @param array $meta_boxes Default meta boxes. By default, there are no meta boxes.
	 *
	 * @return array All registered meta boxes
	 */
	public function register_meta_boxes( $meta_boxes ) {
		// Header
		$meta_boxes[] = $this->register_header_settings();

		// Page Header
		$meta_boxes[] = $this->register_page_header_settings();

		// Content
		$meta_boxes[] = $this->register_content_settings();

		// Page Boxed
		$meta_boxes[] = $this->register_page_boxed_settings();

		// Footer
		$meta_boxes[] = $this->register_footer_settings();

		return $meta_boxes;
	}

	/**
	 * Register header settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_header_settings() {

		return array(
			'id'       => 'header-settings',
			'title'    => esc_html__( 'Header Settings', 'razzi' ),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name' => esc_html__( 'Hide Header Section', 'razzi' ),
					'id'   => 'rz_hide_header_section',
					'type' => 'select',
					'type' => 'checkbox',
					'std'  => false,
				),
				array(
					'name'    => esc_html__( 'Header Layout', 'razzi' ),
					'id'      => 'rz_header_layout',
					'type'    => 'select',
					'options' => array(
						'default' => esc_html__( 'Default', 'razzi' ),
						'v1'      => esc_html__('Header v1', 'razzi'),
						'v2'      => esc_html__('Header v2', 'razzi'),
						'v3'      => esc_html__('Header v3', 'razzi'),
						'v4'      => esc_html__('Header v4', 'razzi'),
						'v5'      => esc_html__('Header v5', 'razzi'),
						'v6'      => esc_html__('Header v6', 'razzi'),
						'v7'      => esc_html__('Header v7', 'razzi'),
					),
				),
				array(
					'name'    => esc_html__( 'Header Background', 'razzi' ),
					'id'      => 'rz_header_background',
					'type'    => 'select',
					'options' => array(
						'default'     => esc_html__( 'Default', 'razzi' ),
						'transparent' => esc_html__( 'Transparent', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Header Text Color', 'razzi' ),
					'id'      => 'rz_header_text_color',
					'class'   => 'header-text-color hidden',
					'type'    => 'select',
					'options' => array(
						'default' => esc_html__( 'Default', 'razzi' ),
						'dark'    => esc_html__( 'Dark', 'razzi' ),
						'light'   => esc_html__( 'Light', 'razzi' ),
					),
				),
				array(
					'name' => esc_html__( 'Hide Border Bottom', 'razzi' ),
					'id'   => 'rz_hide_header_border',
					'type' => 'checkbox',
					'std'  => false,
				),
				array(
					'name'       => esc_html__( 'Header Spacing', 'razzi' ),
					'id'         => 'rz_header_bottom_spacing_bottom',
					'type'       => 'slider',
					'suffix'     => esc_html__( ' px', 'razzi' ),
					'js_options' => array(
						'min' => 0,
						'max' => 300,
					),
					'std'        => '20',
				),
				array(
					'name'    => esc_html__( 'Primary Menu', 'razzi' ),
					'id'      => 'rz_header_primary_menu',
					'type'    => 'select',
					'options' => $this->get_menus(),
				),
				array(
					'name'    => esc_html__( 'Department Menu Display', 'razzi' ),
					'id'      => 'rz_department_menu_display',
					'type'    => 'select',
					'options' => array(
						'default'    => esc_html__( 'On Hover', 'razzi' ),
						'onpageload' => esc_html__( 'On Page Load', 'razzi' ),
					),
				),
			)
		);
	}

	/**
	 * Get nav menus
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_menus() {
		if ( ! is_admin() ) {
			return [];
		}

		$menus = wp_get_nav_menus();
		if ( ! $menus ) {
			return [];
		}

		$output = array(
			0 => esc_html__( 'Default', 'razzi' ),
		);
		foreach ( $menus as $menu ) {
			$output[ $menu->slug ] = $menu->name;
		}

		return $output;
	}

	/**
	 * Register page header settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_page_header_settings() {
		return array(
			'id'       => 'page-header-settings',
			'title'    => esc_html__( 'Page Header Settings', 'razzi' ),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name' => esc_html__( 'Hide Page Header', 'razzi' ),
					'id'   => 'rz_hide_page_header',
					'type' => 'checkbox',
					'std'  => false,
				),

				array(
					'name'  => esc_html__( 'Hide Title', 'razzi' ),
					'id'    => 'rz_hide_title',
					'type'  => 'checkbox',
					'std'   => false,
					'class' => 'page-header-hide-title',
				),

				array(
					'name'  => esc_html__( 'Hide Breadcrumb', 'razzi' ),
					'id'    => 'rz_hide_breadcrumb',
					'type'  => 'checkbox',
					'std'   => false,
					'class' => 'page-header-hide-breadcrumb',
				),

				array(
					'name'    => esc_html__( 'Spacing', 'razzi' ),
					'id'      => 'rz_page_header_spacing',
					'type'    => 'select',
					'options' => array(
						'default' => esc_html__( 'Default', 'razzi' ),
						'custom'  => esc_html__( 'Custom', 'razzi' ),
					),
				),

				array(
					'name'       => esc_html__( 'Top Spacing', 'razzi' ),
					'id'         => 'rz_page_header_top_padding',
					'class'      => 'custom-page-header-spacing hidden',
					'type'       => 'slider',
					'suffix'     => esc_html__( ' px', 'razzi' ),
					'js_options' => array(
						'min' => 0,
						'max' => 300,
					),
					'std'        => '50',
				),

				array(
					'name'       => esc_html__( 'Bottom Spacing', 'razzi' ),
					'id'         => 'rz_page_header_bottom_padding',
					'class'      => 'custom-page-header-spacing hidden',
					'type'       => 'slider',
					'suffix'     => esc_html__( ' px', 'razzi' ),
					'js_options' => array(
						'min' => 0,
						'max' => 300,
					),
					'std'        => '50',
				),
			)
		);
	}

	/**
	 * Register content settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_content_settings() {
		return array(
			'id'       => 'content-settings',
			'title'    => esc_html__( 'Content Settings', 'razzi' ),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name'    => esc_html__( 'Content Width', 'razzi' ),
					'id'      => 'rz_content_width',
					'type'    => 'select',
					'options' => array(
						''      => esc_html__( 'Normal', 'razzi' ),
						'large' => esc_html__( 'Large', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Content Top Spacing', 'razzi' ),
					'id'      => 'rz_content_top_spacing',
					'type'    => 'select',
					'options' => array(
						'default' => esc_html__( 'Default', 'razzi' ),
						'no'      => esc_html__( 'No spacing', 'razzi' ),
						'custom'  => esc_html__( 'Custom', 'razzi' ),
					),
				),
				array(
					'name'       => '&nbsp;',
					'id'         => 'rz_content_top_padding',
					'class'      => 'custom-spacing hidden',
					'type'       => 'slider',
					'suffix'     => esc_html__( ' px', 'razzi' ),
					'js_options' => array(
						'min' => 0,
						'max' => 300,
					),
					'std'        => '80',
				),
				array(
					'name'    => esc_html__( 'Content Bottom Spacing', 'razzi' ),
					'id'      => 'rz_content_bottom_spacing',
					'type'    => 'select',
					'options' => array(
						'default' => esc_html__( 'Default', 'razzi' ),
						'no'      => esc_html__( 'No spacing', 'razzi' ),
						'custom'  => esc_html__( 'Custom', 'razzi' ),
					),
				),
				array(
					'name'       => '&nbsp;',
					'id'         => 'rz_content_bottom_padding',
					'class'      => 'custom-spacing hidden',
					'type'       => 'slider',
					'suffix'     => esc_html__( ' px', 'razzi' ),
					'js_options' => array(
						'min' => 0,
						'max' => 300,
					),
					'std'        => '80',
				),
			)
		);
	}

	/**
	 * Register page boxed settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_page_boxed_settings() {
		return array(
			'id'       => 'page-boxed-settings',
			'title'    => esc_html__( 'Boxed Layout Settings', 'razzi' ),
			'pages'    => array( 'page', 'post', 'product' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name' => esc_html__( 'Disable Boxed Layout', 'razzi' ),
					'id'   => 'rz_disable_page_boxed',
					'type' => 'checkbox',
					'std'  => false,
				),
				array(
					'name' => esc_html__( 'Background Color', 'razzi' ),
					'id'   => 'rz_page_boxed_bg_color',
					'type' => 'color',
					'std'  => false,
				),
				array(
					'name'             => esc_html__( 'Background Image', 'razzi' ),
					'id'               => 'rz_page_boxed_bg_image',
					'type'             => 'image_advanced',
					'max_file_uploads' => 1,
					'std'              => false,
				),
				array(
					'name'    => esc_html__( 'Background Horizontal', 'razzi' ),
					'id'      => 'rz_page_boxed_bg_horizontal',
					'type'    => 'select',
					'options' => array(
						''       => esc_html__( 'Default', 'razzi' ),
						'left'   => esc_html__( 'Left', 'razzi' ),
						'center' => esc_html__( 'Center', 'razzi' ),
						'right'  => esc_html__( 'Right', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Background Vertical', 'razzi' ),
					'id'      => 'rz_page_boxed_bg_vertical',
					'type'    => 'select',
					'options' => array(
						''       => esc_html__( 'Default', 'razzi' ),
						'top'    => esc_html__( 'Top', 'razzi' ),
						'center' => esc_html__( 'Center', 'razzi' ),
						'bottom' => esc_html__( 'Bottom', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Background Repeat', 'razzi' ),
					'id'      => 'rz_page_boxed_bg_repeat',
					'type'    => 'select',
					'options' => array(
						''          => esc_html__( 'Default', 'razzi' ),
						'no-repeat' => esc_html__( 'No Repeat', 'razzi' ),
						'repeat'    => esc_html__( 'Repeat', 'razzi' ),
						'repeat-y'  => esc_html__( 'Repeat Vertical', 'razzi' ),
						'repeat-x'  => esc_html__( 'Repeat Horizontal', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Background Attachment', 'razzi' ),
					'id'      => 'rz_page_boxed_bg_attachment',
					'type'    => 'select',
					'options' => array(
						''       => esc_html__( 'Default', 'razzi' ),
						'scroll' => esc_html__( 'Scroll', 'razzi' ),
						'fixed'  => esc_html__( 'Fixed', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Background Size', 'razzi' ),
					'id'      => 'rz_page_boxed_bg_size',
					'type'    => 'select',
					'options' => array(
						''        => esc_html__( 'Default', 'razzi' ),
						'auto'    => esc_html__( 'Auto', 'razzi' ),
						'cover'   => esc_html__( 'Cover', 'razzi' ),
						'contain' => esc_html__( 'Contain', 'razzi' ),
					),
				),
			)
		);
	}

	/**
	 * Register footer settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_footer_settings() {
		return array(
			'id'       => 'footer-settings',
			'title'    => esc_html__( 'Footer Settings', 'razzi' ),
			'pages'    => array( 'page' ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields'   => array(
				array(
					'name' => esc_html__( 'Hide Footer Section', 'razzi' ),
					'id'   => 'rz_hide_footer_section',
					'type' => 'select',
					'type' => 'checkbox',
					'std'  => false,
				),
				array(
					'name' => esc_html__( 'Footer Border', 'razzi' ),
					'id'   => 'rz_footer_section_border_top',
					'type' => 'select',
					'desc' => esc_html__( 'Show/hide a divide line on top of the footer', 'razzi' ),
					'options' => array(
						'default' => esc_html__( 'Default', 'razzi' ),
						'0' 	  => esc_html__( 'Hide', 'razzi' ),
						'1' 	  => esc_html__( 'Show', 'razzi' ),
					),
				),
				array(
					'name'    => esc_html__( 'Border Color', 'razzi' ),
					'id'      => 'rz_footer_section_border_color',
					'type'    => 'select',
					'options' => array(
						'default'     => esc_html__( 'Default', 'razzi' ),
						'custom' 	  => esc_html__( 'Custom', 'razzi' ),
					),
				),
				array(
					'name' => esc_html__( 'Color', 'razzi' ),
					'id'   => 'rz_footer_section_custom_border_color',
					'class' => 'footer-section-custom-border-color hidden',
					'type' => 'color',
					'std'  => false,
				),
			)
		);
	}
}
