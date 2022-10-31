<?php
/**
 * Elementor Global init
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Razzi
 */

namespace Razzi\Addons\Elementor;

use \Elementor\Controls_Manager;

/**
 * Integrate with Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Page_Settings {

	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;


	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
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
		if ( ! class_exists( '\Elementor\Core\DocumentTypes\PageBase' ) ) {
			return;
		}

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_styles' ) );

		add_action( 'elementor/element/wp-page/document_settings/after_section_end', array( $this, 'add_new_page_settings_section' ) );

		add_action( 'elementor/document/after_save', array( $this, 'save_post_meta' ), 10, 2 );

		add_action( 'save_post', array( $this, 'save_elementor_settings' ), 10, 3 );

	}


	/**
	 * Preview Elementor Page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'reload_elementor', RAZZI_ADDONS_URL . "/assets/js/admin/reload-elementor.js", array( 'jquery' ), '20210308', true );
	}

	/**
	 * Inline Style
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_add_inline_style( 'elementor-editor', '#elementor-panel .elementor-control-hide_title{display:none}' );
	}

	/**
	 * Add settings to Elementor page settings
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_new_page_settings_section( \Elementor\Core\DocumentTypes\PageBase $page ) {
		// Header
		$page->start_controls_section(
			'section_header_settings',
			[
				'label' => esc_html__( 'Header Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);


		$page->add_control(
			'rz_hide_header_section',
			[
				'label'        => esc_html__( 'Hide Header Section', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => ''

			]
		);

		$page->add_control(
			'rz_header_layout',
			[
				'label'       => esc_html__( 'Header Layout', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default' => esc_html__( 'Default', 'razzi' ),
					'v1'      => esc_html__('Header v1', 'razzi'),
					'v2'      => esc_html__('Header v2', 'razzi'),
					'v3'      => esc_html__('Header v3', 'razzi'),
					'v4'      => esc_html__('Header v4', 'razzi'),
					'v5'      => esc_html__('Header v5', 'razzi'),
					'v6'      => esc_html__('Header v6', 'razzi'),
					'v7'      => esc_html__('Header v7', 'razzi'),
				],
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_header_background',
			[
				'label'       => esc_html__( 'Header Background', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default'     => esc_html__( 'Default', 'razzi' ),
					'transparent' => esc_html__( 'Transparent', 'razzi' ),
				],
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_header_text_color',
			[
				'label'       => esc_html__( 'Text Color', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'Default', 'razzi' ),
					'dark'    => esc_html__( 'Dark', 'razzi' ),
					'light'   => esc_html__( 'Light', 'razzi' ),
				),
				'default'     => 'default',
				'label_block' => true,
				'condition'   => [
					'rz_header_background' => 'transparent',
				],
			]
		);

		$page->add_control(
			'rz_hide_header_border',
			[
				'label'        => esc_html__( 'Hide Border Bottom', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => ''

			]
		);

		$page->add_control(
			'rz_header_bottom_spacing_bottom',
			[
				'label'     => esc_html__( 'Header Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 300,
						'min' => 0,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .header-v4 .header-bottom' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$page->add_control(
			'rz_header_primary_menu',
			[
				'label'       => esc_html__( 'Primary Menu', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_menus(),
				'default'     => '0',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_department_menu_display',
			[
				'label'       => esc_html__( 'Department Menu Display', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''           => esc_html__( 'On Hover', 'razzi' ),
					'onpageload' => esc_html__( 'On Page Load', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
			]
		);

		$page->end_controls_section();

		// Content
		$page->start_controls_section(
			'section_content_settings',
			[
				'label' => esc_html__( 'Content Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$page->add_control(
			'rz_content_width',
			[
				'label'       => esc_html__( 'Content Width', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''      => esc_html__( 'Normal', 'razzi' ),
					'large' => esc_html__( 'Large', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_content_top_spacing',
			[
				'label'       => esc_html__( 'Top Spacing', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'Default', 'razzi' ),
					'no'      => esc_html__( 'No spacing', 'razzi' ),
					'custom'  => esc_html__( 'Custom', 'razzi' ),
				),
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_content_top_padding',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 300,
						'min' => 0,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 80,
				],
				'selectors' => [
					'{{WRAPPER}} .site-content.custom-top-spacing' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rz_content_top_spacing' => 'custom',
				],
			]
		);
		$page->add_control(
			'rz_content_bottom_spacing',
			[
				'label'       => esc_html__( 'Bottom Spacing', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'Default', 'razzi' ),
					'no'      => esc_html__( 'No spacing', 'razzi' ),
					'custom'  => esc_html__( 'Custom', 'razzi' ),
				),
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_content_bottom_padding',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 300,
						'min' => 0,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 80,
				],
				'selectors' => [
					'{{WRAPPER}} .site-content.custom-bottom-spacing' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rz_content_bottom_spacing' => 'custom',
				],
			]
		);

		$page->end_controls_section();

		// Page Header
		$page->start_controls_section(
			'section_page_header_settings',
			[
				'label' => esc_html__( 'Page Header Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$page->add_control(
			'rz_hide_page_header',
			[
				'label'        => esc_html__( 'Hide Page Header', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => ''

			]
		);

		$page->add_control(
			'rz_hide_title',
			[
				'label'        => esc_html__( 'Hide Title', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => ''
			]
		);

		$page->add_control(
			'rz_hide_breadcrumb',
			[
				'label'        => esc_html__( 'Hide Breadcrumb', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => ''
			]
		);

		$page->add_control(
			'rz_page_header_spacing',
			[
				'label'       => esc_html__( 'Title Spacing', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'Default', 'razzi' ),
					'custom'  => esc_html__( 'Custom', 'razzi' ),
				),
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_page_header_top_padding',
			[
				'label'     => esc_html__( 'Top Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 300,
						'min' => 0,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} #page-header .page-header__title.custom-spacing' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rz_page_header_spacing' => 'custom',
				],
			]
		);

		$page->add_control(
			'rz_page_header_bottom_padding',
			[
				'label'     => esc_html__( 'Bottom Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 300,
						'min' => 0,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} #page-header .page-header__title.custom-spacing' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'rz_page_header_spacing' => 'custom',
				],
			]
		);

		$page->end_controls_section();

		// Boxed
		$page->start_controls_section(
			'section_boxed_layout_settings',
			[
				'label' => esc_html__( 'Boxed Layout Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);
		$page->add_control(
			'rz_disable_page_boxed',
			[
				'label'        => esc_html__( 'Disable Boxed Layout', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => ''

			]
		);
		$page->add_control(
			'rz_page_boxed_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-color: {{VALUE}};',
				],
			]
		);

		$page->add_control(
			'rz_page_boxed_bg_image',
			[
				'label'     => esc_html__( 'Background Image', 'razzi' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [],
				'selectors' => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-image: url("{{URL}}");',
				],
			]
		);

		$page->add_control(
			'rz_page_boxed_bg_horizontal',
			[
				'label'       => esc_html__( 'Background Horizontal', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => esc_html__( 'Default', 'razzi' ),
					'left'   => esc_html__( 'Left', 'razzi' ),
					'center' => esc_html__( 'Center', 'razzi' ),
					'right'  => esc_html__( 'Right', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-position-x:  {{VALUE}};',
				],
			]
		);

		$page->add_control(
			'rz_page_boxed_bg_vertical',
			[
				'label'       => esc_html__( 'Background Vertical', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => esc_html__( 'Default', 'razzi' ),
					'top'    => esc_html__( 'Top', 'razzi' ),
					'center' => esc_html__( 'Center', 'razzi' ),
					'bottom' => esc_html__( 'Bottom', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-position-y:  {{VALUE}};',
				],
			]
		);

		$page->add_control(
			'rz_page_boxed_bg_repeat',
			[
				'label'       => esc_html__( 'Background Repeat', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''          => esc_html__( 'Default', 'razzi' ),
					'no-repeat' => esc_html__( 'No Repeat', 'razzi' ),
					'repeat'    => esc_html__( 'Repeat', 'razzi' ),
					'repeat-y'  => esc_html__( 'Repeat Vertical', 'razzi' ),
					'repeat-x'  => esc_html__( 'Repeat Horizontal', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-repeat:  {{VALUE}};',
				],
			]
		);

		$page->add_control(
			'rz_page_boxed_bg_attachment',
			[
				'label'       => esc_html__( 'Background Attachment', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''       => esc_html__( 'Default', 'razzi' ),
					'scroll' => esc_html__( 'Scroll', 'razzi' ),
					'fixed'  => esc_html__( 'Fixed', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-attachment:  {{VALUE}};',
				],
			]
		);

		$page->add_control(
			'rz_page_boxed_bg_size',
			[
				'label'       => esc_html__( 'Background Size', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					''        => esc_html__( 'Default', 'razzi' ),
					'auto'    => esc_html__( 'Auto', 'razzi' ),
					'cover'   => esc_html__( 'Cover', 'razzi' ),
					'contain' => esc_html__( 'Contain', 'razzi' ),
				),
				'default'     => '',
				'label_block' => true,
				'selectors'   => [
					'{{WRAPPER}}.razzi-boxed-layout' => 'background-size:  {{VALUE}};',
				],
			]
		);

		$page->end_controls_section();

		$page->start_controls_section(
			'section_footer_settings',
			[
				'label' => esc_html__( 'Footer Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$page->add_control(
			'rz_hide_footer_section',
			[
				'label'        => esc_html__( 'Hide Footer Section', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => '',

			]
		);

		$page->add_control(
			'rz_footer_section_border_top',
			[
				'label'        => esc_html__( 'Footer Border', 'razzi' ),
				'type'         => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'Default', 'razzi' ),
					'0'  => esc_html__( 'Hide', 'razzi' ),
					'1'  => esc_html__( 'Show', 'razzi' ),
				),
				'default'      => 'default',
				'label_block' => true,

			]
		);

		$page->add_control(
			'rz_footer_section_border_color',
			[
				'label'       => esc_html__( 'Footer Border Color', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'default' => esc_html__( 'Default', 'razzi' ),
					'custom'  => esc_html__( 'Custom', 'razzi' ),
				),
				'default'     => 'default',
				'label_block' => true,
			]
		);

		$page->add_control(
			'rz_footer_section_custom_border_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => [
					'rz_footer_section_border_color' => 'custom',
				],
			]
		);

		$page->end_controls_section();
	}

	/**
	 * Save post meta when save page settings in Elementor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_post_meta( $el, $data ) {
		if ( ! isset( $data['settings'] ) ) {
			return;
		}

		$settings = $data['settings'];
		$post_id  = $el->get_post()->ID;

		// Header

		$header_section = isset( $settings['rz_hide_header_section'] ) ? $settings['rz_hide_header_section'] : '0';
		update_post_meta( $post_id, 'rz_hide_header_section', $header_section );

		$header_layout = isset( $settings['rz_header_layout'] ) ? $settings['rz_header_layout'] : 'default';
		update_post_meta( $post_id, 'rz_header_layout', $header_layout );

		$header_background = isset( $settings['rz_header_background'] ) ? $settings['rz_header_background'] : 'default';
		update_post_meta( $post_id, 'rz_header_background', $header_background );

		$header_text_color = isset( $settings['rz_header_text_color'] ) ? $settings['rz_header_text_color'] : 'default';
		update_post_meta( $post_id, 'rz_header_text_color', $header_text_color );

		$page_header = isset( $settings['rz_hide_header_border'] ) ? $settings['rz_hide_header_border'] : false;
		update_post_meta( $post_id, 'rz_hide_header_border', $page_header );

		if ( isset( $settings['rz_header_bottom_spacing_bottom'] ) ) {
			update_post_meta( $post_id, 'rz_header_bottom_spacing_bottom', $settings['rz_header_bottom_spacing_bottom']['size'] );
		}

		$header_layout = isset( $settings['rz_header_primary_menu'] ) ? $settings['rz_header_primary_menu'] : '0';
		update_post_meta( $post_id, 'rz_header_primary_menu', $header_layout );

		$show_menu_department = isset( $settings['rz_department_menu_display'] ) ? $settings['rz_department_menu_display'] : false;
		update_post_meta( $post_id, 'rz_department_menu_display', $show_menu_department );

		// Content
		$content_width = isset( $settings['rz_content_width'] ) ? $settings['rz_content_width'] : '';
		update_post_meta( $post_id, 'rz_content_width', $content_width );

		$content_top_spacing = isset( $settings['rz_content_top_spacing'] ) ? $settings['rz_content_top_spacing'] : 'default';
		update_post_meta( $post_id, 'rz_content_top_spacing', $content_top_spacing );

		if ( isset( $settings['rz_content_top_padding'] ) ) {
			update_post_meta( $post_id, 'rz_content_top_padding', $settings['rz_content_top_padding']['size'] );
		}
		$content_bottom_spacing = isset( $settings['rz_content_bottom_spacing'] ) ? $settings['rz_content_bottom_spacing'] : 'default';
		update_post_meta( $post_id, 'rz_content_bottom_spacing', $content_bottom_spacing );
		if ( isset( $settings['rz_content_bottom_padding'] ) ) {
			update_post_meta( $post_id, 'rz_content_bottom_padding', $settings['rz_content_bottom_padding']['size'] );
		}

		// Page Header
		$page_header = isset( $settings['rz_hide_page_header'] ) ? $settings['rz_hide_page_header'] : false;
		update_post_meta( $post_id, 'rz_hide_page_header', $page_header );

		$hide_title = isset( $settings['rz_hide_title'] ) ? $settings['rz_hide_title'] : false;
		update_post_meta( $post_id, 'rz_hide_title', $hide_title );

		$hide_breadcrumb = isset( $settings['rz_hide_breadcrumb'] ) ? $settings['rz_hide_breadcrumb'] : false;
		update_post_meta( $post_id, 'rz_hide_breadcrumb', $hide_breadcrumb );

		$page_header_spacing = isset( $settings['rz_page_header_spacing'] ) ? $settings['rz_page_header_spacing'] : 'default';
		update_post_meta( $post_id, 'rz_page_header_spacing', $page_header_spacing );

		if ( isset( $settings['rz_page_header_top_padding'] ) ) {
			update_post_meta( $post_id, 'rz_page_header_top_padding', $settings['rz_page_header_top_padding']['size'] );
		}
		if ( isset( $settings['rz_page_header_bottom_padding'] ) ) {
			update_post_meta( $post_id, 'rz_page_header_bottom_padding', $settings['rz_page_header_bottom_padding']['size'] );
		}

		// Boxed Layout
		$disable_boxed_layout = isset( $settings['rz_disable_page_boxed'] ) ? $settings['rz_disable_page_boxed'] : false;
		update_post_meta( $post_id, 'rz_disable_page_boxed', $disable_boxed_layout );

		$page_boxed_bg_color = isset( $settings['rz_page_boxed_bg_color'] ) ? $settings['rz_page_boxed_bg_color'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_color', $page_boxed_bg_color );

		$page_boxed_bg_image = isset( $settings['rz_page_boxed_bg_image'] ) ? $settings['rz_page_boxed_bg_image']['id'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_image', $page_boxed_bg_image );

		$page_boxed_bg_horizontal = isset( $settings['rz_page_boxed_bg_horizontal'] ) ? $settings['rz_page_boxed_bg_horizontal'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_horizontal', $page_boxed_bg_horizontal );

		$page_boxed_bg_vertical = isset( $settings['rz_page_boxed_bg_vertical'] ) ? $settings['rz_page_boxed_bg_vertical'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_vertical', $page_boxed_bg_vertical );

		$page_boxed_bg_repeat = isset( $settings['rz_page_boxed_bg_repeat'] ) ? $settings['rz_page_boxed_bg_repeat'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_repeat', $page_boxed_bg_repeat );

		$page_boxed_bg_attachment = isset( $settings['rz_page_boxed_bg_attachment'] ) ? $settings['rz_page_boxed_bg_attachment'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_attachment', $page_boxed_bg_attachment );

		$page_boxed_bg_size = isset( $settings['rz_page_boxed_bg_size'] ) ? $settings['rz_page_boxed_bg_size'] : '';
		update_post_meta( $post_id, 'rz_page_boxed_bg_size', $page_boxed_bg_size );

		// Footer
		$footer_section = isset( $settings['rz_hide_footer_section'] ) ? $settings['rz_hide_footer_section'] : '0';
		update_post_meta( $post_id, 'rz_hide_footer_section', $footer_section );

		$footer_border_top = isset( $settings['rz_footer_section_border_top'] ) ? $settings['rz_footer_section_border_top'] : 'default';
		update_post_meta( $post_id, 'rz_footer_section_border_top', $footer_border_top );

		$border_color = isset( $settings['rz_footer_section_border_color'] ) ? $settings['rz_footer_section_border_color'] : 'default';
		update_post_meta( $post_id, 'rz_footer_section_border_color', $border_color );

		$custom_border_color = isset( $settings['rz_footer_section_custom_border_color'] ) ? $settings['rz_footer_section_custom_border_color'] : '';
		update_post_meta( $post_id, 'rz_footer_section_custom_border_color', $custom_border_color );
	}

	/**
	 * Save Elementor page settings when save metabox
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function save_elementor_settings( $post_id, $post, $update ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['_elementor_edit_mode_nonce'] ) ) {
			return;
		}

		if ( ! is_admin() ) {
			return;
		}

		if ( $post->post_type !== 'page' ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}


		if ( ! class_exists( '\Elementor\Core\Settings\Manager' ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'elementor_ajax' ) {
			return;
		}

		$settings = array();

		// Header
		if ( isset( $_POST['rz_hide_header_section'] ) ) {
			$settings['rz_hide_header_section'] = $_POST['rz_hide_header_section'];
		}

		if ( isset( $_POST['rz_header_layout'] ) ) {
			$settings['rz_header_layout'] = $_POST['rz_header_layout'];
		}

		if ( isset( $_POST['rz_header_background'] ) ) {
			$settings['rz_header_background'] = $_POST['rz_header_background'];
		}

		if ( isset( $_POST['rz_header_text_color'] ) ) {
			$settings['rz_header_text_color'] = $_POST['rz_header_text_color'];
		}

		if ( isset( $_POST['rz_hide_header_border'] ) ) {
			$settings['rz_hide_header_border'] = $_POST['rz_hide_header_border'];
		}

		if ( isset( $_POST['rz_header_bottom_spacing_bottom'] ) ) {
			$settings['rz_header_bottom_spacing_bottom']['size'] = $_POST['rz_header_bottom_spacing_bottom'];
		}

		if ( isset( $_POST['rz_header_primary_menu'] ) ) {
			$settings['rz_header_primary_menu'] = $_POST['rz_header_primary_menu'];
		}

		if ( isset( $_POST['rz_department_menu_display'] ) ) {
			$settings['rz_department_menu_display'] = $_POST['rz_department_menu_display'];
		}

		// Content
		if ( isset( $_POST['rz_content_width'] ) ) {
			$settings['rz_content_width'] = $_POST['rz_content_width'];
		}
		if ( isset( $_POST['rz_content_top_spacing'] ) ) {
			$settings['rz_content_top_spacing'] = $_POST['rz_content_top_spacing'];
		}
		if ( isset( $_POST['rz_content_top_padding'] ) ) {
			$settings['rz_content_top_padding']['size'] = $_POST['rz_content_top_padding'];
		}
		if ( isset( $_POST['rz_content_bottom_spacing'] ) ) {
			$settings['rz_content_bottom_spacing'] = $_POST['rz_content_bottom_spacing'];
		}
		if ( isset( $_POST['rz_content_bottom_padding'] ) ) {
			$settings['rz_content_bottom_padding']['size'] = $_POST['rz_content_bottom_padding'];
		}

		// Page Header
		if ( isset( $_POST['rz_hide_page_header'] ) ) {
			$settings['rz_hide_page_header'] = $_POST['rz_hide_page_header'];
		}
		if ( isset( $_POST['rz_hide_title'] ) ) {
			$settings['rz_hide_title'] = $_POST['rz_hide_title'];
		}
		if ( isset( $_POST['rz_hide_breadcrumb'] ) ) {
			$settings['rz_hide_breadcrumb'] = $_POST['rz_hide_breadcrumb'];
		}

		if ( isset( $_POST['rz_page_header_spacing'] ) ) {
			$settings['rz_page_header_spacing'] = $_POST['rz_page_header_spacing'];
		}
		if ( isset( $_POST['rz_page_header_top_padding'] ) ) {
			$settings['rz_page_header_top_padding']['size'] = $_POST['rz_page_header_top_padding'];
		}
		if ( isset( $_POST['rz_page_header_bottom_padding'] ) ) {
			$settings['rz_page_header_bottom_padding']['size'] = $_POST['rz_page_header_bottom_padding'];
		}

		//Boxed Layout
		if ( isset( $_POST['rz_disable_page_boxed'] ) ) {
			$settings['rz_disable_page_boxed'] = $_POST['rz_disable_page_boxed'];
		}
		if ( isset( $_POST['rz_page_boxed_bg_color'] ) ) {
			$settings['rz_page_boxed_bg_color'] = $_POST['rz_page_boxed_bg_color'];
		}
		if ( isset( $_POST['rz_page_boxed_bg_image'] ) ) {
			$image_id = $_POST['rz_page_boxed_bg_image'][0];
			$settings['rz_page_boxed_bg_image']['id'] = $image_id;
			$bg_image                                 = wp_get_attachment_image_src( $image_id, 'full' );
			if ( $bg_image ) {
				$settings['rz_page_boxed_bg_image']['url'] = $bg_image[0];
			}
		}
		if ( isset( $_POST['rz_page_boxed_bg_horizontal'] ) ) {
			$settings['rz_page_boxed_bg_horizontal'] = $_POST['rz_page_boxed_bg_horizontal'];
		}
		if ( isset( $_POST['rz_page_boxed_bg_vertical'] ) ) {
			$settings['rz_page_boxed_bg_vertical'] = $_POST['rz_page_boxed_bg_vertical'];
		}

		if ( isset( $_POST['rz_page_boxed_bg_repeat'] ) ) {
			$settings['rz_page_boxed_bg_repeat'] = $_POST['rz_page_boxed_bg_repeat'];
		}
		if ( isset( $_POST['rz_page_boxed_bg_attachment'] ) ) {
			$settings['rz_page_boxed_bg_attachment'] = $_POST['rz_page_boxed_bg_attachment'];
		}
		if ( isset( $_POST['rz_page_boxed_bg_size'] ) ) {
			$settings['rz_page_boxed_bg_size'] = $_POST['rz_page_boxed_bg_size'];
		}

		// Footer
		if ( isset( $_POST['rz_hide_footer_section'] ) ) {
			$settings['rz_hide_footer_section'] = $_POST['rz_hide_footer_section'];
		}

		if ( isset( $_POST['rz_footer_section_border_top'] ) ) {
			$settings['rz_footer_section_border_top'] = $_POST['rz_footer_section_border_top'];
		}
		if ( isset( $_POST['rz_footer_section_border_color'] ) ) {
			$settings['rz_footer_section_border_color'] = $_POST['rz_footer_section_border_color'];
		}
		if ( isset( $_POST['rz_footer_section_custom_border_color'] ) ) {
			$settings['rz_footer_section_custom_border_color'] = $_POST['rz_footer_section_custom_border_color'];
		}

		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_manager->save_settings( $settings, $post_id );

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
}
