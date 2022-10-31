<?php
/**
 * Formlist helper.
 *
 * @package Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**FormList class */
class FormList {

	const WP_DEFAULT       = 'WordPress Default Registration Form';
	const WC_REG_FROM      = 'Woocommerce Registration Form';
	const WC_CHECKOUT_FORM = 'Woocommerce Checkout Form';
	const WC_SOCIAL_LOGIN  = 'Woocommerce Social Login';
	const PB_DEFAULT_FORM  = 'Profile Builder Registration Form';
	const SIMPLR_FORM      = 'Simplr User Registration Form Plus';
	const ULTIMATE_FORM    = 'Ultimate Member Registration Form';
	const EVENT_FORM       = 'Event Registration Form';
	const BP_DEFAULT_FORM  = 'BuddyPress Registration Form';
	const CRF_FORM         = 'Custom User Registration Form Builder';
	const UULTRA_FORM      = 'User Ultra Registration Form';
	const UPME_FORM        = 'UserProfile Made Easy Registration Form';
	const PIE_FORM         = 'PIE Registration Form';
	const CF7_FORM         = 'Contact Form 7 - Contact Form';
	const NINJA_FORM       = 'Ninja Forms';
	const TML_FORM         = 'Theme My Login Form';
	const USERPRO_FORM     = 'UserPro Form';

	/**
	 * Get Form List.
	 *
	 * @return array
	 */
	public static function getFormList() {
		$refl = new ReflectionClass( 'FormList' );
		return $refl->getConstants();
	}

	/**
	 * Is Form Enabled.
	 *
	 * @param string $form form.
	 *
	 * @return void
	 */
	public static function isFormEnabled( $form ) {
		switch ( $form ) {
			case self::WP_DEFAULT:
				return check_default_reg_enabled();
			break;
			case self::WC_REG_FROM:
				return check_wc_reg_enabled();
			break;
			case self::WC_CHECKOUT_FORM:
				return check_wc_checkout_enabled();
			break;
			case self::WC_SOCIAL_LOGIN:
				return check_wc_social_login_enabled();
			break;
			case self::PB_DEFAULT_FORM:
				return check_pb_enabled();
			break;
			case self::SIMPLR_FORM:
				return check_simplr_enabled();
			break;
			case self::ULTIMATE_FORM:
				return check_um_enabled();
			break;
			case self::EVENT_FORM:
				return check_evr_enabled();
			break;
			case self::BP_DEFAULT_FORM:
				return check_bbp_enabled();
			break;
			case self::CRF_FORM:
				return check_crf_enabled();
			break;
			case self::UULTRA_FORM:
				return check_uultra_enabled();
			break;
			case self::UPME_FORM:
				return check_upme_enabled();
			break;
			case self::PIE_FORM:
				return check_pie_enabled();
			break;
			case self::CF7_FORM:
				return check_cf7_enabled();
			break;
			case self::NINJA_FORM:
				return check_ninja_form_enabled();
			break;
			case self::TML_FORM:
				return check_tml_reg_enabled();
			break;
			case self::USERPRO_FORM:
				return check_userpro_enabled();
			break;
		}
	}
}
