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
 * Featured Content widget
 */
class Featured_Content extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-featured-content';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Featured Content', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-time-line';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'razzi' ];
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
			[ 'label' => esc_html__( 'Content', 'razzi' ) ]
		);



		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/443x566/f1f1f1?text=Image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'divider_hr_1',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->start_controls_tabs( 'featureds_repeater' );

		$repeater->start_controls_tab( 'text_content', [ 'label' => esc_html__( 'Line Text', 'razzi' ) ] );

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is the title', 'razzi' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is the desc', 'razzi' ),
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'featured_position_content', [ 'label' => esc_html__( 'Line Position', 'razzi' ) ] );

		$repeater->add_responsive_control(
			'featured_content_position_top',
			[
				'label'          => esc_html__( 'Spacing Top', 'razzi' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} .razzi-featured-content {{CURRENT_ITEM}}.featured-box' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'featured_content_position_bottom',
			[
				'label'          => esc_html__( 'Spacing Bottom', 'razzi' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} .razzi-featured-content {{CURRENT_ITEM}}.featured-box' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'featured_content_width',
			[
				'label'          => esc_html__( 'Line Width', 'razzi' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} .razzi-featured-content {{CURRENT_ITEM}} .featured-control' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->end_controls_tab();

		$this->add_control(
			'featureds_el',
			[
				'label'      => esc_html__( 'Lines Lists', 'razzi' ),
				'type'       => Controls_Manager::REPEATER,
				'show_label' => true,
				'fields'     => $repeater->get_controls(),
				'default'    => [
					[
						'title'          => esc_html__( 'This is title 1', 'razzi' ),
					],[
						'title'          => esc_html__( 'This is title 2', 'razzi' ),
					],[
						'title'          => esc_html__( 'This is title 3', 'razzi' ),
					],[
						'title'          => esc_html__( 'This is title 4', 'razzi' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Section Style
	 */

	protected function section_style() {
		// Content
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'featured_heading_style',
			[
				'label' => esc_html__( 'Line', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'featured_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-featured-content .featured-control' => '--rz-background-color-primary: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_heading_style',
			[
				'label' => esc_html__( 'Title', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
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
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-featured-content .featured-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-featured-content .featured-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-featured-content .featured-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc_heading_style',
			[
				'label' => esc_html__( 'Desc', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-featured-content .featured-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-featured-content .featured-desc' => 'color: {{VALUE}};',
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
			'razzi-featured-content',
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
		$image = $image ? sprintf('<div class="box-thumbnail">%s</div>',$image) : '';

		$itemsTotal = count($settings['featureds_el']);

		$output_left    = $output_right = [];

		foreach ( $settings['featureds_el'] as $index => $value  ) {

			$title = $value['title'] ? sprintf('<h5 class="featured-title">%s</h5>',$value['title']) : '';
			$desc = $value['desc'] ? sprintf('<div class="featured-desc">%s</div>',$value['desc']) : '';
			$html_box = $title == '' && $desc == '' ? '' : sprintf('<div class="featured-box elementor-repeater-item-' . $value['_id'] . '">%s %s <div class="featured-control"></div></div>',$title, $desc );

			if ($index < $itemsTotal/2) {
				$output_left[] = $html_box;
			} else {
				$output_right[] = $html_box;
			}
		}

		// Content html
		$output_html = sprintf('<div class="featured-wrapper featured-wrapper__left text-right col-flex col-flex-md-3 col-flex-sm-3">%s</div>',implode('', $output_left));
		$output_html .= sprintf('<div class="featured-img-wrapper featured-wrapper__center text-center col-flex col-flex-md-6 col-flex-sm-6">%s</div>', $image);
		$output_html .= sprintf('<div class="featured-wrapper featured-wrapper__right text-left col-flex col-flex-md-3 col-flex-sm-3">%s</div>',implode('', $output_right));

		echo sprintf(
			'<div %s><div class="row-flex"> %s</div></div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$output_html
		);
	}
}