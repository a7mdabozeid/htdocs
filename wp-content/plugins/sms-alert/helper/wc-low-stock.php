<?php
/**
 * Woocommerce low stock helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}
	/**
	 * WCLowStock class
	 */
class WCLowStock {
	/**
	 * Construct function
	 */
	public function __construct() {
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 100 );
		add_action( 'woocommerce_low_stock', array( $this, 'smsalert_send_msg_low_stock' ), 11 );
		add_action( 'woocommerce_no_stock', array( $this, 'smsalert_send_msg_out_of_stock' ), 10 );
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public static function add_tabs( $tabs = array() ) {
		$backinstock_param = array(
			'checkTemplateFor' => 'wc_stocknotification',
			'templates'        => self::get_wc_stock_templates(),
		);

		$tabs['woocommerce']['inner_nav']['wc_stocknotification']['title']       = __( 'Stock Notifications', 'sms-alert' );
		$tabs['woocommerce']['inner_nav']['wc_stocknotification']['tab_section'] = 'backinstocktemplates';
		$tabs['woocommerce']['inner_nav']['wc_stocknotification']['tabContent']  = $backinstock_param;
		$tabs['woocommerce']['inner_nav']['wc_stocknotification']['filePath']    = 'views/message-template.php';
		$tabs['woocommerce']['inner_nav']['wc_stocknotification']['icon']        = 'dashicons-products';
		return $tabs;
	}

	/**
	 * Get wc stock templates.
	 *
	 * @return array
	 */
	public static function get_wc_stock_templates() {
		$smsalert_low_stock_admin_msg = smsalert_get_option( 'admin_low_stock_msg', 'smsalert_general', 'on' );
		$sms_body_admin_low_stock_msg = smsalert_get_option( 'sms_body_admin_low_stock_msg', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_LOW_STOCK_MSG' ) );

		$smsalert_out_of_stock_admin_msg = smsalert_get_option( 'admin_out_of_stock_msg', 'smsalert_general', 'on' );
		$sms_body_admin_out_of_stock_msg = smsalert_get_option( 'sms_body_admin_out_of_stock_msg', 'smsalert_message', SmsAlertMessages::showMessage( 'DEFAULT_ADMIN_OUT_OF_STOCK_MSG' ) );

		$templates = array();

		$low_stock_variables                      = array(
			'[item_name]'  => 'Product Name',
			'[store_name]' => 'Store Name',
			'[item_qty]'   => 'Quantity',
			'[shop_url]'   => 'Shop Url',
		);
		$templates['low-stock']['title']          = 'When product is in low stock';
		$templates['low-stock']['enabled']        = $smsalert_low_stock_admin_msg;
		$templates['low-stock']['status']         = 'low-stock';
		$templates['low-stock']['text-body']      = $sms_body_admin_low_stock_msg;
		$templates['low-stock']['checkboxNameId'] = 'smsalert_general[admin_low_stock_msg]';
		$templates['low-stock']['textareaNameId'] = 'smsalert_message[sms_body_admin_low_stock_msg]';
		$templates['low-stock']['token']          = $low_stock_variables;

		$out_of_stock_variables                      = array(
			'[item_name]'  => 'Product Name',
			'[store_name]' => 'Store Name',
			'[item_qty]'   => 'Quantity',
			'[shop_url]'   => 'Shop Url',
		);
		$templates['out-of-stock']['title']          = 'When product is out of stock';
		$templates['out-of-stock']['enabled']        = $smsalert_out_of_stock_admin_msg;
		$templates['out-of-stock']['status']         = 'out-of-stock';
		$templates['out-of-stock']['text-body']      = $sms_body_admin_out_of_stock_msg;
		$templates['out-of-stock']['checkboxNameId'] = 'smsalert_general[admin_out_of_stock_msg]';
		$templates['out-of-stock']['textareaNameId'] = 'smsalert_message[sms_body_admin_out_of_stock_msg]';
		$templates['out-of-stock']['token']          = $out_of_stock_variables;

		return $templates;
	}

	/**
	 * Smsalert send sms on low stock function.
	 *
	 * @param object $product product.
	 */
	public function smsalert_send_msg_low_stock( $product ) {
		$message = smsalert_get_option( 'sms_body_admin_low_stock_msg', 'smsalert_message', '' );
		$message = $this->parse_sms_body( $product, $message );

		$sms_admin_phone = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

		$smsalert_notification_low_stock_admin_msg = smsalert_get_option( 'admin_low_stock_msg', 'smsalert_general', 'on' );

		if ( 'on' === $smsalert_notification_low_stock_admin_msg && '' !== $message ) {
			$admin_phone_number = str_replace( 'postauthor', 'post_author', $sms_admin_phone );
			$author_no          = apply_filters( 'sa_post_author_no', $product->get_id() );
			if ( ( strpos( $admin_phone_number, 'post_author' ) !== false ) && ! empty( $author_no ) ) {
				$admin_phone_number = str_replace( 'post_author', $author_no, $admin_phone_number );
			}

			do_action( 'sa_send_sms', $admin_phone_number, $message );
		}
	}

	/**
	 * Smsalert send sms on out of stock function.
	 *
	 * @param object $product product.
	 */
	public function smsalert_send_msg_out_of_stock( $product ) {
		$message = smsalert_get_option( 'sms_body_admin_out_of_stock_msg', 'smsalert_message', '' );
		$message = $this->parse_sms_body( $product, $message );

		$sms_admin_phone = smsalert_get_option( 'sms_admin_phone', 'smsalert_message', '' );

		$smsalert_notification_out_of_stock_admin_msg = smsalert_get_option( 'admin_out_of_stock_msg', 'smsalert_general', 'on' );
		if ( 'on' === $smsalert_notification_out_of_stock_admin_msg && '' !== $message ) {
			$admin_phone_number = str_replace( 'postauthor', 'post_author', $sms_admin_phone );
			$author_no          = apply_filters( 'sa_post_author_no', $product->get_id() );

			if ( ( strpos( $admin_phone_number, 'post_author' ) !== false ) && ! empty( $author_no ) ) {
				$admin_phone_number = str_replace( 'post_author', $author_no, $admin_phone_number );
			}

			do_action( 'sa_send_sms', $admin_phone_number, $message );
		}
	}

	/**
	 * Parse sms body function
	 *
	 * @param object $product product.
	 * @param string $message message.
	 *
	 * @return string
	 */
	public function parse_sms_body( $product, $message ) {

		$item_name = $product->get_name();
		$item_qty  = $product->get_stock_quantity();

		$find = array(
			'[item_name]',
			'[item_qty]',
		);

		$replace = array(
			$item_name,
			$item_qty,
		);

		$message = str_replace( $find, $replace, $message );
		return $message;
	}
}
new WCLowStock();
