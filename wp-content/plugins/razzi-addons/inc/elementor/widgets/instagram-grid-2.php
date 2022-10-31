<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Instagram_Grid widget
 */
class Instagram_Grid_2 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-instagram-grid-2';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Instagram Grid 2', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-instagram-gallery';
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
			[ 'label' => esc_html__( 'Instagram', 'razzi' ) ]
		);

		$this->add_control(
			'instagram_type',
			[
				'label' => esc_html__( 'Instagram type', 'razzi' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'token' 	=> 'Token',
					'custom' 	=> 'Custom',
				],
				'default' => 'token',
			]
		);

		$this->add_control(
			'access_token',
			[
				'label'       => esc_html__( 'Access Token', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Enter your access token', 'razzi' ),
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'instagram_type',
							'value' => 'token',
						],
					],
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'razzi' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'razzi' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'razzi' ),
			]
		);

		$repeater->add_control(
			'caption',
			[
				'label' => esc_html__( 'Caption', 'razzi' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'placeholder' => esc_html__( 'Enter your caption', 'razzi' ),
				'rows' => 4,
			]
		);

		$this->add_control(
			'image_list',
			[
				'label'         => esc_html__( 'Image List', 'razzi' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => false,
				'conditions' => [
					'terms' => [
						[
							'name' => 'instagram_type',
							'value' => 'custom',
						],
					],
				],
			]
		);

		$this->add_control(
			'limit',
			[
				'label'       => esc_html__( 'Limit', 'razzi' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 8,
				'conditions' => [
					'terms' => [
						[
							'name' => 'instagram_type',
							'value' => 'token',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns', 'razzi' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'default' => 2,
				'tablet_default'  => 2,
				'mobile_default'  => 2,
				'prefix_class' => 'columns-%s',
			]
		);

		$this->add_control(
			'instagram_context_box',
			[
				'label'      => esc_html__( 'Content Box', 'razzi' ),
				'type'       => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'instagram_icon_type',
			[
				'label'      => esc_html__( 'Icon Type', 'razzi' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'default'       => esc_html__( 'Default', 'razzi' ),
					'custom'      	=> esc_html__( 'Custom', 'razzi' ),
				],
				'default'    => 'default',
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icons', 'razzi' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'condition' => [
					'instagram_icon_type' => ['custom'],
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is title', 'razzi' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Desc', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is desc', 'razzi' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Button Text', 'razzi' ),
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => esc_html__( 'Button Link Type', 'razzi' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'   => esc_html__( 'Default user', 'razzi' ),
					'custom' 	 => esc_html__( 'Custom', 'razzi' ),
				],
				'default' => 'default',
				'toggle'  => false,
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
				'conditions' => [
					'terms' => [
						[
							'name'     => 'link_type',
							'operator' => '==',
							'value'    => 'custom',
						],
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
		$this->start_controls_section(
			'style_general',
			[
				'label' => __( 'Instagram Content Box', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'box_bk_color',
			[
				'label'        => esc_html__( 'Background Color', 'razzi' ),
				'type'         => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-instagram-grid-2 .instagram-text-wrapper' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .razzi-instagram-grid-2 .instagram-text-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'style_tabs_icon',
			[
				'label'        => esc_html__( 'Icon', 'razzi' ),
				'type'         => Controls_Manager::HEADING,
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
					'{{WRAPPER}} .razzi-instagram-grid-2 .razzi-icon' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .razzi-instagram-grid-2 .razzi-icon' => 'font-size: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .razzi-instagram-grid-2 .razzi-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'instagram_icon_type' => ['custom'],
				],
			]
		);

		$this->add_control(
			'style_tabs_title',
			[
				'label'        => esc_html__( 'Title', 'razzi' ),
				'type'         => Controls_Manager::HEADING,
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
					'{{WRAPPER}} .razzi-instagram-grid-2 .instagram-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-instagram-grid-2 .instagram-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-instagram-grid-2 .instagram-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'style_tabs_desc',
			[
				'label'        => esc_html__( 'Description', 'razzi' ),
				'type'         => Controls_Manager::HEADING,
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
					'{{WRAPPER}} .razzi-instagram-grid-2 .instagram-desc' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-instagram-grid-2 .instagram-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-instagram-grid-2 .instagram-desc' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'style_tabs_button',
			[
				'label'        => esc_html__( 'Button', 'razzi' ),
				'type'         => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .razzi-instagram-grid-2 .button-text',
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-instagram-grid-2 .button-text' => 'color: {{VALUE}};',
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

		$this->add_render_attribute(
			'wrapper', 'class', [
				'razzi-instagram-grid-2',
				'razzi-element-columns',
			]
		);

		// Instagram
		$output_left    = $output_right = [];

		if ( $settings['instagram_type'] === 'token' ) {
			$instagram = Helper::get_instagram_get_photos_by_token( $settings['limit'],$settings['access_token'] );
			$user = Helper::get_instagram_user( $settings['access_token'] );

			if ( is_wp_error( $instagram ) ) {
				return $instagram->get_error_message();
			}

			if( ! $instagram ) {
				return;
			}

			$count = 1;
			$col = intval($settings['columns']);

			$itemsTotal = $settings['limit'] <= count($instagram) ? $settings['limit'] : count($instagram);

			$output[] = sprintf('<ul class="instagram-wrapper">');

			foreach ( $instagram as $data ) {

				if ( $count > intval( $settings['limit'] ) ) {
					break;
				}

				$item_html = '<li class="elmentor-column-item instagram-item"><a target="_blank" href="' . esc_url( $data['link'] ) . '"><img src="' . esc_url( $data['images']['thumbnail'] ) . '" alt="' . esc_attr( $data['caption'] ) . '"></a></li>';

				if ($count <= $itemsTotal/2) {
					$output_left[] = $item_html;
				} else {
					$output_right[] = $item_html;
				}

				$count ++;
			}
			$output[] = sprintf('</ul>');

		} else {
			$count_t = 1;

			$itemsTotal = count($settings['image_list']);

			$output[] = sprintf('<ul class="instagram-wrapper">');

			foreach ( $settings['image_list'] as $index => $item ) {
				if ( $item['image']['url'] ) {
					$this->add_link_attributes( 'icon-link', $item['link'] );
					$link = $item['link']['url'] ? $item['link']['url'] : '#';
					$target = $item['link']['is_external'] ? ' target="_blank"' : '';
					$nofollow = $item['link']['nofollow'] ? ' rel="nofollow"' : '';

					$item_html = '<li class="elmentor-column-item instagram-item"><a href="' . $link . '" ' . $target . $nofollow . $this->get_render_attribute_string( 'icon-link' ) . '><img src="' . esc_url( $item['image']['url'] ) . '" loading="lazy" alt="' . esc_attr( $item['caption'] ) . '"></a></li>';
				}

				if ($count_t <= $itemsTotal/2) {
					$output_left[] = $item_html;
				} else {
					$output_right[] = $item_html;
				}

				$count_t ++;
			}

			$output[] = sprintf('</ul>');
		}

		// Box text
		$instagram_icon = \Razzi\Addons\Helper::get_svg('instagram', 'razzi-icon', 'social');

		if ( $settings['instagram_icon_type'] == 'custom' && $settings['icon'] && ! empty( $settings['icon']['value'] ) && \Elementor\Icons_Manager::is_migration_allowed() ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );

			$add_class_icon = $settings['icon']['library'] == 'svg' ? 'razzi-svg-icon' : '';

			$instagram_icon = '<span class="razzi-icon '.$add_class_icon.'">' . ob_get_clean() . '</span>';
		}

		$button_text = $settings['button_text'] ? sprintf('<span class="button-text razzi-button--underlined">%s</span>',$settings['button_text']) : '';

		$settings['link']['url'] 		 = $settings['link_type'] == 'default' ? esc_url('https://www.instagram.com/'.$user['username'].'') : $settings['link']['url'];
		$settings['link']['is_external'] = $settings['link_type'] == 'default' ? '' : $settings['link']['is_external'];
		$settings['link']['nofollow'] 	 = $settings['link_type'] == 'default' ? '' : $settings['link']['nofollow'];

		if ( $settings['link']['url'] ) :

			$button_text = Helper::control_url( 'btn', $settings['link'], $button_text, ['class' => 'button-link'] );

		else:
			$button_text = sprintf('<div class="button-link">%s</div>',$button_text);

		endif;

		$title = $settings['title'] ? sprintf('<h2 class="instagram-title">%s</h2>',$settings['title']) : '';
		$desc = $settings['desc'] ? sprintf('<div class="instagram-desc">%s</div>',$settings['desc']) : '';

		// Content html
		$output_left_html = sprintf('<ul class="instagram-wrapper instagram-wrapper__left col-flex col-flex-md-4">%s</ul>',implode('', $output_left));
		$output_center_html = sprintf('<div class="instagram-text-wrapper instagram-wrapper__center col-flex col-flex-md-4">%s%s%s%s</div>', $instagram_icon, $title, $desc, $button_text);
		$output_right_html = sprintf('<ul class="instagram-wrapper instagram-wrapper__right col-flex col-flex-md-4">%s</ul>',implode('', $output_right));

		echo sprintf(
			'<div %s><div class="row-flex">%s%s%s</div></div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$output_left_html,$output_center_html,$output_right_html
		);

	}
}
