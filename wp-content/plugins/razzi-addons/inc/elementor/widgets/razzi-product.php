<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use RazziAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Razzi_Product extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-product-shortcode';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Razzi - Product Summary', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'razzi' ];
	}

	public function get_script_depends() {
		$scripts = [
			'flexslider',
			'wc-single-product',
			'swiper',
			'imagesLoaded',
			'tawcvs-frontend',
			'razzi-product-shortcode'
		];

		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$scripts[] = 'zoom';
			$scripts[] = 'coundown';
		}
		return $scripts;
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->section_content();
		$this->section_style();
	}

	// Tab Content
	protected function section_content() {
		$this->section_products_settings_controls();
	}

	// Tab Style
	protected function section_style() {
		$this->section_content_style_controls();
	}

	protected function section_products_settings_controls() {
		$this->start_controls_section(
			'section_products',
			[ 'label' => esc_html__( 'Products', 'razzi' ) ]
		);

		$this->add_control(
			'products_divider',
			[
				'label' => esc_html__( 'Products', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_id',
			[
				'label'       => esc_html__( 'Product', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => false,
				'source'      => 'product',
				'sortable'    => false,
			]
		);

		$this->add_control(
			'attribute_divider',
			[
				'label' => esc_html__( 'Attributes', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_image_zoom',
			[
				'label'     => esc_html__( 'Image Zoom', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_lightbox',
			[
				'label'     => esc_html__( 'Lightbox', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_size_chart',
			[
				'label'     => esc_html__( 'Size Chart', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'     => esc_html__( 'Description', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => '',
			]
		);

		$this->end_controls_section();
	}

	protected function section_content_style_controls() {
		// Content Tab Style
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_divider',
			[
				'label' => esc_html__( 'Content Wrapper', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'content_style_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-product-shortcode' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_style_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-product-shortcode' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_divider_2',
			[
				'label' => esc_html__( 'Content Inner', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_inner_style_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-product-shortcode .rz-product-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_inner_style_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-product-shortcode .rz-product-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$classes = [
			'razzi-product-shortcode single-product woocommerce razzi-swiper-carousel-elementor'
		];

		$product_id = intval($settings['product_id']);

		if( empty($product_id) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		echo '<div '. $this->get_render_attribute_string( 'wrapper' ) .'>';

		echo '<div class="rz-product-wrapper container">';

		$product = wc_get_product($product_id);

		if ( empty( $product ) ) {
			echo esc_html__( 'No products were found matching your selection.', 'razzi' );
		} else{
			add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_true' );

			if ( $settings['show_lightbox'] === 'show' ) {
				wp_enqueue_script( 'photoswipe-ui-default' );
				wp_enqueue_style( 'photoswipe-default-skin' );
				add_action( 'wp_footer', 'woocommerce_photoswipe' );
			}

			if ( $settings['show_image_zoom'] === 'show' && wp_script_is( 'zoom', 'registered' ) ) {
				wp_enqueue_script( 'zoom' );
			}

			if ( $settings['show_size_chart'] != '' && class_exists('\Razzi\Addons\Modules\Size_Guide') ) {
				add_action( 'razzi_woocommerce_single_product_summary', array( \Razzi\Addons\Modules\Size_Guide::instance(), 'size_guide_button' ), 25 );
				add_action( 'razzi_woocommerce_single_product_summary', array( \Razzi\Addons\Modules\Size_Guide::instance(), 'size_guide_panel' ), 25 );
			}

			if ( $settings['show_description'] != '' ) {
				add_action( 'razzi_woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			}

			$original_post = $GLOBALS['post'];

			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', 'single-product-summary' );


			$GLOBALS['post'] = $original_post; // WPCS: override ok.

			wp_reset_postdata();
		}

		echo '</div>';
		echo '</div>';
	}
}