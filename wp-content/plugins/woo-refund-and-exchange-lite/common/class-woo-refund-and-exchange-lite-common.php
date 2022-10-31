<?php
/**
 * The common functionality of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/common
 */

/**
 * The common functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the common stylesheet and JavaScript.
 * namespace woo_refund_and_exchange_lite_common.
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/common
 */
class Woo_Refund_And_Exchange_Lite_Common {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
	 * @var     string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wrael_common_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . 'common', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'common/css/woo-refund-and-exchange-lite-common.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wrael_common_enqueue_scripts() {
		$pro_active = wps_rma_pro_active();
		if ( get_current_user_id() > 0 ) {
			$myaccount_page     = get_option( 'woocommerce_myaccount_page_id' );
			$myaccount_page_url = get_permalink( $myaccount_page );
		} else {
			$myaccount_page     = get_option( 'woocommerce_myaccount_page_id' );
			$myaccount_page_url = get_permalink( $myaccount_page );
		}
		wp_register_script( $this->plugin_name . 'common', WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'common/js/woo-refund-and-exchange-lite-common.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name . 'common',
			'wrael_common_param',
			array(
				'ajaxurl'                => admin_url( 'admin-ajax.php' ),
				'wps_rma_nonce'          => wp_create_nonce( 'wps_rma_ajax_security' ),
				'return_subject_msg'     => esc_html__( 'Please Enter Refund Subject.', 'woo-refund-and-exchange-lite' ),
				'return_reason_msg'      => esc_html__( 'Please Enter Refund Reason.', 'woo-refund-and-exchange-lite' ),
				'return_select_product'  => esc_html__( 'Please Select Product to refund.', 'woo-refund-and-exchange-lite' ),
				'check_pro_active'       => esc_html( $pro_active ),
				'message_sent'           => esc_html__( 'The message has been sent successfully', 'woo-refund-and-exchange-lite' ),
				'message_empty'          => esc_html__( 'Please Enter a Message.', 'woo-refund-and-exchange-lite' ),
				'myaccount_url'          => esc_attr( $myaccount_page_url ),
				'refund_form_attachment' => get_option( 'wps_rma_refund_attachment' ),
				'order_msg_attachment'   => get_option( 'wps_rma_general_enable_om_attachment' ),
				'no_file_attached'       => esc_html__( 'No File Attached', 'woo-refund-and-exchange-lite' ),
				'file_not_supported'     => esc_html__( 'Attached File type is not supported', 'woo-refund-and-exchange-lite' ),
			)
		);
		wp_enqueue_script( $this->plugin_name . 'common' );
	}

	/**
	 * Add the email classes.
	 *
	 * @param array $email_classes email classes.
	 */
	public function wps_rma_woocommerce_emails( $email_classes ) {
		// include our order message email class.
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'emails/class-wps-rma-order-messages-email.php';
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'emails/class-wps-rma-refund-request-email.php';
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'emails/class-wps-rma-refund-request-accept-email.php';
		require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'emails/class-wps-rma-refund-request-cancel-email.php';
		// add the email class to the list of email classes that WooCommerce loads.

		$email_classes['wps_rma_order_messages_email']        = new Wps_Rma_Order_Messages_Email();
		$email_classes['wps_rma_refund_request_email']        = new Wps_Rma_Refund_Request_Email();
		$email_classes['wps_rma_refund_request_accept_email'] = new Wps_Rma_Refund_Request_Accept_Email();
		$email_classes['wps_rma_refund_request_cancel_email'] = new Wps_Rma_Refund_Request_Cancel_Email();
		return $email_classes;
	}

	/**
	 * This function is to save return request Attachment
	 */
	public function wps_rma_order_return_attach_files() {
		$check_ajax = check_ajax_referer( 'wps_rma_ajax_security', 'security_check' );

		if ( $check_ajax ) {
			if ( isset( $_FILES['wps_rma_return_request_files'] ) && isset( $_FILES['wps_rma_return_request_files']['tmp_name'] ) && isset( $_FILES['wps_rma_return_request_files']['name'] ) ) {
				$filename = array();
				$order_id = isset( $_POST['wps_rma_return_request_order'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_rma_return_request_order'] ) ) : sanitize_text_field( wp_unslash( $_POST['wps_rma_return_request_order'] ) );
				$count    = count( $_FILES['wps_rma_return_request_files']['tmp_name'] );
				for ( $i = 0; $i < $count; $i++ ) {
					if ( isset( $_FILES['wps_rma_return_request_files']['tmp_name'][ $i ] ) ) {
						$directory = ABSPATH . 'wp-content/attachment';
						if ( ! file_exists( $directory ) ) {
							mkdir( $directory, 0755, true );
						}
						$file_name   = isset( $_FILES['wps_rma_return_request_files']['name'][ $i ] ) ? sanitize_text_field( wp_unslash( $_FILES['wps_rma_return_request_files']['name'][ $i ] ) ) : '';
						$source_path = sanitize_text_field( wp_unslash( $_FILES['wps_rma_return_request_files']['tmp_name'][ $i ] ) );
						$target_path = $directory . '/' . $order_id . '-' . $file_name;

						$filename[] = $order_id . '-' . $file_name;
						move_uploaded_file( $source_path, $target_path );
					}
				}

				$request_files = get_post_meta( $order_id, 'wps_rma_return_attachment', true );

				$pending = true;
				if ( isset( $request_files ) && ! empty( $request_files ) ) {
					foreach ( $request_files as $date => $request_file ) {
						if ( 'pending' === $request_file['status'] ) {
							unset( $request_files[ $date ][0] );
							$request_files[ $date ]['files']  = $filename;
							$request_files[ $date ]['status'] = 'pending';
							$pending                          = false;
							break;
						}
					}
				}

				if ( $pending ) {
					$request_files                    = array();
					$date                             = gmdate( 'd-m-Y' );
					$request_files[ $date ]['files']  = $filename;
					$request_files[ $date ]['status'] = 'pending';
				}

				update_post_meta( $order_id, 'wps_rma_return_attachment', $request_files );
				echo 'success';
			}
			wp_die();
		}
	}

	/**
	 * This function is to save return request.
	 */
	public function wps_rma_save_return_request() {
		$check_ajax = check_ajax_referer( 'wps_rma_ajax_security', 'security_check' );
		if ( $check_ajax && current_user_can( 'wps-rma-refund-request' ) ) {
			$order_id = isset( $_POST['orderid'] ) ? sanitize_text_field( wp_unslash( $_POST['orderid'] ) ) : '';
			$re_bank  = get_option( 'wps_rma_refund_manually_de', false );
			if ( 'on' === $re_bank && ! empty( $_POST['bankdetails'] ) ) {
				update_post_meta( $order_id, 'wps_rma_bank_details', sanitize_text_field( wp_unslash( $_POST['bankdetails'] ) ) );
			}
			$refund_method        = isset( $_POST['refund_method'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_method'] ) ) : '';
			$wallet_enabled       = get_option( 'wps_rma_wallet_enable', 'no' );
			$refund_method_check  = get_option( 'wps_rma_refund_method', 'no' );
			if ( wps_rma_pro_active() && 'on' === $wallet_enabled && 'on' !== $refund_method_check ) {
				$refund_method = 'wallet_method';
			}
			$products1 = $_POST;
			$response  = wps_rma_save_return_request_callback( $order_id, $refund_method, $products1 );
			echo wp_json_encode( $response );
			wp_die();
		}
	}

	/**
	 * This function is to add custom order status for return
	 */
	public function wps_rma_register_custom_order_status() {

		register_post_status(
			'wc-return-requested',
			array(
				'label'                     => 'Refund Requested',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true, /* translators: %s: search term */
				'label_count'               => _n_noop( 'Refund Requested <span class="count">(%s)</span>', 'Refund Requested <span class="count">(%s)</span>', 'woo-refund-and-exchange-lite' ),
			)
		);
		register_post_status(
			'wc-return-approved',
			array(
				'label'                     => 'Refund Approved',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true, /* translators: %s: search term */
				'label_count'               => _n_noop( 'Refund Approved <span class="count">(%s)</span>', 'Refund Approved <span class="count">(%s)</span>', 'woo-refund-and-exchange-lite' ),
			)
		);
		register_post_status(
			'wc-return-cancelled',
			array(
				'label'                     => 'Refund Cancelled',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true, /* translators: %s: search term */
				'label_count'               => _n_noop( 'Refund Cancelled <span class="count">(%s)</span>', 'Refund Cancelled <span class="count">(%s)</span>', 'woo-refund-and-exchange-lite' ),
			)
		);
		do_action( 'wps_rma_register_custom_order_status' );
	}

	/**
	 * This function is to register custom order status
	 *
	 * @param array $wps_rma_order_statuses .
	 */
	public function wps_rma_add_custom_order_status( $wps_rma_order_statuses ) {
		$wps_rma_new_order_statuses = array();
		foreach ( $wps_rma_order_statuses as $wps_rma_key => $wps_rma_status ) {

			$wps_rma_new_order_statuses[ $wps_rma_key ] = $wps_rma_status;

			if ( 'wc-completed' === $wps_rma_key ) {

				$wps_rma_new_order_statuses['wc-return-requested'] = esc_html__( 'Refund Requested', 'woo-refund-and-exchange-lite' );
				$wps_rma_new_order_statuses['wc-return-approved']  = esc_html__( 'Refund Approved', 'woo-refund-and-exchange-lite' );
				$wps_rma_new_order_statuses['wc-return-cancelled'] = esc_html__( 'Refund Cancelled', 'woo-refund-and-exchange-lite' );
				$wps_rma_new_order_statuses                        = apply_filters( 'wps_rma_add_custom_order_status', $wps_rma_new_order_statuses );
			}
		}
		return $wps_rma_new_order_statuses;
	}

	/**
	 * Add capabilities for userrole
	 */
	public function wps_rma_role_capability() {
		$wps_rma_customer_role = get_role( 'customer' );
		if ( ! empty( $wps_rma_customer_role ) ) {
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-request', true );
		}

		$wps_rma_customer_role = get_role( 'subscriber' );
		if ( ! empty( $wps_rma_customer_role ) ) {
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-request', true );
		}

		$wps_rma_customer_role = get_role( 'administrator' );
		if ( ! empty( $wps_rma_customer_role ) ) {
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-request', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-approve', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-cancel', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-manage-stock', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-amount', true );
		}

		$wps_rma_customer_role = get_role( 'editor' );
		if ( ! empty( $wps_rma_customer_role ) ) {
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-request', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-approve', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-cancel', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-manage-stock', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-amount', true );
		}

		$wps_rma_customer_role = get_role( 'shop_manager' );
		if ( ! empty( $wps_rma_customer_role ) ) {
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-request', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-approve', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-cancel', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-manage-stock', true );
			$wps_rma_customer_role->add_cap( 'wps-rma-refund-amount', true );
		}
	}

	/**
	 * Include the refund request temail template.
	 *
	 * @param string $order_id .
	 */
	public function wps_rma_refund_req_email( $order_id ) {
		include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'common/partials/email_template/woo-refund-and-exchange-lite-refund-request-email.php';
	}

	/**
	 *  Multisite compatibility .
	 *
	 * @param object $new_site .
	 * @return void
	 */
	public function wps_rma_plugin_on_create_blog( $new_site ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		// Check if the plugin has been activated on the network .
		if ( is_plugin_active_for_network( 'woo-refund-and-exchange-lite/woocommerce-refund-and-exchange-lite.php' ) ) {
			$blog_id = $new_site->blog_id;
			// Switch to newly created site .
			switch_to_blog( $blog_id );
			require_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'includes/class-woo-refund-and-exchange-lite-activator.php';
			Woo_Refund_And_Exchange_Lite_Activator::wps_rma_create_pages();
			update_option( 'wps_rma_plugin_standard_multistep_done', 'yes' );
			restore_current_blog();
		}

	}

	/**
	 * Send refund request accept email to customer
	 *
	 * @param string $order_id .
	 */
	public function wps_rma_refund_req_accept_email( $order_id ) {
		include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/email_template/woo-refund-and-exchange-lite-refund-request-accept-email.php';
	}

	/**
	 * Send refund request cancel email to customer.
	 *
	 * @param string $order_id .
	 */
	public function wps_rma_refund_req_cancel_email( $order_id ) {
		include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/email_template/woo-refund-and-exchange-lite-refund-request-cancel-email.php';
	}
	/**
	 * Save order message from admin side.
	 */
	public function wps_rma_order_messages_save() {
		$check_ajax = check_ajax_referer( 'wps_rma_ajax_security', 'security_check' );
		if ( $check_ajax ) {
			$msg      = isset( $_POST['msg'] ) ? filter_input( INPUT_POST, 'msg' ) : '';
			$msg_type = isset( $_POST['order_msg_type'] ) ? filter_input( INPUT_POST, 'order_msg_type' ) : '';
			$order_id = isset( $_POST['order_id'] ) ? filter_input( INPUT_POST, 'order_id' ) : '';
			$order    = wc_get_order( $order_id );
			$to       = $order->get_billing_email();
			if ( 'admin' === $msg_type ) {
				$sender = 'Shop Manager';
			} else {
				$sender = 'Customer';
				$to = get_option( 'woocommerce_email_from_address', get_option( 'admin_email' ) );
			}
			$flag = wps_rma_lite_send_order_msg_callback( $order_id, $msg, $sender, $to );
			echo esc_html( $flag );
			wp_die();
		}
	}

	/**
	 * Function is used for the sending the track data.
	 *
	 * @param boolean $override .
	 */
	public function wps_rma_tracker_send_event( $override = false ) {
		require WC()->plugin_path() . '/includes/class-wc-tracker.php';

		$last_send = get_option( 'wps_rma_tracker_last_send' );
		if ( ! apply_filters( 'wpswings_tracker_send_override', $override ) ) {
			// Send a maximum of once per week by default.
			$last_send = $this->wps_wrael_last_send_time();
			if ( $last_send && $last_send > apply_filters( 'wpswings_tracker_last_send_interval', strtotime( '-1 week' ) ) ) {
				return;
			}
		} else {
			// Make sure there is at least a 1 hour delay between override sends, we don't want duplicate calls due to double clicking links.
			$last_send = $this->wps_wrael_last_send_time();
			if ( $last_send && $last_send > strtotime( '-1 hours' ) ) {
				return;
			}
		}
		$api_route  = '';
		$api_route  = 'mp';
		$api_route .= 's';
		// Update time first before sending to ensure it is set.
		update_option( 'wps_rma_tracker_last_send', time() );
		$params  = WC_Tracker::get_tracking_data();
		$params  = apply_filters( 'wpswings_tracker_params', $params );
		$api_url = 'https://tracking.wpswings.com/wp-json/' . $api_route . '-route/v1/' . $api_route . '-testing-data/';
		$sucess  = wp_safe_remote_post(
			$api_url,
			array(
				'method' => 'POST',
				'body'   => wp_json_encode( $params ),
			)
		);
	}

	/**
	 * Get the updated time.
	 *
	 * @name wps_wrael_last_send_time
	 *
	 * @since 1.0.0
	 */
	public function wps_wrael_last_send_time() {
		return apply_filters( 'wpswings_tracker_last_send_time', get_option( 'wpswings_tracker_last_send', false ) );
	}

	/**
	 * Update the option for settings from the multistep form.
	 */
	public function wps_rma_standard_save_settings_filter() {
		check_ajax_referer( 'ajax-nonce', 'nonce' );
		unset( $_POST['action'] );
		unset( $_POST['nonce'] );
		$checked_refund          = isset( $_POST['checkedRefund'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedRefund'] ) ) : false;
		$checked_order_msg       = isset( $_POST['checkedOrderMsg'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedOrderMsg'] ) ) : false;
		$checked_order_msg_email = isset( $_POST['checkedOrderMsgEmail'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedOrderMsgEmail'] ) ) : false;
		$checked_exchange        = isset( $_POST['checkedExchange'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedExchange'] ) ) : false;
		$checked_cancel          = isset( $_POST['checkedCancel'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedCancel'] ) ) : false;
		$checked_cancel_prod     = isset( $_POST['checkedCancelProd'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedCancelProd'] ) ) : false;
		$checked_wallet          = isset( $_POST['checkedWallet'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedWallet'] ) ) : false;
		$checked_cod             = isset( $_POST['checkedCOD'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedCOD'] ) ) : false;
		$checked_conset          = isset( $_POST['consetCheck'] ) ? sanitize_text_field( wp_unslash( $_POST['consetCheck'] ) ) : false;
		$checked_reset_license   = isset( $_POST['checkedResetLicense'] ) ? sanitize_text_field( wp_unslash( $_POST['checkedResetLicense'] ) ) : false;
		$license_code            = isset( $_POST['licenseCode'] ) ? sanitize_text_field( wp_unslash( $_POST['licenseCode'] ) ) : '';
		if ( $checked_refund ) {
			update_option( 'wps_rma_refund_enable', 'on' );
		}
		if ( $checked_order_msg ) {
			update_option( 'wps_rma_general_om', 'on' );
		}
		if ( $checked_order_msg_email ) {
			update_option( 'wps_rma_order_email', 'on' );
		}
		if ( $checked_exchange ) {
			update_option( 'wps_rma_exchange_enable', 'on' );
		}
		if ( $checked_cancel ) {
			update_option( 'wps_rma_cancel_enable', 'on' );
		}
		if ( $checked_cancel_prod ) {
			update_option( 'wps_rma_cancel_product', 'on' );
		}
		if ( $checked_wallet ) {
			update_option( 'wps_rma_wallet_enable', 'on' );
		}
		if ( $checked_cod ) {
			update_option( 'wps_rma_hide_rec', 'on' );
		}
		if ( $checked_conset ) {
			update_option( 'wrael_enable_tracking', 'on' );
		}
		if ( $checked_reset_license ) {
			update_option( 'mwr_radio_reset_license', 'on' );
		}
		if ( ! empty( $license_code ) && function_exists( 'wps_rma_license_activate' ) ) {
			wps_rma_license_activate( $license_code );
		}
		update_option( 'wps_rma_plugin_standard_multistep_done', 'yes' );
		wp_send_json( 'yes' );
	}

	/**
	 * Used to Remove the 0 amount refund
	 *
	 * @param array  $results .
	 * @param object $args .
	 * @return results
	 */
	public function wps_rma_woocommerce_get_order_item_totals( $results, $args ) {

		if ( is_account_page() || ( function_exists( 'get_current_screen' ) && isset( get_current_screen()->id ) && 'shop_order' === get_current_screen()->id ) ) {
			$wps_refund = get_post_meta( $args['parent'], 'wps_rma_refund_info', true );

			foreach ( $results as $key => $value ) {
				if ( is_object( $value ) && 'shop_order_refund' === $value->get_type() ) {
					$refund_amount = floatval( $value->get_amount() );
					if ( empty( $refund_amount ) ) {
						if ( is_array( $wps_refund ) && ! empty( $wps_refund ) && in_array( $value->get_id(), $wps_refund, true ) ) {

							unset( $results[ $key ] );
						}
					}
				}
			}
		}
		return $results;
	}
}
