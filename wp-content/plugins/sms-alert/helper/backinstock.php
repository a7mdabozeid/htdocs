<?php
/**
 * Backinstock helper.
 *
 * @package Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}
/**SA_Abandoned_Cart class.*/
class Sa_Backinstock {
	/**Construct function.*/
	public function __construct() {

		$user_authorize = new smsalert_Setting_Options();
		$islogged       = $user_authorize->is_user_authorised();
		if ( ! $islogged ) {
			return false;}

		add_filter( 'sAlertDefaultSettings', __CLASS__ . '::add_default_setting', 1 );
		add_action( 'woocommerce_product_set_stock', array( $this, 'trigger_on_product_stock_changed' ), 10, 1 );
		add_action( 'woocommerce_variation_set_stock_status', array( $this, 'trigger_on_variation_stock_changed' ), 999, 3 );
		$smsalert_bis_subscribed_notify = smsalert_get_option( 'subscribed_bis_notify', 'smsalert_bis_general', 'on' );

		if ( 'on' === $smsalert_bis_subscribed_notify ) {
			add_action( 'woocommerce_simple_add_to_cart', array( $this, 'display_in_simple_product' ), 63 );
			add_action( 'woocommerce_after_variations_form', array( $this, 'sa_display_in_no_variation_product' ) );
			add_filter( 'woocommerce_available_variation', array( $this, 'sa_display_in_variation' ), 100, 3 );
			$this->handle_subcribe_request( $_REQUEST );
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'sa_addTabs', array( $this, 'add_tabs' ), 100 );
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_on_page' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'smsalert_populate_subscriber' ), 10, 2 );
		add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'show_subscriber_in_single_product' ) );
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'show_subscriber_in_variation_product' ), 10, 3 );
		
		add_action('woocommerce_update_product', array($this, 'trigger_on_product_stock_status_changed'), 10, 1); //add on  11-10-21
	}

	/**
	 * Show subscriber in variation product.
	 *
	 * @param array $loop loop.
	 * @param array $variation_data variation_data.
	 * @param array $variation variation.
	 *
	 * @return void
	 */
	public function show_subscriber_in_variation_product( $loop, $variation_data, $variation ) {
		global $allowedposttags;

		$allowedposttags['input'] = array(
			'type'     => array(),
			'name'     => array(),
			'value'    => array(),
			'class'    => array(),
			'id'       => array(),
			'disabled' => array(),
		);

		echo wp_kses(
			'<p class="form-field form-row form-row-full">
		<label for="smsalert_backinstock_subscribers">' . __( 'Backorders', 'sms-alert' ) . '</label>
		<input type="text" name="smsalert_backinstock_subscribers" value="' . All_Subscriber_List::get_nos_subscribers_by_productId( $variation->ID ) . '" class="input-text short smsalert_backinstock_subscribers" disabled style="border:none;box-shadow:none"/>
	</p>',
			$allowedposttags
		);
	}

	/**
	 * Show subscriber in single product.
	 *
	 * @return void
	 */
	public function show_subscriber_in_single_product() {
		global $post, $allowedposttags,$product_object;
		//$product_data = wc_get_product( $post->ID );
		if ( 'variable' !== $product_object->get_type() ) {

			$allowedposttags['input'] = array(
				'type'     => array(),
				'name'     => array(),
				'value'    => array(),
				'class'    => array(),
				'id'       => array(),
				'disabled' => array(),
			);

			echo wp_kses(
				'<p class="form-field">
            <label for="smsalert_backinstock_subscribers">' . esc_html__( 'Backorders', 'sms-alert' ) . '</label>
			<input type="text" name="smsalert_backinstock_subscribers" value="' . All_Subscriber_List::get_nos_subscribers_by_productId( $post->ID ) . '" class="input-text short smsalert_backinstock_subscribers" disabled style="border:none;box-shadow:none"/>
        </p>',
				$allowedposttags
			);
		}
	}

	/**
	 * Smsalert populate subscriber.
	 *
	 * @param string $column_name column_name.
	 * @param int    $product_id product_id.
	 *
	 * @return void
	 */
	public function smsalert_populate_subscriber( $column_name, $product_id ) {

		if ( 'is_in_stock' === $column_name ) {
			$backorders = All_Subscriber_List::get_nos_subscribers_for_productlist( $product_id );
			if ( $backorders > 0 ) {
				echo ' <strong>Backorders</strong> (' . esc_attr( $backorders ) . ') <br><br>';
			}
		}
	}

	/**
	 * Enqueue script on page.
	 *
	 * @return void
	 */
	public function enqueue_script_on_page() {
		if ( ! is_product() ) {
			return;
		}

		wp_register_script( 'sa_single_product', SA_MOV_URL . 'js/wc-product.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, true );

		wp_localize_script(
			'sa_single_product',
			'sa_otp_settings',
			array(
				'show_countrycode' => smsalert_get_option( 'checkout_show_country_code', 'smsalert_general', 'off' ),
			),
			'sa_default_countrycode',
			smsalert_get_option( 'default_country_code', 'smsalert_general' )
		);

		wp_localize_script(
			'sa_single_product',
			'sa_notices',
			array(
				'waiting_txt' => __( 'Please wait...', 'sms-alert' ),
				'enter_here'  => __( 'Enter Number Here', 'sms-alert' ),
			)
		);
		wp_enqueue_script( 'sa_single_product' );
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
			'checkTemplateFor' => 'backinstock',
			'templates'        => self::get_back_in_stock_templates(),
		);

		$tabs['woocommerce']['inner_nav']['backinstock']['title']       = __( 'Notify Me', 'sms-alert' );
		$tabs['woocommerce']['inner_nav']['backinstock']['tab_section'] = 'backinstocktemplates';
		$tabs['woocommerce']['inner_nav']['backinstock']['tabContent']  = $backinstock_param;
		$tabs['woocommerce']['inner_nav']['backinstock']['filePath']    = 'views/message-template.php';
		$tabs['woocommerce']['inner_nav']['backinstock']['icon']        = 'dashicons-products';		
		return $tabs;
	}

	/**
	 * Get back in stock templates.
	 *
	 * @return array
	 */
	public static function get_back_in_stock_templates() {
		$variables = array(
			'[item_name]'       => 'Product Name',
			'[name]'            => 'Name',
			'[subscribed_date]' => 'Date',
			'[product_url]'     => 'Product Url',
			'[store_name]'      => 'Store Name',
			'[shop_url]'        => 'Shop Url',
		);

		// product back in stock.
		$current_val      = smsalert_get_option( 'customer_bis_notify', 'smsalert_bis_general', 'on' );
		$checkbox_name_id = 'smsalert_bis_general[customer_bis_notify]';
		$textarea_name_id = 'smsalert_bis_message[customer_bis_notify]';
		$text_body        = smsalert_get_option(
			'customer_bis_notify',
			'smsalert_bis_message',
			SmsAlertMessages::showMessage( 'DEFAULT_BACK_IN_STOCK_CUST_MSG' )
		);

		$templates = array();

		$templates['backinstock_msg']['title']          = 'Send message to customer when product is back in stock';
		$templates['backinstock_msg']['enabled']        = $current_val;
		$templates['backinstock_msg']['status']         = 'backinstock_msg';
		$templates['backinstock_msg']['text-body']      = $text_body;
		$templates['backinstock_msg']['checkboxNameId'] = $checkbox_name_id;
		$templates['backinstock_msg']['textareaNameId'] = $textarea_name_id;
		$templates['backinstock_msg']['token']          = $variables;

		// product subscribed.
		$current_val      = smsalert_get_option( 'subscribed_bis_notify', 'smsalert_bis_general', 'on' );
		$checkbox_name_id = 'smsalert_bis_general[subscribed_bis_notify]';
		$textarea_name_id = 'smsalert_bis_message[subscribed_bis_notify]';
		$text_body        = smsalert_get_option(
			'subscribed_bis_notify',
			'smsalert_bis_message',
			SmsAlertMessages::showMessage( 'DEFAULT_BACK_IN_STOCK_SUBSCRIBE_MSG' )
		);

		$templates['subscribed']['title']          = 'Send message to customer when product is subscribed';
		$templates['subscribed']['enabled']        = $current_val;
		$templates['subscribed']['status']         = 'subscribed';
		$templates['subscribed']['text-body']      = $text_body;
		$templates['subscribed']['checkboxNameId'] = $checkbox_name_id;
		$templates['subscribed']['textareaNameId'] = $textarea_name_id;
		$templates['subscribed']['token']          = $variables;
		
		$templates['subscribed']['help_links']  = array(
			'youtube_link' => array(
				'href'   => 'https://www.youtube.com/watch?v=UnCUGSan7zM&t=80s',
				'target' => '_blank',
				'alt'    => 'Watch steps on Youtube',
				'class'  => 'btn-outline',
				'label'  => 'Youtube',
				'icon'   => '<span class="dashicons dashicons-video-alt3" style="font-size: 21px;"></span> ',

			),
			'kb_link'      => array(
				'href'   => 'https://kb.smsalert.co.in/knowledgebase/notify-me/',
				'target' => '_blank',
				'alt'    => 'Woocommerce - Back in Stock Notifier via SMS',
				'class'  => 'btn-outline',
				'label'  => 'Documentation',
				'icon'   => '<span class="dashicons dashicons-format-aside"></span>',
			),

		);

		return $templates;
	}

	/**
	 * Trigger on product stock changed(update product quantity).
	 *
	 * @param object $product product.
	 *
	 * @return void
	 */
	public function trigger_on_product_stock_changed( $product ) {
		$product_id     = $product->get_id();
		$product_status = $product->get_stock_status();
		$this->process_sms_for_in_stock_subscribers( $product_id, $product_status );
	}
	
	/**
	 * Trigger on product stock status changed.
	 *
	 * @param object $product_id product id.
	 *
	 * @return void
	 */
	
	public function trigger_on_product_stock_status_changed($product_id ){
		
		$product = wc_get_product( $product_id );
		$product_status = $product->get_stock_status();
		$this->process_sms_for_in_stock_subscribers( $product_id, $product_status );
	}

	/**
	 * Trigger on variation stock changed.
	 *
	 * @param string $variation_id variation_id.
	 * @param string $variation_status variation_status.
	 * @param object $obj obj.
	 *
	 * @return void
	 */
	public function trigger_on_variation_stock_changed( $variation_id, $variation_status, $obj ) {
		$this->process_sms_for_in_stock_subscribers( $variation_id, $variation_status );
	}

	/**
	 * Process sms for in stock subscribers.
	 *
	 * @param int    $product_id product_id.
	 * @param string $product_status product_status.
	 *
	 * @return void
	 */
	public function process_sms_for_in_stock_subscribers( $product_id, $product_status ) {
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$datas        = $wpdb->get_results( "SELECT * FROM {$table_prefix}postmeta WHERE meta_key = 'smsalert_instock_pid' and meta_value = '$product_id'", ARRAY_A );
		$obj          = array();
		$posts        = array();
		foreach ( $datas as $dkey => $data ) {

			$post_id   = $data['post_id'];
			$post_data = $wpdb->get_results( "SELECT ID , post_title, post_author FROM {$table_prefix}posts WHERE post_status = 'smsalert_subscribed' and ID = '$post_id'", ARRAY_A );

			$post_user_id             = $post_data[0]['post_author'];
			$smsalert_bis_cust_notify = smsalert_get_option( 'customer_bis_notify', 'smsalert_bis_general', 'on' );
			if ( ! empty( $post_data ) && 'instock' === $product_status && 'on' === $smsalert_bis_cust_notify ) {
				$posts[ $dkey ]['post_id'] = $post_data[0]['ID'];
				$backinstock_message       = smsalert_get_option( 'customer_bis_notify', 'smsalert_bis_message', '' );
				$obj[ $dkey ]['number']    = $post_data[0]['post_title'];
				$obj[ $dkey ]['sms_body']  = $this->parse_body( $post_user_id, $product_id, $backinstock_message );
			}
		}

		$response     = SmsAlertcURLOTP::send_sms_xml( $obj );
		$response_arr = json_decode( $response, true );
		if ( 'success' === $response_arr['status'] ) {
			$desc   = ( ! empty( $response_arr['description']['desc'] ) ) ? $response_arr['description']['desc'] : '';
			$status = 'smsalert_msgsent';
		} else {
			$desc   = ( ! empty( $response_arr['description']['desc'] ) ) ? $response_arr['description']['desc'] : ( ( ! empty( $response_arr['description'] ) ) ? $response_arr['description'] : '' );
			$status = 'sa_general_error';
		}

		foreach ( $posts as $post ) {
			$msg_status = $this->msg_sent_status( $post['post_id'], $status, $desc );
		}
	}

	/** Handle subcribe request.
	 *
	 * @param array $data data.
	 *
	 * @return void
	 */
	public function handle_subcribe_request( $data ) {
		if ( ! empty( $data ) ) {
			if ( isset( $data['action'] ) && 'smsalertbackinstock' === $data['action'] ) {
				echo $this->perform_action_on_ajax_data( $data );
				exit();
			}
		}
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public static function add_default_setting( $defaults = array() ) {
		$defaults['smsalert_bis_general']['customer_bis_notify']   = 'off';
		$defaults['smsalert_bis_message']['customer_bis_notify']   = '';
		$defaults['smsalert_bis_general']['subscribed_bis_notify'] = 'off';
		$defaults['smsalert_bis_message']['subscribed_bis_notify'] = '';
		return $defaults;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @return void
	 */
	public function display_in_simple_product() {
		global $product;
		echo $this->display_sa_subscribe_box( $product );
	}

	/**
	 * Display in no variation product.
	 *
	 * @return void
	 */
	public function sa_display_in_no_variation_product() {
		global $product;
		$product_type = $product->get_type();
		// Get Available variations?
		if ( 'variable' === $product_type ) {

			$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
			$get_variations = $get_variations ? $product->get_available_variations() : false;

			if ( ! $get_variations ) {
				echo $this->display_sa_subscribe_box( $product );
			}
		}
	}

	/**
	 * Display sa subsribe box.
	 *
	 * @param object $product product.
	 * @param array  $variation variation.
	 *
	 * @return array
	 */
	public function display_sa_subscribe_box( $product, $variation = array() ) {
		SmsAlertUtility::enqueue_script_for_intellinput();

		$get_option              = get_option( 'smsalert_instocksettings' );
		$check_guest_visibility  = isset( $get_option['hide_form_guests'] ) && '' !== $get_option['hide_form_guests'] && ! is_user_logged_in() ? false : true;
		$check_member_visibility = isset( $get_option['hide_form_members'] ) && '' !== $get_option['hide_form_members'] && is_user_logged_in() ? false : true;
		$product_id              = $product->get_id();

		if ( $variation ) {
			$variation_id = $variation->get_id();
		} else {
			$variation_id = 0;
		}

		$product_status = $product->get_stock_status();
		if ( $check_guest_visibility && $check_member_visibility && 'instock' !== $product_status && ! $variation_id ) {
			$params = array(
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
			);
			return get_smsalert_template( 'template/backinstock-template.php', $params, true );
		} elseif ( $variation && ! $variation->is_in_stock() || ( ( $variation && ( ( $variation->managing_stock() && $variation->backorders_allowed() && $variation->is_on_backorder( 1 ) ) || $variation->is_on_backorder( 1 ) ) && $visibility_backorder ) ) ) {
			$params = array(
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
			);
			return get_smsalert_template( 'template/backinstock-template.php', $params, true );
		} else {
			return '';
		}
	}

	/**
	 * SA display in variation.
	 *
	 * @param array  $atts atts.
	 * @param object $product product.
	 * @param array  $variation variation.
	 *
	 * @return array
	 */
	public function sa_display_in_variation( $atts, $product, $variation ) {
		$get_stock                 = $atts['availability_html'];
		$atts['availability_html'] = $get_stock . $this->display_sa_subscribe_box( $product, $variation );
		return $atts;
	}

	/**
	 * Perform action on ajax data.
	 *
	 * @param array $post_data post_data.
	 *
	 * @return array
	 */
	public function perform_action_on_ajax_data( $post_data ) {
		global $phoneLogic;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = 0;
		}

		$user_phone   = isset( $post_data['user_phone'] ) ? $post_data['user_phone'] : '';
		$product_id   = isset( $post_data['product_id'] ) ? $post_data['product_id'] : '';
		$variation_id = isset( $post_data['variation_id'] ) ? $post_data['variation_id'] : '';

		$subscriber_phone = SmsAlertcURLOTP::checkPhoneNos( $user_phone );
		if ( ! $subscriber_phone ) {
			$data['status']      = 'error';
			$data['description'] = str_replace( '##phone##', $user_phone, $phoneLogic->_get_otp_invalid_format_message() );
		} else {
			$get_user_id  = $user_id;
			$parent_id    = $product_id       = $product_id;
			$variation_id = $variation_id;

			$check_is_already_subscribed = $this->is_already_subscribed( $product_id, $variation_id, $subscriber_phone, $get_user_id );

			$data = array();

			if ( ! empty( $check_is_already_subscribed ) ) {
				$data['status']      = 'error';
				$data['description'] = __( 'Seems like you have already subscribed to this product', 'sms-alert' );
			} else {
				if ( '' !== $subscriber_phone ) {

					$post_id      = $this->insert_subscriber( $subscriber_phone, $get_user_id );
					$product_id   = ( $variation_id > '0' || $variation_id > 0 ) ? $variation_id : $product_id;
					$default_data = array(
						'smsalert_instock_variation_id' => $variation_id,
						'smsalert_subscriber_phone'     => $subscriber_phone,
						'smsalert_instock_user_id'      => $get_user_id,
						'smsalert_instock_pid'          => $product_id,
					);
					foreach ( $default_data as $key => $value ) {
						update_post_meta( $post_id, $key, $value );
					}

					$subscribed_bis_notify = smsalert_get_option( 'subscribed_bis_notify', 'smsalert_bis_general', '' );
					$subscribed_message       = smsalert_get_option( 'subscribed_bis_notify', 'smsalert_bis_message', '' );

					if ( 'on' === $subscribed_bis_notify && '' !== $subscribed_message ) {
						$buyer_sms_data['number']   = $subscriber_phone;
						$buyer_sms_data['sms_body'] = $this->parse_body( $get_user_id, $product_id, $subscribed_message, $parent_id );
						SmsAlertcURLOTP::sendsms( $buyer_sms_data );
					}
					$data['status']      = 'success';
					$data['description'] = __( 'You have subscribed successfully.', 'sms-alert' );
				}
			}
		}
		return wp_json_encode( $data );
	}

	/**
	 * Insert subscriber function.
	 *
	 * @param string $mobileno mobileno.
	 * @param string $user_id user_id.
	 *
	 * @return string
	 */
	public function insert_subscriber( $mobileno, $user_id ) {
		$args = array(
			'post_title'  => $mobileno,
			'post_type'   => 'sainstocknotifier',
			'post_status' => 'smsalert_subscribed',
			'post_author' => $user_id,
		);
		global $wp_rewrite;
		$wp_rewrite = new wp_rewrite();
		$id         = wp_insert_post( $args );
		if ( ! is_wp_error( $id ) ) {
			return $id;
		} else {
			return false;
		}
	}

	/**
	 * Parse body function.
	 *
	 * @param string $post_user_id post_user_id.
	 * @param string $product_id product_id.
	 * @param string $message message.
	 * @param string $parent_id parent_id.
	 *
	 * @return string
	 */
	public function parse_body( $post_user_id, $product_id, $message, $parent_id = null ) {
		$item_name = get_post_field( 'post_title', $product_id );
		$user_data = get_userdata( $post_user_id, ARRAY_A );

		$find = array(
			'[item_name]',
			'[name]',
			'[product_url]',
		);

		$replace = array(
			html_entity_decode( $item_name ),
			( isset( $user_data->user_login ) ? $user_data->user_login : '' ),
			( ( ! empty( $parent_id ) ) ? get_permalink( $parent_id ) : get_permalink( $product_id ) ),
		);

		$message = str_replace( $find, $replace, $message );
		return $message;
	}

	/**
	 * Is already subscribed function.
	 *
	 * @param int    $product_id product_id.
	 * @param int    $variation_id variation_id.
	 * @param string $subscriber_phone subscriber_phone.
	 * @param string $get_user_id get_user_id.
	 *
	 * @return array
	 */
	public function is_already_subscribed( $product_id, $variation_id, $subscriber_phone, $get_user_id ) {

		global $wpdb;

		$wcc_ph     = SmsAlertcURLOTP::checkPhoneNos( $subscriber_phone );
		$wocc_ph    = SmsAlertcURLOTP::checkPhoneNos( $subscriber_phone, false );
		$wth_pls_ph = '+' . $wcc_ph;

		$table_prefix = $wpdb->prefix;
		$product_id   = ( $variation_id > '0' || $variation_id > 0 ) ? $variation_id : $product_id;
		$datas        = $wpdb->get_results( "SELECT * FROM {$table_prefix}postmeta pm1 inner join {$table_prefix}postmeta pm2 on pm1.post_id= pm2.post_id WHERE pm1.meta_key = 'smsalert_instock_pid' and pm1.meta_value = '$product_id' and pm2.meta_key ='smsalert_subscriber_phone' and pm2.meta_value in('$wcc_ph','$wocc_ph','$wth_pls_ph')", ARRAY_A );

		$post_ids  = array_map(
			function( $item ) {
				return $item['post_id'];
			},
			$datas
		);
		$post_data = array();

		if ( ! empty( $post_ids ) ) {
			$post_data = $wpdb->get_results( "SELECT ID,post_title, post_status FROM {$table_prefix}posts WHERE post_status = 'smsalert_subscribed' and ID in (" . implode( ',', $post_ids ) . ')', ARRAY_A );
		}
		return $post_data;
	}

	/**
	 * Msg sent status function.
	 *
	 * @param int    $subscribe_id subscribe_id.
	 * @param string $status status.
	 * @param string $desc desc.
	 *
	 * @return array
	 */
	public function msg_sent_status( $subscribe_id, $status, $desc ) {
		$args = array(
			'ID'           => $subscribe_id,
			'post_type'    => 'sainstocknotifier',
			'post_status'  => $status,
			'post_content' => $desc,
		);
		$id   = wp_update_post( $args );
		return $id;
	}
}
new Sa_Backinstock();
?>
<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**All_Subscriber_List extends WP_List_Table class.*/
class All_Subscriber_List extends WP_List_Table {
	/**Construct function.*/
	function __construct() {
		parent::__construct(
			array(
				'singular' => 'backinstock',
				'plural'   => 'backinstocks',
			)
		);
	}

