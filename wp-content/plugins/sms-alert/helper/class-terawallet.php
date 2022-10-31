<?php
/**
 * Woo wallet notification helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woo-wallet/woo-wallet.php' ) ) {
	return;
}
	/**
	 * TeraWallet class
	 */
class TeraWallet {

	/**
	 * Construct function
	 */
	public function __construct() {
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 100 );
		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1 );
		add_action( 'woo_wallet_transaction_recorded', array( $this, 'smsalert_send_msg_wallet_transaction' ), 11, 4 );
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public static function add_tabs( $tabs = array() ) {
		$woowallet_param = array(
			'checkTemplateFor' => 'wc_woowalletnotification',
			'templates'        => self::get_wc_woowallet_templates(),
		);

		$tabs['woocommerce']['inner_nav']['wc_woowalletnotification']['title']       = __( 'TeraWallet Notifications', 'sms-alert' );
		$tabs['woocommerce']['inner_nav']['wc_woowalletnotification']['tab_section'] = 'woowallettemplates';
		$tabs['woocommerce']['inner_nav']['wc_woowalletnotification']['tabContent']  = $woowallet_param;
		$tabs['woocommerce']['inner_nav']['wc_woowalletnotification']['filePath']    = 'views/message-template.php';
		$tabs['woocommerce']['inner_nav']['wc_woowalletnotification']['icon']        = 'dashicons-products';
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
		$defaults['smsalert_general']['woo_wallet_credited_msg']          = 'off';
		$defaults['smsalert_general']['sms_body_woo_wallet_credited_msg'] = '';
		$defaults['smsalert_general']['woo_wallet_debited_msg']           = 'off';
		$defaults['smsalert_general']['sms_body_woo_wallet_debited_msg']  = '';
		return $defaults;
	}

	/**
	 * Get wc woowallet templates.
	 *
	 * @return array
	 */
	public static function get_wc_woowallet_templates() {
		$woo_wallet_credited_msg          = smsalert_get_option( 'woo_wallet_credited_msg', 'smsalert_general', 'on' );
		$sms_body_woo_wallet_credited_msg = smsalert_get_option( 'sms_body_woo_wallet_credited_msg', 'smsalert_message', sprintf( __( 'Dear %1$s, %2$s has been credited to your wallet. Current wallet balance is : %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[amount]', '[wallet_amount]', PHP_EOL, PHP_EOL ) );

		$woo_wallet_debited_msg          = smsalert_get_option( 'woo_wallet_debited_msg', 'smsalert_general', 'on' );
		$sms_body_woo_wallet_debited_msg = smsalert_get_option( 'sms_body_woo_wallet_debited_msg', 'smsalert_message', sprintf( __( 'Dear %1$s, %2$s has been debited from your wallet. Current wallet balance is : %3$s.%4$sPowered by%5$swww.smsalert.co.in', 'sms-alert' ), '[username]', '[amount]', '[wallet_amount]', PHP_EOL, PHP_EOL ) );

		$templates = array();

		$woo_wallet_variables                               = array(
			'[username]'      => 'Username',
			'[amount]'        => 'Amount',
			'[wallet_amount]' => 'Wallet Amount',
			'[store_name]'    => 'Store Name',
			'[shop_url]'      => 'Shop Url',
		);
		$templates['woo-wallet-credited']['title']          = 'When wallet is credited';
		$templates['woo-wallet-credited']['enabled']        = $woo_wallet_credited_msg;
		$templates['woo-wallet-credited']['status']         = 'woo-wallet-credited';
		$templates['woo-wallet-credited']['text-body']      = $sms_body_woo_wallet_credited_msg;
		$templates['woo-wallet-credited']['checkboxNameId'] = 'smsalert_general[woo_wallet_credited_msg]';
		$templates['woo-wallet-credited']['textareaNameId'] = 'smsalert_message[sms_body_woo_wallet_credited_msg]';
		$templates['woo-wallet-credited']['token']          = $woo_wallet_variables;

		$templates['woo-wallet-debited']['title']          = 'When wallet is debited';
		$templates['woo-wallet-debited']['enabled']        = $woo_wallet_debited_msg;
		$templates['woo-wallet-debited']['status']         = 'woo-wallet-debited';
		$templates['woo-wallet-debited']['text-body']      = $sms_body_woo_wallet_debited_msg;
		$templates['woo-wallet-debited']['checkboxNameId'] = 'smsalert_general[woo_wallet_debited_msg]';
		$templates['woo-wallet-debited']['textareaNameId'] = 'smsalert_message[sms_body_woo_wallet_debited_msg]';
		$templates['woo-wallet-debited']['token']          = $woo_wallet_variables;

		return $templates;
	}

	/**
	 * Smsalert send sms on low stock function.
	 *
	 * @param int    $transaction_id Transaction Id.
	 * @param int    $user_id        User Id.
	 * @param int    $amount         Amount.
	 * @param string $type           Transaction Type.
	 */
	public function smsalert_send_msg_wallet_transaction( $transaction_id, $user_id, $amount, $type ) {
		if ( 'credit' === $type ) {
			$message                 = smsalert_get_option( 'sms_body_woo_wallet_credited_msg', 'smsalert_message', '' );
			$message                 = $this->parse_sms_body( $user_id, $amount, $message );
			$woo_wallet_credited_msg = smsalert_get_option( 'woo_wallet_credited_msg', 'smsalert_general', 'on' );
			if ( 'on' === $woo_wallet_credited_msg && '' !== $message ) {
				$user_phone = get_user_meta( $user_id, 'billing_phone', true );
				//do_action( 'sa_send_sms', $user_phone, $message );
				$obj             = array();
				$obj['number']   = $user_phone;
				$obj['sms_body'] = $message;
				SmsAlertcURLOTP::sendsms( $obj );
			}
		} elseif ( 'debit' === $type ) {
			$message                = smsalert_get_option( 'sms_body_woo_wallet_debited_msg', 'smsalert_message', '' );
			$message                = $this->parse_sms_body( $user_id, $amount, $message );
			$woo_wallet_debited_msg = smsalert_get_option( 'woo_wallet_debited_msg', 'smsalert_general', 'on' );
			if ( 'on' === $woo_wallet_debited_msg && '' !== $message ) {
				$user_phone = get_user_meta( $user_id, 'billing_phone', true );
				//do_action( 'sa_send_sms', $user_phone, $message );
				$obj             = array();
				$obj['number']   = $user_phone;
				$obj['sms_body'] = $message;
				SmsAlertcURLOTP::sendsms( $obj );
			}
		}
	}

	/**
	 * Parse sms body function
	 *
	 * @param int    $user_id user id.
	 * @param int    $amount  amount.
	 * @param string $message message.
	 *
	 * @return string
	 */
	public function parse_sms_body( $user_id, $amount, $message ) {
		$find      = array(
			'[username]',
			'[amount]',
			'[wallet_amount]',
		);
		$user_info = get_userdata( $user_id );
		$replace   = array(
			$user_info->user_login,
			$amount,
			woo_wallet()->wallet->get_wallet_balance( $user_id, 'edit' ),
		);
		$message   = str_replace( $find, $replace, $message );
		return $message;
	}
}
new TeraWallet();
