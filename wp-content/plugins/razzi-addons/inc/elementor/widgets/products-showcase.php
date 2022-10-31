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
 * Icon Box widget
 */
class Products_Showcase extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-products-showcase';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Razzi - Products Showcase', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
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
			'razzi-product-shortcode'
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

	// Tab Content
	protected function section_content() {
		$this->section_products_settings_controls();
	}

	// Tab Style
	protected function section_style() {
		$this->section_content_style_controls();
		$this->section_heading_style_controls();
		$this->section_carousel_style_controls();
	}

	protected function section_products_settings_controls() {
		$this->start_controls_section(
			'section_products',
			[ 'label' => esc_html__( 'Products', 'razzi' ) ]
		);

		$this->add_control(
			'heading_divider',
			[
				'label' => esc_html__( 'Heading', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is title', 'razzi' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'sub_title',
			[
				'label'       => esc_html__( 'Sub Title', 'razzi' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'This is sub title', 'razzi' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'products_divider',
			[
				'label' => esc_html__( 'Products', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
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
					'custom'         => esc_html__( 'Custom', 'razzi' ),
				],
				'default'   => 'recent',
				'toggle'    => false,
			]
		);

		$this->add_control(
			'ids',
			[
				'label'       => esc_html__( 'Products', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product',
				'sortable'    => true,
				'condition'   => [
					'products' => 'custom',
				],
			]
		);

		$this->add_control(
			'category',
			[
				'label'       => esc_html__( 'Product Categories', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_cat',
				'sortable'    => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'products',
							'operator' => '!=',
							'value' => 'custom',
						],
					],
				],
			]
		);

		$this->add_control(
			'tag',
			[
				'label'       => esc_html__( 'Products Tags', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_tag',
				'sortable'    => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'products',
							'operator' => '!=',
							'value' => 'custom',
						],
					],
				],
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
				'conditions' => [
					'terms' => [
						[
							'name' => 'products',
							'operator' => '!=',
							'value' => 'custom',
						],
					],
				],
			]
		);

		$this->add_control(
			'per_page',
			[
				'label'   => esc_html__( 'Total Products', 'razzi' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
				'conditions' => [
					'terms' => [
						[
							'name' => 'products',
							'operator' => '!=',
							'value' => 'custom',
						],
					],
				],
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
				'conditions' => [
					'terms' => [
						[
							'name' => 'products',
							'operator' => '!=',
							'value' => 'custom',
						],
					],
				],
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
				'conditions' => [
					'terms' => [
						[
							'name' => 'products',
							'operator' => '!=',
							'value' => 'custom',
						],
					],
				],
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
			'hide_image',
			[
				'label'     => esc_html__( 'Hide Image Large on Mobile', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => '',
			]
		);

		$this->add_control(
			'show_featured',
			[
				'label'     => esc_html__( 'Featured Icon on Tablet & Mobile', 'razzi' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'razzi' ),
				'label_on'  => __( 'Show', 'razzi' ),
				'return_value' => 'show',
				'default'   => '',
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

		$this->end_controls_section();
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
					'{{WRAPPER}} .razzi-products-showcase' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_list_space',
			[
				'label'     => __( 'List Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .showcase-box' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function section_heading_style_controls() {
		// Heading Tab Style
		$this->start_controls_section(
			'section_heading_style',
			[
				'label' => esc_html__( 'Heading', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
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
					'{{WRAPPER}} .razzi-products-showcase .razzi-shortcode-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_style_divider',
			[
				'label' => '',
				'type'  => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'tabs_heading_settings' );

		$this->start_controls_tab( 'heading_title', [ 'label' => esc_html__( 'Title', 'razzi' ) ] );

		$this->add_responsive_control(
			'heading_style_title_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-showcase .razzi-shortcode-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_style_title',
				'selector' => '{{WRAPPER}} .razzi-products-showcase .razzi-shortcode-title',
			]
		);
		$this->add_control(
			'heading_style_title_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .razzi-shortcode-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'heading_subtitle', [ 'label' => esc_html__( 'Sub Title', 'razzi' ) ] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_style_subtitle',
				'selector' => '{{WRAPPER}} .razzi-products-showcase .razzi-shortcode-subtitle',
			]
		);
		$this->add_control(
			'heading_style_subtitle_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .razzi-shortcode-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function section_carousel_style_controls() {
		$this->start_controls_section(
			'section_carousel_style',
			[
				'label' => __( 'Carousel Settings', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'carousel_divider',
			[
				'label' => __( 'Arrows', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'arrows_font_size',
			[
				'label'     => __( 'Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .rz-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .rz-swiper-button' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .rz-swiper-button:hover:not(.swiper-button-disabled)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing_horizontal',
			[
				'label'      => __( 'Horizontal Space', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-showcase.razzi-swiper-carousel-elementor .rz-swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .razzi-products-showcase.razzi-swiper-carousel-elementor .rz-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing_vertical ',
			[
				'label'      => __( 'Vertical Space', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-showcase .rz-swiper-button' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_divider',
			[
				'label' => __( 'Dots', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'dots_font_size',
			[
				'label'     => __( 'Size', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .swiper-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => esc_html__( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .swiper-pagination .swiper-pagination-bullet:before' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'dots_active_color',
			[
				'label'     => esc_html__( 'Active Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-products-showcase .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active:before, {{WRAPPER}} .razzi-products-showcase .swiper-pagination .swiper-pagination-bullet:hover:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_spacing_item',
			[
				'label'      => __( 'Item Space', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-showcase .swiper-container-horizontal > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'dots_spacing',
			[
				'label'      => __( 'Space', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-products-showcase .swiper-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
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
			'razzi-products-showcase razzi-swiper-carousel-elementor',
			$settings['hide_image'] != '' ? 'hide-image-large' : '',
			$settings['show_featured'] != '' ? 'show-featured' : '',
			$settings['show_category'] != '' ? 'show-category' : '',
			$settings['show_rating'] != '' ? 'show-rating' : '',
			$settings['show_quickview'] != '' ? 'show-quickview' : '',
			$settings['show_addtocart'] != '' ? 'show-addtocart' : '',
			$settings['show_wishlist'] != '' ? 'show-wishlist' : '',
			$settings['show_badges'] != '' ? 'show-badges' : ''
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) .'>';

		$output = $title = '';
		$image = array();

		$attr = [
			'products' 			=> $settings['products'],
			'orderby'  			=> $settings['orderby'],
			'order'    			=> $settings['order'],
			'category'    		=> $settings['category'],
			'tag'    			=> $settings['tag'],
			'product_brands'    => $settings['product_brands'],
			'limit'    			=> $settings['per_page'],
			'product_ids'   	=> explode(',', $settings['ids']),
		];

		$product_ids = Helper::products_shortcode( $attr );
		$product_ids = ! empty($product_ids) ? $product_ids['ids'] : 0;

		if( ! $product_ids ) {
			return;
		}

		foreach ( $product_ids as $product_id ) {

			if( empty( $product_id )  ) {
				continue;
			}

			$product = wc_get_product($product_id);

			if( empty( $product )  ) {
				continue;
			}

			$attachment_ids = $product->get_gallery_image_ids();

			$image[] = '<li class="image-item swiper-slide">';

			if ( $attachment_ids ) {
				$image[] = wp_get_attachment_image($attachment_ids[0], 'woocommerce_single');
			} else {
				$image[] = '<img src="'. wp_get_attachment_url( $product->get_image_id() ) .'" />';
			}

			$image[] = '</li>';
		}

		echo sprintf( '<div class="showcase-image swiper-container"><ul class="image-items swiper-wrapper">%s</ul></div>', implode('', $image) );

		if ( $settings['sub_title'] ) {
			$title = sprintf( '<h4 class="razzi-shortcode-subtitle">%s</h4>', esc_html( $settings['sub_title'] ) );
		}

		if ( $settings['title'] ) {
			$title .= sprintf( '<h3 class="razzi-shortcode-title">%s</h3>', esc_html( $settings['title'] ) );
		}

		echo '<div class="showcase-box">';

		echo '<div class="razzi-box-title">'. $title .'</div>';

		echo '<div class="product-content swiper-container">';

		update_meta_cache( 'post', $product_ids );
		update_object_term_cache( $product_ids, 'product' );

		$original_post = $GLOBALS['post'];

		echo '<ul class="products shortcode-element product-loop-layout-1">';

		foreach ( $product_ids as $product_id ) {
			$GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', 'product-showcase' );
		}

		$GLOBALS['post'] = $original_post; // WPCS: override ok.

		echo '</ul>';

		wp_reset_postdata();

		echo '</div>';

		echo '</div>';

		echo sprintf( '%s%s', \Razzi\Addons\Helper::get_svg('chevron-left', 'rz-swiper-showcase-button-prev rz-swiper-button-prev rz-swiper-button'),
								\Razzi\Addons\Helper::get_svg('chevron-right', 'rz-swiper-showcase-button-next rz-swiper-button-next rz-swiper-button') );

		echo '</div>';
	}
}