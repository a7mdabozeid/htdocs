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
 * Map widget
 */
class Map extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-map';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Map', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-google-maps';
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
			'mapbox',
			'mapboxgl',
			'mapbox-sdk',
			'razzi-frontend'
		];
	}

	public function get_style_depends() {
		return [
			'mapbox',
			'mapboxgl'
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
			'access_token',
			[
				'label'       => esc_html__( 'Access Token', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Enter your access token', 'razzi' ),
				'label_block' => true,
				'description' => sprintf(__('Please go to <a href="%s" target="_blank">Maps Box APIs</a> to get a key', 'razzi'), esc_url('https://www.mapbox.com')),
			]
		);

		$this->add_control(
			'marker_heading',
			[
				'label' => esc_html__( 'Marker', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://via.placeholder.com/40x40/f5f5f5?text=40x40',
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);


		$repeater = new \Elementor\Repeater();


		$repeater->add_control(
			'local',
			[
				'label'       => esc_html__( 'Local', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			]
		);

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
				'default'     => esc_html__( 'Enter your content', 'razzi' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'elements',
			[
				'label' => esc_html__( 'Location List', 'razzi' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'local'                 => esc_html__( 'New York', 'razzi' ),
						'title'                 => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					],
					[
						'local'                 => esc_html__( 'London', 'razzi' ),
						'title'         => esc_html__( 'This is title', 'razzi' ),
						'desc'                  => esc_html__( 'This is description', 'razzi' ),
					]
				],
				'title_field'   => '{{{ title }}}',

			]
		);


		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'show_tab',
			[
				'label'        => esc_html__( 'Show Location', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_search',
			[
				'label'        => esc_html__( 'Show Search', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'zoom',
			[
				'label'       => esc_html__( 'Zoom', 'razzi' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '13',
			]
		);

		$this->add_control(
			'mode',
			[
				'label'       => esc_html__( 'Mode', 'razzi' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'streets-v11' 	=> esc_html__( 'Streets', 'razzi' ),
					'light-v10' 	=> esc_html__( 'Light', 'razzi' ),
					'dark-v10'  	=> esc_html__( 'Dark', 'razzi' ),
					'outdoors-v11'  => esc_html__( 'Outdoors', 'razzi' ),
				],
				'default'     => 'light-v10',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Section Style
	 */

	protected function section_style() {

		$this->section_content_style();
		$this->section_box_style();
	}

	protected function section_content_style() {
		$this->start_controls_section(
			'style_general',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_bk_color',
			[
				'label'        => esc_html__( 'Background Color', 'razzi' ),
				'type'         => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} > .elementor-widget-container' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-map' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'color_1',
			[
				'label'     => esc_html__( 'Color water', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'color_2',
			[
				'label'     => esc_html__( 'Color building', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'     => esc_html__( 'Map Height', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .razzi-map > *' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_box_style() {
		$this->start_controls_section(
			'style_box',
			[
				'label' => __( 'Location Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-map .box-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'box_bk_color',
			[
				'label'        => esc_html__( 'Background Color', 'razzi' ),
				'type'         => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-map .box-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'        => esc_html__( 'Border Color', 'razzi' ),
				'type'         => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-map .box-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs(
			'style_tabs_box'
		);

		// Title
		$this->start_controls_tab(
			'box_title',
			[
				'label' => __( 'Title', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-map__table .map-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-map__table .map-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => __( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-map__table .map-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_local',
			[
				'label' => __( 'Local', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'local_typography',
				'selector' => '{{WRAPPER}} .razzi-map__table .map-local',
			]
		);

		$this->add_control(
			'local_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-map__table .map-local' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'local_spacing',
			[
				'label'     => __( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
						'min' => 0,
					],
				],
				'default'   => [ ],
				'selectors' => [
					'{{WRAPPER}} .razzi-map__table .map-local' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_desc',
			[
				'label' => __( 'Desc', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-map__table .map-desc',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-map__table .map-desc' => 'color: {{VALUE}};',
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

		$id     = uniqid( 'razzi-map-' );

		$this->add_render_attribute(
			'wrapper', 'class', [
				'razzi-map',
				$settings['show_tab'] ? 'razzi-map__has-tab' : '',
				$settings['show_search'] ? 'razzi-map__has-search' : ''
			]
		);

		$image = $settings[ 'image' ];
		$src = Group_Control_Image_Size::get_attachment_image_src( $image['id'], 'image', $settings );

		// JS
		$color_1                     = $settings['color_1'] ? $settings['color_1'] : '#c8d7d4';
		$color_2                     = $settings['color_2'] ? $settings['color_2'] : '#f0f0ec';

		$output_tab    = $locals = [];

		$this->add_render_attribute('attr_tab','data-latitude', '' );
		$this->add_render_attribute('attr_tab','data-longitude', '' );

		if ($settings["elements"]) {
			$output_tab[] = '<div class="razzi-map__table">';

		 	foreach (  $settings["elements"]  as $index => $item ) {
				$locals[] = $item["local"];
				$title     = $item["title"] ? '<h5 class="map-title">' . $item["title"]  . '</h5>' : '';
				$local     = $item["local"] ? '<div class="map-local">' . $item["local"]  . '</div>' : '';
				$desc = $item['desc'] ? sprintf( '<div class="map-desc">%s</div>', $item["desc"]  ) : '';

				$output_tab[] = $title == '' && $desc == '' ? '' : sprintf( '<div class="box-item" %s>%s %s %s</div>',$this->get_render_attribute_string( 'attr_tab' ),$title,$local ,$desc  );
			}

			$output_tab[] = '</div>';

		}

		$output_map = array(
			'marker'  => $src,
			'token'   => $settings['access_token'],
			'zom'     => intval( $settings['zoom'] ),
			'color_1' => $color_1,
			'color_2' => $color_2,
			'local'   => $locals,
			'mode'    => $settings['mode'],
		);

		$this->add_render_attribute('map','data-map',wp_json_encode($output_map) );

		echo sprintf(
			'<div %s %s>%s<div class="razzi-map__content" id="%s"></div></div>',
			$this->get_render_attribute_string( 'wrapper' ),
			$this->get_render_attribute_string( 'map' ),
			implode( '', $output_tab ),
			$id
		);

	}
}