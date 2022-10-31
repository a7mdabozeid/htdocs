<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Background;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Deals_Carousel extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-deals-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Deals Carousel', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-countdown';
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
			'razzi-coundown',
			'razzi-product-shortcode'
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

	/**
	 * Section Content
	 */
	protected function section_content() {
		$this->section_content_settings();
		$this->section_carousel_settings();
	}

	protected function section_content_settings() {
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Content', 'razzi' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->start_controls_tabs( 'slides_repeater' );

		$repeater->start_controls_tab( 'countdown_background', [ 'label' => esc_html__( 'Background', 'razzi' ) ] );

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'countdown_background',
				'label'    => __( 'Background Image', 'razzi' ),
				'types'    => [ 'classic' ],
				'selector' => '{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .countdown-bg',
				'fields_options'  => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default'   => '#f5f5f5',
					],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'countdown_content', [ 'label' => esc_html__( 'Content', 'razzi' ) ] );

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is title', 'razzi' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'due_date',
			[
				'label'   => esc_html__( 'Date', 'razzi' ),
				'type'    => Controls_Manager::DATE_TIME,
				'default' => '',
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'button_link', [
				'label'         => esc_html__( 'Button Link', 'razzi' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'razzi' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'countdown_style', [ 'label' => esc_html__( 'Style', 'razzi' ) ] );

		$repeater->add_control(
			'countdown_style_divider',
			[
				'label' => __( 'Title', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'countdown_style_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .countdown-title' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'countdown_style_divider_2',
			[
				'label' => __( 'Countdown', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'countdown_style_digits_color',
			[
				'label'     => esc_html__( 'Digits Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .timer .digits' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'countdown_style_countdown_text',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .timer .text' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'countdown_style_divider_color',
			[
				'label'     => esc_html__( 'Divider Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .timer .divider' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'countdown_style_divider_3',
			[
				'label' => __( 'Button', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'countdown_style_button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .countdown-button a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'countdown_style_button_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel {{CURRENT_ITEM}} .countdown-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'countdown_slider',
			[
				'label'         => '',
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title'        => esc_html__( 'This is title', 'razzi' ),
						'due_date' => '',
						'button_text' => esc_html__( 'Shop Now', 'razzi' )
					],
					[
						'title'        => esc_html__( 'This is title', 'razzi' ),
						'due_date' => '',
						'button_text' => esc_html__( 'Shop Now', 'razzi' )
					]
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
			]
		);

		$this->add_responsive_control(
			'countdown_height',
			[
				'label'      => esc_html__( 'Height', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 550,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-slider' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function section_carousel_settings() {
		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Carousel Settings', 'razzi' ),
				'type'  => Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'effect',
			[
				'label'   => esc_html__( 'Effect', 'razzi' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'fade'   	 => esc_html__( 'Fade', 'razzi' ),
					'slide' 	 => esc_html__( 'Slide', 'razzi' ),
					'cube' 	 	 => esc_html__( 'Cube', 'razzi' ),
					'coverflow'	 => esc_html__( 'Coverflow', 'razzi' ),
				],
				'default' => 'fade',
				'toggle'  => false,
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'navigation',
			[
				'label'     => esc_html__( 'Navigation', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'none'     => esc_html__( 'None', 'razzi' ),
					'arrows'  => esc_html__( 'Arrows', 'razzi' ),
					'dots'  => esc_html__( 'Dots', 'razzi' ),
				],
				'default'   => 'arrows',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'razzi' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'delay',
			[
				'label'     => esc_html__( 'Delay', 'razzi' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'description' => esc_html__('Delay between transitions (in ms). If this parameter is not specified, auto play will be disabled', 'razzi'),
				'conditions' => [
					'terms' => [
						[
							'name'  => 'autoplay',
							'value' => 'yes',
						]
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed', 'razzi' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1000,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'   => esc_html__( 'Infinite Loop', 'razzi' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Section Style
	 */
	protected function section_style() {
		$this->section_general_style();
		$this->section_title_style();
		$this->section_countdown_style();
		$this->section_button_style();
		$this->section_carousel_style();
	}

	/**
	 * Element in Tab Style
	 *
	 * General
	 */
	protected function section_general_style() {
		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __( 'General', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'general_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * Title
	 */
	protected function section_title_style() {
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_style_title',
				'selector' => '{{WRAPPER}} .razzi-deals-carousel .countdown-title',
			]
		);
		$this->add_control(
			'heading_style_title_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_style_spacing',
			[
				'label'     => __( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * Countdown
	 */
	protected function section_countdown_style() {
		$this->start_controls_section(
			'section_countdown_style',
			[
				'label' => __( 'Countdown', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'section_countdown_spacing',
			[
				'label'     => __( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->start_controls_tabs( 'section_countdown_settings' );

		$this->start_controls_tab( 'countdown_style_digits', [ 'label' => esc_html__( 'Number', 'razzi' ) ] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'digit_typography',
				'selector' => '{{WRAPPER}} .razzi-deals-carousel .timer .digits',
			]
		);

		$this->add_control(
			'digit_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .timer .digits' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'digit_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 30,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .timer .digits' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'countdown_style_text', [ 'label' => esc_html__( 'Text', 'razzi' ) ] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .razzi-deals-carousel .timer .text',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .timer .text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'countdown_style_divider', [ 'label' => esc_html__( 'Divider', 'razzi' ) ] );

		$this->add_responsive_control(
			'divider_font_size',
			[
				'label'     => __( 'Font Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 60,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .timer .divider' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .timer .divider' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'divider_position_left',
			[
				'label'              => esc_html__( 'Position ', 'razzi' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => [ 'top', 'right' ],
				'size_units'         => [ 'px', '%' ],
				'default'            => [],
				'selectors'          => [
					'{{WRAPPER}} .razzi-deals-carousel .timer .divider' => ' top:{{TOP}}{{UNIT}};right: {{RIGHT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'divider_space',
			[
				'label'     => __( 'Space', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 60,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .timer' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * Button
	 */
	protected function section_button_style() {
		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Button', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .razzi-deals-carousel .countdown-button a',
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label'     => __( 'Height', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 60,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-button a' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-button a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-deals-carousel .countdown-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * Carousel
	 */
	protected function section_carousel_style() {
			$this->start_controls_section(
				'section_carousel_style',
				[
					'label' => esc_html__( 'Carousel Settings', 'razzi' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'arrows_style_divider',
				[
					'label' => esc_html__( 'Arrows', 'razzi' ),
					'type'  => Controls_Manager::HEADING,
				]
			);

			// Arrows
			$this->add_control(
				'arrows_style',
				[
					'label'        => __( 'Options', 'razzi' ),
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'label_off'    => __( 'Default', 'razzi' ),
					'label_on'     => __( 'Custom', 'razzi' ),
					'return_value' => 'yes',
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				'sliders_arrows_size',
				[
					'label'     => __( 'Size', 'razzi' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 100,
							'min' => 0,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'sliders_arrows_width',
				[
					'label'     => __( 'Width', 'razzi' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 100,
							'min' => 0,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'sliders_arrows_height',
				[
					'label'     => __( 'Height', 'razzi' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 100,
							'min' => 0,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button' => 'height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'arrows_horizontal_position',
				[
					'label'      => esc_html__( 'Horizontal Position', 'razzi' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => - 100,
							'max' => 200,
						],
					],
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'arrows_vertical_position',
				[
					'label'      => esc_html__( 'Vertical Position', 'razzi' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => [
						'px' => [
							'min' => - 100,
							'max' => 200,
						],
					],
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button' => 'top: {{SIZE}}{{UNIT}};transform:none;',
					],
				]
			);

			$this->end_popover();

			$this->start_controls_tabs( 'sliders_normal_settings' );

			$this->start_controls_tab( 'sliders_normal', [ 'label' => esc_html__( 'Normal', 'razzi' ) ] );

			$this->add_control(
				'sliders_arrow_color',
				[
					'label'     => esc_html__( 'Color', 'razzi' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'sliders_arrow_bgcolor',
				[
					'label'     => esc_html__( 'Background Color', 'razzi' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab( 'sliders_hover', [ 'label' => esc_html__( 'Hover', 'razzi' ) ] );

			$this->add_control(
				'sliders_arrow_hover_color',
				[
					'label'     => esc_html__( 'Color', 'razzi' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'sliders_arrow_hover_bgcolor',
				[
					'label'     => esc_html__( 'Background Color', 'razzi' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .rz-swiper-button:hover' => 'background-color: {{VALUE}};',
					],
				]
			);


			$this->end_controls_tab();

			$this->end_controls_tabs();

			// Dots
			$this->add_control(
				'dots_style_divider',
				[
					'label'     => esc_html__( 'Dots', 'razzi' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'dots_style',
				[
					'label'        => __( 'Options', 'razzi' ),
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'label_off'    => __( 'Default', 'razzi' ),
					'label_on'     => __( 'Custom', 'razzi' ),
					'return_value' => 'yes',
				]
			);

			$this->start_popover();

			$this->add_responsive_control(
				'sliders_dots_gap',
				[
					'label'     => __( 'Gap', 'razzi' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 50,
							'min' => 0,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'sliders_dots_size',
				[
					'label'     => __( 'Size', 'razzi' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 100,
							'min' => 0,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'sliders_dots_offset_ver',
				[
					'label'     => esc_html__( 'Spacing Top', 'razzi' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 100,
							'min' => - 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->end_popover();

			$this->start_controls_tabs( 'sliders_dots_normal_settings' );

			$this->start_controls_tab( 'sliders_dots_normal', [ 'label' => esc_html__( 'Normal', 'razzi' ) ] );

			$this->add_control(
				'sliders_dots_bgcolor',
				[
					'label'     => esc_html__( 'Background Color', 'razzi' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .swiper-pagination-bullet:before' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab( 'sliders_dots_active', [ 'label' => esc_html__( 'Active', 'razzi' ) ] );

			$this->add_control(
				'sliders_dots_ac_bgcolor',
				[
					'label'     => esc_html__( 'Background Color', 'razzi' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .razzi-deals-carousel .swiper-pagination-bullet-active:before, {{WRAPPER}} .razzi-deals-carousel .swiper-pagination-bullet:hover:before' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$nav        = $settings['navigation'];
		$nav_tablet = empty( $settings['navigation_tablet'] ) ? $nav : $settings['navigation_tablet'];
		$nav_mobile = empty( $settings['navigation_mobile'] ) ? $nav : $settings['navigation_mobile'];

		$this->add_render_attribute(
			'wrapper', 'class', [
				'razzi-deals-carousel razzi-swiper-carousel-elementor swiper-container ',
				'razzi-swiper-carousel-elementor ',
				'navigation-' . $nav,
				'navigation-tablet-' . $nav_tablet,
				'navigation-mobile-' . $nav_mobile,
			]
		);

		$content = array();
		$countdown_sliders = $settings['countdown_slider'];

		$dataText = \Razzi\Addons\Helper::get_countdown_texts();

		$this->add_render_attribute( 'countdown-slider', 'data-text', wp_json_encode( $dataText ) );
		$current  = strtotime( current_time( 'Y/m/d H:i:s' ) );

		foreach ( $countdown_sliders as $index => $item ) {
			$time = $button = '';
			$link_key = 'link_' . $index;

			$expired = ! empty($item['due_date']) ? strtotime( $item['due_date'] ) : 0;
			$expired = $expired > $current ? $expired - $current : 0;
			$expired = apply_filters( 'razzi_countdown_shortcode_second', $expired );

			if ( $expired > 0 ) {
				$time = sprintf(
					'<div class="countdown-content razzi-countdown" data-expire="%s" %s></div>',
					$expired,
					$this->get_render_attribute_string( 'countdown-slider' )
				);
			}

			if ( $item['button_text'] ) {
				$text = $item['button_text'] . \Razzi\Addons\Helper::get_svg('arrow-right');
				$button = Helper::control_url( $link_key, $item['button_link'], $text, [ 'class' => 'razzi-button' ] );
			}

			$content[] = sprintf(
							'<div class="elementor-repeater-item-%s countdown-slider swiper-slide">
								<div class="countdown-bg"></div>
								<div class="countdown-item">
									<div class="countdown-title">%s</div>
									%s
									<div class="countdown-button">%s</div>
								</div>
							</div>',
							$item['_id'],
							$item['title'],
							$time,
							$button
						);
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div class="linked-deals-carousel">
				<div class="razzi-deals-carousel__inner swiper-wrapper">
					<?php echo implode( '',  $content ); ?>
				</div>
			</div>
        </div>
		<?php
	}
}