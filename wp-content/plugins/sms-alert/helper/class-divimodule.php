<?php
/**
 * Class SMSAlertSelector.
 *
 * @since 1.6.3
 */
class SMSAlertSelector extends ET_Builder_Module {

	/**
	 * Module slug.
	 *
	 * @var string
	 */
	public $slug = 'smsalert_selector';

	/**
	 * VB support.
	 *
	 * @var string
	 */
	public $vb_support = 'on';

	/**
	 * Init module.
	 *
	 * @since 1.6.3
	 */
	public function init() {

		$this->name = esc_html__( 'SMSAlert', 'smsalert' );
	}

	/**
	 * Get list of settings.
	 *
	 * @since 1.6.3
	 *
	 * @return array
	 */
	public function get_fields() {

        $forms = array(''=>'Select Form','1'=>'Signup With Mobile','2'=>'Login With Otp','3'=>'Share Cart Button');
		return [
			'form_id'    => [
				'label'           => esc_html__( 'Form', 'wpforms-lite' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => $forms,
			]
		];
	}


	/**
	 * Disable advanced fields configuration.
	 *
	 * @since 1.6.3
	 *
	 * @return array
	 */
	public function get_advanced_fields_config() {

		return [
			'link_options' => false,
			'text'         => false,
			'background'   => false,
			'borders'      => false,
			'box_shadow'   => false,
			'button'       => false,
			'filters'      => false,
			'fonts'        => false,
		];
	}

	/**
	 * Render module on the frontend.
	 *
	 * @since 1.6.3
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content = null, $render_slug = '' ) {

		if ( $this->props['form_id']!='' ) {
		$shortcode = ($this->props['form_id']==1)?'[sa_signupwithmobile]':(($this->props['form_id']==2)?'[sa_loginwithotp]':'[sa_sharecart]');
		return do_shortcode($shortcode);
		}
		return '';
	}
}
