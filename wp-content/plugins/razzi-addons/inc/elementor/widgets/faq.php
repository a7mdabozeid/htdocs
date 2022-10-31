<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tab List widget
 */
class Faq extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-faq';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - FAQs', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-info-circle-o';
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

		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'FAQs', 'razzi' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is title', 'razzi' ),
				'label_block' => true,
			]
		);

		$repeater -> add_control(
			'desc',
			[
				'label'       => esc_html__( 'Content', 'razzi' ),
				'type'        => Controls_Manager::WYSIWYG ,
				'default'     => esc_html__( 'Event Note', 'razzi' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'elements',
			[
				'label'   => esc_html__( 'FAQs Lists', 'razzi' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'title'                 => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					],
					[
						'title'         => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					],
					[
						'title'         => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					],
					[
						'title'         => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					],
					[
						'title'         => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					],
				],
				'title_field'   => '{{{ title }}}',

			]
		);

		$this->end_controls_section();

	}

	/**
	 * Section Style
	 */
	protected function section_style() {
		$this->section_style_faq();
		$this->section_style_content();
	}

	protected function section_style_faq() {
		// Content
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'FAQs', 'razzi' ),
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
					'{{WRAPPER}} .razzi-faq .box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_bk_color',
			[
				'label'        => esc_html__( 'Background Color', 'razzi' ),
				'type'         => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .box-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_style',
			[
				'label'        => __( 'Border', 'razzi' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'razzi' ),
				'label_on'     => __( 'Custom', 'razzi' ),
				'return_value' => 'yes',
			]
		);
		$this->start_popover();

		$this->add_control(
			'content_border_style',
			[
				'label'     => esc_html__( 'Border Style', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'dotted' => esc_html__( 'Dotted', 'razzi' ),
					'dashed' => esc_html__( 'Dashed', 'razzi' ),
					'solid'  => esc_html__( 'Solid', 'razzi' ),
					'none'   => esc_html__( 'None', 'razzi' ),
				],
				'default'   => '',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .box-content' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_border_width',
			[
				'label'       => __( 'Border Width', 'razzi' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px' ],
				'selectors'   => [
					'{{WRAPPER}} .razzi-faq .box-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_border_color',
			[
				'label'     => __( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .box-content' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator'    => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_width',
			[
				'label'     => __( 'Font Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .faq-title .razzi-svg-icon ' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_normal_color',
			[
				'label'        => esc_html__( 'Color', 'razzi' ),
				'type'         => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .faq-title .razzi-svg-icon  ' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function section_style_content() {
		// Content
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'style_tabs_content'
		);

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
				'selector' => '{{WRAPPER}} .razzi-faq .faq-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .faq-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Desc
		$this->start_controls_tab(
			'content_desc',
			[
				'label' => __( 'Description', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'note_typography',
				'selector' => '{{WRAPPER}} .razzi-faq .faq-desc',
			]
		);

		$this->add_responsive_control(
			'note_spacing',
			[
				'label'     => __( 'Spacing Top', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .faq-desc' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .faq-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'active_style',
			[
				'label'     => esc_html__( 'Active', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tit_ac_color',
			[
				'label'     => __( 'Title Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-faq .box-content.active .faq-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'arrows_border_shadow',
				'selector' => '{{WRAPPER}} .razzi-faq .box-content.active',
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
			'razzi-faq'
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$output_events = array();

		foreach (  $settings["elements"]  as $index => $item ) {

			$title     = $item["title"] ? '<h5 class="faq-title">' . $item["title"] . \Razzi\Addons\Helper::get_svg('chevron-bottom') . '</h5>' : '';
			$desc = $item['desc'] ? sprintf( '<div class="faq-desc">%s</div>', $item["desc"]  ) : '';

			$add_class = $index == 0 ? 'active' : '';

			$output_events[] = $title == '' && $desc == '' ? '' : sprintf( '<div class="box-content %s">%s %s</div>',esc_attr($add_class),$title ,$desc  );
		}

		echo sprintf(
			'<div %s>%s</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			implode( '', $output_events )
		);
	}
}