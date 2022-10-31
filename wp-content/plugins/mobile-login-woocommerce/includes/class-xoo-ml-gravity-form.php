<?php

class Xoo_Ml_Gf_Phone extends GF_Field_Phone {
 
    public $type = 'xoo_ml_phone';

    public $phoneFormat = 'international';

    public static function hooks(){
    	add_action( 'gform_field_standard_settings', array( __CLASS__, 'form_type_setting' ), 10, 2 );
    	add_action( 'gform_editor_js', array( __CLASS__, 'editor_script' ), 10, 2 );
    	add_filter( 'gform_field_content', array( __CLASS__, 'field_html' ), 99, 5 );
    	add_filter( 'gform_field_validation', array( __CLASS__, 'custom_validation' ), 99, 4 );
    	add_filter( 'xoo_ml_get_phone_forms', array( __CLASS__, 'add_to_phone_form' ) );
    	add_action( 'gform_pre_submission', array( __CLASS__, 'pre_submission_handler' ) );    }

    public function get_form_editor_field_title() {
	    return esc_attr__( 'OTP Phone', 'gravityforms' );
	}
	
	public static function form_type_setting( $position, $form_id ) {
	 
	    //create settings on position 25 (right after Field Label)
	    if ( $position == 25 ) {
	        ?>
	        <li class="xoo_ml_phone_type_setting field_setting">
	            <label for="field_admin_label">
	                <?php esc_html_e( 'Form Type', 'gravityforms' ); ?>
	            </label>
	            <select id="xoo_ml_phone_type" onchange="SetFieldProperty('xooMlPhoneType', this.value);">
	            	<option value="register_user"><?php _e( 'Register User', 'gravityforms' ); ?></option>
	            	<option value="gf_default" selected="selected"><?php _e( 'Save as a field entry', 'gravityforms' ); ?></option>
	            </select>
	        </li>

	         <li class="xoo_ml_phone_code_setting field_setting">
	            <input type="checkbox" id="xoo_ml_phone_code" checked="checked" onclick="SetFieldProperty('xooMlPhoneCode', this.checked);" /><?php esc_html_e( 'Enable Country Code', 'gravityforms' ); ?>
	        </li>
	        <?php
	    }
	}



	public static function editor_script(){
	    ?>
	    <script type='text/javascript'>
	        //adding setting to fields of type "text"
	        fieldSettings.xoo_ml_phone += ', .xoo_ml_phone_type_setting, .xoo_ml_phone_code_setting';

	 		// Make sure our field gets populated with its saved value
			jQuery(document).on("gform_load_field_settings", function(event, field, form) {
	        	jQuery('#xoo_ml_phone_type option[value="'+field["xooMlPhoneType"]+'"]').prop('selected', true);
	        	jQuery('#xoo_ml_phone_code').attr('checked', field.xooMlPhoneCode == true);
	    	});

	    </script>
	    <?php
	}



	public static function field_html( $content, $field, $value, $lead_id, $form_id ) {
 
	    if( $field->type !== 'xoo_ml_phone' || is_admin() ) return $content;

	    $content = '';

	   	$description = '<div class="gfield_description">'.esc_html( $field->description ).'</div>';

	   	if( $field->descriptionPlacement === 'above' ){
	    	$content .= $description;
	    }


	    $args = array(
	    	'label' 		=> $field->label,
	    	'label_class' 	=> array('gfield_label'),
	    	'cc_show' 		=> $field->xooMlPhoneCode == 1 ? 'yes' : 'no',
	    	'otp_display' 	=> 'inline_input',
			'show_phone' 	=> 'required',
			'default_phone' => isset( $_POST['xoo-ml-reg-phone'] ) ? sanitize_text_field( $_POST['xoo-ml-reg-phone'] ) : '',	
	    );

	    if( isset( $_POST['xoo-ml-reg-phone-cc'] ) ){
	    	$args['default_cc'] = sanitize_text_field( $_POST['xoo-ml-reg-phone-cc'] );
	    }

	    $content .= xoo_ml_get_phone_input_field( $args, true );

	    if( $field->descriptionPlacement === 'below' || !$field->descriptionPlacement ){
	    	$content .= $description;
	    }


	    return $content;
	}


	public static function custom_validation( $result, $value, $form, $field ) {

		if( $field['type'] === 'xoo_ml_phone' ){
			$result['is_valid'] = true;
		}
	 
	    return $result;
	}


	public static function add_to_phone_form( $forms ){

		if( !class_exists( 'GFCommon' ) ) return $forms;

		$GravityForms = GFAPI::get_forms();
		
		foreach( $GravityForms as $gravityForm ) {
			foreach ( $gravityForm['fields'] as $gfFieldObj ) {

				if( $gfFieldObj['type'] === 'xoo_ml_phone' ){
					$forms[] = array(
						'key'			=> 'gform_submit',
						'value' 		=> $gravityForm['id'],
						'form' 			=> $gfFieldObj->xooMlPhoneType,
						'required' 		=> 'yes',
						'cc_required' 	=> $gfFieldObj->xooMlPhoneCode == 1 ? 'yes' : 'no'
					);

				}
			}
		}

		return $forms;


	}

	public static function pre_submission_handler( $form ) {
		foreach ( $form['fields'] as $field ) {
			if( $field->type === 'xoo_ml_phone' ){

				$phone = '';

				if( isset( $_POST['xoo-ml-reg-phone-cc'] ) ){
					$phone .= sanitize_text_field( $_POST['xoo-ml-reg-phone-cc'] );
				}

				if( isset( $_POST['xoo-ml-reg-phone'] ) ){
					$phone .= sanitize_text_field( $_POST['xoo-ml-reg-phone'] );
				}

				$_POST[ 'input_'.$field->id ] = $phone;
				break;
			}
		}
	}

	public function get_form_editor_field_settings(){
		$default = array_flip(parent::get_form_editor_field_settings());
		unset( $default['phone_format_setting'] );
		return array_flip($default);
	}
 
		 
}
GF_Fields::register( new Xoo_Ml_Gf_Phone() );
Xoo_Ml_Gf_Phone::hooks();
?>