	/**
	 * Get all subscriber info.
	 *
	 * @param int $per_page   Page size.
	 * @param int $page_number Page number.
	 *
	 * @return array
	 */
	public static function get_all_subscriber( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT P.ID, P.post_author, P.post_title, P.post_status,P.post_content, PM.meta_value FROM {$wpdb->prefix}posts P inner join {$wpdb->prefix}postmeta PM on P.ID = PM.post_id WHERE P.post_type = 'sainstocknotifier' and PM.meta_key = 'smsalert_instock_pid'";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : ' DESC';
		} else {
			$sql .= ' ORDER BY post_date desc';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Get nos subscribers by productId.
	 *
	 * @param int $product_id product_id.
	 *
	 * @return int
	 */
	public static function get_nos_subscribers_by_productId( $product_id = null ) {
		global $wpdb;
		$sql    = "SELECT count(*) as cnt FROM {$wpdb->prefix}posts P inner join {$wpdb->prefix}postmeta PM on P.ID = PM.post_id WHERE P.post_type = 'sainstocknotifier' and PM.meta_key = 'smsalert_instock_pid' and P.post_status = 'smsalert_subscribed'";
		$sql   .= "and PM.meta_value='" . $product_id . "'";
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return ( ! empty( $result ) ) ? $result[0]['cnt'] : 0;
	}

	/**
	 * Get nos subscribers for productlist.
	 *
	 * @param int $product_id product_id.
	 *
	 * @return int
	 */
	public static function get_nos_subscribers_for_productlist( $product_id = null ) {
		global $wpdb;
		global $product;
		$sql          = "SELECT count(*) as cnt FROM {$wpdb->prefix}posts P inner join {$wpdb->prefix}postmeta PM on P.ID = PM.post_id WHERE P.post_type = 'sainstocknotifier' and PM.meta_key = 'smsalert_instock_pid' and P.post_status = 'smsalert_subscribed'";
		$product_type = $product->get_type();
		if ( 'variable' === $product_type ) {
			$product_ids = count( $product->get_children() ) > 0 ? $product->get_children() : array();
			$sql        .= 'and PM.meta_value in (' . implode( ',', $product_ids ) . ')';
		} else {
			$sql .= "and PM.meta_value='" . $product_id . "'";
		}
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return ( ! empty( $result ) ) ? $result[0]['cnt'] : 0;
	}

	/**
	 * Text displayed when no data is available.
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No Subscriber.', 'sms-alert' );
	}

	/**
	 * [REQUIRED] this is a default column renderer.
	 *
	 * @param array  $item - row (key, value array).
	 * @param string $column_name - string (key).
	 *
	 * @return HTML
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Checkbox shown in every row especially for bulk action.
	 *
	 * @param array $item Item array.
	 *
	 * @return HTML
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="ID[]" value="%s" />',
			$item['ID']
		);
	}

	/**
	 * Shows post status in every row especially for bulk action.
	 *
	 * @param array $item Item array.
	 *
	 * @return HTML
	 */
	public function column_post_status( $item ) {
		if ( 'smsalert_subscribed' === $item['post_status'] ) {
			$post_status = '<button class="button-primary"/>Subscribed</a>';
		} elseif ( 'sa_general_error' === $item['post_status'] ) {
			$post_status = '<button class="button-primary" style="background: red;border: 1px solid red;" title="' . $item['post_content'] . '">General Error</a>';
		} else {
			$post_status = '<button class="button-primary" style="background: green;border: 1px solid green;">Message Sent</a>';
		}
		return $post_status;
	}

	/**
	 * Shows post author in every row especially for bulk action.
	 *
	 * @param array $item Item array.
	 *
	 * @return HTML
	 */
	public function column_post_author( $item ) {
		if ( '0' === $item['post_author'] ) {
			$register_or_not = '<button class="button-primary" style="background: red;border: 1px solid red;">Guest</a>';
		} else {
			$register_or_not = '<button class="button-primary" style="background: green;border: 1px solid green;">Yes</a>';
		}
		return $register_or_not;
	}

	/**
	 * Shows column meta value in every row especially for bulk action.
	 *
	 * @param array $item Item array.
	 *
	 * @return HTML
	 */
	public function column_meta_value( $item ) {
		$product_name = '<a href="' . get_permalink( $item['meta_value'] ) . '" target="_blank">' . get_the_title( $item['meta_value'] ) . '</a>';
		return $product_name;
	}

	/**
	 * Get columns shown in table.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'post_title'  => __( 'Mobile Number' ),
			'post_status' => __( 'Status' ),
			'meta_value'  => __( 'Product' ),
			'post_author' => __( 'Registered User' ),
		);

		return $columns;
	}

	/**
	 * [OPTIONAL] This method return columns that may be used to sort table.
	 * All strings in array - is column names.
	 * Notice that true on name column means that its default sort.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_author' => array( 'post_author', true ),
			'post_title'  => array( 'post_title', false ),
			'post_status' => array( 'post_status', false ),
			'meta_value'  => array( 'meta_value', false ),
		);
		return $sortable_columns;
	}

	/**
	 * [OPTIONAL] Return array of bult actions if has any.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
		);
		return $actions;
	}

	/**
	 * [OPTIONAL] This method processes bulk actions.
	 * It can be outside of class.
	 * It can not use wp_redirect coz there is output already.
	 * In this example we are processing delete action.
	 * Message about successful deletion will be shown on page in next part.
	 *
	 * @return void
	 */
	public function process_bulk_action() {
		global $wpdb;
		// $table_name = $wpdb->prefix; // do not forget about tables prefix

		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_REQUEST['ID'] ) ? smsalert_sanitize_array( $_REQUEST['ID'] ) : array();
			if ( is_array( $ids ) ) {
				$ids = implode( ',', $ids );
			}

			if ( ! empty( $ids ) ) {
				$wpdb->query( "DELETE P, PM FROM {$wpdb->prefix}posts P inner join {$wpdb->prefix}postmeta PM on P.ID = PM.post_id WHERE ID IN($ids)" );
			}
		}
	}

	/**
	 * Get total records of the table.
	 *
	 * @return int
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts where post_type = 'sainstocknotifier'";

		return $wpdb->get_var( $sql );
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @return array
	 */
	public function prepare_items() {

		$per_page     = 10;
		$current_page = ( isset( $_REQUEST['paged'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['paged'] ) ) : 1 );
		$columns      = $this->get_columns();
		$this->items  = self::get_all_subscriber( $per_page, $current_page );

		$this->process_bulk_action();

		$hidden      = array();
		$sortable    = $this->get_sortable_columns();
		$total_items = self::record_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		$this->_column_headers = array( $columns, $hidden, $sortable );
		return $this->items;
	}
}

