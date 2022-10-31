<?php

class Plugin_Feedback {

	/**
	 * Plugin Name.
	 *
	 * @since 1.0.0
	 */
	private static $store_name;
	private static $store_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_footer', array( $this, 'add_deactivation_popup_screen' ) );

		add_filter( 'smsalert_deactivation_form_fields', array( $this, 'add_deactivation_form_fields' ) );

		// Ajax to send data.
		add_action( 'wp_ajax_send_onboarding_data', array( $this, 'send_onboarding_data' ) );
		add_action( 'wp_ajax_nopriv_send_onboarding_data', array( $this, 'send_onboarding_data' ) );

		// Ajax to Skip popup.
		add_action( 'wp_ajax_skip_onboarding_popup', array( $this, 'skip_onboarding_popup' ) );
		add_action( 'wp_ajax_nopriv_skip_onboarding_popup', array( $this, 'skip_onboarding_popup' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( $this->is_valid_page_screen() ) {

			wp_enqueue_style( 'admin-feedback-style', plugins_url( '../css/feedback-admin.css', __FILE__ ), array(), SmsAlertConstants::SA_VERSION );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( $this->is_valid_page_screen() ) {

			wp_enqueue_script( 'admin-feedback-scripts', plugins_url( '../js/feedback-admin.js', __FILE__ ), array( 'jquery' ), SmsAlertConstants::SA_VERSION, true );

			global $pagenow;
			$current_slug = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';

			wp_localize_script(
				'admin-feedback-scripts',
				'smsf',
				array(
					'ajaxurl'                => admin_url( 'admin-ajax.php' ),
					'auth_nonce'             => wp_create_nonce( 'smsf_onboarding_nonce' ),
					'current_screen'         => $pagenow,
					'current_supported_slug' => $this->add_smsf_deactivation_screens( array( $current_slug ) ),
				)
			);
		}
	}

	public function add_deactivation_popup_screen() {
		global $pagenow;
		if ( ! empty( $pagenow ) && 'plugins.php' == $pagenow ) {
			require_once plugin_dir_path( __DIR__ ) . 'views/feedback-template.php';
		}
	}

	public function is_valid_page_screen() {

		global $pagenow;
		$screen   = get_current_screen();
		$is_valid = false;
		if ( ! empty( $screen->id ) ) {

			$is_valid = in_array( $screen->id, $this->add_smsf_frontend_screens() ) && $this->add_smsf_additional_validation();
		}

		if ( empty( $is_valid ) && 'plugins.php' == $pagenow ) {
			$is_valid = true;
		}

		return $is_valid;
	}

	public function add_deactivation_form_fields() {

		$current_user = wp_get_current_user();
		if ( ! empty( $current_user ) ) {
			$current_user_email = $current_user->user_email ? $current_user->user_email : '';
			$current_user_login = $current_user->user_login ? $current_user->user_login : '';
		}

		$store_name = get_bloginfo( 'name ' );
		$store_url  = get_home_url();

		// Do not repeat id index.

		$fields = array(

			/**
			 * Input field with label.
			 * Radio field with label ( select only one ).
			 * Radio field with label ( select multiple one ).
			 * Email field with label. ( auto filled with admin email )
			 */

			rand() => array(
				'id'          => 'deactivation-reason',
				'label'       => '',
				'type'        => 'radio',
				'name'        => 'plugin_deactivation_reason',
				'value'       => '',
				'multiple'    => 'no',
				'required'    => 'yes',
				'extra-class' => '',
				'options'     => array(
					'temporary-deactivation-for-debug' => 'It is a temporary deactivation. I am just debugging an issue.',
					'site-layout-broke'                => 'The plugin broke my layout or some functionality.',
					'complicated-configuration'        => 'The plugin is too complicated to configure.',
					'no-longer-need'                   => 'I no longer need the plugin',
					'found-better-plugin'              => 'I found a better plugin',
					'other'                            => 'Other',
				),
			),

			rand() => array(
				'id'          => 'deactivation-reason-text',
				'label'       => 'Let us know why you are deactivating {plugin-name} so we can improve the plugin',
				'type'        => 'textarea',
				'name'        => 'deactivation_reason_text',
				'value'       => '',
				'required'    => '',
				'extra-class' => 'smsf-keep-hidden',
			),

			rand() => array(
				'id'          => 'admin-email',
				'label'       => '',
				'type'        => 'hidden',
				'name'        => 'email',
				'value'       => $current_user_email,
				'required'    => '',
				'extra-class' => '',
			),

			rand() => array(
				'id'          => 'name',
				'label'       => '',
				'type'        => 'hidden',
				'name'        => 'name',
				'value'       => $current_user_login,
				'required'    => '',
				'extra-class' => '',
			),

			rand() => array(
				'id'          => 'store-name',
				'label'       => '',
				'type'        => 'hidden',
				'name'        => 'company',
				'value'       => $store_name,
				'required'    => '',
				'extra-class' => '',
			),

			rand() => array(
				'id'          => 'store-url',
				'label'       => '',
				'type'        => 'hidden',
				'name'        => 'website',
				'value'       => $store_url,
				'required'    => '',
				'extra-class' => '',
			),

			rand() => array(
				'id'          => 'plugin-name',
				'label'       => '',
				'type'        => 'hidden',
				'name'        => 'org_plugin_name',
				'value'       => '',
				'required'    => '',
				'extra-class' => '',
			),
		);

		return $fields;
	}

	public function render_field_html( $attr = array(), $base_class = 'on-boarding' ) {

		$id       = ! empty( $attr['id'] ) ? $attr['id'] : '';
		$name     = ! empty( $attr['name'] ) ? $attr['name'] : '';
		$label    = ! empty( $attr['label'] ) ? $attr['label'] : '';
		$type     = ! empty( $attr['type'] ) ? $attr['type'] : '';
		$class    = ! empty( $attr['extra-class'] ) ? $attr['extra-class'] : '';
		$value    = ! empty( $attr['value'] ) ? $attr['value'] : '';
		$options  = ! empty( $attr['options'] ) ? $attr['options'] : array();
		$multiple = ! empty( $attr['multiple'] ) && 'yes' === $attr['multiple'] ? 'yes' : 'no';
		$required = ! empty( $attr['required'] ) ? 'required="required"' : '';

		$html = '';

		if ( 'hidden' !== $type ) : ?>
			<div class ="smsf-customer-data-form-single-field">
			<?php
		endif;

		switch ( $type ) {

			case 'radio':
				// If field requires multiple answers.
				if ( ! empty( $options ) && is_array( $options ) ) :
					?>

					<label class="on-boarding-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $label ); ?></label>

					<?php
					$is_multiple = ! empty( $multiple ) && 'yes' !== $multiple ? 'name = "' . $name . '"' : '';

					foreach ( $options as $option_value => $option_label ) :
						?>
						<div class="smsf-<?php echo esc_html( $base_class ); ?>-radio-wrapper">
							<input type="<?php echo esc_attr( $type ); ?>" class="on-boarding-<?php echo esc_attr( $type ); ?>-field <?php echo esc_attr( $class ); ?>" value="<?php echo esc_attr( $option_value ); ?>" id="<?php echo esc_attr( $option_value ); ?>" <?php echo esc_html( $required ); ?> <?php echo esc_attr( $is_multiple ); ?>>
							<label class="on-boarding-field-label" for="<?php echo esc_html( $option_value ); ?>"><?php echo esc_html( $option_label ); ?></label>
						</div>
					<?php endforeach; ?>

					<?php
				endif;

				break;

			case 'label':
				// Only a text in label.
				?>
				<label class="on-boarding-label <?php echo( esc_html( $class ) ); ?>" for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $label ) ); ?></label>
				<?php
				break;

			case 'textarea':
				// Text Area Field.
				?>
				<textarea rows="3" cols="50" class="<?php echo( esc_html( $base_class ) ); ?>-textarea-field <?php echo( esc_html( $class ) ); ?>" placeholder="<?php echo( esc_attr( $label ) ); ?>" id="<?php echo( esc_attr( $id ) ); ?>" name="<?php echo( esc_attr( $name ) ); ?>"><?php echo( esc_attr( $value ) ); ?></textarea>

				<?php
				break;

			default:
				// Text/ Password/ Email.
				?>
				<label class="on-boarding-label" for="<?php echo( esc_attr( $id ) ); ?>"><?php echo( esc_html( $label ) ); ?></label>
				<input type="<?php echo( esc_attr( $type ) ); ?>" class="on-boarding-<?php echo( esc_attr( $type ) ); ?>-field <?php echo( esc_attr( $class ) ); ?>" value="<?php echo( esc_attr( $value ) ); ?>"  name="<?php echo( esc_attr( $name ) ); ?>" id="<?php echo( esc_attr( $id ) ); ?>" <?php echo( esc_html( $required ) ); ?>>

				<?php
		}

		if ( 'hidden' !== $type ) :
			?>
			</div>
			<?php
		endif;
	}

