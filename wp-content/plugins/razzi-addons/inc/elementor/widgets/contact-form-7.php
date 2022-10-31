<?php

namespace Razzi\Addons\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon Box widget
 */
class Contact_Form_7 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'razzi-contact-form-7';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Razzi - Contact Form 7', 'razzi' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-mail';
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

	protected function section_content() {

		$this->start_controls_section(
			'section_content',
			[ 'label' => esc_html__( 'Content', 'razzi' ) ]
		);

		$this->add_control(
			'form',
			[
				'label'   => esc_html__( 'Contact Form 7', 'razzi' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_contact_form(),
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Textarea Field Height', 'razzi' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 60,
						'max' => 300,
					],
				],
				'default' => [],
				'selectors' => [
					'{{WRAPPER}} .razzi-contact-form-7 textarea' => 'height: {{SIZE}}{{UNIT}};',
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
				'label' => __( 'Content', 'razzi' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_align',
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
				'default'   => 'left',
				'selectors'   => [
					'{{WRAPPER}} .razzi-contact-form-7 .wpcf7' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => '',
					'center'   => 'margin:auto',
					'right'   => 'margin-left:auto; margin-right:0;',
				],
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label'      => __( 'Form Width', 'razzi' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-contact-form-7 .wpcf7' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'razzi' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'general_padding',
			[
				'label'      => __( 'Input Field', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-contact-form-7 input:not([type="submit"]), {{WRAPPER}} .razzi-contact-form-7 textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'general_padding_2',
			[
				'label'      => __( 'Submit Field', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-contact-form-7 input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'general_padding_3',
			[
				'label'      => __( 'Textarea Field', 'razzi' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .razzi-contact-form-7 textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'razzi-contact-form-7'
		];

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $settings['form'] ) :
				echo do_shortcode( '[contact-form-7 id="' . esc_attr( $settings['form'] ) . '"]' );
			endif; ?>
		</div>
		<?php
	}

	/**
	 * Get Contact Form
	 */
	protected function get_contact_form() {
		$mail_forms    = get_posts( 'post_type=wpcf7_contact_form&posts_per_page=-1' );
		$mail_form_ids = array(
			'' => esc_html__( 'Select Form', 'razzi' ),
		);
		foreach ( $mail_forms as $form ) {
			$mail_form_ids[$form->ID] = $form->post_title;
		}

		return $mail_form_ids;
	}
}