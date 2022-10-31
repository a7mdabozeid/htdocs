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
 * Icon Box widget
 */
class Products_Masonry extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-products-masonry';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Razzi - Products Masonry', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-inner-section';
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
		$scripts = [
			'razzi-product-shortcode'
		];

		return $scripts;
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

	// Tab Content
	protected function section_content() {
		$this->section_banner_settings_controls();
		$this->section_products_settings_controls();
		$this->section_pagination_settings_controls();
	}

	// Tab Style
	protected function section_style() {
		$this->section_content_style_controls();
		$this->section_pagination_style_controls();
	}

	protected function section_banner_settings_controls() {
		$this->start_controls_section(
			'section_banner',
			[ 'label' => esc_html__( 'Banner', 'razzi' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->start_controls_tabs( 'banners_repeater' );

		$repeater->start_controls_tab( 'background', [ 'label' => esc_html__( 'Background', 'razzi' ) ] );

		$repeater->add_control(
			'banner_image',
			[
				'label'   => esc_html__( 'Image', 'razzi' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [],
			]
		);

		$repeater->add_control(
			'background_overlay',
			[
				'label'      => esc_html__( 'Background Overlay', 'razzi' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .masonry-banner-content .banner-image:before' => 'background-color: {{VALUE}}',
				]
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'text_content', [ 'label' => esc_html__( 'Content', 'razzi' ) ] );

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is title', 'razzi' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'sub_title',
			[
				'label'       => esc_html__( 'Sub Title', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is sub title', 'razzi' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is description', 'razzi' ),
			]
		);

		$repeater->add_control(
			'banner_link', [
				'label'         => esc_html__( 'Link', 'razzi' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'razzi' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
				'condition' => [
					'button_enable' => '',
				],
			]
		);

		$repeater->add_control(
			'button_enable',
			[
				'label'        => __( 'Button', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'razzi' ),
				'label_off'    => __( 'Hide', 'razzi' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition' => [
					'button_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'button_link', [
				'label'         => esc_html__( 'Button Link', 'razzi' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'razzi' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
				'condition' => [
					'button_enable' => 'yes',
				],
			]
		);

		$repeater->add_responsive_control(
			'vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'razzi' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'razzi' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'razzi' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'razzi' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .masonry-banner-content .banner-content' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'separator'  => 'before',
			]
		);

		$repeater->add_responsive_control(
			'text_align',
			[
				'label'       => esc_html__( 'Text Align', 'razzi' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => esc_html__( 'Left', 'razzi' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'razzi' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'razzi' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .masonry-banner-content .banner-content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'style', [ 'label' => esc_html__( 'Style', 'razzi' ) ] );

		$repeater->add_responsive_control(
			'content_padding_repeater',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .masonry-banner-content .banner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'title_heading_name',
			[
				'label' => esc_html__( 'Title', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'title_color',
			[
				'label'      => esc_html__( ' Color', 'razzi' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__title' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__title',
			]
		);

		$repeater->add_responsive_control(
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
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$repeater->add_control(
			'sub_title_heading_name',
			[
				'label' => esc_html__( 'Sub Title', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'sub_title_color',
			[
				'label'      => esc_html__( 'Color', 'razzi' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__sub-title' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__sub-title',
			]
		);

		$repeater->add_responsive_control(
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
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$repeater->add_control(
			'desc_heading_name',
			[
				'label' => esc_html__( 'Description', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'description_color',
			[
				'label'      => esc_html__( 'Color', 'razzi' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__description' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__description',
			]
		);

		$repeater->add_responsive_control(
			'description_spacing',
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
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$repeater->add_control(
			'btn_heading_name',
			[
				'label' => esc_html__( 'Button', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'btn_custom_color',
			[
				'label'      => esc_html__( 'Color', 'razzi' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__button a' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .razzi-products-masonry {{CURRENT_ITEM}} .razzi-banner__button a',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'banners',
			[
				'label'         => '',
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'full',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function section_products_settings_controls() {
		$this->start_controls_section(
			'section_products',
			[ 'label' => esc_html__( 'Products', 'razzi' ) ]
		);


		$this->add_control(
			'products',
			[
				'label'     => esc_html__( 'Product', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'recent'       => esc_html__( 'Recent', 'razzi' ),
					'featured'     => esc_html__( 'Featured', 'razzi' ),
					'best_selling' => esc_html__( 'Best Selling', 'razzi' ),
					'top_rated'    => esc_html__( 'Top Rated', 'razzi' ),
					'sale'         => esc_html__( 'On Sale', 'razzi' ),
				],
				'default'   => 'recent',
				'toggle'    => false,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''           => esc_html__( 'Default', 'razzi' ),
					'date'       => esc_html__( 'Date', 'razzi' ),
					'title'      => esc_html__( 'Title', 'razzi' ),
					'menu_order' => esc_html__( 'Menu Order', 'razzi' ),
					'rand'       => esc_html__( 'Random', 'razzi' ),
				],
				'default'   => '',
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Order', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''     => esc_html__( 'Default', 'razzi' ),
					'asc'  => esc_html__( 'Ascending', 'razzi' ),
					'desc' => esc_html__( 'Descending', 'razzi' ),
				],
				'default'   => '',
			]
		);

		$this->add_control(
			'product_category',
			[
				'label'       => esc_html__( 'Product Categories', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_cat',
				'sortable'    => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_tag',
			[
				'label'       => esc_html__( 'Products Tags', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_tag',
				'sortable'    => true,
			]
		);

		$this->add_control(
			'product_brands',
			[
				'label'       => esc_html__( 'Products Brands', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_brand',
				'sortable'    => true,
			]
		);

		$this->add_control(
			'attributes_divider',
			[
				'label' => esc_html__( 'Attributes', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'     => esc_html__( 'Category', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => '',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'     => esc_html__( 'Rating', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => '',
			]
		);

		$this->add_control(
			'show_quickview',
			[
				'label'     => esc_html__( 'Quick View', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->add_control(
			'show_addtocart',
			[
				'label'     => esc_html__( 'Add To Cart', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->add_control(
			'show_wishlist',
			[
				'label'     => esc_html__( 'Wishlist', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->add_control(
			'show_badges',
			[
				'label'     => esc_html__( 'Badges', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->add_control(
			'show_atc_mobile',
			[
				'label'     => esc_html__( 'Show Add to Cart Mobile', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->add_control(
			'show_featured_icons_mobile',
			[
				'label'     => esc_html__( 'Show Featured Icons Mobile', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => 'show',
			]
		);

		$this->end_controls_section();
	}

	protected function section_pagination_settings_controls() {
		// Pagination Settings
		$this->start_controls_section(
			'section_pagination',
			[
				'label' => esc_html__( 'Pagination', 'razzi' ),
			]
		);
		$this->add_control(
			'product_found',
			[
				'label'        => __( 'Product Found', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'razzi' ),
				'label_off'    => __( 'Hide', 'razzi' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'load_more',
			[
				'label'        => __( 'Button', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'razzi' ),
				'label_off'    => __( 'Hide', 'razzi' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
		$this->add_control(
			'load_more_text',
			[
				'label'       => esc_html__( 'Text', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Show More', 'razzi' ),
				'label_block' => true,
			]
		);
		$this->end_controls_section(); // End Pagination Settings
	}

	protected function section_content_style_controls() {
		// Content Style
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_pagination_style_controls() {
		$this->start_controls_section(
			'section_pagination_style',
			[
				'label' => __( 'Pagination', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'found_divider',
			[
				'label' => __( 'Product Found', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'found_space',
			[
				'label'      => __( 'Space', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-masonry .razzi-posts__found' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'found_typography',
				'selector' => '{{WRAPPER}} .razzi-products-masonry .razzi-posts__found-inner',
			]
		);

		$this->add_control(
			'found_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .razzi-posts__found-inner' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'found_bg_color',
			[
				'label'     => esc_html__( 'Background Line', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .razzi-posts__found-inner' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'found_active_bg_color',
			[
				'label'     => esc_html__( 'Background Line Active', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .razzi-posts__found-inner .count-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_divider',
			[
				'label' => __( 'Button', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'     => __( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .woocommerce-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_text',
				'selector' => '{{WRAPPER}} .razzi-products-masonry .woocommerce-pagination ul .btn-load-more a',
			]
		);
		$this->add_control(
			'button_bgcolor',
			[
				'label'     => esc_html__( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .woocommerce-pagination ul .btn-load-more a' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .woocommerce-pagination ul .btn-load-more a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-masonry .woocommerce-pagination ul .btn-load-more a' => 'border-color: {{VALUE}};',
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

		$classes = [
			'razzi-products-masonry',
			$settings['show_category'] != '' ? 'show-category' : '',
			$settings['show_rating'] != '' ? 'show-rating' : '',
			$settings['show_quickview'] != '' ? 'show-quickview' : '',
			$settings['show_addtocart'] != '' ? 'show-addtocart' : '',
			$settings['show_wishlist'] != '' ? 'show-wishlist' : '',
			$settings['show_badges'] != '' ? 'show-badges' : '',
			$settings['show_quickview'] == '' && $settings['show_addtocart'] == '' && $settings['show_wishlist'] == '' ? 'btn-hidden' : ''
		];

		$this->add_render_attribute( 'wrapper', [
			'class' 			=> $classes,
			'data-nonce' 		=> wp_create_nonce( 'razzi_get_products' )
		] );

		$limit = 16;

		$attr = [
			'products' 			=> $settings['products'],
			'orderby'  			=> $settings['orderby'],
			'order'    			=> $settings['order'],
			'category'    		=> $settings['product_category'],
			'tag'    			=> $settings['product_tag'],
			'product_brands'    => $settings['product_brands'],
			'limit'    			=> $limit,
			'paginate'			=> true,
			'load_more_text'	=> $settings['load_more_text'],
		];

		$results = Helper::products_shortcode( $attr );
		if ( ! $results ) {
			return;
		}

		$product_ids = $results['ids'];
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php
				self::get_template_masonry_loop( $product_ids, 'product-masonry', $settings['banners'], $settings['show_atc_mobile'], $settings['show_featured_icons_mobile'], $settings );

				self::get_products_found( $results, $settings );

				self::get_query_pagination( $results['total_pages'], $results['current_page'], $settings );
			?>
		</div>
		<?php
	}

	/**
	 * Loop over products
	 *
	 * @param array $products_ids
	 */
	public static function get_template_masonry_loop( $products_ids, $template, $banners, $atc_mobile, $featured_mobile, $settings ) {
		update_meta_cache( 'post', $products_ids );
		update_object_term_cache( $products_ids, 'product' );

		$original_post = $GLOBALS['post'];

		$class_mobile = '';
		if ( $atc_mobile == 'show' ) {
			$class_mobile = 'mobile-show-atc';
		}
		if ( $featured_mobile == 'show' ) {
			$class_mobile .= ' mobile-show-featured-icons';
		}

		if( class_exists('\Razzi\Helper') && method_exists('\Razzi\Helper', 'get_option') ) {
			if ( $mobile_pl_col = intval( \Razzi\Helper::get_option( 'mobile_landscape_product_columns' ) ) ) {
				$class_mobile .= ' mobile-pl-col-' . $mobile_pl_col;
			}
	
			if ( $mobile_pp_col = intval( \Razzi\Helper::get_option( 'mobile_portrait_product_columns' ) ) ) {
				$class_mobile .= ' mobile-pp-col-' . $mobile_pp_col;
			}
		}

		echo '<ul class="products product-loop-layout-7 product-loop-center shortcode-element layout-masonry razzi-products-masonry__content '. esc_attr( $class_mobile ) .'">';

		$i  = 0;

		$current = isset($_GET['product-page']) ? (int) $_GET['product-page'] : 1;

		$index = $current > 1 ? ($current - 1) * 2 : 0;

		foreach ( $products_ids as $product_id ) {

			if(isset( $banners[$index] )) {
				if( $i == 0 || $i % 9 == 0  ) {
					self::get_banner_html( $banners[$index], $settings );
					$index++;
				}
			}

			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', $template );

			$i++;
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		echo '</ul>';

		wp_reset_postdata();

	}

	public static function get_banner_html( $banner, $settings ) {
		if ( empty( $banner ) ) {
			return;
		}

		$banner_html = '';

		if( $banner['button_enable'] == '' && $banner['banner_link']['url'] ) {
			$banner_html .= '<a class="has-link masonry-banner-content" href="'. $banner['banner_link']['url'] .'">';
		} else {
			$banner_html .= '<div class="masonry-banner-content">';
		}

		if ( $banner['banner_image']['url'] ) {
			$settings['image']      = $banner['banner_image'];

			$image = Group_Control_Image_Size::get_attachment_image_html( $settings );
			$banner_html .= $image ? sprintf('<div class="banner-image">%s</div>',$image) : '';
		}

		$banner_html .= '<div class="banner-content">';

		if ( $banner['sub_title'] ) {
			$banner_html .= '<span class="razzi-banner__sub-title">' . $banner['sub_title'] . '</span>';
		}

		if ( $banner['title'] ) {
			$banner_html .= '<span class="razzi-banner__title">' . $banner['title'] . '</span>';
		}

		if ( $banner['description'] ) {
			$banner_html .= '<span class="razzi-banner__description">' . $banner['description'] . '</span>';
		}

		if ( $banner['button_enable'] == 'yes' && $banner['button_text'] ) {
			$button_text = $banner['button_text'] . \Razzi\Addons\Helper::get_svg('arrow-right');
			$banner_html .= sprintf( '<span class="razzi-banner__button">%s</span>', Helper::control_url( $banner['_id'], $banner['button_link'], $button_text, [ 'class' => 'razzi-button button-normal' ] ) );
		}

		$banner_html .= '</div>';

		if( $banner['button_enable'] == '' && $banner['banner_link']['url'] ) {
			$banner_html .= '</a>';
		} else {
			$banner_html .= '</div>';
		}

		echo '<li class="product has-banner"><div class="razzi-banner__masonry elementor-repeater-item-' . $banner['_id'] .'">' . $banner_html . '</div></li>';

	}

	public static function get_products_found($results, $settings) {
		if( $settings['product_found'] == '') {
			return;
		}

		$product_text = $results['total'] > 1 ? esc_html( 'products', 'razzi' ) : esc_html( 'product', 'razzi' );

		echo sprintf( '<div class="razzi-posts__found"><div class="razzi-posts__found-inner">%s<span class="current-post"> %s </span> %s <span class="found-post"> %s </span> %s <span class="count-bar"></span></div> </div>',
			esc_html( 'Showing', 'razzi' ), count($results['ids']), esc_html( 'of', 'razzi' ), $results['total'], $product_text );

	}


	public static function get_query_pagination($total_pages, $current_page, $settings) {
		if ( ! $settings['load_more'] ) {
			return;
		}

		$next_html = sprintf(
				'<span class="load-more-text">%s</span>
					<div class="razzi-gooey-loading">
						<div class="razzi-gooey">
							<div class="dots">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>',
				$settings['load_more_text']
			);

		echo '<nav class="woocommerce-pagination">';
		echo paginate_links(
			array( // WPCS: XSS ok.
				'base'      => esc_url_raw( add_query_arg( 'product-page', '%#%', false ) ),
				'format'    => '?product-page=%#%',
				'add_args'  => false,
				'current'   => max( 1, $current_page ),
				'total'     => $total_pages,
				'prev_text' => false,
				'next_text' => $next_html,
				'type'      => 'list',
			)
		);
		echo '</nav>';
	}
}