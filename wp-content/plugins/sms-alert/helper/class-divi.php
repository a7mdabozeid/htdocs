<?php
/**
 * Class Divi.
 */

class SMSAlertDivi {

	public function __construct()
	{
		$this->load();
		$this->allow_load();
     
	}
	/**
	 * Load integration
	 *
	 * @return bool
	 */
	public function allow_load() {

		if ( function_exists( 'et_divi_builder_init_plugin' ) ) {
			return true;
		}

		$allow_themes = [ 'Divi', 'Extra' ];
		$theme        = wp_get_theme();
		$theme_name   = $theme->get_template();
		$theme_parent = $theme->parent();

		return (bool) array_intersect( [ $theme_name, $theme_parent ], $allow_themes );
	}

	/**
	 * Load integration
	 */
	public function load() {
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
        
		add_action( 'et_builder_ready', [ $this, 'register_module' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_styles' ], 12 );

		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_smsalert_divi_preview', [ $this, 'preview' ] );
		}

		if ( $this->is_divi_builder() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'builder_scripts' ] );
        
		}

	}

	/**
	 * Check is div
	 *
	 * @return bool
	 */
	private function is_divi_builder() {
		return ! empty( $_GET['et_fb'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}


	/**
	 * Get current style name.
	 * Overwrite st
	 *
	 * @return string
	 */
	public function get_current_styles_name() {

		$disable_css ='disable-css';
		if ( 1 === $disable_css ) {
			return 'full';
		}
		if ( 2 === $disable_css ) {
			return 'base';
		}

		return '';
	}

	/**
	 * Is the Divi 
	 *
	 * @return bool
	 */
	protected function is_divi_plugin_loaded() {

		if ( ! is_singular() ) {
			return false;
		}

		return function_exists( 'et_is_builder_plugin_active' );
	}

	/**
	 * Register frontend_styles
	 */
	public function frontend_styles() {

		if ( ! $this->is_divi_plugin_loaded() ) {
			return;
		}
	
	}

	/**
	 * Load scripts
	 */
	public function builder_scripts() {
        wp_enqueue_script( 'smsalert-divi', SA_MOV_URL . 'js/divi.js', [ 'react', 'react-dom' ], SmsAlertConstants::SA_VERSION, true );

		wp_localize_script(
			'smsalert-divi',
			'smsalert_divi_builder',
			[
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'smsalert_divi_builder' ),
				'placeholder'       => '',
				'placeholder_title' => esc_html__( 'SMSAlert', 'smsalert' ),
			]
		);
	}

	/**
	 * Register mod
	 */

    public function register_module() {
        if ( ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}
		require_once(plugin_dir_path(__FILE__)."class-divimodule.php");
		new SMSAlertSelector();

	}
    
	/**
	 * Ajax handler
	 */
	public function preview() {

		check_ajax_referer( 'smsalert_divi_builder', 'nonce' );

		$form_id    = filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_STRING ) ;

		add_action(
			'smsalert_frontend_output',
			function () {
				echo '<fieldset disabled>';
			},
			3
		);
		add_action(
			'smsalert_frontend_output',
			function () {

				echo '</fieldset>';
			},
			30
		);
		if($form_id!='')
		{
        $shortcode = ($form_id==1)?'[sa_signupwithmobile]':(($form_id==2)?'[sa_loginwithotp]':'[sa_sharecart]');
		wp_send_json_success(
			do_shortcode($shortcode)
		);
		}
	}
}
new SMSAlertDivi();

