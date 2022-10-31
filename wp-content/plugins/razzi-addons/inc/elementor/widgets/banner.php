<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Controls_Stack;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner Small widget
 */
class Banner extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-banner';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Banner', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-banner';
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
		return [
			'razzi-elementor'
		];
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

	protected function section_content() {
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Banner', 'razzi' ) ]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/1056x521/f1f1f1?text=Banner Image',
				],
			]
		);

		$this->add_control(
			'sub_title',
			[
				'label'   => esc_html__( 'Sub Title', 'razzi' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'razzi' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'This is title', 'razzi' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'razzi' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);

		$this->add_control(
			'button_style',
			[
				'label'      => esc_html__( 'Button Style', 'razzi' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'style_1' => esc_html__( 'Style 1', 'razzi' ),
					'style_2'  => esc_html__( 'Style 2', 'razzi' ),
				],
				'default'    => 'style_1',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Button text', 'razzi' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Button Text', 'razzi' ),
			]
		);

		$this->add_control(
			'show_default_icon',
			[
				'label'        => esc_html__( 'Show Button Icon', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'razzi' ),
				'label_on'     => __( 'On', 'razzi' ),
				'return_value' => 'yes',
				'default'      => 'yes'
			]
		);

		$this->add_control(
			'link', [
				'label'         => esc_html__( 'Button Link', 'razzi' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'razzi' ),
				'description'   => esc_html__( 'Just works if the value of Lightbox is No', 'razzi' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$this->add_control(
			'text_box',
			[
				'label'       => esc_html__( 'Box Sale Before Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your text', 'razzi' ),
				'label_block' => true,
				'default'     => '',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'price_box',
			[
				'label'       => esc_html__( 'Box Sale Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your text', 'razzi' ),
				'label_block' => true,
				'default'     => '',
			]
		);

		$this->add_control(
			'unit_box',
			[
				'label'       => esc_html__( 'Box Sale Unit', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your text', 'razzi' ),
				'label_block' => true,
				'default'     => '',
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'     => esc_html__( 'Height', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [],
				'range'     => [
					'px' => [
						'min' => 100,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => esc_html__( 'Link Type', 'razzi' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'only' => esc_html__( 'Only button text', 'razzi' ),
					'all'  => esc_html__( 'All banner', 'razzi' ),
				],
				'default' => 'all',
				'toggle'  => false,
			]
		);

		$this->end_controls_section();
	}

	// Tab Style
	protected function section_style() {
		$this->section_style_banner();
		$this->section_style_content();
		$this->section_style_sale();
	}

	protected function section_style_banner() {
		// Banner
		$this->start_controls_section(
			'section_style_banner',
			[
				'label' => esc_html__( 'Banner', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'banner_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'banner_horizontal_position',
			[
				'label'        => esc_html__( 'Horizontal Position', 'razzi' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'default'      => 'left',
				'options'      => [
					'left'   => [
						'title' => esc_html__( 'Left', 'razzi' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'razzi' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'razzi' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'razzi--h-position-',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'banner_vertical_position',
			[
				'label'        => esc_html__( 'Vertical Position', 'razzi' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'default'      => 'middle',
				'options'      => [
					'top'    => [
						'title' => esc_html__( 'Top', 'razzi' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'razzi' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'razzi' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'prefix_class' => 'razzi--v-position-',
			]
		);

		$this->add_control(
			'banner_text_align',
			[
				'label'       => esc_html__( 'Text Align', 'razzi' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					''       => [
						'title' => esc_html__( 'Left', 'razzi' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'razzi' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'razzi' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .razzi-banner-content-inner' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'background_position',
			[
				'label'     => esc_html__( 'Background Position', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''              => esc_html__( 'Default', 'razzi' ),
					'left top'      => esc_html__( 'Left Top', 'razzi' ),
					'left center'   => esc_html__( 'Left Center', 'razzi' ),
					'left bottom'   => esc_html__( 'Left Bottom', 'razzi' ),
					'right top'     => esc_html__( 'Right Top', 'razzi' ),
					'right center'  => esc_html__( 'Right Center', 'razzi' ),
					'right bottom'  => esc_html__( 'Right Bottom', 'razzi' ),
					'center top'    => esc_html__( 'Center Top', 'razzi' ),
					'center center' => esc_html__( 'Center Center', 'razzi' ),
					'center bottom' => esc_html__( 'Center Bottom', 'razzi' ),
					'initial' 		=> esc_html__( 'Custom', 'razzi' ),
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner__featured-image' => 'background-position: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'background_position_xy',
			[
				'label'              => esc_html__( 'Custom Background Position', 'razzi' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => [ 'top', 'left' ],
				'size_units'         => [ 'px', '%' ],
				'default'            => [ ],
				'selectors'          => [
					'{{WRAPPER}} .razzi-banner__featured-image' => 'background-position: {{LEFT}}{{UNIT}} {{TOP}}{{UNIT}};',
				],
				'condition' => [
					'background_position' => [ 'initial' ],
				],
				'required' => true,
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'condition' => [
							'background_position_tablet' => [ 'initial' ],
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'condition' => [
							'background_position_mobile' => [ 'initial' ],
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'background_repeat',
			[
				'label'     => esc_html__( 'Background Repeat', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no-repeat',
				'options'   => [
					'no-repeat' => esc_html__( 'No-repeat', 'razzi' ),
					'repeat' 	=> esc_html__( 'Repeat', 'razzi' ),
					'repeat-x'  => esc_html__( 'Repeat-x', 'razzi' ),
					'repeat-y'  => esc_html__( 'Repeat-y', 'razzi' ),
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner__featured-image' => 'background-repeat: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'background_size',
			[
				'label'     => esc_html__( 'Background Size', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'   => esc_html__( 'Cover', 'razzi' ),
					'contain' => esc_html__( 'Contain', 'razzi' ),
					'auto'    => esc_html__( 'Auto', 'razzi' ),
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner__featured-image' => 'background-size: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_overlay',
			[
				'label'      => esc_html__( 'Background Overlay', 'razzi' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner__featured-image::before' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function section_style_content() {
		// Content
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_spacing',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_bg',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		// sub title
		$this->add_control(
			'content_style_subtitle',
			[
				'label' => __( 'Subtitle', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'subtitle_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-content__sub-title',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		// title
		$this->add_control(
			'content_style_title',
			[
				'label' => __( 'Title', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-content__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__title' => 'color: {{VALUE}};',
				],
			]
		);

		// Description
		$this->add_control(
			'content_style_des',
			[
				'label' => __( 'Description', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'desc_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-content__description',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__description' => 'color: {{VALUE}};',
				],
			]
		);

		// button
		$this->add_control(
			'content_style_button',
			[
				'label' => __( 'Button', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-content__button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btn_border',
				'label' => __( 'Border', 'razzi' ),
				'selector' => '{{WRAPPER}} .razzi-banner-content__button.button-normal',
				'conditions' => [
					'terms' => [
						[
						'name' => 'button_style',
						'value' => 'style_1',
						],
					],
				],
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label'     => __( 'background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_style_button_hover',
			[
				'label' => __( 'Button Hover', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'btn_bg_color_hover',
			[
				'label'     => __( 'background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_color_hover',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function section_style_sale() {
		$this->start_controls_section(
			'section_style_sale',
			[
				'label' => __( 'Sale', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => esc_html__( 'Before Text', 'razzi' ),
				'name'     => 'regular_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-content__sale--text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => esc_html__( 'Text', 'razzi' ),
				'name'     => 'sale_price_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-content__sale--number',
			]
		);

		$this->add_control(
			'sale_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__sale' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-content__sale' => 'background-color: {{VALUE}}',

				],
			]
		);

		$this->add_responsive_control(
			'price_box_position_top',
			[
				'label'      => esc_html__( 'Spacing Top', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content__sale' => 'top: {{SIZE}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'price_box_position_right',
			[
				'label'      => esc_html__( 'Spacing Right', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content__sale' => 'right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'price_box_width',
			[
				'label'      => esc_html__( 'Width (px)', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 250,
						'min' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content__sale' => 'width: {{SIZE}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'price_box_height',
			[
				'label'      => esc_html__( 'Height (px)', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'max' => 250,
						'min' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-content__sale' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render circle box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$classes = [
			'razzi-banner',
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$link_icon = $settings['show_default_icon'] ? \Razzi\Addons\Helper::get_svg( 'arrow-right', 'razzi-icon' ) : '';

		$button_text = $settings['button_text'] ? sprintf('%s%s',$settings['button_text'], $link_icon) : '';

		$btn_full = '';
		if ( $settings['link']['url'] ) :

			$button_class = $settings['button_style'] == 'style_2' ? 'razzi-banner-content__button razzi-button--underlined' : 'razzi-banner-content__button razzi-button button-normal';

			$btn_full = $settings['link_type'] == 'all' ? Helper::control_url( 'btn_full', $settings['link'], '', [ 'class' => 'razzi-banner-content__button-link' ] ) : '';

			$button_text = ! empty($button_text  ) ? Helper::control_url( 'btn', $settings['link'], $button_text, [ 'class' => $button_class ] ) : '';
		endif;

		$sub_title = $settings['sub_title'] ? sprintf( '<h5 class="razzi-banner-content__sub-title">%s</h5>', $settings['sub_title'] ) : '';
		$title = $settings['title'] ? sprintf( '<h4 class="razzi-banner-content__title">%s</h4>', $settings['title'] ) : '';
		$description = $settings['description'] ? sprintf( '<div class="razzi-banner-content__description">%s</div>', $settings['description'] ) : '';

		// Sale
		$text_sale = $settings['text_box'] ? sprintf( '<div class="razzi-banner-content__sale--text">%s</div>', $settings['text_box'] ) : '';
		$unit_sale = $settings['unit_box'] ? sprintf( '<span class="razzi-banner-content__sale--unit">%s</span>', $settings['unit_box'] ) : '';
		$num_sale  = $settings['price_box'] ? sprintf( '<div class="razzi-banner-content__sale--number">%s%s</div>', $settings['price_box'], $unit_sale ) : '';

		$html_sale = $text_sale == '' && $num_sale == '' ? '' : sprintf( '<div class="razzi-banner-content__sale">%s %s</div>', $text_sale, $num_sale );

		$bg_image = $settings['image'];
		$output = '';
		if( $bg_image && isset($bg_image['url']) ) {
			$image_bg = $bg_image['url'];
			if (function_exists('jetpack_photon_url')) {
				$image_bg = jetpack_photon_url($image_bg);
			}
			$output .= sprintf('<div class="razzi-banner__featured-image" style="background-image: url(%s)"></div>', esc_url( $image_bg ));
		}

		$output .= '<div class="razzi-banner-content">';
		$output .= '<div class="razzi-banner-content-inner">';
		$output .= $sub_title;
		$output .= $title;
		$output .= $description;
		$output .= $button_text;
		$output .= '</div>';
		$output .= '</div>';
		$output .= $html_sale;
		$output .= $btn_full;

		echo sprintf(
			'<div %s> %s</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$output
		);
	}
}