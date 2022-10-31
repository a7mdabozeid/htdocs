<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background ;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Newsletter widget
 */
class Newsletter_2 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-newsletter-2';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Razzi - Newsletter 2', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-mailchimp';
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
			'title',
			[
				'label'       => esc_html__( 'Title', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is title', 'razzi' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Desc', 'razzi' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'This is desc', 'razzi' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'form',
			[
				'label'   => esc_html__( 'Mailchimp Form', 'razzi' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_contact_form(),
			]
		);

		$this->add_control(
			'background_heading',
			[
				'label' => esc_html__( 'Background', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'banners_background',
				'label'    => __( 'Background', 'razzi' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .razzi-newsletter-2',
				'fields_options'  => [
					'background' => [
						'default' => 'classic',
					]
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Section Style
	 */
	protected function section_style() {
		$this->section_content_style();
		$this->section_heading_style();
		$this->section_form_style();
		$this->section_field_style();
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
					'{{WRAPPER}} .razzi-newsletter-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function section_heading_style() {
		// Content
		$this->start_controls_section(
			'section_heading_style',
			[
				'label' => __( 'Heading', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'razzi' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2__heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs(
			'style_tabs_heading_content'
		);

		// Title
		$this->start_controls_tab(
			'content_headin_style_title',
			[
				'label' => __( 'Title', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_title_typography',
				'selector' => '{{WRAPPER}} .razzi-newsletter-2__heading .newsletter-title',
			]
		);

		$this->add_control(
			'heading_title_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2__heading .newsletter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Desc
		$this->start_controls_tab(
			'heading_desc',
			[
				'label' => __( 'Desc', 'razzi' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .razzi-newsletter-2__heading .newsletter-desc',
			]
		);



		$this->add_control(
			'desc_color',
			[
				'label'     => __( 'Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2__heading .newsletter-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * Form
	 */
	protected function section_form_style() {
		$this->start_controls_section(
			'section_form_style',
			[
				'label' => __( 'Form', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'form_width',
			[
				'label'      => __( 'Width', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-newsletter-2 .mc4wp-form'      => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_align',
			[
				'label'       => esc_html__( 'Align', 'razzi' ),
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
					'{{WRAPPER}} .razzi-newsletter-2' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'field_display',
			[
				'label'     => __( 'Display', 'farmart' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Default', 'farmart' ),
				'label_on'  => __( 'Colmns', 'farmart' ),
				'default'   => '',
				'selectors_dictionary' => [
					'yes' => 'flex-direction: column;',
				],
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2 .mc4wp-form-fields' => '{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_border_toggle',
			[
				'label'        => __( 'Border', 'razzi' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'razzi' ),
				'label_on'     => __( 'Custom', 'razzi' ),
				'return_value' => 'yes',
			]
		);
		$this->start_popover();

		$this->add_control(
			'field_border_style',
			[
				'label'     => esc_html__( 'Border Style', 'razzi' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'dotted' => esc_html__( 'Dotted', 'razzi' ),
					'dashed' => esc_html__( 'Dashed', 'razzi' ),
					'solid'  => esc_html__( 'Solid', 'razzi' ),
					'none'   => esc_html__( 'None', 'razzi' ),
					''       => esc_html__( 'Default', 'razzi' ),
				],
				'default'   => '',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .mc4wp-form-fields' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'field_border_color',
			[
				'label'     => __( 'Border Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mc4wp-form-fields' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'field_border_width',
			[
				'label'       => __( 'Border Width', 'razzi' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px' ],
				'selectors'   => [
					'{{WRAPPER}} .mc4wp-form-fields' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'field_border_radius',
			[
				'label'      => __( 'Border Radius', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mc4wp-form-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	/**
	 * Element in Tab Style
	 *
	 * Field
	 */
	protected function section_field_style() {
		$this->start_controls_section(
			'section_field_style',
			[
				'label' => __( 'Field', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'email_heading',
			[
				'label' => esc_html__( 'Email Field', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'field_margin',
			[
				'label'      => __( 'Margin', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-newsletter-2  .mc4wp-form-fields input[type=text], {{WRAPPER}} .razzi-newsletter-2  .mc4wp-form-fields input[type=email]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-newsletter-2  .mc4wp-form-fields input[type=text], {{WRAPPER}} .razzi-newsletter-2  .mc4wp-form-fields input[type=email]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'     => __( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2 .mc4wp-form-fields input[type="email"], {{WRAPPER}} .razzi-newsletter-2 .mc4wp-form-fields input[type="email"]::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'field_typography',
				'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type=text], {{WRAPPER}} .mc4wp-form-fields input[type=email]',
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .mc4wp-form-fields input[type=text], {{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'background-color: {{VALUE}};',
				],
			]
		);

		// Submit
		$this->add_control(
			'submit_heading',
			[
				'label' => esc_html__( 'Submit Field', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'field_submit_padding',
			[
				'label'      => __( 'Padding', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-newsletter-2  .mc4wp-form-fields input[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => __( 'Background Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2 .mc4wp-form-fields input[type=submit]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __( 'Text Color', 'razzi' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .razzi-newsletter-2 .mc4wp-form-fields input[type=submit]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .razzi-newsletter-2 .mc4wp-form-fields input[type=submit]',
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
			'razzi-newsletter-2'
		];

		// heading
		$title = $settings['title'] ? sprintf('<h3 class="newsletter-title">%s</h3>',$settings['title']) : '';
		$desc = $settings['desc'] ? sprintf('<div class="newsletter-desc">%s</div>',$settings['desc']) : '';
		$heading_html = $desc == '' && $title == '' ? '' : sprintf('<div class="razzi-newsletter-2__heading">%s %s</div>',$title,$desc );

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo ! empty($heading_html) ? $heading_html : '' ?>
			<?php if ( $settings['form'] ) :
				echo do_shortcode( '[mc4wp_form id="' . esc_attr( $settings['form'] ) . '"]' );
			endif; ?>
		</div>
		<?php
	}

	/**
	 * Get Contact Form
	 */
	protected function get_contact_form() {
		$mail_forms    = get_posts( 'post_type=mc4wp-form&posts_per_page=-1' );
		$mail_form_ids = array(
			'' => esc_html__( 'Select Form', 'razzi' ),
		);
		foreach ( $mail_forms as $form ) {
			$mail_form_ids[$form->ID] = $form->post_title;
		}

		return $mail_form_ids;
	}
}