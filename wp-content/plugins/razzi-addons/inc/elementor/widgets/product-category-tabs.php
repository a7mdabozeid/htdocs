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
class Product_Category_Tabs extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-product-category-tabs';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Razzi - Product Category Tabs', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
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

	/**
	 * Section Content
	 */
	protected function section_content() {
		$this->section_heading_settings();
		$this->section_carousel_settings();
	}

	/**
	 * Section Style
	 */
	protected function section_style() {
		$this->section_tab_header_style();
		$this->section_content_style();
		$this->section_carousel_style();
	}

	protected function section_heading_settings() {
		$this->start_controls_section(
			'section_category',
			[ 'label' => esc_html__( 'Category', 'razzi' ) ]
		);

		$this->add_control(
			'product_cat',
			[
				'label'       => esc_html__( 'Category Tabs', 'razzi' ),
				'placeholder' => esc_html__( 'Click here and start typing...', 'razzi' ),
				'type'        => 'rzautocomplete',
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'source'      => 'product_cat',
				'sortable'    => true,
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
					'count'      => esc_html__( 'Count', 'razzi' ),
					'menu_order' => esc_html__( 'Menu Order', 'razzi' ),
				],
				'default'   => '',
				'separator' => 'before',
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

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				// Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cats_count',
			[
				'label'        => __( 'Count', 'razzi' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'razzi' ),
				'label_off'    => __( 'Hide', 'razzi' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function section_carousel_settings() {
		$this->start_controls_section(
			'section_carousel_settings',
			[ 'label' => esc_html__( 'Carousel Settings', 'razzi' ) ]
		);

		$this->add_responsive_control(
			'slidesToShow',
			[
				'label'           => esc_html__( 'Slides to show', 'razzi' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 7,
				'default' 		=> 5,
				'frontend_available' => true,
			]
		);
		$this->add_responsive_control(
			'slidesToScroll',
			[
				'label'           => esc_html__( 'Slides to scroll', 'razzi' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => 7,
				'default' 		=> 1,
				'frontend_available' => true,
			]
		);
		$this->add_responsive_control(
			'navigation',
			[
				'label'     => esc_html__( 'Navigation', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'none'     => esc_html__( 'None', 'razzi' ),
					'arrows'  => esc_html__( 'Arrows', 'razzi' ),
					'dots' => esc_html__( 'Dots', 'razzi' ),
				],
				'default'   => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * General
	 */

	protected function section_tab_header_style() {
		$this->start_controls_section(
			'section_tab_header_style',
			[
				'label' => esc_html__( 'Tab Header', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tab_header_space',
			[
				'label'     => __( 'Space', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs .tabs-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_header_space_left',
			[
				'label'     => __( 'Space Left', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs .tabs-header' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_header_item_space',
			[
				'label'     => __( 'Item Space', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs ul.tabs li:not(:first-child)' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .razzi-product-category-tabs ul.tabs li:not(:last-child)' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_header_align',
			[
				'label'       => esc_html__( 'Align', 'razzi' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'flex-start'   => [
						'title' => esc_html__( 'Left', 'razzi' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'razzi' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'razzi' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .razzi-product-category-tabs ul.tabs' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tab_header_divider',
			[
				'label' => '',
				'type'  => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_header_title',
				'selector' => '{{WRAPPER}} .razzi-product-category-tabs ul.tabs li a',
			]
		);
		$this->add_control(
			'tab_header_title_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs ul.tabs li a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'tab_header_active_color',
			[
				'label'     => esc_html__( 'Active Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs ul.tabs li a.active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .razzi-product-category-tabs ul.tabs li a:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_content_style() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-product-category-tabs .tabs-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .razzi-product-category-tabs .category-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_divider',
			[
				'label' => __( 'Title', 'razzi' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_title_spacing',
			[
				'label'     => __( 'Space', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
						'min' => 0,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs .category-list li .cat-thumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_divider_2',
			[
				'label' => '',
				'type'  => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_style_title',
				'selector' => '{{WRAPPER}} .razzi-product-category-tabs .category-list li .cat-name',
			]
		);
		$this->add_control(
			'content_style_title_color',
			[
				'label'     => esc_html__( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs .category-list li .cat-name' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'content_style_title_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-product-category-tabs .category-list li:hover .cat-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_carousel_style() {
		$this->start_controls_section(
			'section_carousel_style',
			[
				'label' => __( 'Carousel Setting', 'razzi' ),
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
					'{{WRAPPER}} .razzi-product-category-tabs .rz-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .rz-swiper-button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .rz-swiper-button:hover:not(.swiper-button-disabled)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing_horizontal',
			[
				'label'      => __( 'Horizontal Space', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => - 200,
						'max' => 300,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-product-category-tabs .rz-category-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .razzi-product-category-tabs .rz-category-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .rz-swiper-button' => 'top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .swiper-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .swiper-pagination .swiper-pagination-bullet:before' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active:before, {{WRAPPER}} .razzi-product-category-tabs .swiper-pagination .swiper-pagination-bullet:hover:before' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .swiper-container-horizontal > .swiper-pagination-bullets .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .razzi-product-category-tabs .swiper-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
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

		$nav        = $settings['navigation'];
		$nav_tablet = empty( $settings['navigation_tablet'] ) ? $nav : $settings['navigation_tablet'];
		$nav_mobile = empty( $settings['navigation_mobile'] ) ? $nav : $settings['navigation_mobile'];

		$this->add_render_attribute(
			'wrapper', 'class', [
				'razzi-product-category-tabs razzi-tabs razzi-swiper-carousel-elementor',
				$settings['product_cat'] == '' ? 'razzi-category-parent' : '',
				'navigation-' . $nav,
				'navigation-tablet-' . $nav_tablet,
				'navigation-mobile-' . $nav_mobile,
			]
		);

		$option_settings = [
			'image_size'    			=> $settings['image_size'],
			'image_custom_dimension'  	=> $settings['image_custom_dimension'],
			'cats_count' 				=> $settings['cats_count'],
		];

		$this->add_render_attribute( 'wrapper', 'data-option', wp_json_encode( $option_settings ) );

		$output      = [ ];
		$header_tabs = [ ];

		if ( $settings['product_cat'] ) {
			$cats = explode(',', $settings['product_cat']);

			$tab_content = [ ];

			$header_tabs[] = '<ul class="tabs-header tabs-nav tabs">';
			$i             = 0;
			foreach ( $cats as $tab ) {
				$term = get_term_by( 'slug', $tab, 'product_cat' );

				if ( is_wp_error( $term ) || !$term ) {
					continue;
				}

				$class_active = '';

				if ( $i == 0 ) {
					$class_active = 'active';
				}

				$header_tabs[] = sprintf( '<li><a href="#" data-href="%s" class="%s">%s</a></li>', esc_attr( $tab ), $class_active, esc_html( $term->name ) );

				if ( $i == 0 ) {
					$tab_content[] = sprintf(
						'<div class="tabs-panel tabs-%s tab-loaded active"><div class="tab-content swiper-container linked-products-category">%s</div></div>',
						esc_attr( $tab ),
						Helper::get_product_sub_categories_list( $settings, $term->term_id )
					);
				} else {

					$tab_atts = array(
						'term_id' 		=> $term->term_id,
						'orderby' 		=> $settings['orderby'],
						'order' 		=> $settings['order'],
					);

					$tab_content[] = sprintf(
						'<div class="tabs-panel tabs-%s" data-settings="%s"><div class="tab-content swiper-container linked-products-category"><div class="razzi-loading"></div></div></div>',
						esc_attr( $tab ),
						esc_attr( wp_json_encode( $tab_atts ) )
					);
				}

				$i ++;

			}

			$header_tabs[] = '</ul>';

			$output[] = sprintf( '%s<div class="tabs-content">%s</div>', implode( ' ', $header_tabs ), implode( ' ', $tab_content ) );
		} else {

			$parent_array = array(
				'taxonomy' => 'product_cat',
				'orderby'  => $settings['orderby'],
				'order'    => $settings['order']
			);

			$terms = get_terms($parent_array);
			$output[] = '<div class="tabs-panel tab-loaded active">';
			$output[] = '<div class="swiper-container linked-products-category">';
			$output[] = '<ul class="category-list swiper-wrapper">';

			foreach ( $terms as $term ) {
				$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
				$settings['image']['url'] = wp_get_attachment_image_src( $thumbnail_id );
				$settings['image']['id']  = $thumbnail_id;
				$image = Group_Control_Image_Size::get_attachment_image_html( $settings );

				$add_class = $thumbnail_html = $count = '';
				if ( $thumbnail_id ) {
					$thumbnail_html = sprintf( '<a class="cat-thumb image-zoom" href="%s">%s</a>', esc_url( get_term_link( $term->term_id, 'product_cat' ) ), $image);
					$add_class = 'has-thumbnail';
				}

				if ( $settings['cats_count'] == 'yes' ) {
					$count = '<span class="cat-count">';
					$count .= sprintf( _n( '(%s)', '(%s)', $term->count, 'razzi' ), number_format_i18n( $term->count ) );
					$count .= '</span>';
				}

				$output[] = sprintf(
					'<li class="cat-item %s">
						%s
						<a class="cat-name" href="%s">%s%s</a>
					</li>',
					esc_attr( $add_class ),
					$thumbnail_html,
					esc_url( get_term_link( $term->term_id, 'product_cat' ) ),
					$term->name,
					$count
				);
			}

			$output[] = '</ul>';

			$output[] = '</div>';
			$output[] = '</div>';
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo implode( '', $output ); ?>
        </div>
		<?php
	}
}