/**
 * Adds a sub menu page for all subscribers.
 *
 * @return void
 */
function all_subscriber_admin_menu() {
	add_submenu_page( null, 'All Subscriber', 'All Subscriber', 'manage_options', 'all-subscriber', 'subscriber_page_handler' );
}

add_action( 'admin_menu', 'all_subscriber_admin_menu' );

/**
 * List page handler.
 *
 * This function renders our custom table.
 * Notice how we display message about successfull deletion.
 * Actualy this is very easy, and you can add as many features as you want.
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples.
 *
 * @return void
 */
function subscriber_page_handler() {
	global $wpdb;

	$table_data = new All_Subscriber_List();
	$data       = $table_data->prepare_items();
	$message    = '';
	$cnt        = empty( $_REQUEST['ID'] ) ? 0 : count( $_REQUEST['ID'] );

	if ( 'delete' === $table_data->current_action() ) {
		/* translators: %d: Number of items deleted */
		$message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d', 'custom_table_example' ), $cnt ) . '</p></div>';
	}
	?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2>All Subscriber</h2>
	<?php echo wp_kses_post( $message ); ?>
	<form id="persons-table" method="GET">
		<input type="hidden" name="page" value="<?php echo esc_attr( empty( $_REQUEST['page'] ) ? '' : $_REQUEST['page'] ); ?>"/>
		<?php $table_data->display(); ?>
	</form>
</div>
<?php } ?>
