<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Testimonials Carousel widget
 */
class Testimonials_Carousel_2 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-testimonials-carousel-2';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Testimonials Carousel 2', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
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
			'razzi-frontend'
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
		$this->content_settings_controls();
		$this->carousel_settings_controls();
	}

	protected function content_settings_controls() {
		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Content', 'razzi' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->start_controls_tabs( 'banner_content_settings' );

		$repeater->start_controls_tab( 'content_other', [ 'label' => esc_html__( 'Image', 'razzi' ) ] );

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/80x80/f5f5f5?text=80x80',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'content_text', [ 'label' => esc_html__( 'Content', 'razzi' ) ] );

		$repeater->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is subtitle', 'razzi' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is title', 'razzi' ),
			]
		);

		$repeater->add_control(
			'rate',
			[
				'label' => __( 'Rate', 'razzi' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 5,
				'step' => 1,
				'default' => 5,
			]
		);

		$repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is desc', 'razzi' ),
			]
		);

		$repeater->add_control(
			'customer',
			[
				'label'       => esc_html__( 'Customer', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Customer Name', 'razzi' ),
			]
		);

		$repeater->add_control(
			'date',
			[
				'label'       => esc_html__( 'Date', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is Date', 'razzi' ),
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'elements',
			[
				'label'         => esc_html__( 'Testimonials List', 'razzi' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'title' => esc_html__( 'This is the title', 'razzi' ),
					],[
						'title' => esc_html__( 'This is the title', 'razzi' ),
					],[
						'title' => esc_html__( 'This is the title', 'razzi' ),
					],[
						'title' => esc_html__( 'This is the title', 'razzi' ),
					],[
						'title' => esc_html__( 'This is the title', 'razzi' ),
					],
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false
			]
		);

		$this->end_controls_section();
	}

	protected function carousel_settings_controls() {
		// Carousel Settings
		$this->start_controls_section(
			'section_carousel_settings',
			[ 'label' => esc_html__( 'Carousel Settings', 'razzi' ) ]
		);
		$this->add_responsive_control(
			'slidesToShow',
			[
				'label'   => esc_html__( 'Slides to show', 'razzi' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 7,
				'desktop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'frontend_available' => true,
			]
		);
		$this->add_responsive_control(
			'slidesToScroll',
			[
				'label'   => esc_html__( 'Slides to scroll', 'razzi' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 5,
				'desktop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'navigation',
			[
				'label'     => esc_html__( 'Navigation', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options' => [
					'none'   => esc_html__( 'None', 'razzi' ),
					'dots' 	 => esc_html__( 'Dots', 'razzi' ),
				],
				'default' => 'dots',
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'     => __( 'Infinite', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'razzi' ),
				'label_on'  => __( 'On', 'razzi' ),
				'return_value' => 'yes',
				'default'   => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'     => __( 'Autoplay', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'razzi' ),
				'label_on'  => __( 'On', 'razzi' ),
				'return_value' => 'yes',
				'default'   => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'   => __( 'Autoplay Speed (in ms)', 'razzi' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1000,
				'min'     => 100,
				'step'    => 100,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section(); // End Carousel Settings
	}

	/**
	 * Section Style
	 */
	protected function section_style() {
		$this->section_content_style();
		$this->section_carousel_style();
	}

	/**
	 * Element in Tab Style
	 *
	 * Title
	 */
	protected function section_content_style() {
		// Content
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_style',
			[
				'label' => esc_html__( 'Items', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .testimonials-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_border_color',
			[
				'label'     => __( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .testimonials-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_spacing',
			[
				'label'     => esc_html__( 'Header Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'elements_name',
			[
				'label' => esc_html__( 'Elements', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// TAB 1
		$this->start_controls_tabs(
			'style_tabs_header_content'
		);

		$this->start_controls_tab(
			'content_style_img',
			[
				'label' => __( 'Image', 'razzi' ),
			]
		);

		$this->add_responsive_control(
			'img_spacing',
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .testi-image' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'content_style_subtitle',
			[
				'label' => __( 'Subtitle', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .razzi-testimonials-carousel-2__header .subtitle',
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_spacing',
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();


		// Title
		$this->start_controls_tab(
			'content_style_title',
			[
				'label' => __( 'Title', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-testimonials-carousel-2__header .testi-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .testi-title' => 'color: {{VALUE}};',
				],
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .testi-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		// Rating
		$this->start_controls_tab(
			'content_style_rate',
			[
				'label' => __( 'Rating', 'razzi' ),
			]
		);

		$this->add_control(
			'staring_font',
			[
				'label'     => esc_html__( 'Font Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .razzi-svg-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'staring_color',
			[
				'label'     => __( 'Normal Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .razzi-svg-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'staring_color_ac',
			[
				'label'     => __( 'Active Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__header .razzi-svg-icon.rate-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();


		$this->end_controls_tabs();

		$this->add_control(
			'testi_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		// TAB 2
		$this->start_controls_tabs(
			'style_tabs_footer_content'
		);

		// Desc
		$this->start_controls_tab(
			'content_desc',
			[
				'label' => __( 'Desc', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-testimonials-carousel-2__footer .testi-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__footer .testi-desc' => 'color: {{VALUE}};',
				],
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2__footer .testi-desc' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'content_meta',
			[
				'label' => __( 'Meta', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .razzi-testimonials-carousel-2__footer .testi-meta',
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2__footer .testi-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();

	}

	protected function section_carousel_style() {
		$this->start_controls_section(
			'section_carousel_style',
			[
				'label' => esc_html__( 'Carousel Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dots_style_divider',
			[
				'label' => esc_html__( 'Dots', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		// Arrows
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
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
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .swiper-pagination' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_popover();

		$this->start_controls_tabs( 'sliders_normal_settings' );

		$this->start_controls_tab( 'sliders_normal', [ 'label' => esc_html__( 'Normal', 'razzi' ) ] );

		$this->add_control(
			'sliders_dots_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .swiper-pagination-bullet:before' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .razzi-testimonials-carousel-2 .swiper-pagination-bullet-active:before, {{WRAPPER}} .razzi-testimonials-carousel-2 .swiper-pagination-bullet:hover:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render circle box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$nav        = $settings['navigation'];
		$nav_tablet = empty( $settings['navigation_tablet'] ) ? $nav : $settings['navigation_tablet'];
		$nav_mobile = empty( $settings['navigation_mobile'] ) ? $nav : $settings['navigation_mobile'];

		$classes = [
			'razzi-testimonials-carousel-2 swiper-container',
			'razzi-swiper-carousel-elementor',
			'navigation-' . $nav,
			'navigation-tablet-' . $nav_tablet,
			'navigation-mobile-' . $nav_mobile,
		];

		$swiper_settings = [
			'slidesToShow'					=> $settings['slidesToShow'],
			'slidesToScroll' 				=> $settings['slidesToScroll'],
			'loop' 							=> $settings['infinite'],
			'autoplay' 						=> $settings['autoplay'],
			'speed' 						=> $settings['autoplay_speed'],
		];

		if (isset($settings['slidesToShow_tablet']) && $settings['slidesToShow_tablet']) {
			$swiper_settings['slidesToShow_tablet'] = absint( $settings['slidesToShow_tablet'] );
		}

		if (isset($settings['slidesToScroll_tablet']) && $settings['slidesToScroll_tablet']) {
			$swiper_settings['slidesToScroll_tablet'] = absint( $settings['slidesToScroll_tablet'] );
		}

		if (isset($settings['slidesToShow_mobile']) && $settings['slidesToShow_mobile']) {
			$swiper_settings['slidesToShow_mobile'] = absint( $settings['slidesToShow_mobile'] );
		}

		if (isset($settings['slidesToScroll_mobile']) && $settings['slidesToScroll_mobile']) {
			$swiper_settings['slidesToScroll_mobile'] = absint( $settings['slidesToScroll_mobile'] );
		}

		$this->add_render_attribute( 'wrapper', 'data-swiper', wp_json_encode( $swiper_settings ) );

		$output =  array();

		$els = $settings['elements'];
		$item_lenght = 0;

		if ( ! empty ( $els ) ) {
			foreach ( $els as $index => $item ) {

				$settings['image']      = $item['image'];
				$settings['image_size'] = 'thumbnail';

				$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
				$image = $image ? sprintf('<div class="testi-image">%s</div>',$image) : '';

				$subtitle = $item['subtitle'] ? sprintf('<span class="subtitle">%s</span>',$item['subtitle']) : '';
				$title = $item['title'] ? sprintf('<h6 class="testi-title">%s</h6>',$item['title']) : '';
				$desc = $item['desc'] ? sprintf('<div class="testi-desc">%s</div>',$item['desc']) : '';

				$customer = $item['customer'] ? sprintf('<span class="testi-customer">%s</span>',$item['customer']) : '';
				$date = $item['date'] ? sprintf('<span class="testi-date">%s</span>',$item['date']) : '';
				$meta_html = $customer == '' && $date == '' ? '' : sprintf('<div class="testi-meta">%s %s</div>',$customer,$date );

				// rate
				$rate_content = [];

				$rate_content[] = '<div class="testi-rate">';
				for ($i=0; $i < 5 ; $i++) {
					if( $i < intval($item['rate'])){
						$rate_content[] = \Razzi\Addons\Helper::get_svg('staring', 'rate-active', 'widget');
					} else {
						$rate_content[] = \Razzi\Addons\Helper::get_svg('staring', '', 'widget');
					}
				}
				$rate_content[] = '</div>';

				$output_content = [];
				$output_content[] = '<div class="razzi-testimonials-carousel-2__header">';
				$output_content[] = $image;
				$output_content[] = '<div class="header-content">';
				$output_content[] = $subtitle;
				$output_content[] = $title;
				$output_content[] = implode('', $rate_content);
				$output_content[] = '</div>';
				$output_content[] = '</div>';
				$output_content[] = '<div class="razzi-testimonials-carousel-2__footer">';
				$output_content[] = $desc;
				$output_content[] = $meta_html;
				$output_content[] = '</div>';

				$output[] = sprintf( '<div class="testimonials-item swiper-slide">%s</div>', implode('', $output_content) );

				$item_lenght++;
			}

		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		echo sprintf(
			'<div %s><div class="razzi-testimonials-carousel-2__inner swiper-wrapper"> %s</div><div class="swiper-pagination"></div></div>',
			$this->get_render_attribute_string( 'wrapper' ),
			implode('', $output)
		);
	}
}