	public function send_onboarding_data() {

		check_ajax_referer( 'smsf_onboarding_nonce', 'nonce' );

		$form_data = ! empty( $_POST['form_data'] ) ? wp_unslash( $_POST['form_data'] ) : '';

		$formatted_data = array();
		if ( ! empty( $form_data ) && is_array( $form_data ) ) {

			foreach ( $form_data as $key => $input ) {

				$key = str_replace( '"', '', $input['name'] );

				array_push(
					$formatted_data,
					array(
						$key => $input['value'],
					)
				);
			}
		}
		
		$param = array();
		foreach ( $formatted_data as $child_array ) {
			foreach ( $child_array as $key => $value ) {
				$param[ $key ] = $value;
			}
		}
		if ( ! empty( $param['plugin_deactivation_reason'] ) ) {
			$param['body'] = $param['plugin_deactivation_reason']." <br/> submitted by ".$param['email'];
		}
		if ( ! empty( $param['deactivation_reason_text'] ) ) {
			$param['body'] = $param['body'] . ' ' . $param['deactivation_reason_text']." <br/>submitted by ".$param['email'];
		}
		
		$param['shop_id'] = (!empty($param['website']) ) ? $param['website'] : get_site_url();
		
		$param['name'] =  smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );

		$url = 'https://apps.smsalert.co.in/apps/wordpress/webhook.php?topic=app/uninstalled';
		$result = SmsAlertcURLOTP::callAPI( $url, $param );
		echo json_encode( $formatted_data );
		wp_die();
	}

	public function skip_onboarding_popup() {

		$get_skipped_timstamp = update_option( 'onboarding-data-skipped', time() );
		echo json_encode( 'true' );
		wp_die();
	}

	public function add_smsf_additional_validation( $result = true ) {

		if ( ! empty( $_GET['tab'] ) && 'ced_rnx_setting' === $_GET['tab'] ) {
			$result = true;
		} else {
			$result = false;
		}

		return $result;
	}

	public function add_smsf_frontend_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'woocommerce_page_wc-settings' );
		}
		return $valid_screens;
	}

	public function add_smsf_deactivation_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'sms-alert' );
		}

		return $valid_screens;
	}
}
new Plugin_Feedback();
