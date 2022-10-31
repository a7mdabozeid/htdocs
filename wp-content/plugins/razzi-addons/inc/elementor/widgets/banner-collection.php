<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner Large widget
 */
class Banner_Collection extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-banner-collection';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Banner Collection', 'razzi' );
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
			'section_content_img',
			[ 'label' => esc_html__( 'Banner Collection', 'razzi' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/476X634/cccccc?text=476x634',
				],
			]
		);

		$this->add_control(
			'images_list',
			[
				'label'         => esc_html__( 'Images List', 'razzi' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'image' => [
							'url' => 'https://via.placeholder.com/476X634/cccccc?text=476x634',
						],
					],
					[
						'image' => [
							'url' => 'https://via.placeholder.com/476X634/cccccc?text=476x634',
						],
					],
					[
						'image' => [
							'url' => 'https://via.placeholder.com/476X634/cccccc?text=476x634',
						],
					],
					[
						'image' => [
							'url' => 'https://via.placeholder.com/476X634/cccccc?text=476x634',
						],
					],

				],
				'prevent_empty' => false
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
			'title',
			[
				'label'   => esc_html__( 'Title', 'razzi' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'This is title', 'razzi' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label'   => esc_html__( 'Description', 'razzi' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'This is description', 'razzi' ),
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
			'link_type',
			[
				'label'     => esc_html__( 'Link Type', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'only' => esc_html__( 'Only button text', 'razzi' ),
					'all'  => esc_html__( 'All banner', 'razzi' ),
				],
				'default'   => 'all',
				'toggle'    => false,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'columns',
			[
				'label'           => esc_html__( 'Columns', 'razzi' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 6,
				'default' => 4,
				'tablet_default'  => 2,
				'mobile_default'  => 2,
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

		$this->end_controls_section();

	}

	/**
	 * Section Style
	 */

	protected function section_style() {
		$this->start_controls_section(
			'section_content_img_style',
			[
				'label' => __( 'Banner Collection', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'images_gap',
			[
				'label'      => __( 'Images Gap', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-clt__images'      => 'margin-left: -{{SIZE}}{{UNIT}};margin-right: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .razzi-banner-clt__images-item' => 'padding: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_box_heading',
			[
				'label'     => esc_html__( 'Content Box', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-clt__content-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_box_position',
			[
				'label'      => esc_html__( 'Horizontal Position', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [],
				'selectors'  => [
					'{{WRAPPER}} .razzi-banner-clt__content' => 'bottom: {{SIZE}}%; transform: translateY({{SIZE}}%)',
				],
			]
		);

		$this->add_control(
			'content_tile_heading',
			[
				'label'     => esc_html__( 'Title', 'razzi' ),
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
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-clt__content-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-clt__content-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-collection .banner-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_desc_heading',
			[
				'label'     => esc_html__( 'Description', 'razzi' ),
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
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-clt__content-desc' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-clt__content-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-clt__content-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_button_heading',
			[
				'label'     => esc_html__( 'Button', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-clt__content-button' => 'transform: translateY({{SIZE}}{{UNIT}})',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .razzi-banner-clt__content-button',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-clt__content-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-clt__content-button' => 'background-color: {{VALUE}};',
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
			'razzi-banner-collection',
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$images = array();

		$els           = $settings['images_list'];
		$item_lenght   = 0;

		$columns = isset( $settings['columns'] ) ? $settings['columns'] : '6';
		$columns_tablet = isset( $settings['columns_tablet'] ) && ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : $settings['columns'];
		$columns_mobile = isset( $settings['columns_mobile'] ) && ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : $settings['columns'];

		$image_classes = $columns != '5' ? 'col-flex-md-'.(12/$columns) : 'col-flex-md-1-5';
		$image_classes .= $columns_tablet != '5' ? ' col-flex-sm-'.(12/$columns_tablet) : ' col-flex-sm-1-5';
		$image_classes .= $columns_mobile != '5' ? ' col-flex-xs-'.(12/$columns_mobile) : ' col-flex-xs-1-5';

		if ( ! empty ( $els ) ) {
			foreach ( $els as $index => $item ) {
				$settings['image'] = $item['image'];

				$image    = Group_Control_Image_Size::get_attachment_image_html( $settings );
				$images[] = $image ? sprintf( '<div class="razzi-banner-clt__images-item %s">%s</div>', $image_classes, $image ) : '';

				$item_lenght ++;
			}
		}

		$output_img = sprintf( '<div class="razzi-banner-clt__images">%s</div>', implode( '', $images ) );

		//  Content
		$link_icon = $settings['show_default_icon'] ? \Razzi\Addons\Helper::get_svg( 'arrow-right', 'razzi-icon' ) : '';

		$button_text = $settings['button_text'] ? sprintf( '%s %s', $settings['button_text'], $link_icon ) : '';
		$btn_full    = '';

		if ( $settings['link']['url'] ) :
			$btn_full    = $settings['link_type'] == 'all' ? Helper::control_url( 'btn_link', $settings['link'], '', [ 'class' => 'razzi-banner-clt-link' ] ) : '';
			$button_text = Helper::control_url( 'btn_link_2', $settings['link'], $button_text, [ 'class' => 'razzi-banner-clt__content-button razzi-button' ] );

		else:
			$button_text = sprintf( '<div class="razzi-banner-clt__content-button">%s</div>', $button_text );
		endif;

		$title = $settings['title'] ? sprintf( '<h3 class="razzi-banner-clt__content-title">%s</h3>', $settings['title'] ) : '';
		$desc  = $settings['desc'] ? sprintf( '<div class="razzi-banner-clt__content-desc">%s</div>', $settings['desc'] ) : '';

		$output_text = sprintf( '<div class="razzi-banner-clt__content"><div class="razzi-banner-clt__content-inner">%s %s %s</div></div>', $title, $desc, $button_text );

		echo sprintf(
			'<div %s>%s %s %s</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$output_img, $output_text, $btn_full
		);
	}
}
