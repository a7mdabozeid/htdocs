<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Image Box widget
 */
class Image_Box extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-image-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Image Box', 'razzi' );
	}

	/**
	 * Retrieve the widget circle.
	 *
	 * @return string Widget circle.
	 */
	public function get_icon() {
		return 'eicon-image-box';
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
			[ 'label' => esc_html__( 'Image Box', 'razzi' ) ]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/270x210/f1f1f1?text=Image',
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
			'image_position',
			[
				'label' => esc_html__( 'Image Position', 'razzi' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' 	=> esc_html__( 'Top', 'razzi' ),
					'left' 	=> esc_html__( 'Left', 'razzi' ),
				],
				'default' => 'top',
				'prefix_class' => 'razzi-image-box-position--',
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label' => __( 'Icon', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'razzi' ),
				'type' => Controls_Manager::SELECT,

				'options' => [
					'left' => esc_html__( 'Left', 'razzi' ),
					'right' 	=> esc_html__( 'Right', 'razzi' ),
				],
				'default' => 'left',
				'conditions' => [
					'terms' => [
						[
							'name' => 'image_position',
							'value' => 'top',
						],
					],
				],
			]
		);


		$this->add_control(
			'icon_type',
			[
				'label' => esc_html__( 'Icon type', 'razzi' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => esc_html__( 'Image', 'razzi' ),
					'icon' 	=> esc_html__( 'Icon', 'razzi' ),
					'external' 	=> esc_html__( 'External', 'razzi' ),
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon', 'razzi' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [],
				'conditions' => [
					'terms' => [
						[
							'name' => 'icon_type',
							'value' => 'icon',
						],
					],
				],
			]
		);

		$this->add_control(
			'image_icon',
			[
				'label' => esc_html__( 'Choose Image', 'razzi' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'icon_type',
							'value' => 'image',
						],
					],
				],
			]
		);

		$this->add_control(
			'external_url',
			[
				'label' => esc_html__( 'External URL', 'razzi' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'icon_type',
							'value' => 'external',
						],
					],
				],
			]
		);

		$this->add_control(
			'text',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'separator' => 'before',
				'default'     => esc_html__( 'This is the title', 'razzi' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
			]
		);

		$this->add_control(
			'number',
			[
				'label'       => esc_html__( 'Badge Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'razzi' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'razzi' ),
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
				'default' => 'only',
				'toggle'  => false,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Section Style
	 */
	protected function section_style() {
		$this->section_style_img_box();
		$this->section_style_image();
		$this->section_style_content();
		$this->section_style_badge();

	}

	protected function section_style_img_box() {

		$this->start_controls_section(
			'section_img_box_style',
			[
				'label' => __( 'Image Box', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'img_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-image-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_box_border',
				'label' => __( 'Border', 'razzi' ),
				'selector' => '{{WRAPPER}} .razzi-image-box',
			]
		);

		$this->add_control(
			'img_box_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_box_box_shadow',
				'label' => __( 'Box Shadow', 'razzi' ),
				'selector' => '{{WRAPPER}} .razzi-image-box',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_box_box_shadow_hover',
				'label' => __( 'Box Shadow Hover', 'razzi' ),
				'selector' => '{{WRAPPER}} .razzi-image-box:hover',
			]
		);

		$this->end_controls_section();

	}

	protected function section_style_image() {
		$this->start_controls_section(
			'section_img_style',
			[
				'label' => __( 'Image', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_zoom',
			[
				'label'        => esc_html__( 'Image Zoom', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'razzi' ),
				'label_on'     => __( 'On', 'razzi' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'link_type',
							'value' => 'only',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'image_max_width',
			[
				'label'     => esc_html__( 'Max Width', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-thumbnail' => 'max-width: {{SIZE}}{{UNIT}}',
				],
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
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.razzi-image-box-position--top .razzi-image-box .box-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.razzi-image-box-position--left .razzi-image-box .box-thumbnail' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'img_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-thumbnail .image-zoom' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_style_content() {
		$this->start_controls_section(
			'section_content_style',
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
					'{{WRAPPER}} .razzi-image-box .box-summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_style',
			[
				'label' => __( 'Icon', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
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
					'{{WRAPPER}} .razzi-image-box .razzi-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .razzi-image-box__icon-position--left .razzi-image-box .razzi-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .razzi-image-box__icon-position--right .razzi-image-box .razzi-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => esc_html__( 'Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .razzi-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .razzi-image-box .razzi-svg-image' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);


		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .razzi-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_box',
			[
				'label' => __( 'Text', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
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
					'{{WRAPPER}}.razzi-image-box-position--top .razzi-image-box .box-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-image-box .box-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc_box',
			[
				'label' => __( 'Description', 'razzi' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-image-box .box-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_style_badge() {
		$this->start_controls_section(
			'section_badge_style',
			[
				'label' => __( 'Badge', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'badge_min_width',
			[
				'label'     => esc_html__( 'Min Width', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%' ],
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-number' => 'min-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'badge_min_height',
			[
				'label'     => esc_html__( 'Min Height', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%' ],
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-number' => 'min-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'num_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'num_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-number' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_spacing_bottom',
			[
				'label'     => esc_html__( 'Spacing Top', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [],
				'range'     => [
					'px' => [
						'min' => -100,
						'max' => 200,
					],
				],
				'size_units'         => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-number' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_spacing_left',
			[
				'label'     => esc_html__( 'Spacing Right', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [],
				'range'     => [
					'px' => [
						'min' => -100,
						'max' => 200,
					],
				],
				'size_units'         => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .razzi-image-box .box-number' => 'right: {{SIZE}}{{UNIT}};',
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
			'razzi-image-box',
			$settings['img_box_box_shadow_hover_box_shadow_type'] ? 'transition-y' : '',
			$settings['image_position'] == 'top' ? 'razzi-image-box__icon-position--' . $settings['icon_position'] : ''
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

		$number = $settings['number'] ? sprintf('<span class="box-number">%s</span>',$settings['number']) : '';

		$text_val = ! empty( $settings['text'] ) ? $settings['text'] : esc_html__( 'Image Icon', 'razzi' );


		$btn_full = '';
		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );

			$image = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $image . '</a>';
			$settings['text'] = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $settings['text'] . '</a>';

			$btn_full = $settings['link_type'] == 'all' ? Helper::control_url( 'btn_full', $settings['link'], '', [ 'class' => 'razzi-image-box__button-link' ] ) : '';
		}

		$image_zoom = $settings['image_zoom'] && $settings['link_type'] == 'only' ? 'image-zoom' : '';

		$image = $image ? sprintf('<div class="box-thumbnail"><div class="box-zoom %s">%s</div> %s</div>',$image_zoom, $image, $number) : '';

		$icon  = '';

		if ( $settings['icon_type'] === 'image' ) {
			if ( $settings['image_icon']['url'] ) {
				$icon =  sprintf( '<span class="razzi-icon razzi-svg-image"><img alt="%s" src="%s"></span>', esc_attr( $text_val ), esc_url( $settings['image_icon']['url'] ) );
			}
		} if ( $settings['icon_type'] === 'external' ) {
			if ( $settings['external_url'] ) {
				$icon = '<span class="razzi-icon razzi-svg-image"><img src="' . $settings['external_url'] . '" alt="' . esc_attr( $text_val ) . '" /></span>';
			}
		} else {
			if ( $settings['icon'] && ! empty( $settings['icon']['value'] ) && \Elementor\Icons_Manager::is_migration_allowed() ) {
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );

				$add_class_icon = $settings['icon']['library'] == 'svg' ? 'razzi-svg-icon' : '';

				$icon = '<span class="razzi-icon '.$add_class_icon.'">' . ob_get_clean() . '</span>';
			}
		}

		$text = $settings['text'] ? sprintf('<h6 class="box-title">%s %s</h6>',$icon, $settings['text']) : '';

		$desc = $settings['desc'] ? sprintf('<div class="box-desc">%s</div>',$settings['desc']) : '';

		$box_summary = $text == '' && $desc == '' ? '' : sprintf('<div class="box-summary">%s %s</div>', $text,$desc) ;

		echo sprintf(
			'<div %s> %s %s %s</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$image,
			$box_summary,
			$btn_full
		);
	}
}