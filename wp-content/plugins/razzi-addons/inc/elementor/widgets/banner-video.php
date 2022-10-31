<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Widget_Base;
use Razzi\Addons\Elementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner Video widget
 */
class Banner_Video extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-banner-video';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Banner Video', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-youtube';
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
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'video', 'banner' ];
	}

	public function get_script_depends() {
		return [
			'magnific',
			'razzi-frontend'
		];
	}

	public function get_style_depends() {
		return [
			'magnific'
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
			[ 'label' => esc_html__( 'Content', 'razzi' ) ]
		);

		$this->add_control(
			'video_type',
			[
				'label' => __( 'Source', 'razzi' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => __( 'YouTube', 'razzi' ),
					'vimeo' => __( 'Vimeo', 'razzi' ),
					'self_hosted' => __( 'Self Hosted', 'razzi' ),
				],
			]
		);

		$this->add_control(
			'youtube_url',
			[
				'label' => __( 'Link', 'razzi' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your URL', 'razzi' ) . ' (YouTube)',
				'default' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				'label_block' => false,
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'vimeo_url',
			[
				'label' => __( 'Link', 'razzi' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your URL', 'razzi' ) . ' (Vimeo)',
				'default' => 'https://vimeo.com/235215203',
				'label_block' => false,
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'insert_url',
			[
				'label' => __( 'External URL', 'razzi' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => 'self_hosted',
				],
			]
		);

		$this->add_control(
			'external_url',
			[
				'label' => __( 'Link', 'razzi' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'Enter your URL', 'razzi' ),
				'autocomplete' => false,
				'options' => false,
				'label_block' => true,
				'show_label' => false,
				'media_type' => 'video',
				'condition' => [
					'video_type' => 'self_hosted',
					'insert_url' => 'yes'
				],
			]
		);

		$this->add_control(
			'hosted_url',
			[
				'label' => __( 'Choose File', 'razzi' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'video',
				'condition' => [
					'video_type' => 'self_hosted',
					'insert_url' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'banners_background',
				'label'    => __( 'Background', 'razzi' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .razzi-banner-video .banner-featured-image',
				'fields_options'  => [
					'background' => [
						'default' => 'classic',
					],
					'image' => [
						'default'   => [
							'url' => 'https://via.placeholder.com/1170X430/f5f5f5?text=Banner Image',
						],
					],
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'     => esc_html__( 'Height', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 430,
				],
				'range'     => [
					'px' => [
						'min' => 100,
						'max' => 1000
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-video' => 'height: {{SIZE}}{{UNIT}};',
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
					'only'   => esc_html__( 'Only marker', 'razzi' ),
					'all' 	 => esc_html__( 'All banner', 'razzi' ),
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
		$this->start_controls_section(
			'style_content',
			[
				'label' => __( 'Marker', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => __( 'Font Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-video__marker .razzi-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .razzi-banner-video__marker .razzi-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-video__marker:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_width',
			[
				'label'     => __( 'Width', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-video__marker:after' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_height',
			[
				'label'     => __( 'Height', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-banner-video__marker:after' => 'height: {{SIZE}}{{UNIT}};',
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
				'razzi-banner-video'
			]
		);

		$icon =  \Razzi\Addons\Helper::get_svg('play', 'razzi-icon', 'widget');

		$marker_html =  sprintf('<span class="razzi-banner-video__marker">%s</span>', $icon) ;

		$video_url = array();


		if ($settings['video_type'] == 'youtube') {

			$video_url['url'] = $settings['youtube_url'];

		} elseif ($settings['video_type'] == 'vimeo') {

			$video_url['url'] = $settings['vimeo_url'];

		} else {

			if ( ! empty( $settings['insert_url'] ) ) {
				$video_url['url'] = $settings['external_url']['url'];
			} else {
				$video_url['url'] = $settings['hosted_url']['url'];
			}
		}

		$video_url['is_external'] = $video_url['nofollow'] = '';

		$btn_full = '';
		if ( $video_url['url']) :
			if ( $settings['link_type'] == 'only') {
				$marker_html = Helper::control_url( 'btn_1', $video_url, $marker_html, [ 'class' => 'razzi-banner-video__play' ] );
			} else {
				$btn_full =  Helper::control_url( 'btn_2', $video_url, '', [ 'class' => 'razzi-banner-video__play full-box-button' ] ) ;
			}
		endif;

		echo sprintf(
			'<div %s>
				<div class="banner-featured-image"></div>
				%s %s
			</div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$marker_html, $btn_full
		);
	}
}