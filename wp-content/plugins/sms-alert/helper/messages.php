<?php
	/**
	 * Messages helper.
	 *
	 * @package Helper
	 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**SmsAlertMessages class */
class SmsAlertMessages {
	/**Construct function.*/
	function __construct() {
		// created an array instead of messages instead of constant variables for Translation reasons.
		define(
			'SALRT_MESSAGES',
			serialize(
				array(
					// General Messages
					'OTP_RANGE'                            => __( 'Only digits within range 4-8 are allowed.', 'sms-alert' ),
					'SEND_OTP'                             => __( 'Send OTP', 'sms-alert' ),
					'RESEND_OTP'                           => __( 'Resend OTP', 'sms-alert' ),
					'VALIDATE_OTP'                         => __( 'Validate OTP', 'sms-alert' ),
					'RESEND'                               => __( 'Resend', 'sms-alert' ),
					'Phone'                                => __( 'Phone', 'sms-alert' ),
					'INVALID_OTP'                          => __( 'Invalid one time passcode. Please enter a valid passcode.', 'sms-alert' ),
					'ENTER_PHONE_CODE'                     => __( 'Please enter the verification code sent to your phone.', 'sms-alert' ),
					'CHANGE_PWD'                           => __( 'Please change Your password', 'sms-alert' ),
					'ENTER_PWD'                            => __( 'Please enter your password.', 'sms-alert' ),
					'PWD_MISMATCH'                         => __( 'Passwords do not match.', 'sms-alert' ),
					// one time use message start

					'DEFAULT_BUYER_SMS_PENDING'            => sprintf( __( 'Hello %s, you are just one step away from placing your order, please complete your payment, to proceed.', 'sms-alert' ), '[billing_first_name]' ),
					'DEFAULT_ADMIN_SMS_CANCELLED'          => sprintf( __( '%1$s: Your order %2$s Rs. %3$s. is Cancelled.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_amount]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_SMS_PENDING'            => sprintf( __( '%1$s: Hello, %2$s is trying to place order %3$s value Rs. %4$s', 'sms-alert' ), '[store_name]', '[billing_first_name]', '#[order_id]', '[order_amount]' ),
					'DEFAULT_ADMIN_SMS_ON_HOLD'            => sprintf( __( '%1$s: Your order %2$s Rs. %3$s. is On Hold Now.', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_amount]' ),
					'DEFAULT_ADMIN_SMS_COMPLETED'          => sprintf( __( '%1$s: Your order %2$s Rs. %3$s. is completed.', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_amount]' ),
					'DEFAULT_ADMIN_SMS_PROCESSING'         => sprintf( __( '%1$s: You have a new order %2$s for order value %3$s. Please check your admin dashboard for complete details.', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_amount]' ),
					'DEFAULT_BUYER_SMS_PROCESSING'         => sprintf( __( 'Hello %1$s, thank you for placing your order %2$s with %3$s.', 'sms-alert' ), '[billing_first_name]', '#[order_id]', '[store_name]' ),
					'DEFAULT_BUYER_SMS_COMPLETED'          => sprintf( __( 'Hello %1$s, your order %2$s with %3$s has been dispatched and shall deliver to you shortly.', 'sms-alert' ), '[billing_first_name]', '#[order_id]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BUYER_SMS_ON_HOLD'            => sprintf( __( 'Hello %1$s, your order %2$s with %3$s has been put on hold, our team will contact you shortly with more details.', 'sms-alert' ), '[billing_first_name]', '#[order_id]', '[store_name]' ),
					'DEFAULT_BUYER_SMS_CANCELLED'          => sprintf( __( 'Hello %1$s, your order %2$s with %3$s has been cancelled due to some un-avoidable conditions. Sorry for the inconvenience caused.', 'sms-alert' ), '[billing_first_name]', '#[order_id]', '[store_name]' ),
					'DEFAULT_ADMIN_OUT_OF_STOCK_MSG'       => sprintf( __( '%1$s: Out Of Stock Alert For Product %2$s, current stock %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[item_name]', '[item_qty]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_LOW_STOCK_MSG'          => sprintf( __( '%1$s: Low Stock Alert For Product %2$s, current stock %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[item_name]', '[item_qty]', PHP_EOL, PHP_EOL ),
					'DEFAULT_AC_ADMIN_MESSAGE'             => sprintf( __( '%1$s: Product %2$s is left in cart by %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[item_name]', '[name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_AC_CUSTOMER_MESSAGE'          => sprintf( __( 'Hey %1$s, We noticed you could not complete your order. Click on the link below to place your order. Shop Now - %2$s%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[checkout_url]', PHP_EOL, PHP_EOL ),
					'DEFAULT_AB_CART_CUSTOMER_MESSAGE'     => sprintf( __( 'Hey %1$s, We noticed you could not complete your order. Click on the link below to place your order. Shop Now - %2$s%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[checkout_url]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_SMS_STATUS_CHANGED'     => sprintf( __( '%1$s: status of order %2$s has been changed to %3$s.', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_status]' ),
					// one time use message end

					// not in use start
					'OTP_INVALID_NO'                       => sprintf( __( 'your verification code is %1$s. Only valid for %2$s min.', 'sms-alert' ), '[otp]', '15' ),
					'OTP_ADMIN_MESSAGE'                    => sprintf( __( 'You have a new Order%1$sThe %2$s is now %3$s', 'sms-alert' ), PHP_EOL, '[order_id]', '[order_status]' . PHP_EOL ),
					'OTP_BUYER_MESSAGE'                    => sprintf( __( 'Thanks for purchasing%1$sYour %2$s is now %3$sThank you', 'sms-alert' ), PHP_EOL, '[order_id]', '[order_status]' . PHP_EOL ),
					// not in use end

					'DEFAULT_BUYER_SMS_STATUS_CHANGED'     => sprintf( __( 'Hello %1$s, status of your order %2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[billing_first_name]', '#[order_id]', '[store_name]', '[order_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BUYER_NOTE'                   => sprintf( __( 'Hello %1$s, a new note has been added to your order %2$s on %3$s: %4$s%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[billing_first_name]', '#[order_id]:', '[shop_url]', '[note]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BUYER_OTP'                    => sprintf( __( 'Your verification code for %1$s is %2$s', 'sms-alert' ), '[shop_url]', '[otp]' ),
					'OTP_SENT_PHONE'                       => sprintf( __( 'A OTP (One Time Passcode) has been sent to %s. Please enter the OTP in the field below to verify your phone.', 'sms-alert' ), '##phone##' ),
					'DEFAULT_WPAM_BUYER_SMS_STATUS_CHANGED' =>
																		sprintf( __( 'Hello %1$s, status of your affiliate account %2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[first_name]', '[affiliate_id]', '[store_name]', '[affiliate_status]', PHP_EOL, PHP_EOL ),
					// Review Request
					'DEFAULT_CUSTOMER_REVIEW_MESSAGE'      =>
					sprintf( __( 'Hi %1$s, thank you for your recent order on %2$s. Can you take 1 minute to leave a review about your experience with us? %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[first_name]', '[store_name]', 'https://www.google.com/search?q=[shop_url]', PHP_EOL, PHP_EOL ),
					// New User Approve
					'DEFAULT_NEW_USER_APPROVED'            =>
					sprintf( __( 'Dear %1$s, your account with %2$s has been approved.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_NEW_USER_REJECTED'            =>
					sprintf( __( 'Dear %1$s, your account with %2$s has been rejected.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]', PHP_EOL, PHP_EOL ),
					// LearnPress
					'DEFAULT_LPRESS_BUYER_SMS_STATUS_CHANGED' =>
					sprintf( __( 'Hello %1$s, status of your %2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[order_id]', '[store_name]', '[order_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_LPRESS_ADMIN_SMS_STATUS_CHANGED' =>
					sprintf( __( '%1$s: status of order %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_status]', PHP_EOL, PHP_EOL ),
					// Notify Me
					'DEFAULT_BACK_IN_STOCK_CUST_MSG'       =>
					sprintf( __( 'Hello, %1$s is now available, you can order it on %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[item_name]', '[shop_url]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BACK_IN_STOCK_SUBSCRIBE_MSG'  =>
					sprintf( __( 'We have noted your request and we will notify you as soon as %s is available for order with us.', 'sms-alert' ), '[item_name]' ),
					// Event Manager
					'DEFAULT_EM_CUSTOMER_MESSAGE'          =>
					sprintf( __( 'Hello %1$s, status of your booking %2$s%3$s with %4$s has been changed to %5$s.%6$sPowered by%7$swww.smsalert.co.in', 'sms-alert' ), '[#_BOOKINGNAME]', '[#_BOOKINGID]', '[#_EVENTNAME]', '[store_name]', '[#_BOOKINGSTATUS]', PHP_EOL, PHP_EOL ),
					'DEFAULT_EM_ADMIN_MESSAGE'             =>
					sprintf( __( '%1$s: status of booking %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[#_BOOKINGID]', '[#_BOOKINGSTATUS]', PHP_EOL, PHP_EOL ),                 // EDD
					'DEFAULT_EDD_BUYER_SMS_STATUS_CHANGED' =>
					sprintf( __( 'Hello %1$s, status of your order %2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[first_name]', '[order_id]', '[store_name]', '[order_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_EDD_ADMIN_SMS_STATUS_CHANGED' =>
					sprintf( __( '%1$s: status of order %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '#[order_id]', '[order_status]', PHP_EOL, PHP_EOL ),
					// Delivery Driver
					'DEFAULT_DELIVERY_DRIVER_MESSAGE'      =>
					sprintf( __( '%1$s: Hello %2$s, you have been assigned a new delivery for %3$s%4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[first_name]', '[item_name]', '[item_name_qty]', PHP_EOL, PHP_EOL ),

					// Fluent form
					'DEFAULT_FLUENT_ADMIN_MESSAGE'         => sprintf( __( '%1$s: Dear %2$s, new Contact from %3$s on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', 'admin', '[first_name]', '[shop_url]', PHP_EOL, PHP_EOL ),
					'DEFAULT_FLUENT_CUSTOMER_MESSAGE'      => sprintf( __( 'Hello %1$s, Thank you for submitting on %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[first_name]', '[store_name]', PHP_EOL, PHP_EOL ),

					// Ninja
					'DEFAULT_NINJA_ADMIN_MESSAGE'          =>
					sprintf( __( '%1$s: Dear %2$s, new Contact from %3$s on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', 'admin', '[name]', '[shop_url]', PHP_EOL, PHP_EOL ),
					'DEFAULT_NINJA_CUSTOMER_MESSAGE'       =>
																		sprintf( __( 'Hello %1$s, Thank you for submitting on %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_WPAM_ADMIN_SMS_STATUS_CHANGED' => sprintf( __( '%1$s: status of affiliate %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '#[affiliate_id]', '[affiliate_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_WPAM_BUYER_SMS_TRANS_STATUS_CHANGED' => sprintf( __( 'Hello %1$s,commission has been %2$s for %3$s to your affiliate account %4$s against order %5$s.%6$sPowered by%7$swww.smsalert.co.in', 'sms-alert' ), '[first_name]', '[transaction_type]', '[commission_amt]', '[affiliate_id]', '#[order_id]', PHP_EOL, PHP_EOL ),
					'DEFAULT_WPAM_ADMIN_SMS_TRANS_STATUS_CHANGED' => sprintf( __( '%1$s: commission has been %2$s for %3$s to affiliate account %4$s against order %5$s.%6$sPowered by%7$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[transaction_type]', '[commission_amt]', '[affiliate_id]', '#[order_id]', PHP_EOL, PHP_EOL ),

					'DEFAULT_ADMIN_NEW_USER_REGISTER'      => sprintf( __( 'Dear admin, %1$s has just signed up on %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]', PHP_EOL, PHP_EOL ),
					'PHONE_NOT_FOUND'                      => __( 'Sorry, but you do not have a registered phone number, please logged in your account and update this number.', 'sms-alert' ),
					'PHONE_MISMATCH'                       => __( 'The phone number OTP was sent to and the phone number in contact submission do not match.', 'sms-alert' ),
					'DEFAULT_USER_COURSE_ENROLL'           => sprintf( __( 'Congratulation %1$s, you have enrolled course %2$s with %3$s%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[course_name]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_NEW_USER_REGISTER'            => sprintf( __( 'Hello %1$s, Thank you for registering with %2$s.', 'sms-alert' ), '[username]', '[store_name]' ),
					'DEFAULT_WARRANTY_STATUS_CHANGED'      => sprintf( __( 'Hello %1$s, status of your RMA no. %2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[billing_first_name]', '[rma_number]', '[store_name]', '[rma_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_COURSE_FINISHED'        => sprintf( __( '%1$s: Hi Admin %2$s has finished course %3$s%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[username]', '[course_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_USER_COURSE_FINISHED'         => sprintf( __( 'Congratulation you have finished course %1$s with %2$s%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[course_name]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_NEW_TEACHER_REGISTER'   => sprintf( __( '%1$s: Hi admin, an instructor %2$s has been joined.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[username]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_COURSE_ENROLL'          => sprintf( __( '%1$s: Hi Admin %2$s has enrolled course - %3$s%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[username]', '[course_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_NEW_TEACHER_REGISTER'         => sprintf( __( 'Congratulation %1$s, you have become an instructor with %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_CUSTOMER'    => sprintf( __( 'Congratulation %1$s, you have become an instructor with %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_CUSTOMER_PENDING' => sprintf( __( 'Dear %1$s, thank you for scheduling your booking with %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_CUSTOMER_APPROVED' => sprintf( __( 'Hello %1$s, status of your order with %2$s has been changed to confirmed.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_CUSTOMER_TRASH' => sprintf( __( 'Hello %1$s, status of your order with %2$s has been changed to rejected.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_ADMIN'       => sprintf( __( 'Congratulation %1$s, you have become an instructor with %2$s.%3$sPowered by%4$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_ADMIN_PENDING' => sprintf( __( 'You have a new booking from %1$s for %2$s %3$s. Please check admin dashboard for complete details.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[name]', '[date]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_ADMIN_APPROVED' => sprintf( __( '%1$s: status of booking %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[name]', 'confirmed', PHP_EOL, PHP_EOL ),
					'DEFAULT_BOOKING_CALENDAR_ADMIN_TRASH' => sprintf( __( '%1$s: status of booking %2$s has been changed to %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]', '[name]', 'rejected', PHP_EOL, PHP_EOL ),

					'DEFAULT_ADMIN_SUBS_CREATE_MSG'        => sprintf( __( '%1$s You have a new subscription %2$s for order value Rs. %3$s. Please check your admin dashboard for complete details.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[store_name]:', '#[subscription_id]', '[order_amount]', PHP_EOL, PHP_EOL ),
					'DEFAULT_ADMIN_SUBS_STATUS_MSG'        => sprintf( __( '%1$s Status of subscription %2$s worth Rs. %3$s is changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[store_name]:', '#[subscription_id]', '[order_amount]', '[subscription_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_CUST_SUBS_CREATE_MSG'         => sprintf( __( 'Hello %1$s thank you for your subscription %2$s with %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[billing_first_name]', '#[subscription_id]', '[store_name]', PHP_EOL, PHP_EOL ),
					'DEFAULT_CUST_SUBS_STATUS_MSG'         => sprintf( __( 'Hello %1$s, status of your subscription %2$s with %3$s has been changed to %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[billing_first_name]', '#[subscription_id]', '[store_name]', '[subscription_status]', PHP_EOL, PHP_EOL ),
					'DEFAULT_WC_RENEWAL_CUSTOMER_MESSAGE'  => sprintf( __( 'Hello %1$s, your subscription %2$s with %3$s is due for renewal on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[billing_first_name]', '#[subscription_id]', '[store_name]', '[renewal_date]', PHP_EOL, PHP_EOL ),
					'DEFAULT_SHARE_CART_MSG'               => sprintf( __( 'Hey %1$s, your friend %2$s has shared his %3$s cart with you. Click %4$s to view.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[friend_name]', '[billing_first_name]', '[store_name]', '[cart_url]', PHP_EOL, PHP_EOL ),
					'DEFAULT_WCBK_REMINDER_MESSAGE'  => sprintf( __( 'Hello %1$s, your booking %2$s with %3$s is fixed on %4$s.%5$sPowered by%6$swww.smsalert.co.in', 'sms-alert' ), '[first_name]', '#[booking_id]', '[store_name]', '[booking_start]', PHP_EOL, PHP_EOL ),
				/*translation required*/
				)
			)
		);
	}

	/**
	 * Show message function.
	 *
	 * @param string $message message.
	 * @param array  $data data.
	 *
	 * @return string
	 */
	public static function showMessage( $message, $data = array() ) {
		$displayMessage = '';
		$messages       = explode( ' ', $message );
		$msg            = unserialize( SALRT_MESSAGES );
		// return __($msg[$message],'sms-alert');
		return ( ! empty( $msg[ $message ] ) ? $msg[ $message ] : '' );
		/*
		 foreach ($messages as $message)
		{
			if(!SmsAlertUtility::isBlank($message))
			{
				//$formatMessage = constant( "self::".$message );
				$formatMessage = $msg[$message];
				foreach($data as $key => $value)
				{
					$formatMessage = str_replace("{{" . $key . "}}", $value ,$formatMessage);
				}
				$displayMessage.=$formatMessage;
			}
		}
		return $displayMessage; */
	}
}
new SmsAlertMessages();
