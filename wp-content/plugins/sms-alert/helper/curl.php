<?php
/**
 * Curl helper.
 *
 * @package Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**SmsAlertcURLOTP class */
class SmsAlertcURLOTP {

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param string $template template.
	 *
	 * @return void
	 */
	public static function sendtemplatemismatchemail( $template ) {
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$to_mail  = smsalert_get_option( 'alert_email', 'smsalert_general', '' );

		// Email template with content
		$params       = array(
			'template'    => nl2br( $template ),
			'username'    => $username,
			'server_name' => ( ( ! empty( $_SERVER['SERVER_NAME'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' ),
			'admin_url'   => admin_url(),
		);
		$emailcontent = get_smsalert_template( 'template/emails/mismatch-template.php', $params, true );
		wp_mail( $to_mail, 'SMS Alert - Template Mismatch', $emailcontent, 'content-type:text/html' );
	}

	/**
	 * Send email For Invalid Credentials.
	 *
	 * @param string $template template.
	 *
	 * @return void
	 */
	public static function sendemailForInvalidCred( $template ) {
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$to_mail  = smsalert_get_option( 'alert_email', 'smsalert_general', '' );

		// Email template with content
		$params       = array(
			'template'    => nl2br( $template ),
			'username'    => $username,
			'server_name' => ( ( ! empty( $_SERVER['SERVER_NAME'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' ),
			'admin_url'   => admin_url(),
		);
		$emailcontent = get_smsalert_template( 'template/emails/invalid-credentials.php', $params, true );
		wp_mail( $to_mail, 'SMS Alert - Wrong Credentials', $emailcontent, 'content-type:text/html' );
	}
	
	/**
	 * Send email For Dormant Account.
	 *
	 * @param string $template template.
	 *
	 * @return void
	 */
	public static function sendemailForDormant( $template ) {
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway', '' );
		$to_mail  = smsalert_get_option( 'alert_email', 'smsalert_general', '' );

		// Email template with content
		$params       = array(
			'template'    => nl2br( $template ),
			'username'    => $username,
			'server_name' => ( ( ! empty( $_SERVER['SERVER_NAME'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' ),
			'admin_url'   => admin_url(),
		);
		$emailcontent = get_smsalert_template( 'template/emails/dormant-account.php', $params, true );
		wp_mail( $to_mail, 'SMS Alert - Dormant Account', $emailcontent, 'content-type:text/html' );
	}

	/**
	 * Check Phone Numbers.
	 *
	 * @param string  $nos numbers.
	 * @param boolean $force_prefix force_prefix.
	 *
	 * @return string
	 */
	public static function checkPhoneNos( $nos = null, $force_prefix = true ) {
		$country_code         = smsalert_get_option( 'default_country_code', 'smsalert_general' );
		$country_code_enabled = smsalert_get_option( 'checkout_show_country_code', 'smsalert_general' );
		$nos                  = explode( ',', $nos );
		$valid_no             = array();
		if ( is_array( $nos ) ) {
			foreach ( $nos as $no ) {
				$no = ltrim( ltrim( $no, '+' ), '0' ); // remove leading + and 0
				$no = preg_replace( '/[^0-9]/', '', $no );// remove spaces and special characters

				if ( ! empty( $no ) ) {

					//if ( 'on' === $country_code_enabled ) {
						//$valid_no[] = $no;
					//} 
					//else {
						if ( ! $force_prefix ) {
							$no = ( substr( $no, 0, strlen( $country_code ) ) == $country_code ) ? substr( $no, strlen( $country_code ) ) : $no;
						} else {
							$no = ( substr( $no, 0, strlen( $country_code ) ) != $country_code ) ? $country_code . $no : $no;
						}
						$match = preg_match( SmsAlertConstants::getPhonePattern(), $no );
						if ( $match ) {
							$valid_no[] = $no;
						}
					//}
				}
			}
		}
		if ( sizeof( $valid_no ) > 0 ) {
			return implode( ',', $valid_no );
		} else {
			return false;
		}
	}

	/**
	 * Send sms.
	 *
	 * @param array $sms_data sms_data.
	 *
	 * @return array
	 */
	public static function sendsms( $sms_data ) {
		$response = false;
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
		$senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );

		$enable_short_url = smsalert_get_option( 'enable_short_url', 'smsalert_general' );

		$phone = self::checkPhoneNos( $sms_data['number'] );
		if ( $phone === false ) {
			$data                = array();
			$data['status']      = 'error';
			$data['description'] = 'phone number not valid';
			return json_encode( $data );
		}
		$text = htmlspecialchars_decode( $sms_data['sms_body'] );
		// bail out if nothing provided
		if ( empty( $username ) || empty( $password ) || empty( $senderid ) || empty( $text ) ) {
			return $response;
		}

		$url    = 'http://www.smsalert.co.in/api/push.json';
		$fields = array(
			'user'     => $username,
			'pwd'      => $password,
			'mobileno' => $phone,
			'sender'   => $senderid,
			'text'     => $text,
		);

		if ( ! empty( $sms_data['schedule'] ) ) {
			$fields['schedule'] = $sms_data['schedule'];} //add on 27-08-20
		if ( $enable_short_url === 'on' ) {
			$fields['shortenurl'] = 1;}
		$json         = json_encode( $fields );
		$fields       = apply_filters( 'sa_before_send_sms', $fields );
		$response     = self::callAPI( $url, $fields, null );
		$response_arr = json_decode( $response, true );

		$text = ! empty( $fields['text'] ) ? $fields['text'] : $text;
		apply_filters( 'sa_after_send_sms', $response_arr );

		if ( $response_arr['status'] === 'error' ) {
			$error = ( is_array( $response_arr['description'] ) ) ? $response_arr['description']['desc'] : $response_arr['description'];
			if ( $error === 'Invalid Template Match' ) {
				self::sendtemplatemismatchemail( $text );
			}
		}
		return $response;
	}

	/**
	 * Smsalert send otp token.
	 *
	 * @param string $form form.
	 * @param string $email email.
	 * @param string $phone phone.
	 *
	 * @return array
	 */
	public static function smsalert_send_otp_token( $form, $email = '', $phone = '' ) {
		$phone                  = self::checkPhoneNos( $phone );
		$cookie_value           = get_smsalert_cookie( $phone );
		$max_otp_resend_allowed = smsalert_get_option( 'max_otp_resend_allowed', 'smsalert_general' );

		if ( $cookie_value >= $max_otp_resend_allowed ) {
			$data                        = array();
			$data['status']              = 'error';
			$data['description']['desc'] = __( 'Maximum OTP limit exceeded', 'sms-alert' );
			return json_encode( $data );
		}

		$response = false;
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
		$senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );
		$template = smsalert_get_option( 'sms_otp_send', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_BUYER_OTP' ) );
		$template = str_replace( '[shop_url]', get_site_url(), $template );

		if ( $phone === false ) {
			$data                        = array();
			$data['status']              = 'error';
			$data['description']['desc'] = __( 'phone number not valid', 'sms-alert' );
			return json_encode( $data );
		}

		if ( empty( $username ) || empty( $password ) || empty( $senderid ) ) {
			$data                        = array();
			$data['status']              = 'error';
			$data['description']['desc'] = __( 'Wrong SMSAlert credentials', 'sms-alert' );
			return json_encode( $data );
		}
		$url = 'http://www.smsalert.co.in/api/mverify.json';

		$fields       = array(
			'user'     => $username,
			'pwd'      => $password,
			'mobileno' => $phone,
			'sender'   => $senderid,
			'template' => $template,
		);
		$json         = json_encode( $fields );
		$response     = self::callAPI( $url, $fields, null );
		$response_arr = (array) json_decode( $response, true );
		if ( array_key_exists( 'status', $response_arr ) && $response_arr['status'] === 'error' ) {
			$error = ( is_array( $response_arr['description'] ) ) ? $response_arr['description']['desc'] : $response_arr['description'];
			if ( $error == 'Invalid Template Match' ) {
				self::sendtemplatemismatchemail( $template );
				$response = false;
			}
		} else {
			create_smsalert_cookie( $phone, $cookie_value + 1 );
		}

		return $response;
	}

	/**
	 * Smsalert validate otp token.
	 *
	 * @param string $mobileno mobileno.
	 * @param string $otpToken otpToken.
	 *
	 * @return array
	 */

	public static function validate_otp_token( $mobileno, $otpToken ) {
		if ( empty( $otpToken ) ) {
			return false;}

		$response = false;
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
		$senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );
		$mobileno = self::checkPhoneNos( $mobileno );
		if ( $mobileno === false ) {
			$data                = array();
			$data['status']      = 'error';
			$data['description'] = 'phone number not valid';
			return json_encode( $data );
		}

		if ( empty( $username ) || empty( $password ) || empty( $senderid ) ) {
			return $response;
		}
		$url = 'http://www.smsalert.co.in/api/mverify.json';

		$fields = array(
			'user'     => $username,
			'pwd'      => $password,
			'mobileno' => $mobileno,
			'code'     => $otpToken,
		);

		$response = self::callAPI( $url, $fields, null );
		$content  = json_decode( $response, true );
		if ( isset( $content['description']['desc'] ) && strcasecmp( $content['description']['desc'], 'Code Matched successfully.' ) === 0 ) {
			clear_smsalert_cookie( $mobileno );
		}

		return $response;
	}

	/**
	 * Get senderids.
	 *
	 * @param string $username username.
	 * @param string $password password.
	 *
	 * @return array
	 */

	public static function get_senderids( $username = null, $password = null ) {
		if ( empty( $username ) || empty( $password ) ) {
			return '';
		}

		$url = 'http://www.smsalert.co.in/api/senderlist.json';

		$fields = array(
			'user' => $username,
			'pwd'  => $password,
		);

		$response = self::callAPI( $url, $fields, null );
		return $response;
	}

	/**
	 * Get templates.
	 *
	 * @param string $username username.
	 * @param string $password password.
	 *
	 * @return array
	 */
	public static function get_templates( $username = null, $password = null ) {
		if ( empty( $username ) || empty( $password ) ) {
			return '';
		}
		$url = 'http://www.smsalert.co.in/api/templatelist.json';

		$fields = array(
			'user'  => $username,
			'pwd'   => $password,
			'limit' => 100,
		);

		$response = self::callAPI( $url, $fields, null );
		return $response;
	}

	/**
	 * Get credits.
	 *
	 * @return array
	 */
	public static function get_credits() {
		$response = false;
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );

		if ( empty( $username ) || empty( $password ) ) {
			return $response;
		}

		$url = 'http://www.smsalert.co.in/api/creditstatus.json';

		$fields   = array(
			'user' => $username,
			'pwd'  => $password,
		);
		$response = self::callAPI( $url, $fields, null );
		return $response;
	}

	/**
	 * Group list.
	 *
	 * @return array
	 */
	public static function group_list() {
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );

		if ( empty( $username ) || empty( $password ) ) {
			return '';
		}

		$url = 'http://www.smsalert.co.in/api/grouplist.json';

		$fields = array(
			'user' => $username,
			'pwd'  => $password,
		);

		$response = self::callAPI( $url, $fields, null );
		return $response;
	}

	/**
	 * Get country list.
	 *
	 * @return array
	 */
	/* public static function country_list() {
		$url      = 'http://www.smsalert.co.in/api/countrylist.json';
		$response = self::callAPI( $url, null, null );
		return $response;
	} */

	/**
	 * Create group.
	 *
	 * @return array
	 */
	public static function creategrp() {
		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );

		if ( empty( $username ) || empty( $password ) ) {
			return '';
		}

		$url = 'http://www.smsalert.co.in/api/creategroup.json';

		$fields = array(
			'user' => $username,
			'pwd'  => $password,
			'name' => $_SERVER['SERVER_NAME'],
		);

		$response = self::callAPI( $url, $fields, null );
		return $response;
	}

	/**
	 * Create contact.
	 *
	 * @param array  $sms_datas sms_datas.
	 * @param string $group_name group_name.
	 * @param array  $extra_fields extra_fields.
	 *
	 * @return array
	 */
	public static function create_contact( $sms_datas, $group_name, $extra_fields = array() ) {
		if ( is_array( $sms_datas ) && sizeof( $sms_datas ) == 0 ) {
			return false;
		}

		if ( empty( $group_name ) ) {
			return false;
		}

		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );

		// $group_name   = smsalert_get_option( 'group_auto_sync', 'smsalert_general', '');
		$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<group>
</group>
XML;

		$msg  = new SimpleXMLElement( $xmlstr );
		$user = $msg->addChild( 'user' );
		$user->addAttribute( 'username', $username );
		$user->addAttribute( 'password', $password );
		$user->addAttribute( 'grp_name', $group_name );
		$members = $msg->addChild( 'members' );
		$cnt     = 0;
		foreach ( $sms_datas as $sms_data ) {
			$phone = self::checkPhoneNos( $sms_data['number'] );

			if ( $phone !== false ) {
				$member = $members->addChild( 'member' );
				$member->addAttribute( 'name', $sms_data['person_name'] );
				$member->addAttribute( 'number', $phone );

				if ( ! empty( $extra_fields ) ) {
					$memb = $member->addChild( 'meta-data' );
					foreach ( $extra_fields as $key => $value ) {
						$memb->addAttribute( $key, $value );
					}
				}
				$cnt++;
			}
		}
		$xmldata = $msg->asXML();
		$url     = 'https://www.smsalert.co.in/api/createcontactxml.json';
		$fields  = array( 'data' => $xmldata );
		if ( $cnt > 0 ) {
			$response = self::callAPI( $url, $fields, null );
		} else {
			$response = json_encode(
				array(
					'status'      => 'error',
					'description' => 'Invalid WC Users Contact Numbers',
				)
			);
		}

		return $response;
	}

	/**
	 * Send sms xml.
	 *
	 * @param array $sms_datas sms_datas.
	 *
	 * @return array
	 */
	public static function send_sms_xml( $sms_datas ) {
		if ( is_array( $sms_datas ) && sizeof( $sms_datas ) == 0 ) {
			return false;
		}

		$username = smsalert_get_option( 'smsalert_name', 'smsalert_gateway' );
		$password = smsalert_get_option( 'smsalert_password', 'smsalert_gateway' );
		$senderid = smsalert_get_option( 'smsalert_api', 'smsalert_gateway' );

		$xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<message>
</message>
XML;
		$msg    = new SimpleXMLElement( $xmlstr );
		$user   = $msg->addChild( 'user' );
		$user->addAttribute( 'username', $username );
		$user->addAttribute( 'password', $password );
		$enable_short_url = smsalert_get_option( 'enable_short_url', 'smsalert_general' );
		if ( $enable_short_url === 'on' ) {
			$user->addAttribute( 'shortenurl', 1 );}

		$cnt = 0;
		foreach ( $sms_datas as $sms_data ) {
			$phone = self::checkPhoneNos( $sms_data['number'] );
			if ( $phone !== false ) {
				$sms = $msg->addChild( 'sms' );

				$datas = apply_filters( 'sa_before_send_sms', array( 'text' => $sms_data['sms_body'] ) );

				if ( ! empty( $datas['text'] ) ) {
					$sms_data['sms_body'] = $datas['text'];
				}

				$sms->addAttribute( 'text', $sms_data['sms_body'] );

				$address = $sms->addChild( 'address' );
				$address->addAttribute( 'from', $senderid );
				$address->addAttribute( 'to', $phone );
				$cnt++;
			}
		}

		if ( $msg->count() <= 1 ) {
			return false;
		}

		$xmldata = $msg->asXML();
		$url     = 'http://www.smsalert.co.in/api/xmlpush.json?';
		$fields  = array( 'data' => $xmldata );
		if ( $cnt > 0 ) {
			$response = self::callAPI( $url, $fields, null );
		} else {
			$response = json_encode(
				array(
					'status'      => 'error',
					'description' => 'Invalid WC Users Contact Numbers',
				)
			);
		}

		return $response;
	}

	/**
	 * CallAPI function.
	 *
	 * @param string $url url.
	 * @param array  $params params.
	 * @param array  $headers headers.
	 *
	 * @return array
	 */
	public static function callAPI( $url, $params, $headers = array( 'Content-Type: application/json' ) ) {
		$extra_params = array(
			'plugin'  => 'woocommerce',
			'website' => ( ( ! empty( $_SERVER['SERVER_NAME'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' ),
			'version' =>SmsAlertConstants::SA_VERSION
		);
		$params       = ( ! is_null( $params ) ) ? array_merge( $params, $extra_params ) : $extra_params;
		$args         = array(
			'body'    => $params,
			'timeout' => 15,
		);
		$request      = wp_remote_post( $url, $args );

		if ( is_wp_error( $request ) ) {
			$data                = array();
			$data['status']      = 'error';
			$data['description'] = $request->get_error_message();
			return json_encode( $data );
		}

		$resp     = wp_remote_retrieve_body( $request );
		$response = (array) json_decode( $resp, true );

		if ( $response['status'] === 'error' && $response['description'] === 'invalid username/password.' ) {
			$template = 'you are using wrong credentials of smsalert. Please check once.';
			self::sendemailForInvalidCred( $template );
			smsalert_Setting_Options::logout();
		}
		elseif($response['status'] === 'error' && $response['description'] === 'dormant account.')
		{
			$template = 'your account status is dormant, when you will purchase sms credits then it will be active.';
			self::sendemailForDormant( $template );
			smsalert_Setting_Options::logout();
		}
		return $resp;
	}
}
