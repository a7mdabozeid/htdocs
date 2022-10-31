<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Box_Shadow;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pricing Table widget
 */
class Pricing_Table extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-pricing-table';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Pricing Table', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-price-table';
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
			[ 'label' => esc_html__( 'Pricing Table', 'razzi' ) ]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'razzi' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'This is title', 'razzi' ),
			]
		);

		$this->add_control(
			'price',
			[
				'label'       => esc_html__( 'Price', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your price', 'razzi' ),
				'default'     => __( '$29.00', 'razzi' ),
			]
		);

		$this->add_control(
			'after_price',
			[
				'label'       => esc_html__( 'After Price', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your text', 'razzi' ),
				'default'     => __( '/per one', 'razzi' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label'   => esc_html__( 'Description', 'razzi' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '<ul class="razzi-checkmark-lists">
											<li>Behold in creature likeness </li>
											<li class="wrong">To hath for fly land </li>
											<li class="wrong">Third under god above bearing</li>
										</ul>',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => esc_html__( 'Button Text', 'razzi' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Button Text', 'razzi' ),
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
			'show_featured',
			[
				'label'        => esc_html__( 'Show Featured', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'razzi' ),
				'label_on'     => __( 'On', 'razzi' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'featured_text',
			[
				'label'     => esc_html__( 'Featured Text', 'razzi' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Popular', 'razzi' ),
				'condition' => [
					'show_featured' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_style() {
		$this->section_style_pricing();
		$this->section_style_header();
		$this->section_style_content();
	}

	protected function section_style_pricing() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Pricing Table', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label'      => esc_html__( 'Width', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .razzi-pricing-table' => 'width: {{SIZE}}%',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_shadow',
				'label'    => __( 'Box Shadow', 'razzi' ),
				'selector' => '{{WRAPPER}} .razzi-pricing-table',
			]
		);

		$this->add_control(
			'content_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function section_style_header() {
		$this->start_controls_section(
			'section_content_header',
			[
				'label' => __( 'Header', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'summary_header_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-pricing-table__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'header_content_border_color',
			[
				'label'     => __( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table__header' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Title
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
					'{{WRAPPER}} .razzi-pricing-table .pricing-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-pricing-table .pricing-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_style_price',
			[
				'label' => __( 'Price', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .razzi-pricing-table .pricing-price',
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_style_after_price',
			[
				'label' => __( 'After Price', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'afprice_typography',
				'selector' => '{{WRAPPER}} .razzi-pricing-table .pricing-afprice',
			]
		);

		$this->add_control(
			'afprice_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-afprice' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_style_featured',
			[
				'label' => __( 'Featured', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_badges_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-badges' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badges_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-badges' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'sale_badges_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-badges' => 'background-color: {{VALUE}}',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function section_style_content() {
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'summary_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-pricing-table__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_style_desc',
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
					'{{WRAPPER}} .razzi-pricing-table .pricing-desc' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-pricing-table .pricing-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .pricing-desc' => 'color: {{VALUE}}',

				],
			]
		);

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
					'{{WRAPPER}} .razzi-pricing-table .button-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .razzi-pricing-table .button-text',
			]
		);

		$this->start_controls_tabs(
			'button_tabs_content'
		);

		// Title
		$this->start_controls_tab(
			'button_style_normal',
			[
				'label' => __( 'Title', 'razzi' ),
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .button-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_border_color',
			[
				'label'     => __( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .button-text' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .button-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Title
		$this->start_controls_tab(
			'button_style_hover',
			[
				'label' => __( 'Hover', 'razzi' ),
			]
		);


		$this->add_control(
			'hover_link_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .button-text:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_link_border_color',
			[
				'label'     => __( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .button-text:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_link_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-pricing-table .button-text:hover' => 'background-color: {{VALUE}};',
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

		$classes = [
			'razzi-pricing-table',
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );


		$button_text = $settings['button_text'] ? sprintf( '<span class="razzi-button button-outline btn-primary button-text">%s</span>', $settings['button_text'] ) : '';

		if ( $settings['link']['url'] ) :
			$button_text = Helper::control_url( 'btn', $settings['link'], $button_text, [ 'class' => 'button-link' ] );
		else:
			$button_text = sprintf( '<div class="button-link">%s</div>', $button_text );
		endif;

		$title         = $settings['title'] ? sprintf( '<h2 class="pricing-title">%s</h2>', $settings['title'] ) : '';
		$desc          = $settings['desc'] ? sprintf( '<div class="pricing-desc">%s</div>', $settings['desc'] ) : '';
		$featured_text = $settings['featured_text'] ? sprintf( '<span class="pricing-badges">%s</span>', $settings['featured_text'] ) : '';

		// price
		$price       = $settings['price'] ? sprintf( '<div class="pricing-price">%s</div>', $settings['price'] ) : '';
		$after_price = $settings['after_price'] ? sprintf( '<div class="pricing-afprice">%s</div>', $settings['after_price'] ) : '';

		$html_price = $price == '' && $after_price == '' ? '' : sprintf( '<div class="pricing-header__price">%s %s</div>', $price, $after_price );

		$output = '<div class="razzi-pricing-table__header">';
		$output .= $title;
		$output .= $html_price;
		$output .= $featured_text;
		$output .= '</div>';
		$output .= '<div class="razzi-pricing-table__content">';
		$output .= $desc;
		$output .= $button_text;
		$output .= '</div>';

		echo sprintf(
			'<div %s> %s</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$output
		);
	}
}