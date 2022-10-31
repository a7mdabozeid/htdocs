<?php
namespace Razzi\Addons\Elementor\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Base\Module;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Core\DynamicTags\Dynamic_CSS;

class Motion_Parallax extends Module {
	public function __construct() {
		$this->add_actions();
	}

	public function get_name() {
		return 'motion-parallax';
	}

	public function enqueue_frontend_scripts() {
		wp_enqueue_script( 'jarallax' );
		wp_enqueue_script( 'razzi-elementor-parallax' );
	}

	public function register_controls( $element ) {
		$element->add_control(
			'background_motion_fx_motion_fx_scrolling',
			[
				'label' => __( 'Parallax Scrolling', 'razzi' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'razzi' ),
				'label_on' => __( 'On', 'razzi' ),
				'render_type' => 'ui',
				'frontend_available' => true,
				'separator' => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'terms' => [
								[
									'name' => 'background_background',
									'value' => 'classic',
								],
								[
									'name' => 'background_image[url]',
									'operator' => '!==',
									'value' => '',
								],
							],
						],
						[
							'terms' => [
								[
									'name' => 'background_background',
									'value' => 'gradient',
								],
								[
									'name' => 'background_color',
									'operator' => '!==',
									'value' => '',
								],
								[
									'name' => 'background_color_b',
									'operator' => '!==',
									'value' => '',
								],
							],
						],
					],
				],
			]
		);
	}

	protected function add_actions() {
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );

		add_action( 'elementor/element/section/section_background/before_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/column/section_style/before_section_end', [ $this, 'register_controls' ] );
	}
}