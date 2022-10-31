<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! is_plugin_active( 'elementor/elementor.php' ) ) {
	return; }

if ( ! is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
	return; }

class Elementor extends ElementorPro\Modules\Forms\Fields\Field_Base {
	
	public function get_type() {
		return 'sa_billing_phone';
	}

	public function get_name() {
		return __( 'SMSAlert', 'sms-alert' );
	}

	public function __construct() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		if ( !$islogged ) { return; }
		
		parent::__construct();

		add_action( 'elementor_pro/init', [ $this, 'add_custom_action' ]);
		add_action( 'elementor/widget/before_render_content', [ $this, 'add_shortcode' ] );	
		add_filter( 'elementor_pro/forms/field_types', [ $this, 'register_field_type' ] );
		add_action( 'elementor/preview/init', [ $this, 'editor_inline_JS' ] );
	}
	
	public function editor_inline_JS() {
		add_action( 'wp_footer', function() {
		?>
		<script>
		var ElementorFormSAField = ElementorFormSAField || {};
		jQuery( document ).ready( function( $ ) {
		
			function renderField( inputField, item, i, settings ) {
				var itemClasses = item.css_classes,
					required = '',
					fieldName = 'form_field_';

				if ( item.required ) {
					required = 'required';
				}
				return '<input type="sa_billing_phone" class="elementor-field-textual ' + itemClasses + '" name="' + fieldName + '" id="form_field_' + i + '" ' + required + ' placeholder="' + item.field_label + '">';
			}
			
			elementor.hooks.addFilter( 'elementor_pro/forms/content_template/field/sa_billing_phone', renderField, 10, 4 );
		} );
		</script>
		<?php
		} );	
	}
	
	public function register_field_type( $fields ) {
		ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
		$fields[ self::get_type() ] = self::get_name();
		return $fields;
	}

	public function add_shortcode($form){
		if( 'form' === $form->get_name() ) {
			$country_flag_enable    = smsalert_get_option( 'checkout_show_country_code', 'smsalert_general' );
			
    		$settings 				= $form->get_settings();
			$form_name 				= $settings['form_name'];
			$fields	   				= $settings['form_fields'];
			
			foreach($fields as $field){
				if( $field['field_type'] == 'sa_billing_phone' ){
					if( 'on' === $country_flag_enable ){
						$uniqueNo = rand();
						$unique  = $form->get_id();
						echo '<script>
						jQuery(document).ready(function(){
							jQuery(".elementor-form #form-field-'.$field['custom_id'].'").each(function () 
							{
								jQuery(this).addClass("phone-valid");
							});	
							initialiseCountrySelector(".phone-valid");						
						})
						</script>';
					}
					if( 'true' === $settings['otp_verification_enable'] ) {
						
						$uniqueNo = rand();
						$unique  = $form->get_id();
						echo '<script>
						jQuery(document).on("elementor/popup/show", (event, id, instance) => {
						 add_smsalert_button(".elementor-element-'.$unique.' .elementor-field-type-submit .elementor-button","#form-field-'.$field['custom_id'].'","'.$uniqueNo.'");
							jQuery(document).on("click", "#sa_verify_'.$uniqueNo.'",function(event){
							event.stopImmediatePropagation();
							send_otp(this,".elementor-element-'.$unique.' .elementor-field-type-submit .elementor-button","#form-field-'.$field['custom_id'].'","","");
							});	
							initialiseCountrySelector(".phone-valid");	
						});</script>'; 
						
						echo do_shortcode( '[sa_verify id="" phone_selector="#form-field-'.$field['custom_id'].'" submit_selector=".elementor-element-'.$unique.' .elementor-field-type-submit .elementor-button"]' );
					?>
					<script>
						jQuery(document).ready(function(){
							function addModalInForm(){

								jQuery(".modal.smsalertModal").each(function(){

									var form_id = jQuery(this).attr("data-form-id");

									if( form_id.indexOf("saFormNo_") > -1){

										var class_unq = form_id.substring(form_id.indexOf("_")+ 1);								jQuery("#sa_verify_"+class_unq).parents('form').append(jQuery(".modal.smsalertModal[data-form-id="+form_id+"]"));
									}
								});
							}
							setTimeout(function(){ addModalInForm(); }, 3000);
						});
					</script>
					<?php
					}
				}
			}
  		}
	}

	// Add action smsalert
	public function add_custom_action(){
		// Instantiate the action class
		$smsalert_action = new Sendmsms_Action_After_Submit;

		// Register the action with form widget
		\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $smsalert_action->get_name(), $smsalert_action );
	}

	/**
	 * @param string      $item
	 * @param integer     $item_index
	 * @param Widget_Base $widget
	 */
	public function render( $item, $item_index, $form ) {
	
		$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual' );
		
		$form->add_render_attribute( 'input' . $item_index, 'type', 'sa_billing_phone', true );
		$form->add_render_attribute( 'input' . $item_index, 'placeholder', $item['field_label'] );
		
		echo '<input ' . $form->get_render_attribute_string( 'input' . $item_index ) . '>';
	}
}
new Elementor();

