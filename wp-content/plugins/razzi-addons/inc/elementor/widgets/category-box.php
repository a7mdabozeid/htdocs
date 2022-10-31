<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Categor Box widget
 */
class Category_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-category-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Category Box', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-product-categories';
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
			[ 'label' => esc_html__( 'Category Box', 'razzi' ) ]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/585x650/f5f5f5?text=Image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label' => esc_html__( 'Content', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is title', 'razzi' ),
			]
		);

		$this->add_control(
			'link', [
				'label'         => esc_html__( 'Link', 'razzi' ),
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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'sub_cat',
			[
				'label'       => esc_html__( 'Category Name', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is cat name', 'razzi' ),
			]
		);

		$repeater->add_control(
			'sub_link', [
				'label'         => esc_html__( 'Link', 'razzi' ),
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
			'sub_categories',
			[
				'label'         => esc_html__( 'Sub Categories', 'razzi' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'sub_cat' => esc_html__( 'This is cat name 1', 'razzi' ),

					],[
						'sub_cat' => esc_html__( 'This is cat name 2', 'razzi' ),

					],[
						'sub_cat' => esc_html__( 'This is cat name 3', 'razzi' ),

					],[
						'sub_cat' => esc_html__( 'This is cat name 4', 'razzi' ),

					],
				],
				'title_field'   => '{{{ sub_cat }}}',
				'prevent_empty' => false,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 */

	protected function section_style() {
		$this->section_style_category();
		$this->section_style_image();
		$this->section_style_content();
	}

	protected function section_style_category() {
		$this->start_controls_section(
			'section_style_category',
			[
				'label' => __( 'Category Box', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_box_shadow',
				'label' => __( 'Box Shadow', 'razzi' ),
				'selector' => '{{WRAPPER}} .razzi-category-box',
			]
		);

		$this->add_control(
			'content_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}.razzi-position-next-image .razzi-category-box' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.razzi-position-under-image .razzi-category-box__content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_style_image() {
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_max_width',
			[
				'label'     => esc_html__( 'Max Width', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'     => [
					'px' => [
						'max' => 500,
						'min' => 0,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.razzi-position-next-image .razzi-category-box__img' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing_bottom',
			[
				'label'     => esc_html__( 'Spacing Bottom', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'     => [
					'px' => [
						'max' => 500,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.razzi-position-under-image .razzi-category-box__img' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_box_position' => 'under-image',
				],
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
			'content_box_position',
			[
				'label' => __( 'Content Position', 'razzi' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'next-image',
				'options' => [
					'under-image' 	=> __( 'Under Image', 'razzi' ),
					'next-image' 	=> __( 'Next To Image', 'razzi' ),
				],
				'prefix_class' 	=> 'razzi-position%s-',
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'default' => 'next-image',
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'default' => 'next-image',
					],
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-category-box__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' 	=> 'before',
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

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Spacing Bottom', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-category-box__content .category-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-category-box__content .category-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-category-box__content .category-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Btn
		$this->start_controls_tab(
			'content_subcat',
			[
				'label' => __( 'Sub Cat', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subcat_typography',
				'selector' => '{{WRAPPER}} .razzi-category-box__content .sub-cat',
			]
		);

		$this->add_control(
			'subcat_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-category-box__content .sub-cat' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'subcat_spacing',
			[
				'label'     => esc_html__( 'Spacing Bottom', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-category-box__content .sub-cat:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
			'razzi-category-box',
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );

			$image = $image ? '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $image . '</a>' : '';
			$settings['title'] = $settings['title'] ? '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $settings['title'] . '</a>' : '';
		}

		$image = $image ? sprintf('<div class="razzi-category-box__img">%s</div>',$image) : '';
		$title = $settings['title'] ? sprintf('<h4 class="category-title">%s</h4>',$settings['title']) : '';

		$els = $settings['sub_categories'];
		$output =  array();

		$icon = \Razzi\Addons\Helper::get_svg('arrow-right', 'razzi-icon');

		if ( ! empty ( $els ) ) {
			$output[] = '<ul class ="sub-cats">';

			foreach ( $els as $index => $item ) {

				if ( ! empty( $item['sub_link']['url'] ) ) {
					$item['sub_cat'] = '<a href='.$item['sub_link']['url'] .'>'.$item['sub_cat'].'</a>';
				}

				$output[] = $item['sub_cat'] ? sprintf('<li class="sub-cat">%s %s</li>',$item['sub_cat'], $icon) : '';
			}

			$output[] = '</ul>';
		}

	 	$content = sprintf('<div class="razzi-category-box__content">%s %s</div>', $title, implode('', $output));

		echo sprintf(
			'<div %s> %s %s</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$image,
			$content
		);
	}
}