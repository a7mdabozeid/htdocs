<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Xoo_Ml_Users_Table{


	protected static $_instance = null;

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	public function __construct(){
		add_action( 'edit_user_profile', array( $this, 'edit_profile_page' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_fields' ) );
		add_action( 'user_profile_update_errors', array( $this, 'verify_user_fields' ), 10, 3 );
		add_filter( 'xoo_el_user_profile_fields', array( $this, 'remove_phone_fields' ) );

		add_filter( 'manage_users_columns', array( $this, 'add_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'columns_output' ), 11, 3 );
	}


	public function remove_phone_fields( $fields ){
		unset( $fields['xoo-ml-reg-phone'], $fields['xoo-ml-reg-phone-cc']  );
		return $fields;
	}


	public function verify_user_fields( $wp_error, $update, $user ){
		if( isset( $_POST['xoo-ml-user-reg-phone'] ) && $_POST['xoo-ml-user-reg-phone'] ){
			if( !isset( $_POST['xoo-ml-user-reg-phone-cc'] ) || !$_POST['xoo-ml-user-reg-phone-cc'] ){
				$wp_error->add( 'no-phone-code', __( 'Please select country code', 'mobile-login-woocommerce' ) );
			}
			$user_by_phone = xoo_ml_get_user_by_phone( $_POST['xoo-ml-user-reg-phone'], $_POST['xoo-ml-user-reg-phone-cc'] );
			if( $user_by_phone && $user_by_phone->ID !== $user->ID  ){
				$wp_error->add( 'user-already-exists', sprintf( __( 'User: #%1s is already registered with %2s phone number', 'mobile-login-woocommerce' ), $user->ID, esc_attr( $_POST['xoo-ml-user-reg-phone'] ) ) );
			}
		}
	}


	public function edit_profile_page( $user ){
		
		$phoneCodes = (array) include XOO_ML_PATH.'/countries/phone.php';
		?>
		<table class="form-table">
			<tr>
				<th><?php  _e( 'Phone', 'mobile-login-woocommerce' ); ?></th>
				<td>
					<select name="xoo-ml-user-reg-phone-cc">
						<option disabled><?php _e( 'Select Country Code', 'mobile-login-woocommerce' ); ?></option>
						<?php foreach( $phoneCodes as $country_code => $country_phone_code ): ?>
							<option value="<?php echo esc_attr( $country_phone_code ); ?>" <?php echo $country_phone_code === get_user_meta( $user->ID, 'xoo_ml_phone_code',true) ? 'selected' : ''; ?> ><?php echo esc_html( $country_code.' '.$country_phone_code ); ?></option>
						<?php endforeach; ?>
				</select>
					<input type="text" name="xoo-ml-user-reg-phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'xoo_ml_phone_no',true) ); ?>">
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * Save Address Fields on edit user pages.
	 *
	 * @param int $user_id User ID of the user being saved
	 */
	public function save_customer_meta_fields( $user_id ) {

		if( isset( $_POST['xoo-ml-user-reg-phone'] ) ){
			update_user_meta( $user_id, 'xoo_ml_phone_no', sanitize_text_field( $_POST['xoo-ml-user-reg-phone'] ) );
		}

		if( isset( $_POST['xoo-ml-user-reg-phone-cc'] ) ){
			update_user_meta( $user_id, 'xoo_ml_phone_code', sanitize_text_field( $_POST['xoo-ml-user-reg-phone-cc'] ) );
		}

		if( isset( $_POST['xoo-ml-user-reg-phone-cc'] ) || isset( $_POST['xoo-ml-user-reg-phone'] ) ){
			update_user_meta(
				$user_id,
				'xoo_ml_phone_display',
				get_user_meta( $user_id, 'xoo_ml_phone_code', true ) . get_user_meta( $user_id, 'xoo_ml_phone_no', true )
			);
		}

	}


	public function add_columns( $columns ){
		$columns['xoo_ml_phone'] = __( 'Phone', 'mobile-login-woocommerce' );
		if( isset( $columns['email'] ) ){
			$arr1 = $arr2 = array();
			$useArr2 = false;
			foreach ( $columns as $id => $label ) {

				if( $useArr2 ){
					$arr2[ $id ] = $label;
				}
				else{
					$arr1[ $id ] = $label;
				}

				if( $id === 'email' ){
					$useArr2 = true;	
				}
				
			}
			$arr1['xoo_ml_phone'] = __( 'Phone', 'mobile-login-woocommerce' );
			$columns = array_merge( $arr1, $arr2 );
		}
	    return $columns;
	}


	public function columns_output( $val, $column_name, $user_id ){
		if( $column_name === "xoo_ml_phone" ){
			$val = get_user_meta( $user_id, 'xoo_ml_phone_display', true ); 		
		}
		return $val;
	}

}

function xoo_ml_users_table(){
	return Xoo_Ml_Users_Table::get_instance();
}
xoo_ml_users_table();

?>
