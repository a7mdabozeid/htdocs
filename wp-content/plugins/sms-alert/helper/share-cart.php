<?php
/**
 * Share cart helper.
 *
 * @category Share cart
 * @author   cozy vision technologies pvt ltd <support@cozyvision.com>
 * @package  Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}
/**
 * Share_Cart class
 */
class Share_Cart {

	/**
	 * Construct function.
	 */
	public function __construct() {
		add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 10 );
		add_action( 'sa_tabContent', array( $this, 'tabContent' ), 1 );
		add_filter( 'sAlertDefaultSettings', array( $this, 'add_default_setting' ), 1 );
		$user_authorize = new smsalert_Setting_Options();
		if($user_authorize->is_user_authorised())
		{
			$share_cart_enable = smsalert_get_option( 'customer_notify', 'smsalert_share_cart_general' );
			
			if ( 'on' === $share_cart_enable ) {
				add_action( 'init', array( $this, 'share_cart_button_position' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'share_cart_load_front' ) );
				add_shortcode( 'sa_sharecart', array( $this, 'add_share_cart_button' ), 100 );
			}

			add_action( 'wp_loaded', array( $this, 'restore_share_cart' ) );
			add_action( 'wp_ajax_check_cart_data', array( $this, 'check_cart_is_empty' ) );
			add_action( 'wp_ajax_nopriv_check_cart_data', array( $this, 'check_cart_is_empty' ) );

			add_action( 'wp_ajax_save_cart_data', array( $this, 'save_share_cart_data' ) );
			add_action( 'wp_ajax_nopriv_save_cart_data', array( $this, 'save_share_cart_data' ) );
		}
	}
	
	function share_cart_load_front() {
		wp_enqueue_script( 'share_cart_admin_script', SA_MOV_URL . 'js/share_cart_front_script.js', false, SmsAlertConstants::SA_VERSION );
		wp_localize_script(
			'share_cart_admin_script',
			'ajax_url',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	function restore_share_cart() {
		$cart = SA_Cart_Admin::restore_cart();
	}

	function check_cart_is_empty() {

		global $woocommerce;

		// Retrieving cart total value.
		$cart_total = WC()->cart->total;
		if ( ! empty( $cart_total ) ) {
			echo esc_attr( $cart_total );
		} else {
			return;
		}
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public function add_tabs( $tabs = array() ) {
		$smsalertsharecart_param = array(
			'checkTemplateFor' => 'Share_Cart',
			'templates'        => $this->get_sms_alert_share_cart_templates(),
		);

		$tabs['woocommerce']['inner_nav']['share_cart']['title']
       = 'Share Cart';
		$tabs['woocommerce']['inner_nav']['share_cart']['tab_section']
		= 'smsalertsharecarttemplates';
		$tabs['woocommerce']['inner_nav']['share_cart']['tabContent'] = $smsalertsharecart_param;
		$tabs['woocommerce']['inner_nav']['share_cart']['filePath'] = 'views/sharecart-template.php';
		$tabs['woocommerce']['inner_nav']['share_cart']['help_links']                        = array(
			'youtube_link' => array(
				'href'   => 'https://youtu.be/vFJhnIiDrE8',
				'target' => '_blank',
				'alt'    => 'Watch steps on Youtube',
				'class'  => 'btn-outline',
				'label'  => 'Youtube',
				'icon'   => '<span class="dashicons dashicons-video-alt3" style="font-size: 21px;"></span> ',

			),
			'kb_link'      => array(
				'href'   => 'https://kb.smsalert.co.in/knowledgebase/share-cart/',
				'target' => '_blank',
				'alt'    => 'Read how to use share cart',
				'class'  => 'btn-outline',
				'label'  => 'Documentation',
				'icon'   => '<span class="dashicons dashicons-format-aside"></span>',
			),
		);
		return $tabs;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public function add_default_setting( $defaults = array() ) {
		$defaults['smsalert_share_cart_general']['customer_notify'] = 'off';
		$defaults['smsalert_share_cart_general']['share_btnpos']    = 'after_cart_table';
		$defaults['smsalert_share_cart_general']['share_btntext']   = 'Share Cart';
		$defaults['smsalert_share_cart_general']['share_boxtitle']  = 'Your Cart';
		$defaults['smsalert_share_cart_message']['customer_notify'] = '';
		return $defaults;
	}

	/**
	 * Get sms alert share cart templates.
	 *
	 * @return array
	 */
	public function get_sms_alert_share_cart_templates() {
		$current_val      = smsalert_get_option(
			'customer_notify',
			'smsalert_share_cart_general
		',
			'on'
		);
		$checkbox_name_id = 'smsalert_share_cart_general[customer_notify]';

		$templates = array();

		$textarea_name_id = 'smsalert_share_cart_message[customer_notify]';
		$text_body        = smsalert_get_option(
			'customer_notify',
			'smsalert_share_cart_message',
			SmsAlertMessages::showMessage( 'DEFAULT_SHARE_CART_MSG' )
		);

		$templates['share_cart']['enabled']        = $current_val;
		$templates['share_cart']['title']          = 'Share your Cart';
		$templates['share_cart']['checkboxNameId'] = $checkbox_name_id;
		$templates['share_cart']['text-body']      = $text_body;
		$templates['share_cart']['textareaNameId'] = $textarea_name_id;
		$templates['share_cart']['status']         = 'share_cart';
		$templates['share_cart']['token']          = $this->get_share_cart_variables();

		return $templates;
	}

	function share_cart_button_position() {
		$share_btnpos = '';
		if ( ! empty( smsalert_get_option( 'share_btnpos', 'smsalert_share_cart_general' ) ) ) {
			$share_btnpos = smsalert_get_option( 'share_btnpos', 'smsalert_share_cart_general' );
		}

		if ( 'before_cart_table' === $share_btnpos || 'before_cart' === $share_btnpos ) {
			add_action( 'woocommerce_before_cart_table', array( $this, 'show_share_cart_button' ) );
		}

		if ( 'after_cart_table' === $share_btnpos ) {
			add_action( 'woocommerce_after_cart_table', array( $this, 'show_share_cart_button' ) );
		}

		if ( 'after_cart' === $share_btnpos ) {
			add_action( 'woocommerce_after_cart', array( $this, 'show_share_cart_button' ) );
		}

		if ( 'beside_update_cart' === $share_btnpos ) {
			add_action( 'woocommerce_cart_actions', array( $this, 'show_share_cart_button' ) );
		}
	}
	
	function show_share_cart_button() {
		echo do_shortcode('[sa_sharecart]');
	}

	function add_share_cart_button() {
		global $woocommerce;
		$cart_total = isset(WC()->cart->total)?WC()->cart->total:'';
		if(!empty($cart_total))
		{
		  add_action( 'wp_footer', array( $this, 'share_cart_popup_div' ) );	
		  $btn_text = smsalert_get_option( 'share_btntext', 'smsalert_share_cart_general' );
		  return '<button class="button button-primary" id="smsalert_share_cart"><span class="button__text">' . esc_html__( $btn_text ) . '</span></button>';
		}
		else if(current_user_can('administrator')){
			return 'Your cart is empty!</br><small>This message will be only visible to admin</small>';
		}
	}

	function share_cart_popup_div() {
		global $woocommerce;
		$cart_total = isset(WC()->cart->total)?WC()->cart->total:'';
		if(empty($cart_total))
		{
			return;
		}
		$current_user_id = get_current_user_id();

		$phone = ( get_user_meta( $current_user_id, 'billing_phone', true ) !== '' ) ? SmsAlertUtility::formatNumberForCountryCode( get_user_meta( $current_user_id, 'billing_phone', true ) ) : '';
		$uname = ( get_user_meta( $current_user_id, 'first_name', true ) !== '' ) ? ( get_user_meta( $current_user_id, 'first_name', true ) ) : '';

		$modal_style = smsalert_get_option( 'modal_style', 'smsalert_general', 'center' );

		?>
		<div id="smsalert_sharecart_popup" class="smsalert_sharecart_popup_class smsalertModal <?php echo esc_attr( $modal_style ); ?>" data-modal-close="<?php echo esc_attr( substr( $modal_style, 0, -2 ) ); ?>">
			<div class="smsalert_scp_close_modal-content modal-content">
				<div class="smsalert_scp_inner_div">
					<div class="close"><span></span></div>
					<form class="sc_form">
						<ul id="smsalert_scp_ul">
							<h2 class="box-title"><?php esc_html_e( smsalert_get_option( 'share_boxtitle', 'smsalert_share_cart_general' ), 'sms-alert' ); ?></h2>
							<li class="savecart_li">
								<input type="text" name="sc_uname" id="sc_uname" placeholder="Your Name*" value="<?php echo esc_attr( $uname ); ?>" <?php echo $uname ? "disabled='disabled'" : ''; ?>>
							</li>
							<li class="savecart_li">
								<input type="text" name="sc_umobile" id="sc_umobile" placeholder="Your Mobile No*" class="phone-valid" value="<?php echo esc_attr( $phone ); ?>" <?php echo $phone ? "disabled='disabled'" : ''; ?>>
							</li>
							<li class="savecart_li">
								<input type="text" name="sc_fname" id="sc_fname" placeholder="Friend Name*">
							</li>
							<li class="savecart_li">
								<input type="text" name="sc_fmobile" id="sc_fmobile" placeholder="Friend Mobile No*" class="phone-valid">
							</li>
							<li class="savecart_li">
								<button class="button btn" id="sc_btn" name="sc_btn"><span class="button__text"><?php esc_html_e( 'Share Cart', 'sms-alert' ); ?></span></button>
							</li>
						</ul>
						<?php 
						echo wp_nonce_field('smsalert_wp_sharecart_nonce','smsalert_sharecart_nonce', true, false);
						?>
					</form>
					<div id="sc_response"></div>
				</div>				
			</div>
		</div>
		<?php
	}

	function save_share_cart_data() {
		check_ajax_referer( 'smsalert_wp_sharecart_nonce', 'smsalert_sharecart_nonce' );
		if ( isset( $_REQUEST['sc_umobile'] ) ) {
			global $phoneLogic;
			
			$invalid_fmob= str_replace( '##phone##', $_REQUEST['sc_fmobile'], $phoneLogic->_get_otp_invalid_format_message() );
			
			$invalid_scmob= str_replace( '##phone##', $_REQUEST['sc_umobile'], $phoneLogic->_get_otp_invalid_format_message() );
			
			$_REQUEST['sc_fmobile'] = SmsAlertcURLOTP::checkPhoneNos( $_REQUEST['sc_fmobile'] );
			
			$_REQUEST['sc_umobile'] = SmsAlertcURLOTP::checkPhoneNos( $_REQUEST['sc_umobile'] );
			
			if (empty($_REQUEST['sc_umobile'])) {
				echo $invalid_scmob;die();
			}
			if (empty($_REQUEST['sc_fmobile'])) {
				echo $invalid_fmob;die();
			}
			
			$public = new SA_Cart_Public( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

			global $wpdb;
			$table_name = $wpdb->prefix . SA_CART_TABLE_NAME; // do not forget about tables prefix

			// Retrieving cart array consisting of currency, cart total, time, msg status, session id and products and their quantities.
			$cart_data       = $public->read_cart();
			$cart_total      = $cart_data['cart_total'];
			$cart_currency   = $cart_data['cart_currency'];
			$current_time    = $cart_data['current_time'];
			$msg_sent        = $cart_data['msg_sent'];
			$session_id      = $cart_data['session_id'];
			$product_array   = $cart_data['product_array'];
			$cart_session_id = WC()->session->get( 'cart_session_id' );

			// In case if the cart has no items in it, we need to delete the abandoned cart.
			if ( empty( $product_array ) ) {
				SA_Cart_Admin::clear_cart_data();
				return;
			}

			// Checking if we have values coming from the input fields.
			$name  = sanitize_text_field( $_REQUEST['sc_uname'] );
			$phone = sanitize_text_field( $_REQUEST['sc_umobile'] );

			$current_session_exist_in_db = $public->current_session_exist_in_db( $cart_session_id );
			// If we have already inserted the Users session ID in Session variable and it is not NULL and Current session ID exists in Database we update the abandoned cart row.
			if ( $current_session_exist_in_db && null !== $cart_session_id ) {

				$msg_sent = 0;
				// Updating row in the Database where users Session id = same as prevously saved in Session.
				$updated_rows = $wpdb->prepare(
					'%s',
					$wpdb->update(
						$table_name,
						array(
							'name'          => sanitize_text_field( $name ),
							'phone'         => filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ),
							'cart_contents' => serialize( $product_array ),
							'cart_total'    => sanitize_text_field( $cart_total ),
							'currency'      => sanitize_text_field( $cart_currency ),
							'time'          => sanitize_text_field( $current_time ),
							'msg_sent'      => sanitize_text_field( $msg_sent ),
						),
						array( 'session_id' => $cart_session_id ),
						array( '%s', '%s', '%s', '%0.2f', '%s', '%s', '%d' ),
						array( '%s' )
					)
				);

				if ( $updated_rows ) { // If we have updated at least one row.
					   $updated_rows = str_replace( "'", '', $updated_rows ); // Removing quotes from the number of updated rows.

					if ( $updated_rows > 1 ) { // Checking if we have updated more than a single row to know if there were duplicates.
						$public->delete_duplicate_carts( $cart_session_id, $updated_rows );
					}
				}
			} else {
				// Inserting row into Database.
				$wpdb->query(
					$wpdb->prepare(
						'INSERT INTO ' . $table_name . '
						( name, phone, cart_contents, cart_total, currency, time, session_id, msg_sent )
						VALUES ( %s, %s, %s, %0.2f, %s, %s, %s, %d )',
						array(
							sanitize_text_field( $name ),
							filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ),
							serialize( $product_array ),
							sanitize_text_field( $cart_total ),
							sanitize_text_field( $cart_currency ),
							sanitize_text_field( $current_time ),
							sanitize_text_field( $session_id ),
							sanitize_text_field( $msg_sent ),
						)
					)
				);
				// Storing session_id in WooCommerce session
				WC()->session->set( 'cart_session_id', $session_id );
				$public->increase_captured_abandoned_cart_count(); // Increasing total count of captured abandoned carts
			}

			// Send Msg to friend
			if ( $_REQUEST['sc_fmobile'] ) {

				$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

				// $lastid               = $wpdb->insert_id;
				$lastid = $wpdb->get_results( 'SELECT MAX(id) FROM ' . $table_name, ARRAY_A );

				$data = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' WHERE id = ' . $lastid[0]['MAX(id)'], ARRAY_A );
				$data = array_shift( $data );

				$data['cart_url']     = $this->create_cart_url( $session_id, $data['id'] );
				$data['friend_name']  = sanitize_text_field( $_REQUEST['sc_fname'] );
				$data['friend_phone'] = sanitize_text_field( $_REQUEST['sc_fmobile'] );
				$data['your_phone']   = sanitize_text_field( $_REQUEST['sc_umobile'] );
				$data['your_name']    = sanitize_text_field( $_REQUEST['sc_uname'] );
				$phone                = sanitize_text_field( $_REQUEST['sc_fmobile'] );
				$message              = smsalert_get_option( 'customer_notify', 'smsalert_share_cart_message' );
				do_action( 'sa_send_sms', $phone, $this->parse_sms_body( $data, $message ) );
			}

			echo 'Cart Shared Successfully.';
			die();
		}
	}

	/**
	 * Parse sms body function.
	 *
	 * @param array  $data    data.
	 * @param string $content content.
	 *
	 * @return array
	 */
	public function parse_sms_body( $data = array(), $content = null ) {
		$cart_items         = (array) unserialize( $data['cart_contents'] );
		$item_name          = implode(
			', ',
			array_map(
				function ( $o ) {
					return $o['product_title'];
				},
				$cart_items
			)
		);
		$item_name_with_qty = implode(
			', ',
			array_map(
				function ( $o ) {
					return sprintf( '%s [%u]', $o['product_title'], $o['quantity'] );
				},
				$cart_items
			)
		);

		$find = array(
			'[item_name]',
			'[item_name_qty]',
			'[cart_url]',
			'[friend_name]',
			'[friend_phone]',
			'[billing_first_name]',
			'[your_phone]',
			'[store_name]',
			'[shop_url]',
			'[currency]',
			'[time]',
			'[cart_total]',
		);

		$replace = array(
			wp_specialchars_decode( $item_name ),
			$item_name_with_qty,
			( array_key_exists( 'cart_url', $data ) ? $data['cart_url'] : '' ),
			$data['friend_name'],
			$data['friend_phone'],
			$data['your_name'],
			$data['your_phone'],
			get_bloginfo(),
			get_site_url(),
			$data['currency'],
			$data['time'],
			$data['cart_total'],
		);

		$content         = str_replace( $find, $replace, $content );
		return $content;
	}

	/**
	 * Get Share Cart variables.
	 *
	 * @return array
	 */
	public static function get_share_cart_variables() {
		$variables = array(
			'[billing_first_name]' => 'Your Name',
			'[friend_name]'        => 'Friend Name',
			'[your_phone]'         => 'Your Phone',
			'[friend_phone]'       => 'Friend Phone',
			'[cart_total]'         => 'Cart Total',
			'[currency]'           => 'Currency',
			'[time]'               => 'Time',
			'[item_name]'          => 'Item name',
			'[item_name_qty]'      => 'Item with Qty',
			'[store_name]'         => 'Store Name',
			'[shop_url]'           => 'Shop Url',
			'[cart_url]'           => 'Cart Url',
		);
		return $variables;
	}

	/**
	 * Create cart url function.
	 *
	 * @param string $session_id session_id.
	 * @param string $cart_id    cart_id.
	 *
	 * @return string
	 */
	public function create_cart_url( $session_id, $cart_id ) {
		$cart_url            = wc_get_cart_url();
		$hash                = hash_hmac( 'md5', $session_id, CART_ENCRYPTION_KEY ) . '-' . $cart_id;
		return $checkout_url = $cart_url . '?cart=' . $hash;
	}
}
new Share_Cart();
?>
