<?php
/**
 * Session vars helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class FormSessionVars {
	const WC_DEFAULT_REG              = 'woocommerce_registration';
	const BC_FORM        = 'booking_calendar_form';
	const WP_JOB_MANAGER              = 'wp_job_manager';
	const WC_REG_POPUP                = 'woocommerce_registration_popup';
	const WC_CHECKOUT_POPUP                = 'woocommerce_checkout_popup';
	const WC_POST_CHECKOUT                = 'woocommerce_post_checkout';
	const WC_REG_WTH_MOB              = 'woocommerce_reg_with_mob';
	const WC_CHECKOUT                 = 'woocommerce_checkout_page';
	const WC_SOCIAL_LOGIN             = 'wc_social_login';
	const PB_DEFAULT_REG              = 'profileBuilder_registration';
	const UM_DEFAULT_REG              = 'ultimate_members_registration';
	const UR_DEFAULT_REG              = 'users_registration';
	const ER_DEFAULT_REG              = 'easy_registration';
	const PR_DEFAULT_REG              = 'profile_registration';
	const SA_SHORTCODE_FORM_VERIFY    = 'shortcode';
	const PV_DEFAULT_REG              = 'vendor_registration';
	const WCF_DEFAULT_REG             = 'wcf_marketplace';
	const EVENT_REG                   = 'event_registration';
	const CRF_DEFAULT_REG             = 'crf_user_registration';
	const UULTRA_REG                  = 'uultra_user_registration';
	const SIMPLR_REG                  = 'simplr_registration';
	const BUDDYPRESS_REG              = 'buddyPress_user_registration';
	const PIE_REG                     = 'pie_form';
	const PIE_POPUP                   = 'pie_form_popup';
	const UMR_POPUP                   = 'umr_form_popup';
	const PIE_REG_STATUS              = 'pie_user_registration_status';
	const WP_DEFAULT_REG              = 'default_wp_registration';
	const TML_REG                     = 'tml_registration';
	const CF7_FORMS                   = 'cf7_contact_page';
	const NF_FORMS                    = 'nf_contact_page';
	const AJAX_FORM                   = 'ajax_phone_verified';
	const CF7_EMAIL_VER               = 'cf7_email_verified';
	const CF7_PHONE_VER               = 'cf7_phone_verified';
	const NF_PHONE_VER                = 'nf_phone_verified';
	const CF7_EMAIL_SUB               = 'cf7_email_submitted';
	const CF7_PHONE_SUB               = 'cf7_phone_submitted';
	const UPME_REG                    = 'upme_user_registration';
	const NINJA_FORM                  = 'ninja_form_submit';
	const USERPRO_FORM                = 'userpro_form_submit';
	const USERPRO_EMAIL_VER           = 'userpro_email_verified';
	const USERPRO_PHONE_VER           = 'userpro_phone_verified';
	const WP_DEFAULT_LOGIN            = 'default_wp_login';
	const WP_LOGIN_REG_PHONE          = 'default_wp_reg_phone';
	const WP_LOGIN_WITH_OTP           = 'default_wp_login_with_otp';
	const WPMEMBER_REG                = 'wpmember_registration';
	const WPM_PHONE_VER               = 'wpmember_phone_verified';
	const AFFILIATE_MANAGER_REG       = 'affiliate_manager_registration';
	const AFFILIATE_MANAGER_PHONE_VER = 'affiliate_manager_phone_verified';
	const WP_DEFAULT_LOST_PWD         = 'wp_default_lost_pwd';
	const LEARNPRESS_DEFAULT_REG      = 'learnpress_default_reg';
	const FLUENT_FORM                 = 'fluent_form';
	const USERSWP_FORM                = 'userswp_form';
	const USERSWP_POPUP               = 'userswp_form_popup';
	const WPFORM                      = 'wp_form';
	const FORMIDABLE                  = 'wp_formidable';
	const UR_FORM                     = 'userreg_form';
	const WP_RES_RESERVATION          = 'res_reservation_form';
	const WP_QUICK_RES_RESERVATION    = 'quick_res_reservation_form';
	const AR_MEMBER_FORM              = 'armember_form';
}
new FormSessionVars();