/**
 * Class Sendmsms_Action_After_Submit
 * Custom elementor form action after submit to redirect to smsalert
 * Sendmsms_Action_After_Submit
 */

class Sendmsms_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */

	public function get_name() {
		return 'smsalert';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */

	public function get_label() {
		return __( 'SMSAlert', 'sms-alert' );
	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_smsalert',
			[
				'label' => __( 'SMS Alert', 'sms-alert' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		
		$widget->add_control(
			'otp_verification_enable',
			[
				'label' => __( 'OTP verification', 'sms-alert' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'sms-alert' ),
				'label_off' => __( 'Off', 'sms-alert' ),
				'return_value' => 'true',
				'default' => 'true',
			]
		);
		
		$widget->add_control(
			'customer_sms_enable',
			[
				'label' => __( 'Customer SMS', 'sms-alert' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'sms-alert' ),
				'label_off' => __( 'Off', 'sms-alert' ),
				'return_value' => 'true',
				'default' => 'true',
			]
		);

		$widget->add_control(
			'customer_message',
			[
				'label' => __( 'Customer Message', 'sms-alert' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Write yout text or use fields shortcode', 'sms-alert' ),
				'label_block' => true,
				'render_type' => 'none',
				'default' => sprintf( __( 'Hello user, thank you for contacting with %1$s.', 'sms-alert' ), '[shop_url]' ),
				'classes' => '',
				'description' => __( 'Use fields shortcodes for send form data or write your custom text.', 'sms-alert' ),
			]
		);

		$widget->add_control(
			'admin_sms_enable',
			[
				'label' => __( 'Admin SMS', 'sms-alert' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'sms-alert' ),
				'label_off' => __( 'Off', 'sms-alert' ),
				'return_value' => 'true',
				'default' => 'true',
			]
		);
		
		$widget->add_control(
			'admin_number',
			[
				'label' => __( 'Admin Phone', 'sms-alert' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( '8010551055', 'sms-alert' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => '',
				'description' => __( 'Send Message to admin on this number', 'sms-alert' ),
			]
		);

		$widget->add_control(
			'admin_message',
			[
				'label' => __( 'Admin Message', 'sms-alert' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Write yout text or use fields shortcode', 'sms-alert' ),
				'label_block' => true,
				'render_type' => 'none',
				'default' => sprintf( __( 'Dear admin, you have a new enquiry from %1$s.%2$sPowered by%3$swww.smsalert.co.in', 'sms-alert' ), '[shop_url]', PHP_EOL, PHP_EOL ),
				'classes' => '',
				'description' => __( 'Use fields shortcodes for send form data or write your custom text.', 'sms-alert' ),
				'separator' => 'after',

			]
		);

		$widget->end_controls_section();
	}


	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */

	public function on_export( $element ) {
		unset(
			$element['settings']['otp_verification_enable'],
			$element['settings']['admin_sms_enable'],
			$element['settings']['admin_number'],
			$element['settings']['admin_message'],
			$element['settings']['customer_sms_enable'],
			$element['settings']['customer_message']
		);
		return $element;
	}


	/**
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */

	public function run( $record, $ajax_handler ) {

		if(!$ajax_handler->is_success){
			return;
		}

		$admin_number 			= $record->get_form_settings( 'admin_number' );
		$admin_message 			= $record->get_form_settings( 'admin_message' );
		$customer_message 		= $record->get_form_settings( 'customer_message' );
		$customer_sms_enable	= $record->get_form_settings( 'customer_sms_enable' );
		$admin_sms_enable 		= $record->get_form_settings( 'admin_sms_enable' );

		// get form fields
		$fields      			= $record->get( 'fields' );

		if ( 'true' === $customer_sms_enable && '' !== $customer_message ) {

			$cust_phone = '';
			foreach ( $fields as $field ) {
				if ( $field['type'] == 'sa_billing_phone' ) {
					$cust_phone = $field['value'];
				}
			}

			$message = $this->parse_sms_body( $fields, $customer_message );
			do_action( 'sa_send_sms', $cust_phone, $message );
		}

		if ( 'true' === $admin_sms_enable && '' !== $admin_message && '' !== $admin_number) {

			$message = $this->parse_sms_body( $fields, $admin_message );
			do_action( 'sa_send_sms', $admin_number, $message );
		}
	}

	public function parse_sms_body( $fields, $message ){

		$replaced_arr = array();

		foreach ( $fields as $key => $val ) {

			$replaced_arr['[field id="'.$key.'"]'] = $val['value'];
		}

		$message = str_replace( array_keys( $replaced_arr ), array_values( $replaced_arr ), $message );
		return $message;
	}

}
new Sendmsms_Action_After_Submit();