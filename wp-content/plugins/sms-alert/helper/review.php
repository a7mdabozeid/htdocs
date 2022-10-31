<?php
/**
 * Review helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}
class WCReview {

	/**
	 * Construct function.
	 */
	public function __construct() {
		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1 );
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 100 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'schedule_sms' ), 100, 4 );
		add_action( 'comment_post', array( $this, 'smsalert_send_review_msg' ), 11, 3 );
		add_action( 'comment_form_after_fields', array( $this, 'add_review_phone_field_on_comment_form' ) );
	}

	/**
	 * Add phone field field.
	 *
	 * @return void
	 */
	public static function add_review_phone_field_on_comment_form() {
		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		if(!$islogged){return;}
		
		$review_added_user_msg  = smsalert_get_option( 'review_added_user_msg', 'smsalert_review', 'on' );
																									 
		if ( 'on' === $review_added_user_msg ) {
			echo '<p class="comment-form-phone"><label for="phone">Phone<span class="required">*</span></label><input type="text" class="phone-valid" name="billing_phone" id="billing_phone"/></p>';
		}
	}

	/**
	 * Smsalert send sms on review.
	 *
	 * @param int $comment_id       Transaction Id.
	 * @param int $comment_approved Comment Approved.
	 * @param int $commentdata      Comment Data.
	 */
	public function smsalert_send_review_msg( $comment_id, $comment_approved, $commentdata ) {
		if ( 'review' === $commentdata['comment_type'] ) {
			$message               = smsalert_get_option( 'sms_body_review_added_user_msg', 'smsalert_review', '' );
			$message               = $this->parse_sms_body( $comment_id, $commentdata, $message );
			$review_added_user_msg = smsalert_get_option( 'review_added_user_msg', 'smsalert_review', 'on' );
			if ( 'on' === $review_added_user_msg && '' !== $message ) {
				$user_phone = ( isset( $_POST['billing_phone'] ) && '' !== $_POST['billing_phone'] ) ? $_POST['billing_phone'] : get_user_meta( $commentdata['user_id'], 'billing_phone', true );
				do_action( 'sa_send_sms', $user_phone, $message );
			}
			// send admin notificaton.
			$message                = smsalert_get_option( 'sms_body_review_added_admin_msg', 'smsalert_review', '' );
			$message                = $this->parse_sms_body( $comment_id, $commentdata, $message );
			$review_added_admin_msg = smsalert_get_option( 'review_added_admin_msg', 'smsalert_review', 'on' );
			if ( 'on' === $review_added_admin_msg && '' !== $message ) {
				$sms_admin_phone      = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );
				  $admin_phone_number = str_replace( 'postauthor', 'post_author', $sms_admin_phone );
				  $author_no          = apply_filters( 'sa_post_author_no', $commentdata['comment_post_ID'] );
				if ( ( strpos( $admin_phone_number, 'post_author' ) !== false ) && ! empty( $author_no ) ) {
					$admin_phone_number = str_replace( 'post_author', $author_no, $admin_phone_number );
				}
				do_action( 'sa_send_sms', $admin_phone_number, $message );
			}
		}
	}

	/**
	 * Parse sms body function
	 *
	 * @param int    $comment_id  comment id.
	 * @param int    $commentdata comment data.
	 * @param string $message     message.
	 *
	 * @return string
	 */
	public function parse_sms_body( $comment_id, $commentdata, $message ) {
		$find      = array(
			'[name]',
			'[email]',
			'[item_name]',
			'[rating]',
			'[review_content]',
		);
		$rating    = get_comment_meta( $comment_id, 'rating', true );
		$item_name = get_the_title( $commentdata['comment_post_ID'] );
		$replace   = array(
			$commentdata['comment_author'],
			$commentdata['comment_author_email'],
			$item_name,
			$rating,
			$commentdata['comment_content'],
		);
		$message   = str_replace( $find, $replace, $message );
		return $message;
	}

	/**
	 * Schedule sms function.
	 *
	 * @param int                 $order_id   order_id.
	 * @param string              $old_status old_status.
	 * @param string              $new_status new_status.
	 * @param $instance   instance.
	 */
	public function schedule_sms( $order_id, $old_status, $new_status, $instance ) {

		$order       = new WC_Order( $order_id );
		$order_items = $order->get_items();
		$first_item  = current( $order_items );
		$post_id     = $first_item['order_id'];
		$buyer_no    = get_post_meta( $post_id, '_billing_phone', true );

		$customer_notify = smsalert_get_option( 'customer_notify', 'smsalert_or_general', 'on' );
		$review_message  = smsalert_get_option( 'customer_notify', 'smsalert_or_message', '' );
		$message_status  = smsalert_get_option( 'review_status', 'smsalert_review' );
		$days            = smsalert_get_option( 'schedule_day', 'smsalert_review' );

		if ( $new_status === $message_status && 'on' === $customer_notify && '' !== $review_message && 0 === $order->get_parent_id() ) {

			$time_enabled = smsalert_get_option( 'send_at', 'smsalert_review' );

			if ( 'on' === $time_enabled ) {
				$schedule_time = smsalert_get_option( 'schedule_time', 'smsalert_review' );

				$date_modified = SmsAlertUtility::sa_date_time( $order->get_date_modified(), 'Y-m-d' );
				$default_time  = $date_modified . ' ' . $schedule_time;
				$schedule      = SmsAlertUtility::sa_date_time( $default_time, 'Y-m-d H:i:s', $days . ' days' );
				$ist           = SmsAlertUtility::date_time_ist( $schedule );
			} else {
				$order_time = SmsAlertUtility::date_time_ist();
				$schedule   = SmsAlertUtility::sa_date_time( $order_time, 'Y-m-d H:i:s', $days . ' days' );
			}

			$buyer_sms_data['number']   = $buyer_no;
			$buyer_sms_data['sms_body'] = $review_message;
			$buyer_sms_data             = WooCommerceCheckOutForm::pharse_sms_body( $buyer_sms_data, $order_id );
			$review_message             = ( ! empty( $buyer_sms_data['sms_body'] ) ) ? $buyer_sms_data['sms_body'] : '';
			do_action( 'sa_send_sms', $buyer_no, $review_message, $schedule );
		}
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public static function add_tabs( $tabs = array() ) {
		$review_param = array(
			'checkTemplateFor' => 'review',
			'templates'        => self::get_review_templates(),
		);

		$tabs['woocommerce']['inner_nav']['review']['title']       = 'Review';
		$tabs['woocommerce']['inner_nav']['review']['tab_section'] = 'reviewtemplates';
		$tabs['woocommerce']['inner_nav']['review']['tabContent']  = $review_param;
		$tabs['woocommerce']['inner_nav']['review']['filePath']    = 'views/review-template.php';
		return $tabs;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public static function add_default_setting( $defaults = array() ) {
		$defaults['smsalert_review']['schedule_day']                    = '1';
		$defaults['smsalert_review']['review_status']                   = 'completed';
		$defaults['smsalert_review']['schedule_time']                   = '10:00';
		$defaults['smsalert_review']['send_at']                         = 'off';
		$defaults['smsalert_or_general']['customer_notify']             = 'off';
		$defaults['smsalert_or_message']['customer_notify']             = '';
		$defaults['smsalert_review']['review_added_user_msg']           = 'off';
		$defaults['smsalert_review']['sms_body_review_added_user_msg']  = '';
		$defaults['smsalert_review']['review_added_admin_msg']          = 'off';
		$defaults['smsalert_review']['sms_body_review_added_admin_msg'] = '';
		return $defaults;
	}

	/**
	 * Get review template function.
	 *
	 * @return array
	 */
	public static function get_review_templates() {
		$datas                          = array();
		$review_variables               = array(
			'[name]'           => 'Name',
			'[email]'          => 'Email',
			'[item_name]'      => 'Product Name',
			'[rating]'         => 'Rating',
			'[review_content]' => 'Review Content',
			'[shop_url]'       => 'Shop Url',
		);
		$current_val                    = smsalert_get_option( 'customer_notify', 'smsalert_or_general', 'on' );
		$checkbox_name_id               = 'smsalert_or_general[customer_notify]';
		$text_area_name_id              = 'smsalert_or_message[customer_notify]';
		$text_body                      = smsalert_get_option( 'customer_notify', 'smsalert_or_message', SmsAlertMessages::showMessage( 'DEFAULT_CUSTOMER_REVIEW_MESSAGE' ) );
		$review_added_user_msg          = smsalert_get_option( 'review_added_user_msg', 'smsalert_review', 'on' );
		$sms_body_review_added_user_msg = smsalert_get_option( 'sms_body_review_added_user_msg', 'smsalert_review', sprintf( __( 'Dear %1$s, Thank you for sharing your valuable feedback on %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[store_name]', PHP_EOL, PHP_EOL ) );

		$review_added_admin_msg          = smsalert_get_option( 'review_added_admin_msg', 'smsalert_review', 'on' );
		$sms_body_review_added_admin_msg = smsalert_get_option( 'sms_body_review_added_admin_msg', 'smsalert_review', sprintf( __( 'Dear admin, %1$s has left a %2$s star review for %3$s on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[rating]', '[item_name]', '[store_name]', PHP_EOL, PHP_EOL ) );

		$datas[]   = array(
			'title'          => 'Customer notification, when review is added',
			'status'         => 'user_review_added',
			'enabled'        => $review_added_user_msg,
			'text-body'      => $sms_body_review_added_user_msg,
			'checkboxNameId' => 'smsalert_review[review_added_user_msg]',
			'textareaNameId' => 'smsalert_review[sms_body_review_added_user_msg]',
			'moreoption'     => 0,
			'token'          => $review_variables,
		);
		$datas[]   = array(
			'title'          => 'Admin notification, when review is added',
			'status'         => 'admin_review_added',
			'enabled'        => $review_added_admin_msg,
			'text-body'      => $sms_body_review_added_admin_msg,
			'checkboxNameId' => 'smsalert_review[review_added_admin_msg]',
			'textareaNameId' => 'smsalert_review[sms_body_review_added_admin_msg]',
			'moreoption'     => 0,
			'token'          => $review_variables,
		);
		$datas[]   = array(
			'title'          => 'Request for Review',
			'status'         => 'review_request',
			'enabled'        => $current_val,
			'text-body'      => $text_body,
			'checkboxNameId' => $checkbox_name_id,
			'textareaNameId' => $text_area_name_id,
			'moreoption'     => 1,
			'token'          => WooCommerceCheckOutForm::getvariables(),
		);
		$templates = array();
		foreach ( $datas as $key => $data ) {
			$templates[ $key ]['title']          = $data['title'];
			$templates[ $key ]['status']         = $data['status'];
			$templates[ $key ]['enabled']        = $data['enabled'];
			$templates[ $key ]['text-body']      = $data['text-body'];
			$templates[ $key ]['checkboxNameId'] = $data['checkboxNameId'];
			$templates[ $key ]['textareaNameId'] = $data['textareaNameId'];
			$templates[ $key ]['moreoption']     = $data['moreoption'];
			$templates[ $key ]['token']          = $data['token'];
		}
		return $templates;
	}
}
new WCReview();
