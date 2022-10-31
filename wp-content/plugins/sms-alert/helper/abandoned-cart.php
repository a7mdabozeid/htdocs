<?php
/**
 * Abandoned cart helper.
 *
 * @category Abandoned cart
 * @author   cozy vision technologies pvt ltd <support@cozyvision.com>
 * @package  Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
} else {
	$user_authorize = new smsalert_Setting_Options();
	$islogged       = $user_authorize->is_user_authorised();
	if($islogged)
	{
		$sa_abcart = new SA_Abandoned_Cart();
		$sa_abcart->run();
	}
}
/**
 * SA_Abandoned_Cart class
 */
class SA_Abandoned_Cart {

	/**
	 * Loader.
	 *
	 * @var stirng
	 */
	protected $loader;

	/**
	 * Plugin Name.
	 *
	 * @var stirng
	 */
	protected $plugin_name;

	/**
	 * Version.
	 *
	 * @var stirng
	 */
	protected $version;

	/**
	 * Construct function.
	 */
	public function __construct() {

		$this->plugin_name = SMSALERT_PLUGIN_NAME_SLUG;
		$this->version     = 'sms-alert';

		$this->load_dependencies();
		if ( 'on' === smsalert_get_option( 'customer_notify', 'smsalert_abandoned_cart' ) ) {
			$this->define_admin_hooks();
			$this->define_public_hooks();
		}
		add_action( 'sa_addTabs', array( $this, 'addTabs' ), 10 );
		add_action( 'sa_tabContent', array( $this, 'tabContent' ), 1 );
		add_filter( 'sAlertDefaultSettings', array( $this, 'addDefaultSetting' ), 1 );
		add_action( 'woocommerce_review_order_after_submit', array( $this, 'addNonceField' ), 1, 1 );
	}

	/**
	 * Load dependencies function.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		$this->loader = new SA_Loader();
	}
	
	/**
	 * Add nonce field on checkout page.
	 */
	public function addNonceField() { 
	   echo wp_nonce_field('smsalert_wp_abcart_nonce','smsalert_abcart_nonce', true, false);
	}

	/**
	 * Define admin hooks function.
	 *
	 * @return void
	 */
	private function define_admin_hooks() {
		$plugin_admin = new SA_Cart_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_wp_cron_warnings' ); // Outputing warnings if any of the WP Cron events are note scheduled or if WP Cron is disabled
		$this->loader->add_action( 'ab_cart_notification_sendsms_hook', $plugin_admin, 'send_sms' ); // Hooks into WordPress cron event to launch function for sending out SMS

		$this->loader->add_action( 'woocommerce_new_order', $plugin_admin, 'clear_cart_data', 30 ); // Hook fired once a new order is created via Checkout process. Order is created as soon as user is taken to payment page. No matter if he pays or not
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_admin, 'clear_cart_data', 30 ); // Hooks into Thank you page to delete a row with a user who completes the checkout (Backup version if first hook does not get triggered after an WooCommerce order gets created)
	}

	/**
	 * Define public hooks function.
	 *
	 * @return void
	 */
	private function define_public_hooks() {
		$plugin_admin  = new SA_Cart_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_public = new SA_Cart_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_before_checkout_form', $plugin_public, 'add_additional_scripts_on_checkout' ); // Adds additional functionality only to Checkout page
		$this->loader->add_action( 'wp_ajax_nopriv_save_data', $plugin_public, 'save_user_data' ); // Handles data saving using Ajax after any changes made by the user on the Phone field in Checkout form
		$this->loader->add_action( 'wp_ajax_save_data', $plugin_public, 'save_user_data' ); // Handles data saving using Ajax after any changes made by the user on the Mobile field for Logged in users
		$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'save_logged_in_user_data', 200 ); // Handles data saving if an item is added to shopping cart, 200 = priority set to run the function last after all other functions are finished
		$this->loader->add_action( 'woocommerce_cart_actions', $plugin_public, 'save_logged_in_user_data', 200 ); // Handles data updating if a cart is updated. 200 = priority set to run the function last after all other functions are finished
		$this->loader->add_action( 'woocommerce_cart_item_removed', $plugin_public, 'save_logged_in_user_data', 200 ); // Handles data updating if an item is removed from cart. 200 = priority set to run the function last after all other functions are finished
		$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'update_cart_data', 210 );
		$this->loader->add_action( 'woocommerce_cart_actions', $plugin_public, 'update_cart_data', 210 );
		$this->loader->add_action( 'woocommerce_cart_item_removed', $plugin_public, 'update_cart_data', 210 );

		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'restore_cart' ); // Restoring abandoned cart if a user returns back from an abandoned cart msg link
		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'restore_input_data', 1 ); // Restoring previous user input in Checkout form
		$this->loader->add_action( 'wp_footer', $plugin_public, 'display_exit_intent_form' ); // Outputing the exit intent form in the footer of the page
		$this->loader->add_action( 'wp_ajax_nopriv_insert_exit_intent', $plugin_public, 'display_exit_intent_form' ); // Outputing the exit intent form in case if Ajax Add to Cart button pressed if the user is not logged in
		$this->loader->add_action( 'wp_ajax_insert_exit_intent', $plugin_public, 'display_exit_intent_form' ); // Outputing the exit intent form in case if Ajax Add to Cart button pressed if the user is logged in
		$this->loader->add_action( 'wp_ajax_nopriv_remove_exit_intent', $plugin_public, 'remove_exit_intent_form' ); // Checking if we have an empty cart in case of Ajax action
		$this->loader->add_action( 'wp_ajax_remove_exit_intent', $plugin_public, 'remove_exit_intent_form' ); // Checking if we have an empty cart in case of Ajax action if the user is logged in
		$this->loader->add_action( 'woocommerce_before_checkout_form', $plugin_public, 'update_logged_customer_id', 10 ); // Fires when the Checkout form is loaded to update the abandoned cart session from unknown customer_id to known one in case if the user has logged in
	}

	/**
	 * Run function.
	 *
	 * @return void
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Get plugin name function.
	 *
	 * @return void
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get loader function.
	 *
	 * @return void
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Get version function.
	 *
	 * @return void
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Add default settings to savesetting in setting-options.
	 *
	 * @param array $defaults defaults.
	 *
	 * @return array
	 */
	public function addDefaultSetting( $defaults = array() ) {
		$defaults['smsalert_abandoned_cart']['notification_frequency']         = '10';
		$defaults['smsalert_abandoned_cart']['cart_exit_intent_status']        = '';
		$defaults['smsalert_abandoned_cart']['enable_exit_intent_custom_page'] = '';
		$defaults['smsalert_abandoned_cart']['enable_quiet_hours'] = '';
		$defaults['smsalert_abandoned_cart']['from_quiet_hours'] = '22:00';
		$defaults['smsalert_abandoned_cart']['to_quiet_hours'] = '08:00';
		$defaults['smsalert_abandoned_cart']['cart_exit_intent_custom_page']   = '';
		$defaults['smsalert_abandoned_cart']['cart_exit_intent_main_color']    = '#fff';
		$defaults['smsalert_abandoned_cart']['cart_exit_intent_inverse_color'] = '#000';
		$defaults['smsalert_abandoned_cart']['cart_exit_intent_image']         = '';
		$defaults['smsalert_abandoned_cart']['customer_notify']                = 'off';
		$defaults['smsalert_abandoned_cart_scheduler']['cron'][0]['frequency'] = '60';
		$defaults['smsalert_abandoned_cart_scheduler']['cron'][0]['message']   = '';
		$defaults['smsalert_abandoned_cart_scheduler']['cron'][1]['frequency'] = '120';
		$defaults['smsalert_abandoned_cart_scheduler']['cron'][1]['message']   = '';

		return $defaults;
	}

	/**
	 * Add tabs to smsalert settings at backend.
	 *
	 * @param array $tabs tabs.
	 *
	 * @return array
	 */
	public function addTabs( $tabs = array() ) {
		$smsalertcart_param = array(
			'checkTemplateFor' => 'Abandoned_Cart',
			'templates'        => $this->get_sms_alert_cart_templates(),
		);

		$tabs['woocommerce']['inner_nav']['abandoned_cart']['title']
		= 'Abandoned Cart';
		$tabs['woocommerce']['inner_nav']['abandoned_cart']['tab_section'] = 'smsalertcarttemplates';
		$tabs['woocommerce']['inner_nav']['abandoned_cart']['tabContent']  = $smsalertcart_param;
		$tabs['woocommerce']['inner_nav']['abandoned_cart']['filePath']
		= 'views/ab-cart-setting-template.php';
		$tabs['woocommerce']['inner_nav']['abandoned_cart']['params']
		= $smsalertcart_param;
		return $tabs;
	}

	/**
	 * Get sms alert cart templates.
	 *
	 * @return array
	 */
	public function get_sms_alert_cart_templates() {
		$current_val      = smsalert_get_option( 'customer_notify', 'smsalert_abandoned_cart', 'on' );
		$checkbox_name_id = 'smsalert_abandoned_cart[customer_notify]';

		$scheduler_data = get_option( 'smsalert_abandoned_cart_scheduler' );
		$templates      = array();
		$count          = 0;

		if ( empty( $scheduler_data ) ) {
			$scheduler_data['cron'][] = array(
				'frequency' => '60',
				'message'   => SmsAlertMessages::showMessage( 'DEFAULT_AB_CART_CUSTOMER_MESSAGE' ),
			);
			$scheduler_data['cron'][] = array(
				'frequency' => '120',
				'message'   => SmsAlertMessages::showMessage( 'DEFAULT_AB_CART_CUSTOMER_MESSAGE' ),
			);
		}

		foreach ( $scheduler_data['cron'] as $key => $data ) {
			$textarea_name_id = 'smsalert_abandoned_cart_scheduler[cron][' . $count . '][message]';
			$selectNameId     = 'smsalert_abandoned_cart_scheduler[cron][' . $count . '][frequency]';
			$text_body        = $data['message'];

			$templates[ $key ]['frequency']      = $data['frequency'];
			$templates[ $key ]['enabled']        = $current_val;
			$templates[ $key ]['title']          = 'Send message to customer when product is left in cart';
			$templates[ $key ]['checkboxNameId'] = $checkbox_name_id;
			$templates[ $key ]['text-body']      = $text_body;
			$templates[ $key ]['textareaNameId'] = $textarea_name_id;
			$templates[ $key ]['selectNameId']   = $selectNameId;
			$templates[ $key ]['token']          = $this->getAbandonCartvariables();

			$count++;
		}
		return $templates;
	}

	/**
	 * Get Abandoned Cart variables.
	 *
	 * @return array
	 */
	public static function getAbandonCartvariables() {
		$variables = array(
			'[name]'          => 'Name',
			'[surname]'       => 'Surname',
			'[email]'         => 'Email',
			'[phone]'         => 'Phone',
			'[location]'      => 'Location',
			'[cart_total]'    => 'Cart Total',
			'[currency]'      => 'Currency',
			'[time]'          => 'Time',
			'[item_name]'     => 'Item name',
			'[item_name_qty]' => 'Item with Qty',
			'[store_name]'    => 'Store Name',
			'[shop_url]'      => 'Shop Url',
			'[checkout_url]'  => 'Checkout Url',
		);
		return $variables;
	}
}
new SA_Abandoned_Cart();
?>
<?php
/**
 * SA_Loader class.
 */
class SA_Loader {

	/**
	 * Actions.
	 *
	 * @var stirng
	 */
	protected $actions;

	/**
	 * Filters.
	 *
	 * @var stirng
	 */
	protected $filters;

	/**
	 * Construct function.
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add action function.
	 *
	 * @param string $hook          hook.
	 * @param string $component     component.
	 * @param string $callback      callback.
	 * @param int    $priority      priority.
	 * @param int    $accepted_args accepted_args.
	 *
	 * @return void
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add filter function.
	 *
	 * @param string $hook          hook.
	 * @param string $component     component.
	 * @param string $callback      callback.
	 * @param int    $priority      priority.
	 * @param int    $accepted_args accepted_args.
	 *
	 * @return void
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add add function.
	 *
	 * @param string $hook          hook.
	 * @param string $component     component.
	 * @param string $callback      callback.
	 * @param int    $priority      priority.
	 * @param int    $accepted_args accepted_args.
	 *
	 * @return array
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Run function.
	 *
	 * @return void
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}
new SA_Loader();
?>
<?php
/**
 * SA_Cart_Admin class.
 */
class SA_Cart_Admin {

	/**
	 * Plugin Name.
	 *
	 * @var stirng
	 */
	private $plugin_name;

	/**
	 * Version.
	 *
	 * @var stirng
	 */
	private $version;
	/**
	 * Construct function.
	 */
	 /**
	  * Start Timestamp for filter.
	  *
	  * @var str start_timestamp - Start timestamp.
	  */
	public static $start_timestamp = '';

		/**
		 * End Timestamp for filter.
		 *
		 * @var str end_timestamp - End Timestamp.
		 */
	public static $end_timestamp = '';
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Display page function.
	 *
	 * @return void
	 */
	public static function display_page() {
		global $wpdb, $pagenow;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

		$wp_list_table = new SA_Admin_Table();
		$wp_list_table->prepare_items();
		// Output table contents
		$deleted = false;
		if ( 'delete' === $wp_list_table->current_action() ) {
			if ( is_array( $_REQUEST['id'] ) ) { // If deleting multiple lines from table
				$deleted_row_count = count( $_REQUEST['id'] );
			} else { // If a single row is deleted
				$deleted_row_count = 1;
			}
			$deleted = true;
		}
		?>
		<div class="wrap">
			<h1>Abandoned Cart <a href="admin.php?page=ab-cart-reports" class="button action">View Reports</a></h1>
			<h2 id="heading-for-admin-notice-dislay"></h2>

		<?php
		if ( 'admin.php' === $pagenow && 'ab-cart' === $_GET['page'] ) {
			if ( $deleted ) {
				?>
					<div class="updated below-h2" id="message"><p>Items deleted:  <?php echo esc_attr( $deleted_row_count ); ?></p></div>
				<?php
			}
			if ( 0 === self::abandoned_cart_count() ) { // If no abandoned carts, then output this note
				?>
				<p>
				<?php esc_html_e( 'Looks like you do not have any saved Abandoned carts yet.<br/>But do not worry, as soon as someone fills the <strong>Phone number</strong> fields of your WooCommerce Checkout form and abandons the cart, it will automatically appear here.', 'sms-alert' ); ?>
				</p>
			<?php } else { ?>
				<form method="GET">
					<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
				<?php $wp_list_table->display(); ?>
				</form>
				<?php
			}
		}
		?>
		</div>
		<?php
	}
	/**
	 * Setup the start & end time stamps to be used by all the functions to retrieve the data.
	 */
	public static function initialise_start_end_date() {

		$smsalert_date_range = isset( $_GET['smsalert_date_range'] ) ? sanitize_text_field( wp_unslash( $_GET['smsalert_date_range'] ) ) : 'this_month'; //phpcs:ignore

		$current_time  = current_time( 'timestamp' ); // phpcs:ignore
		$current_month = date( 'n' ); //phpcs:ignore
		$current_year  = date( 'Y' ); //phpcs:ignore

		switch ( $smsalert_date_range ) {

			case 'this_month':
				self::$start_timestamp = mktime( 00, 01, 01, $current_month, 1 );
				self::$end_timestamp   = $current_time;
				break;

			case 'last_month':
				self::$start_timestamp = mktime( 00, 01, 01, $current_month - 1, 1 );
				self::$end_timestamp   = mktime( 23, 59, 59, $current_month - 1, date( 't' ) ); //phpcs:ignore
				break;

			case 'this_quarter':
				if ( $current_month >= 1 && $current_month <= 3 ) {
					self::$start_timestamp = mktime( 00, 01, 01, 1, 01 );
				} elseif ( $current_month >= 4 && $current_month <= 6 ) {
					self::$start_timestamp = mktime( 00, 01, 01, 4, 01 );
				} elseif ( $current_month >= 7 && $current_month <= 9 ) {
					self::$start_timestamp = mktime( 00, 01, 01, 7, 01 );
				} elseif ( $current_month >= 10 && $current_month <= 12 ) {
					self::$start_timestamp = mktime( 00, 01, 01, 10, 01 );
				}
				self::$end_timestamp = $current_time;
				break;

			case 'last_quarter':
				if ( $current_month >= 1 && $current_month <= 3 ) {
					self::$start_timestamp = strtotime( '01-October-' . ( $current_year - 1 ) . '00:01:01' );
					self::$end_timestamp   = strtotime( '31-December-' . ( $current_year - 1 ) . '23:59:59' );
				} elseif ( $current_month >= 4 && $current_month <= 6 ) {
					self::$start_timestamp = strtotime( "01-January-$current_year" . '00:01:01' );
					self::$end_timestamp   = strtotime( "31-March-$current_year" . '23:59:59' );
				} elseif ( $current_month >= 7 && $current_month <= 9 ) {
					self::$start_timestamp = strtotime( "01-April-$current_year" . '00:01:01' );
					self::$end_timestamp   = strtotime( "30-June-$current_year" . '23:59:59' );
				} elseif ( $current_month >= 10 && $current_month <= 12 ) {
					self::$start_timestamp = strtotime( "01-July-$current_year" . '00:01:01' );
					self::$end_timestamp   = strtotime( "30-September-$current_year" . '23:59:59' );
				}
				break;

			case 'this_year':
				self::$start_timestamp = mktime( 00, 01, 01, 1, 1, $current_year );
				self::$end_timestamp   = $current_time;
				break;

			case 'last_year':
				self::$start_timestamp = mktime( 00, 01, 01, 1, 1, $current_year - 1 );
				self::$end_timestamp   = mktime( 23, 59, 59, 12, 31, $current_year - 1 );
				break;

			case 'custom':
				$user_start = isset( $_GET['sa_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['sa_start_date'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				$user_end   = isset( $_GET['sa_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['sa_end_date'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

				if ( '' === $user_start ) {
					$user_start = date( 'Y-m-d', mktime( 00, 01, 01, $current_month, 1 ) ); //phpcs:ignore
					$user_end   = date( 'Y-m-d', $current_time ); //phpcs:ignore
				}

				if ( '' === $user_end ) {
					$user_end = date( 'Y-m-d', $current_time ); //phpcs:ignore
				}

				$start_explode         = explode( '-', $user_start );
				$end_explode           = explode( '-', $user_end );
				self::$start_timestamp = mktime( 00, 01, 01, $start_explode[1], $start_explode[2], $start_explode[0] ); //phpcs:ignore
				self::$end_timestamp   = mktime( 23, 59, 59, $end_explode[1], $end_explode[2], $end_explode[0] );
				break;

		}
	}

	/**
	 * Returned Abandoned & Recovered cart stats.
	 *
	 * @param string $selected_data_range - Range selected.
	 * @param string $start_date - Range Start Date.
	 * @param string $end_date - Range End Date.
	 * @return array - Stats Data.
	 *
	 * @since 5.8.8
	 */
	public static function sa_get_range_data( $selected_data_range, $start_date, $end_date ) {
		global $wpdb;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;
		if ( '' === $start_date && '' === $end_date ) {
			return array();
		}
		$results = $wpdb->get_results( ( "SELECT cart_total,msg_sent,recovered,currency FROM $table_name WHERE time >= '$start_date' AND time <= '$end_date'" ), ARRAY_A );
		$rec_amount   = 0;
		$ab_amount    = 0;
		$ab_carts     = 0;
		$rec_carts    = 0;
		$tot_msg_sent = 0;
		$currency     = '';
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				if ( '1' === $result['recovered'] ) {
					$rec_amount += $result['cart_total'];
					$rec_carts++;
				} else {
					$ab_amount += $result['cart_total'];
					$ab_carts++;
				}
				$tot_msg_sent += $result['msg_sent'];
				$currency      = $result['currency'];
			}
		}
		return array(
			'abandoned_count'  => $ab_carts,
			'recovered_count'  => $rec_carts,
			'abandoned_amount' => $ab_amount,
			'recovered_amount' => $rec_amount,
			'msg_count'        => $tot_msg_sent,
			'currency'         => $currency,
		);
	}
	/**
	 * Get Graph Data
	 *
	 * @since 5.8.8
	 */
	public static function get_abandoned_data() {

		$start_timestamp = self::$start_timestamp;
		$end_timestamp   = self::$end_timestamp;

		$current_date  = date( 'd' ); // phpcs:ignore
		$current_month = date( 'm' ); // phpcs:ignore
		$current_year  = date( 'Y' ); // phpcs:ignore

		$selected_data_range = isset( $_GET['smsalert_date_range'] ) ? sanitize_text_field( wp_unslash( $_GET['smsalert_date_range'] ) ) : 'this_month'; //phpcs:ignore
		switch ( $selected_data_range ) {
			case 'this_month':
				$display_freq  = $current_date > 15 ? 'weekly' : 'daily';
				$end_timestamp = current_time( 'timestamp' ); // phpcs:ignore
				break;
			case 'last_month':
			case 'this_quarter':
			case 'last_quarter':
				$display_freq = 'weekly';
				break;
			case 'this_year':
				$display_freq  = $current_month > 3 ? 'monthly' : 'weekly';
				$end_timestamp = current_time( 'timestamp' ); // phpcs:ignore
				break;
			case 'last_year':
				$display_freq = 'monthly';
				break;
			case 'custom':
				$display_freq   = 'weekly';
				$number_of_days = round( ( $end_timestamp - $start_timestamp ) / ( 60 * 60 * 24 ) );
				if ( is_numeric( $number_of_days ) && $number_of_days > 0 ) {
					if ( $number_of_days <= 15 ) {
						$display_freq = 'daily';
					} elseif ( $number_of_days <= 90 ) {
						$display_freq = 'weekly';
					} else {
						$display_freq = 'monthly';
					}
				}
				break;
		}
		$data = self::sa_get_graph_data( $selected_data_range, $start_timestamp, $end_timestamp, $display_freq );
		return $data;

	}

		/**
		 * Collect Graph data to be displayed & return.
		 *
		 * @param string    $selected_data_range - Selected Date Range.
		 * @param timestamp $start_timestamp - Start Timestamp.
		 * @param timestamp $end_timestamp - End Timestamp.
		 * @param string    $display_freq - Display Frequency.
		 * @return array $data - Data to be returned.
		 *
		 * @since 5.8.8
		 */
	public static function sa_get_graph_data( $selected_data_range, $start_timestamp, $end_timestamp, $display_freq ) {

		$start_date = date( 'Y-m-d H:i:s', $start_timestamp ); // phpcs:ignore
		$end_date = date( 'Y-m-d H:i:s', $end_timestamp ); // phpcs:ignore
		$result     = self::sa_get_range_data( $selected_data_range, $start_date, $end_date );
		switch ( $display_freq ) {
			case 'daily':
				$range_end = date( 'Y-m-d H:i:s', strtotime( '+1 day', $start_timestamp ) ); // phpcs:ignore
				do {
					$get_stats                   = self::sa_get_range_data( $selected_data_range, $start_date, $range_end );
					$start_date_display          = date( 'd M', strtotime( $start_date ) ); // phpcs:ignore
					$data[ $start_date_display ] = array(
						'abandoned_amount' => $get_stats['abandoned_amount'],
						'recovered_amount' => $get_stats['recovered_amount'],
					);
					$start_date                  = date( 'Y-m-d H:i:s', strtotime( $range_end ) ); // phpcs:ignore
					$range_end                   = date( 'Y-m-d', strtotime( "$start_date +1 day" ) ); // phpcs:ignore
				} while ( strtotime( $start_date ) < $end_timestamp );
				break;
			case 'weekly':
				$range_end       = date( 'Y-m-d H:i:s', strtotime( '+7 days', $start_timestamp ) ); // phpcs:ignore
				$range_end_stamp = strtotime( $range_end );

				do {
					if ( $range_end_stamp > $end_timestamp ) {
						$range_end       = date( 'Y-m-d H:i:s', $end_timestamp ); // phpcs:ignore
						$range_end_stamp = $end_timestamp;
					}

					$get_stats                   = self::sa_get_range_data( $selected_data_range, $start_date, $range_end );
					$start_date_display          = date( 'd M', strtotime( $start_date ) ); // phpcs:ignore
					$data[ $start_date_display ] = array(
						'abandoned_amount' => $get_stats['abandoned_amount'],
						'recovered_amount' => $get_stats['recovered_amount'],
					);
					$start_date                  = date( 'Y-m-d H:i:s', $range_end_stamp ); // phpcs:ignore
					$range_end                   = date( 'Y-m-d', strtotime( "$start_date +7 days" ) ); // phpcs:ignore
					$range_end_stamp             = strtotime( $range_end );

				} while ( strtotime( $start_date ) < $end_timestamp );
				break;
			case 'monthly':
				$range_end = date( 'Y-m-d H:i:s', strtotime( '+1 month', $start_timestamp ) ); // phpcs:ignore
				do {
					$get_stats                   = self::sa_get_range_data( $selected_data_range, $start_date, $range_end );
					$start_date_display          = date( 'M y', strtotime( $start_date ) ); // phpcs:ignore
					$data[ $start_date_display ] = array(
						'abandoned_amount' => $get_stats['abandoned_amount'],
						'recovered_amount' => $get_stats['recovered_amount'],
					);
					$start_date                  = date( 'Y-m-d H:i:s', strtotime( $range_end ) ); // phpcs:ignore
					$range_end                   = date( 'Y-m-d', strtotime( "$start_date +1 month" ) ); // phpcs:ignore
				} while ( strtotime( $start_date ) < $end_timestamp );

				break;
		}
		$data['abandoned_amount'] = $result['abandoned_amount'];
		$data['recovered_count']  = $result['recovered_count'];
		$data['abandoned_count']  = $result['abandoned_count'];
		$data['recovered_amount'] = $result['recovered_amount'];
		$data['msg_count']        = $result['msg_count'];
		$data['currency']         = $result['currency'];
		return $data;
	}

	/**
	 * Display page function.
	 *
	 * @return void
	 */
	public static function display_reports_page() {
		if ( 'ab-cart-reports' === $_GET['page'] ) {
			wp_enqueue_script(
				'd3_js',
				SA_MOV_URL . 'js/d3.v3.min.js',
				'',
				SmsAlertConstants::SA_VERSION,
				false
			);
			wp_register_script( 'reports_js', SA_MOV_URL . 'js/ab-cart-graph.js', '', SmsAlertConstants::SA_VERSION, false );

			$sa_duration_range_select = array(
				'this_month'   => __( 'This Month', 'sms-alert' ),
				'last_month'   => __( 'Last Month', 'sms-alert' ),
				'this_quarter' => __( 'This Quarter', 'sms-alert' ),
				'last_quarter' => __( 'Last Quarter', 'sms-alert' ),
				'this_year'    => __( 'This Year', 'sms-alert' ),
				'last_year'    => __( 'Last Year', 'sms-alert' ),
				'custom'       => __( 'Custom', 'sms-alert' ),
			);
			$sa_duration_range        = isset( $_GET['smsalert_date_range'] ) ? sanitize_text_field( wp_unslash( $_GET['smsalert_date_range'] ) ) : 'this_month';

			$start_date              = isset( $_GET['sa_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['sa_start_date'] ) ) : '';
			$end_date                = isset( $_GET['sa_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['sa_end_date'] ) ) : '';
			$start_end_date_div_show = ( ! isset( $_GET['smsalert_date_range'] ) || 'custom' !== $_GET['smsalert_date_range'] ) ? 'none' : 'block';
			?>
		<div class="wrap">
			<h1>Abandoned Cart Reports <a href="admin.php?page=ab-cart" class="button action">View List</a></h1>
			<h2 id="heading-for-admin-notice-dislay"></h2>
				<form method="GET">
					<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
					<div class="main_start_end_date" id="main_start_end_date" >
				<div class = "filter_date_drop_down" id = "filter_date_drop_down" >
					<label class="date_time_filter_label" for="date_time_filter_label">
						<strong>
							<?php esc_html_e( 'Select date range:', 'sms-alert' ); ?>
						</strong>
					</label>

					<select id=smsalert_date_range name="smsalert_date_range">
						<?php
						foreach ( $sa_duration_range_select as $key => $value ) {
							$sel = '';
							if ( $key == $sa_duration_range ) {
								$sel = 'selected';
							}
							echo sprintf( "<option value='%s' %s>%s</option>", esc_attr( $key ), esc_attr( $sel ), esc_attr( __( $value, 'sms-alert' ) ) ); //phpcs:ignore
						}
						?>
					</select>
					<div class = "sa_start_end_date_div" id = "sa_start_end_date_div" style="display: <?php echo esc_attr( $start_end_date_div_show ); ?>"  >
						<input type="date" id="sa_start_date" name="sa_start_date" value="<?php echo esc_attr( $start_date ); ?>" placeholder="yyyy-mm-dd"/>
						<input type="date" id="sa_end_date" name="sa_end_date" value="<?php echo esc_attr( $end_date ); ?>" placeholder="yyyy-mm-dd"/>
					</div>
					<div id="sa_submit_button" class="sa_submit_button">
						<button type="submit" class="button-primary" id="sa_search" value="go"><?php esc_html_e( 'Go', 'sms-alert' ); ?></button>
						<a href="admin.php?page=ab-cart-reports" class="button-secondary">Reset</a>
					</div>

				</div>
			</div>
			<?php
			self::initialise_start_end_date();
			$graph_data = self::get_abandoned_data();
			$result     = $graph_data;
			unset( $graph_data['abandoned_amount'] );
			unset( $graph_data['abandoned_count'] );
			unset( $graph_data['recovered_amount'] );
			unset( $graph_data['recovered_count'] );
			unset( $graph_data['msg_count'] );
			unset( $graph_data['currency'] );
			wp_localize_script(
				'reports_js',
				'sa_graph_data',
				array(
					'data' => $graph_data,
				)
			);
			wp_enqueue_script( 'reports_js' );

			?>
		</form>
		<div class="clear"></div>
		<div id="sa_counter_container">
		<div class="counter_widget">
		<div id="sa_total_abandoned_amount" class="sa_counter_container">
		<div class="sa_widget_title_container">
		<span class="sa_counter_title">Abandoned Amount</span>
		</div>
		<div class="sa_counter_body">
		<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol"><?php echo $result['currency']; ?></span><?php echo ' ' . $result['abandoned_amount']; ?></bdi></span>		</div>

		</div>
					</div>

		<div class="counter_widget">
							<div id="sa_total_recovered_count" class="sa_counter_container">
		<div class="sa_widget_title_container">
		<span class="sa_counter_title">Abandoned Carts</span>
		</div>
		<div class="sa_counter_body">
			<?php echo $result['abandoned_count']; ?>		</div>

		</div>
					</div>

				<div class="counter_widget">
							<div id="sa_total_recovered_amount" class="sa_counter_container">
		<div class="sa_widget_title_container">
		<span class="sa_counter_title">Recovered Amount</span>
		</div>
		<div class="sa_counter_body">
		<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol"><?php echo $result['currency']; ?></span><?php echo ' ' . $result['recovered_amount']; ?></bdi></span>		</div>

		</div>
					</div>
					<div class="counter_widget">
				<div id="sa_total_abandoned_count" class="sa_counter_container">
		<div class="sa_widget_title_container">
		<span class="sa_counter_title">Recovered Carts</span>
		</div>
		<div class="sa_counter_body">
			<?php echo $result['recovered_count']; ?></div>

		</div>
					</div>

				<div class="counter_widget count_widget_right">
							<div id="sa_total_recovery_rate" class="sa_counter_container">
		<div class="sa_widget_title_container">
		<span class="sa_counter_title">SMS Sent</span>
		</div>
		<div class="sa_counter_body">
			<?php echo $result['msg_count']; ?></div>

		</div>
					</div>
			</div>
			<div class="clear"></div>
				<div class="abgraph"></div>
		</div>
			<?php
		}
	}


	/**
	 * Display wp cron warnings function.
	 *
	 * @return void
	 */
	function display_wp_cron_warnings() {
		global $pagenow;

		// Checking if we are on open plugin page
		if ( 'admin.php' === $pagenow && 'sms-alert' === sanitize_text_field( $_GET['page'] ) ) {

			// Checking if WP Cron hooks are scheduled
			$missing_hooks = array();
			// $user_settings_notification_frequency = smsalert_get_option('customer_notify','smsalert_abandoned_cart');

			if ( wp_next_scheduled( 'ab_cart_notification_sendsms_hook' ) === false ) { // If we havent scheduled msg notifications and notifications have not been disabled
				$missing_hooks[] = 'ab_cart_notification_sendsms_hook';
			}
			if ( ! empty( $missing_hooks ) ) { // If we have hooks that are not scheduled
				$hooks   = '';
				$current = 1;
				$total   = count( $missing_hooks );
				foreach ( $missing_hooks as $missing_hook ) {
					$hooks .= $missing_hook;
					if ( $current !== $total ) {
						$hooks .= ', ';
					}
					$current++;
				}
				?>
				<div class="warning notice updated">
					<?php
					echo sprintf(
					/* translators: %s - Cron event name */
						_n( 'It seems that WP Cron event <strong>%s</strong> required for automation is not scheduled.', 'It seems that WP Cron events <strong>%s</strong> required for automation are not scheduled.', $total, 'sms-alert' ),
						$hooks
					);
					?>
					 <?php
						echo sprintf(
							/* translators: %1$s - Plugin name, %2$s - Link */
							__( 'Please try disabling and enabling %1$s plugin. If this notice does not go away after that, please <a href="https://wordpress.org/support/plugin/sms-alert/" target="_blank">get in touch with us</a>.', 'sms-alert' ),
							SMSALERT_PLUGIN_NAME
						);
						?>
					</p>
				</div>
				<?php
			}

			// Checking if WP Cron is enabled
			if ( defined( 'DISABLE_WP_CRON' ) ) {
				if ( DISABLE_WP_CRON == true ) {
					?>
					<div class="warning notice updated">
						<p class="left-part"><?php esc_html_e( 'WP Cron has been disabled. Several WordPress core features, such as checking for updates or sending notifications utilize this function. Please enable it or contact your system administrator to help you with this.', 'sms-alert' ); ?></p>
					</div>
					<?php
				}
			}
		}
	}

	/**
	 * Send sms function.
	 *
	 * @return void
	 */
	function send_sms() {
		$notification_enabled = smsalert_get_option( 'customer_notify', 'smsalert_abandoned_cart', 'off' );
		if ( 'off' === $notification_enabled ) {
			return;
		}

		global $wpdb;
		$cron_frequency = CART_CRON_INTERVAL; // pick data from previous CART_CRON_INTERVAL min
		$table_name     = $wpdb->prefix . SA_CART_TABLE_NAME;

		$scheduler_data = get_option( 'smsalert_abandoned_cart_scheduler' );
        $quiet_hours = smsalert_get_option( 'enable_quiet_hours', 'smsalert_abandoned_cart', '0' );
        $form_quiet_hours = smsalert_get_option( 'from_quiet_hours', 'smsalert_abandoned_cart', '22:00' );
        $to_quiet_hours = smsalert_get_option( 'to_quiet_hours', 'smsalert_abandoned_cart', '08:00' );
		if('1' === $quiet_hours)
		{
			$current_time = date('H:i:s',strtotime(current_time( 'mysql' )));
			if($current_time >= $form_quiet_hours && $current_time <= $to_quiet_hours)
			{
				return;
			}
		} 
		foreach ( $scheduler_data['cron'] as $sdata ) {

			$datetime = current_time( 'mysql' );
			$fromdate = date( 'Y-m-d H:i:s', strtotime( '-' . $sdata['frequency'] . ' minutes', strtotime( $datetime ) ) );

			$todate = date( 'Y-m-d H:i:s', strtotime( '-' . ( $sdata['frequency'] + $cron_frequency ) . ' minutes', strtotime( $datetime ) ) );	
			 
			$rows_to_phone = $wpdb->get_results(
				'SELECT * FROM ' . $table_name . " WHERE cart_contents != '' AND recovered = '0' AND time >= '" . $todate . "' AND time <= '" . $fromdate . "' ",
				ARRAY_A
			);

			if ( $rows_to_phone ) { // If we have new rows in the database

				   $customer_message = $sdata['message'];
				   $frequency_time   = $sdata['frequency'];
				if ( '' !== $customer_message && 0 !== $frequency_time ) {
					$obj = array();
					foreach ( $rows_to_phone as $key=>$data ) {	
						$data['checkout_url'] = $this->create_cart_url( $data['email'], $data['session_id'], $data['id'] );
                        $obj[ $key ]['number']    = $data['phone'];
                        $obj[ $key ]['sms_body']  = $this->parse_sms_body( $data, $customer_message );
					}
					$response     = SmsAlertcURLOTP::send_sms_xml( $obj );
					$response_arr = json_decode( $response, true );
					if ( 'success' === $response_arr['status'] ) {
					    foreach ( $rows_to_phone as $data )
						{
							$last_msg_count = $data['msg_sent'];
						    $total_msg_sent = $last_msg_count + 1;
							$wpdb->query(
								$wpdb->prepare(
									"UPDATE $table_name
									SET msg_sent = %d
									WHERE msg_sent = %d AND
									session_id = %s",
									$total_msg_sent,
									$last_msg_count,
									$data['session_id']
								)
							);
						}
					} 
				}
			}
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
			'[checkout_url]',
			'[shop_url]'
		);

		$replace = array(
			wp_specialchars_decode( $item_name ),
			$item_name_with_qty,
			( array_key_exists( 'checkout_url', $data ) ? $data['checkout_url'] : '' ),
			get_site_url()
		);

		$content         = str_replace( $find, $replace, $content );
		$order_variables = SA_Abandoned_Cart::getAbandonCartvariables();

		foreach ( $order_variables as $key => $value ) {
			foreach ( $data as $dkey => $dvalue ) {
				if ( trim( $key, '[]' ) == $dkey ) {
					$array_trim_keys[ $key ] = $dvalue;
				}
			}
		}
		$content = str_replace( array_keys( $order_variables ), array_values( $array_trim_keys ), $content );

		return $content;
	}

	/**
	 * Abandoned cart count function.
	 *
	 * @return int
	 */
	public static function abandoned_cart_count() {
		global $wpdb;
		$table_name  = $wpdb->prefix . SA_CART_TABLE_NAME;
		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" );
		return $total_items;
	}

	/**
	 * Total captured abandoned cart count function.
	 *
	 * @return int
	 */
	function total_captured_abandoned_cart_count() {
		if ( false === ( $captured_abandoned_cart_count = get_transient( 'cart_captured_abandoned_cart_count' ) ) ) { // If value is not cached or has expired
			$captured_abandoned_cart_count = get_option( 'cart_captured_abandoned_cart_count' );
			set_transient( 'cart_captured_abandoned_cart_count', $captured_abandoned_cart_count, 60 * 10 ); // Temporary cache will expire in 10 minutes
		}
		return $captured_abandoned_cart_count;
	}

	/**
	 * Clear cart data function.
	 *
	 * @param int $order_id order_id.
	 *
	 * @return void
	 */
	function clear_cart_data( $order_id = null ) {

		global $wpdb;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

		// If a new Order is added from the WooCommerce admin panel, we must check if WooCommerce session is set. Otherwise we would get a Fatal error.
		if ( isset( WC()->session ) ) {

			$cart_session_id = WC()->session->get( 'cart_session_id' );
			if ( isset( $cart_session_id ) ) {
				$public        = new SA_Cart_Public( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );
				$cart_data     = $public->read_cart();
				$cart_currency = $cart_data['cart_currency'];
				$current_time  = $cart_data['current_time'];
				$msg_sent      = $cart_data['msg_sent'];

				$datas = array(
					'currency' => sanitize_text_field( $cart_currency ),
					'msg_sent' => sanitize_text_field( $msg_sent ),
				);

				if ( ! empty( $order_id ) ) {
					$datas['recovered'] = 1;
				} else {
					$datas['cart_contents'] = '';
					$datas['time']          = sanitize_text_field( $current_time );
				}

				// Cleaning Cart data
				$wpdb->prepare(
					'%s',
					$wpdb->update(
						$table_name,
						$datas,
						array( 'session_id' => $cart_session_id ),
						array( '%s', '%d', '%s' ),
						array( '%s' )
					)
				);
			}
		}
	}

	/**
	 * Restore cart function.
	 *
	 * @return void
	 */
	public static function restore_cart() {
		global $wpdb, $woocommerce;

		if ( empty( $_GET['cart'] ) ) {
			return;
		}

		// Processing GET parameter from the link
		$hash_id = sanitize_text_field( $_GET['cart'] ); // Getting and sanitizing GET value from the link
		$parts   = explode( '-', $hash_id ); // Splitting GET value into hash and ID
		$hash    = $parts[0];
		$id      = $parts[1];

		// Retrieve row from the abandoned cart table in order to check if hashes match
		$main_table = $wpdb->prefix . SA_CART_TABLE_NAME;
		$row        = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT id, email, cart_contents, session_id FROM ' . $main_table . '
			WHERE id = %d',
				$id
			)
		);

		if ( empty( $row ) ) { // Exit function if no row found
			return;
		}

		// Checking if hashes match
		$row_hash = hash_hmac( 'md5', $row->email . $row->session_id, CART_ENCRYPTION_KEY ); // Building encrypted hash from the row
		if ( ! hash_equals( $hash, $row_hash ) ) { // If hashes do not match, exit function
			return;
		}

		// Restore our cart with previous products
		if ( $woocommerce->cart ) { // Checking if WooCommerce has loaded
			$woocommerce->cart->empty_cart();// Removing any products that might have be added in the cart

			$products = @unserialize( $row->cart_contents );
			if ( ! $products ) { // If missing products
				return;
			}

			foreach ( $products as $product ) { // Looping through cart products
				$product_exists = wc_get_product( $product['product_id'] ); // Checking if the product exists
				if ( ! $product_exists ) {
					$this->log(
						'notice',
						sprintf(
						/* translators: %d - Product ID */
							__( 'Unable to restore product in the shopping cart since the product no longer exists. ID: %d', 'sms-alert' ),
							$product['product_id']
						)
					);
				} else {
					// Get product variation attributes if present
					if ( $product['product_variation_id'] ) {
						$single_variation      = new WC_Product_Variation( $product['product_variation_id'] );
						$single_variation_data = $single_variation->get_data();

						// Handling variable product title output with attributes
						$variation_attributes = $single_variation->get_variation_attributes();
					} else {
						$variation_attributes = '';
					}

					$restore = WC()->cart->add_to_cart( $product['product_id'], $product['quantity'], $product['product_variation_id'], $variation_attributes ); // Adding previous products back to cart
				}
			}

			$public = new SA_Cart_Public( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

			WC()->session->set( 'cart_session_id', $row->session_id ); // Putting previous customer ID back to WooCommerce session
		}

		// Redirecting user to Checkout page
		$checkout_url = wc_get_checkout_url();
		wp_redirect( $checkout_url, '303' );
		exit();
	}

	/**
	 * Create cart url function.
	 *
	 * @param string $email      email.
	 * @param string $session_id session_id.
	 * @param string $cart_id    cart_id.
	 *
	 * @return string
	 */
	public function create_cart_url( $email, $session_id, $cart_id ) {
		$cart_url            = wc_get_cart_url();
		$hash                = hash_hmac( 'md5', $email . $session_id, CART_ENCRYPTION_KEY ) . '-' . $cart_id; // Creating encrypted hash with abandoned cart row ID in the end
		return $checkout_url = $cart_url . '?cart=' . $hash;
	}
}
?>
<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class SA_Admin_Table extends WP_List_Table {


	/**
	 * Construct function.
	 */
	function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'id',
				'plural'   => 'ids',
			)
		);
	}

	/**
	 * Get columns function.
	 *
	 * @return array
	 */
	function get_columns() {
		return $columns = array(
			'cb'            => '<input type="checkbox" />',
			'id'            => __( 'ID', 'sms-alert' ),
			'name'          => __( 'Name, Surname', 'sms-alert' ),
			'email'         => __( 'Email', 'sms-alert' ),
			'phone'         => __( 'Phone', 'sms-alert' ),
			'location'      => __( 'Location', 'sms-alert' ),
			'cart_contents' => __( 'Cart contents', 'sms-alert' ),
			'cart_total'    => __( 'Cart total', 'sms-alert' ),
			'time'          => __( 'Time', 'sms-alert' ),
			'status'        => __( 'Status', 'sms-alert' ),
		);
	}

	/**
	 * Get sortable columns function.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return $sortable = array(
			'id'         => array( 'id', true ),
			'name'       => array( 'name', true ),
			'email'      => array( 'email', true ),
			'phone'      => array( 'phone', true ),
			'cart_total' => array( 'cart_total', true ),
			'time'       => array( 'time', true ),
		);
	}

	/**
	 * Column default function.
	 *
	 * @param array  $item        item.
	 * @param string $column_name column_name.
	 *
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Column name function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$req_page = sanitize_text_field( wp_unslash( $_REQUEST['page'] ) );
		$actions  = array(
			'delete' => sprintf( '<a href="?page=%s&action=delete&id=%s">%s</a>', $req_page, $item['id'], __( 'Delete', 'sms-alert' ) ),
		);

		return sprintf(
			'%s %s %s',
			$item['name'],
			$item['surname'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Column email function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_email( $item ) {
		return sprintf(
			'<a href="mailto:%1$s" title="">%1$s</a>',
			$item['email']
		);
	}

	/**
	 * Column location function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_location( $item ) {
		if ( is_serialized( $item['location'] ) ) {
			$location_data = unserialize( $item['location'] );
			$country       = $location_data['country'];
			$city          = $location_data['city'];
			$postcode      = $location_data['postcode'];

		} else {
			$parts = explode( ',', $item['location'] ); // Splits the Location field into parts where there are commas
			if ( count( $parts ) > 1 ) {
				$country = $parts[0];
				$city    = trim( $parts[1] ); // Trim removes white space before and after the string
			} else {
				$country = $parts[0];
				$city    = '';
			}

			$postcode = '';
			if ( is_serialized( $item['other_fields'] ) ) {
				$other_fields = @unserialize( $item['other_fields'] );
				if ( isset( $other_fields['ab_cart_billing_postcode'] ) ) {
					$postcode = $other_fields['ab_cart_billing_postcode'];
				}
			}
		}
		$location = $country;
		if ( ! empty( $city ) ) {
			$location .= ', ' . $city;
		}
		if ( ! empty( $postcode ) ) {
			$location .= ', ' . $postcode;
		}
		return sprintf(
			'%s',
			$location
		);
	}

	/**
	 * Column cart contents function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_cart_contents( $item ) {
		if ( ! is_serialized( $item['cart_contents'] ) ) {
			return;
		}

		$product_array = @unserialize( $item['cart_contents'] ); // Retrieving array from database column cart_contents
		$output        = '';

		if ( $product_array ) {
			// Displaying cart contents with thumbnails
			foreach ( $product_array as $product ) {
				if ( is_array( $product ) ) {
					if ( isset( $product['product_title'] ) ) {
						   // Checking product image
						if ( ! empty( $product['product_variation_id'] ) ) { // In case of a variable product
							$image = get_the_post_thumbnail_url( $product['product_variation_id'], 'thumbnail' );
							if ( empty( $image ) ) { // If variation didn't have an image set
									  $image = get_the_post_thumbnail_url( $product['product_id'], 'thumbnail' );
							}
						} else { // In case of a simple product
							$image = get_the_post_thumbnail_url( $product['product_id'], 'thumbnail' );
						}

						if ( empty( $image ) ) { // In case product has no image, output default WooCommerce image
							$image = wc_placeholder_img_src( 'thumbnail' );
						}

						$product_title     = $product['product_title'];
						$quantity          = ' (' . $product['quantity'] . ')'; // Enclose product quantity in brackets
						$edit_product_link = get_edit_post_link( $product['product_id'], '&' ); // Get product link by product ID
						if ( $edit_product_link ) { // If link exists (meaning the product hasn't been deleted)
							$output .= '<div><a href="' . $edit_product_link . '" title="' . $product_title . $quantity . '" target="_blank"><img src="' . $image . '" title="' . $product_title . $quantity . '" alt ="' . $product_title . $quantity . '" height="50" width="50" /></a><br><span class="tooltiptext">' . $product_title . $quantity . '</span></div>';
						} else {
							$output .= '<div><img src="' . $image . '" title="' . $product_title . $quantity . '" alt ="' . $product_title . $quantity . '" /><br><span class="tooltiptext">' . $product_title . $quantity . '</span></div>';
						}
					}
				}
			}
		}
		return sprintf( '%s', $output );
	}

	/**
	 * Column cart total function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_cart_total( $item ) {
		return sprintf(
			'%0.2f %s',
			$item['cart_total'],
			$item['currency']
		);
	}

	/**
	 * Column time function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_time( $item ) {
		$time       = new DateTime( $item['time'] );
		$date_iso   = $time->format( 'c' );
		$date_title = $time->format( 'M d, Y H:i:s' );
		$utc_time   = $time->format( 'U' );

		if ( $utc_time > strtotime( '-1 day', current_time( 'timestamp' ) ) ) { // In case the abandoned cart is newly captued
			$friendly_time = sprintf(
				/* translators: %1$s - Time, e.g. 1 minute, 5 hours */
				__( '%1$s ago', 'sms-alert' ),
				human_time_diff(
					$utc_time,
					current_time( 'timestamp' )
				)
			);
		} else { // In case the abandoned cart is older tahn 24 hours
			$friendly_time = $time->format( 'M d, Y' );
		}

		return sprintf( '<time datetime="%s" title="%s">%s</time>', $date_iso, $date_title, $friendly_time );
	}

	/**
	 * Column status function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_status( $item ) {
		$cart_time    = strtotime( $item['time'] );
		$date         = date_create( current_time( 'mysql', false ) );
		$current_time = strtotime( date_format( $date, 'Y-m-d H:i:s' ) );
		$status       = '';

		if ( $cart_time > $current_time - CART_STILL_SHOPPING * 60 && '0' === $item['msg_sent'] && '0' === $item['recovered'] ) { // Checking time if user is still shopping or might return - we add shopping label
			$status .= sprintf( '<span class="status shopping">%s</span>', __( 'Shopping', 'sms-alert' ) );

		} else {
			if ( $cart_time > ( $current_time - CART_NEW_STATUS_NOTICE * 60 ) && '0' === $item['msg_sent'] && '0' === $item['recovered'] ) { // Checking time if user has not gone through with the checkout after the specified time we add new label
				$status .= sprintf( '<span class="status new" >%s</span>', __( 'New', 'sms-alert' ) );
			}
			if ( '0' !== $item['msg_sent'] && '0' === $item['recovered'] ) {
				$status .= sprintf( '<div class="status-item-container"><span class="status msg-sent" >%s (%s)</span></div>', __( 'MSG Sent', 'sms-alert' ), $item['msg_sent'] );
			}
			if ( '1' === $item['recovered'] ) {
				$status .= sprintf( '<div class="status-item-container"><span class="status recovered" >%s</span></div>', __( 'Recovered', 'sms-alert' ) );
			}
		}
		return $status;
	}

	/**
	 * Column cb function.
	 *
	 * @param array $item item.
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * Get bulk actions function.
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'sms-alert' ),
		);
		return $actions;
	}

	/**
	 * Process bulk actions function.
	 *
	 * @return void
	 */
	function process_bulk_action() {
		global $wpdb;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME; // do not forget about tables prefix

		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_REQUEST['id'] ) ? smsalert_sanitize_array( $_REQUEST['id'] ) : array();
			if ( ! empty( $ids ) ) {
				if ( is_array( $ids ) ) { // Bulk abandoned cart deletion
					foreach ( $ids as $key => $id ) {
						$wpdb->query(
							$wpdb->prepare(
								"DELETE FROM $table_name
                                WHERE id = %d",
								intval( $id )
							)
						);
					}
				} else { // Single abandoned cart deletion
					$id = $ids;
					$wpdb->query(
						$wpdb->prepare(
							"DELETE FROM $table_name
                            WHERE id = %d",
							intval( $id )
						)
					);
				}
			}
		}
	}

	/**
	 * Prepare items function.
	 *
	 * @return void
	 */
	function prepare_items() {
		global $wpdb;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

		$screen = get_current_screen();
		$user   = get_current_user_id();
		$option = $screen->get_option( 'per_page', 'option' );
		// $per_page = get_user_meta($user, $option, true);
		$per_page = 10;

		// How much records will be shown per page, if the user has not saved any custom values under Screen options, then default amount of 10 rows will be shown
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable ); // here we configure table headers, defined in our methods
		$this->process_bulk_action(); // process bulk action if any
		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE 1" );// will be used in pagination settings

		// prepare query params, as usual current page, order by and order direction
		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( sanitize_text_field( $_REQUEST['paged'] ) ) - 1 ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'time';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';

		// configure pagination
		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // total items defined above
				'per_page'    => $per_page, // per page constant defined at top of method
				'total_pages' => ceil( $total_items / $per_page ), // calculate pages count
			)
		);

		// define $items array
		   $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE 1 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page ), ARRAY_A );
	}
}
?>
<?php
/**
 * SA_Cart_Public class
 */
class SA_Cart_Public {


	/**
	 * Plugin_name.
	 *
	 * @var stirng
	 */
	private $plugin_name;

	/**
	 * Version.
	 *
	 * @var stirng
	 */
	private $version;

	/**
	 * Construct function.
	 */
	public function __construct( $plugin_name, $version ) {
		global $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_shortcode( 'sa-exit-intent', array( $this, 'ab_cart_exit_intent' ), 100 );
	}
	/**
	 * Ab cart exit intent function
	 *
	 * @param array $attrs attrs.
	 */
	public function ab_cart_exit_intent( $attrs = array() ) {
		$main_color    = isset( $attrs['main_color'] ) ? $attrs['main_color'] : '#e3e3e3';
		$inverse_color = isset( $attrs['inverse_color'] ) ? $attrs['inverse_color'] : $this->invert_color( '#e3e3e3' );

		$this->get_template(
			'ab-cart-shortcode-html.php',
			array(
				'main_color'    => $main_color,
				'inverse_color' => $inverse_color,
			)
		);
	}


	/**
	 * Enqueue styles function.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		if ( $this->exit_intent_enabled() ) { // If Exit Intent Enabled
			wp_enqueue_style( $this->plugin_name, SA_MOV_URL . 'css/ab-public.css', array(), SmsAlertConstants::SA_VERSION, 'all' );
		}
	}

	/**
	 * Enqueue scripts function.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( $this->exit_intent_enabled() ) { // If Exit Intent Enabled

			if ( is_user_logged_in() ) {
				$user_logged_in = true;
			} else {
				$user_logged_in = false;
				$plugin_admin   = new SA_Cart_Admin( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );
			}
			$cart_content_count = 0;
			if ( WC()->cart ) {
				$cart_content_count = WC()->cart->get_cart_contents_count();
			}

			if ( smsalert_get_option( 'cart_exit_intent_status', 'smsalert_abandoned_cart', '0' ) ) {
				$data = array(
					'hours'             => 1,
					'product_count'     => $cart_content_count,
					'is_user_logged_in' => $user_logged_in,
					'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				);
				wp_enqueue_script( $this->plugin_name . 'exit_intent', SA_MOV_URL . 'js/ab-public-exit-intent.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, false );
				wp_localize_script( $this->plugin_name . 'exit_intent', 'cart_exit_intent_data', $data ); // Sending variable over to JS file
			}
		}
	}

	/**
	 * Add additional scripts on checkout.
	 *
	 * @return void
	 */
	public function add_additional_scripts_on_checkout() {

		$user_settings_notification_frequency = smsalert_get_option( 'customer_notify', 'smsalert_abandoned_cart', 'off' );
		if ( 'off' === $user_settings_notification_frequency ) {
			return;
		}

		$plugin_admin = new SA_Cart_Admin( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

		if ( is_user_logged_in() ) {
			$user_logged_in = true;
		} else {
			$user_logged_in = false;
		}
		$data = array(
			'is_user_logged_in' => $user_logged_in,
			'ajaxurl'           => admin_url( 'admin-ajax.php' ),
		);
		wp_enqueue_script( $this->plugin_name, SA_MOV_URL . 'js/ab-cart-public.js', array( 'jquery' ), SmsAlertConstants::SA_VERSION, false );
		wp_localize_script( $this->plugin_name, 'ab_cart_checkout_form_data', $data );
	}

	/**
	 * Save user data function.
	 *
	 * @return void
	 */
	function save_user_data() {
		// First check if data is being sent and that it is the data we want
		check_ajax_referer( 'smsalert_wp_abcart_nonce', 'smsalert_abcart_nonce' );
		if ( isset( $_POST['ab_cart_phone'] ) ) {
			$plugin_admin = new SA_Cart_Admin( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

			global $wpdb;
			$table_name = $wpdb->prefix . SA_CART_TABLE_NAME; // do not forget about tables prefix

			// Retrieving cart array consisting of currency, cart total, time, msg status, session id and products and their quantities
			$cart_data       = $this->read_cart();
			$cart_total      = $cart_data['cart_total'];
			$cart_currency   = $cart_data['cart_currency'];
			$current_time    = $cart_data['current_time'];
			$msg_sent        = $cart_data['msg_sent'];
			$session_id      = $cart_data['session_id'];
			$product_array   = $cart_data['product_array'];
			$cart_session_id = WC()->session->get( 'cart_session_id' );

			// In case if the cart has no items in it, we need to delete the abandoned cart
			if ( empty( $product_array ) ) {
				$plugin_admin->clear_cart_data();
				return;
			}
			// Checking if we have values coming from the input fields
			( isset( $_POST['ab_cart_name'] ) ) ? $name       = sanitize_text_field( $_POST['ab_cart_name'] ) : $name = '';
			( isset( $_POST['ab_cart_surname'] ) ) ? $surname = sanitize_text_field( $_POST['ab_cart_surname'] ) : $surname = '';
			( isset( $_POST['ab_cart_phone'] ) ) ? $phone     = sanitize_text_field( $_POST['ab_cart_phone'] ) : $phone = '';
			( isset( $_POST['ab_cart_country'] ) ) ? $country = sanitize_text_field( $_POST['ab_cart_country'] ) : $country = '';
			( isset( $_POST['ab_cart_city'] ) && '' !== sanitize_text_field( $_POST['ab_cart_city'] ) ) ? $city = sanitize_text_field( $_POST['ab_cart_city'] ) : $city = '';
			( isset( $_POST['ab_cart_billing_company'] ) ) ? $company               = sanitize_text_field( $_POST['ab_cart_billing_company'] ) : $company = '';
			( isset( $_POST['ab_cart_billing_address_1'] ) ) ? $address_1           = sanitize_text_field( $_POST['ab_cart_billing_address_1'] ) : $address_1 = '';
			( isset( $_POST['ab_cart_billing_address_2'] ) ) ? $address_2           = sanitize_text_field( $_POST['ab_cart_billing_address_2'] ) : $address_2 = '';
			( isset( $_POST['ab_cart_billing_state'] ) ) ? $state                   = sanitize_text_field( $_POST['ab_cart_billing_state'] ) : $state = '';
			( isset( $_POST['ab_cart_billing_postcode'] ) ) ? $postcode             = sanitize_text_field( $_POST['ab_cart_billing_postcode'] ) : $postcode = '';
			( isset( $_POST['ab_cart_shipping_first_name'] ) ) ? $shipping_name     = sanitize_text_field( $_POST['ab_cart_shipping_first_name'] ) : $shipping_name = '';
			( isset( $_POST['ab_cart_shipping_last_name'] ) ) ? $shipping_surname   = sanitize_text_field( $_POST['ab_cart_shipping_last_name'] ) : $shipping_surname = '';
			( isset( $_POST['ab_cart_shipping_company'] ) ) ? $shipping_company     = sanitize_text_field( $_POST['ab_cart_shipping_company'] ) : $shipping_company = '';
			( isset( $_POST['ab_cart_shipping_country'] ) ) ? $shipping_country     = sanitize_text_field( $_POST['ab_cart_shipping_country'] ) : $shipping_country = '';
			( isset( $_POST['ab_cart_shipping_address_1'] ) ) ? $shipping_address_1 = sanitize_text_field( $_POST['ab_cart_shipping_address_1'] ) : $shipping_address_1 = '';
			( isset( $_POST['ab_cart_shipping_address_2'] ) ) ? $shipping_address_2 = sanitize_text_field( $_POST['ab_cart_shipping_address_2'] ) : $shipping_address_2 = '';
			( isset( $_POST['ab_cart_shipping_city'] ) ) ? $shipping_city           = sanitize_text_field( $_POST['ab_cart_shipping_city'] ) : $shipping_city = '';
			( isset( $_POST['ab_cart_shipping_state'] ) ) ? $shipping_state         = sanitize_text_field( $_POST['ab_cart_shipping_state'] ) : $shipping_state = '';
			( isset( $_POST['ab_cart_shipping_postcode'] ) ) ? $shipping_postcode   = sanitize_text_field( $_POST['ab_cart_shipping_postcode'] ) : $shipping_postcode = '';
			( isset( $_POST['ab_cart_order_comments'] ) ) ? $comments               = sanitize_text_field( $_POST['ab_cart_order_comments'] ) : $comments = '';
			( isset( $_POST['ab_cart_create_account'] ) ) ? $create_account         = sanitize_text_field( $_POST['ab_cart_create_account'] ) : $create_account = '';
			( isset( $_POST['ab_cart_ship_elsewhere'] ) ) ? $ship_elsewhere         = sanitize_text_field( $_POST['ab_cart_ship_elsewhere'] ) : $ship_elsewhere = '';

			$phone = SmsAlertUtility::formatNumberForCountryCode( $phone );
			$other_fields = array(
				'ab_cart_billing_company'     => $company,
				'ab_cart_billing_address_1'   => $address_1,
				'ab_cart_billing_address_2'   => $address_2,
				'ab_cart_billing_state'       => $state,
				'ab_cart_shipping_first_name' => $shipping_name,
				'ab_cart_shipping_last_name'  => $shipping_surname,
				'ab_cart_shipping_company'    => $shipping_company,
				'ab_cart_shipping_country'    => $shipping_country,
				'ab_cart_shipping_address_1'  => $shipping_address_1,
				'ab_cart_shipping_address_2'  => $shipping_address_2,
				'ab_cart_shipping_city'       => $shipping_city,
				'ab_cart_shipping_state'      => $shipping_state,
				'ab_cart_shipping_postcode'   => $shipping_postcode,
				'ab_cart_order_comments'      => $comments,
				'ab_cart_create_account'      => $create_account,
				'ab_cart_ship_elsewhere'      => $ship_elsewhere,
			);

			$location = array(
				'country'  => $country,
				'city'     => $city,
				'postcode' => $postcode,
			);

			$current_session_exist_in_db = $this->current_session_exist_in_db( $cart_session_id );
			// If we have already inserted the Users session ID in Session variable and it is not NULL and Current session ID exists in Database we update the abandoned cart row
			if ( $current_session_exist_in_db && $cart_session_id !== null ) {

				$msg_sent = 0;
				// Updating row in the Database where users Session id = same as prevously saved in Session
				$updated_rows = $wpdb->prepare(
					'%s',
					$wpdb->update(
						$table_name,
						array(
							'name'          => sanitize_text_field( $name ),
							'surname'       => sanitize_text_field( $surname ),
							'email'         => sanitize_email( $_POST['ab_cart_email'] ),
							'phone'         => filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ),
							'location'      => sanitize_text_field( serialize( $location ) ),
							'cart_contents' => serialize( $product_array ),
							'cart_total'    => sanitize_text_field( $cart_total ),
							'currency'      => sanitize_text_field( $cart_currency ),
							'time'          => sanitize_text_field( $current_time ),
							'msg_sent'      => sanitize_text_field( $msg_sent ),
							'other_fields'  => sanitize_text_field( serialize( $other_fields ) ),
						),
						array( 'session_id' => $cart_session_id ),
						array( '%s', '%s', '%s', '%s', '%s', '%s', '%0.2f', '%s', '%s', '%d', '%s' ),
						array( '%s' )
					)
				);

				if ( $updated_rows ) { // If we have updated at least one row
					   $updated_rows = str_replace( "'", '', $updated_rows ); // Removing quotes from the number of updated rows

					if ( $updated_rows > 1 ) { // Checking if we have updated more than a single row to know if there were duplicates
						$this->delete_duplicate_carts( $cart_session_id, $updated_rows );
					}
				}
			} elseif( $session_id !== null ) {
				// Inserting row into Database
				$wpdb->query(
					$wpdb->prepare(
						'INSERT INTO ' . $table_name . '
						( name, surname, email, phone, location, cart_contents, cart_total, currency, time, session_id, msg_sent, other_fields )
						VALUES ( %s, %s, %s, %s, %s, %s, %0.2f, %s, %s, %s, %d, %s )',
						array(
							sanitize_text_field( $name ),
							sanitize_text_field( $surname ),
							sanitize_email( $_POST['ab_cart_email'] ),
							filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ),
							sanitize_text_field( serialize( $location ) ),
							serialize( $product_array ),
							sanitize_text_field( $cart_total ),
							sanitize_text_field( $cart_currency ),
							sanitize_text_field( $current_time ),
							sanitize_text_field( $session_id ),
							sanitize_text_field( $msg_sent ),
							sanitize_text_field( serialize( $other_fields ) ),
						)
					)
				);
				// Storing session_id in WooCommerce session
				WC()->session->set( 'cart_session_id', $session_id );
				$this->increase_captured_abandoned_cart_count(); // Increasing total count of captured abandoned carts
			}
			die();
		}
	}

	/**
	 * Save logged in user data.
	 *
	 * @return void
	 */
	function save_logged_in_user_data() {
		if ( is_user_logged_in() ) { // If a user is logged in
			$plugin_admin = new SA_Cart_Admin( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

			global $wpdb;
			$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

			// Retrieving cart array consisting of currency, cart total, time, msg status, session id and products and their quantities
			$cart_data       = $this->read_cart();
			$cart_total      = $cart_data['cart_total'];
			$cart_currency   = $cart_data['cart_currency'];
			$current_time    = $cart_data['current_time'];
			$msg_sent        = $cart_data['msg_sent'];
			$session_id      = $cart_data['session_id'];
			$product_array   = $cart_data['product_array'];
			$cart_session_id = WC()->session->get( 'cart_session_id' );

			// In case if the user updates the cart and takes out all items from the cart
			if ( empty( $product_array ) ) {
				$plugin_admin->clear_cart_data();
				return;
			}

			$abandoned_cart = '';

			// If we haven't set cart_session_id, then need to check in the database if the current user has got an abandoned cart already
			if ( $cart_session_id === null ) {
				$main_table     = $wpdb->prefix . SA_CART_TABLE_NAME;
				$abandoned_cart = $wpdb->get_row(
					$wpdb->prepare(
						'SELECT session_id FROM ' . $main_table . '
					WHERE session_id = %d',
						get_current_user_id()
					)
				);
			}

			$current_session_exist_in_db = $this->current_session_exist_in_db( $cart_session_id );
			// If the current user has got an abandoned cart already or if we have already inserted the Users session ID in Session variable and it is not NULL and already inserted the Users session ID in Session variable we update the abandoned cart row
			if ( $current_session_exist_in_db && ( ! empty( $abandoned_cart ) || $cart_session_id !== null ) ) {

				// If the user has got an abandoned cart previously, we set session ID back
				if ( ! empty( $abandoned_cart ) ) {
					$session_id = $abandoned_cart->session_id;
					// Storing session_id in WooCommerce session
					WC()->session->set( 'cart_session_id', $session_id );

				} else {
					$session_id = $cart_session_id;
				}

				// Updating row in the Database where users Session id = same as prevously saved in Session
				// Updating only Cart related data since the user can change his data only in the Checkout form
				$updated_rows = $wpdb->prepare(
					'%s',
					$wpdb->update(
						$table_name,
						array(
							'cart_contents' => serialize( $product_array ),
							'cart_total'    => sanitize_text_field( $cart_total ),
							'currency'      => sanitize_text_field( $cart_currency ),
							'time'          => sanitize_text_field( $current_time ),
							'msg_sent'      => sanitize_text_field( $msg_sent ),
						),
						array( 'session_id' => $session_id ),
						array( '%s', '%0.2f', '%s', '%s', '%d' ),
						array( '%s' )
					)
				);

				if ( $updated_rows ) { // If we have updated at least one row
					   $updated_rows = str_replace( "'", '', $updated_rows ); // Removing quotes from the number of updated rows

					if ( $updated_rows > 1 ) { // Checking if we have updated more than a single row to know if there were duplicates
						$this->delete_duplicate_carts( $cart_session_id, $updated_rows );
					}
				}
			} elseif( $session_id !== null ) {

				// Looking if a user has previously made an order
				// If not, using default WordPress assigned data
				// Handling users name
				$current_user = wp_get_current_user(); // Retrieving users data
				if ( $current_user->billing_first_name ) {
					$name = $current_user->billing_first_name;
				} else {
					$name = $current_user->user_firstname; // Users name
				}

				// Handling users surname
				if ( $current_user->billing_last_name ) {
					$surname = $current_user->billing_last_name;
				} else {
					$surname = $current_user->user_lastname;
				}

				// Handling users email address
				if ( $current_user->billing_email ) {
					$email = $current_user->billing_email;
				} else {
					$email = $current_user->user_email;
				}

				// Handling users phone
				$phone = $current_user->billing_phone;

				// Handling users address
				if ( $current_user->billing_country ) {
					$country = $current_user->billing_country;
				} else {
					$country = WC_Geolocation::geolocate_ip(); // Getting users country from his IP address
					$country = $country['country'];
				}

				if ( $current_user->billing_city ) {
					$city = $current_user->billing_city;
				} else {
					$city = '';
				}

				if ( $current_user->billing_postcode ) {
					$postcode = $current_user->billing_postcode;
				} else {
					$postcode = '';
				}

				$location = array(
					'country'  => $country,
					'city'     => $city,
					'postcode' => $postcode,
				);

				// Inserting row into Database
				$wpdb->query(
					$wpdb->prepare(
						'INSERT INTO ' . $table_name . '
						( name, surname, email, phone, location, cart_contents, cart_total, currency, time, session_id, msg_sent )
						VALUES ( %s, %s, %s, %s, %s, %s, %0.2f, %s, %s, %s, %d )',
						array(
							sanitize_text_field( $name ),
							sanitize_text_field( $surname ),
							sanitize_email( $email ),
							filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ),
							sanitize_text_field( serialize( $location ) ),
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

				$this->increase_captured_abandoned_cart_count(); // Increasing total count of captured abandoned carts
			}
		}
	}

	/**
	 * Update cart data.
	 *
	 * @return void
	 */
	function update_cart_data() {
		if ( ! is_user_logged_in() ) { // If a user is not logged in
			$plugin_admin = new SA_Cart_Admin( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

			$cart_session_id = WC()->session->get( 'cart_session_id' );
			if ( $cart_session_id !== null ) {

				global $wpdb;
				$table_name    = $wpdb->prefix . SA_CART_TABLE_NAME;
				$cart_data     = $this->read_cart();
				$product_array = $cart_data['product_array'];
				$cart_total    = $cart_data['cart_total'];
				$cart_currency = $cart_data['cart_currency'];
				$current_time  = $cart_data['current_time'];
				$msg_sent      = $cart_data['msg_sent'];

				// In case if the cart has no items in it, we need to delete the abandoned cart
				if ( empty( $product_array ) ) {
					$plugin_admin->clear_cart_data();
					return;
				}

				// Updating row in the Database where users Session id = same as prevously saved in Session
				$wpdb->prepare(
					'%s',
					$wpdb->update(
						$table_name,
						array(
							'cart_contents' => serialize( $product_array ),
							'cart_total'    => sanitize_text_field( $cart_total ),
							'currency'      => sanitize_text_field( $cart_currency ),
							'time'          => sanitize_text_field( $current_time ),
							'msg_sent'      => sanitize_text_field( $msg_sent ),
						),
						array( 'session_id' => $cart_session_id ),
						array( '%s', '%0.2f', '%s', '%s', '%d' ),
						array( '%s' )
					)
				);
			}
		}
	}

	/**
	 * Current session exist in db.
	 *
	 * @param  string $cart_session_id cart_session_id.
	 * @return array
	 */
	function current_session_exist_in_db( $cart_session_id ) {
		// If we have saved the abandoned cart in session variable
		if ( $cart_session_id !== null ) {
			global $wpdb;
			$main_table = $wpdb->prefix . SA_CART_TABLE_NAME;

			// Checking if we have this abandoned cart in our database already
			return $result = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT session_id
				FROM ' . $main_table . '
				WHERE session_id = %s',
					$cart_session_id
				)
			);

		} else {
			return false;
		}
	}

	/**
	 * Update logged customer id.
	 *
	 * @return void
	 */
	function update_logged_customer_id() {

		if ( is_user_logged_in() ) { // If a user is logged in
			$session_id = WC()->session->get_customer_id();

			if ( WC()->session->get( 'cart_session_id' ) !== null && WC()->session->get( 'cart_session_id' ) !== $session_id ) { // If session is set and it is different from the one that currently is assigned to the customer

				global $wpdb;
				$main_table = $wpdb->prefix . SA_CART_TABLE_NAME;

				// Updating session ID to match the one of a logged in user
				$wpdb->prepare(
					'%s',
					$wpdb->update(
						$main_table,
						array( 'session_id' => $session_id ),
						array( 'session_id' => WC()->session->get( 'cart_session_id' ) )
					)
				);

				WC()->session->set( 'cart_session_id', $session_id );

			} else {
				return;
			}
		} else {
			return;
		}
	}

	/**
	 * Delete duplicate carts.
	 *
	 * @param string $cart_session_id cart_session_id.
	 * @param int    $duplicate_count duplicate_count.
	 *
	 * @return void
	 */
	private function delete_duplicate_carts( $cart_session_id, $duplicate_count ) {
		global $wpdb;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

		$duplicate_rows = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name
				WHERE session_id = %s
				ORDER BY %s DESC
				LIMIT %d",
				$cart_session_id,
				'id',
				$duplicate_count - 1
			)
		);
	}

	/**
	 * Read carts function.
	 *
	 * @return array
	 */
	function read_cart() {
		global $woocommerce;

		global $wpdb;
		$table_name = $wpdb->prefix . SA_CART_TABLE_NAME;

		// Retrieving cart total value and currency
		$cart_total    = WC()->cart->total;
		$cart_currency = get_woocommerce_currency();
		$current_time  = current_time( 'mysql', false ); // Retrieving current time

		// Set the value that msg has not been sent
		// $msg_sent = 0;
		$cart_session_id = WC()->session->get( 'cart_session_id' );
		$msg_sent        = $wpdb->get_var( 'SELECT msg_sent, session_id FROM ' . $table_name . " WHERE session_id = '" . $cart_session_id . "'" );

		$row = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT cart_contents,time
			FROM ' . $table_name . '
			WHERE session_id = %s',
				$cart_session_id
			)
		);

		// Retrieving customer ID from WooCommerce sessions variable in order to use it as a session_id value
		$session_id = WC()->session->get_customer_id();

		// Retrieving cart
		$products      = $woocommerce->cart->cart_contents;
		$product_array = array();

		foreach ( $products as $product => $values ) {
			$item = wc_get_product( $values['data']->get_id() );

			$product_title           = $item->get_title();
			$product_quantity        = $values['quantity'];
			$product_variation_price = $values['line_total'];

			// Handling product variations
			if ( $values['variation_id'] ) { // If user has chosen a variation
				$single_variation = new WC_Product_Variation( $values['variation_id'] );

				// Handling variable product title output with attributes
				$product_attributes   = $this->attribute_slug_to_title( $single_variation->get_variation_attributes() );
				$product_variation_id = $values['variation_id'];
			} else {
				$product_attributes   = false;
				$product_variation_id = '';
			}

			// Inserting Product title, Variation and Quantity into array
			$product_array[] = array(
				'product_title'           => $product_title . $product_attributes,
				'quantity'                => $product_quantity,
				'product_id'              => $values['product_id'],
				'product_variation_id'    => $product_variation_id,
				'product_variation_price' => $product_variation_price,
			);
		}

		$results_array = array(
			'cart_total'    => $cart_total,
			'cart_currency' => $cart_currency,
			// 'current_time' => $current_time,
			'msg_sent'      => $msg_sent,
			'session_id'    => $session_id,
			'product_array' => $product_array,
		);

		$tbl_cart_content = ( ! empty( $row->cart_contents ) ) ? (array) unserialize( $row->cart_contents ) : '';
		if ( $tbl_cart_content === $product_array ) {
			$results_array['current_time'] = $row->time;
		} else {
			$results_array['current_time'] = $current_time;
		}
		return $results_array;
	}

	/**
	 * Attribute slug to title function.
	 *
	 * @param string $product_variations product_variations.
	 *
	 * @return string
	 */
	public function attribute_slug_to_title( $product_variations ) {
		global $woocommerce;
		$attribute_array = array();

		if ( $product_variations ) {

			foreach ( $product_variations as $product_variation_key => $product_variation_name ) {

				$value = '';
				if ( taxonomy_exists( esc_attr( str_replace( 'attribute_', '', $product_variation_key ) ) ) ) {
					$term = get_term_by( 'slug', $product_variation_name, esc_attr( str_replace( 'attribute_', '', $product_variation_key ) ) );
					if ( ! is_wp_error( $term ) && ! empty( $term->name ) ) {
						$value = $term->name;
						if ( ! empty( $value ) ) {
							$attribute_array[] = $value;
						}
					}
				} else {
					$value = apply_filters( 'woocommerce_variation_option_name', $product_variation_name );
					if ( ! empty( $value ) ) {
						$attribute_array[] = $value;
					}
				}
			}

			// Generating attribute output
			$total_variations  = count( $attribute_array );
			$increment         = 0;
			$product_attribute = '';
			foreach ( $attribute_array as $attribute ) {
				if ( 0 === $increment && $increment !== $total_variations - 1 ) { // If this is first variation and we have multiple variations
					$colon = ': ';
					$comma = ', ';
				} elseif ( 0 === $increment && $increment === $total_variations - 1 ) { // If we have only one variation
					$colon = ': ';
					$comma = false;
				} elseif ( $increment === $total_variations - 1 ) { // If this is the last variation
					$comma = '';
					$colon = false;
				} else {
					$comma = ', ';
					$colon = false;
				}
				$product_attribute .= $colon . $attribute . $comma;
				$increment++;
			}
			return $product_attribute;
		} else {
			return;
		}
	}

	/**
	 * Restore input data function.
	 *
	 * @param array $fields fields.
	 *
	 * @return array
	 */
	public function restore_input_data( $fields = array() ) {
		$wc_session = WC()->session;

		if ( $wc_session == null ) {
			return $fields;
		}

		global $wpdb;

		$table_name      = $wpdb->prefix . SA_CART_TABLE_NAME;
		$cart_session_id = $wc_session->get( 'cart_session_id' ); // Retrieving current session ID from WooCommerce Session

		if(!$this->current_session_exist_in_db( $cart_session_id )) {
			return $fields;
		}
		
		// Retrieve a single row with current customer ID
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT *
			FROM ' . $table_name . '
			WHERE session_id = %s',
				$cart_session_id
			)
		);

		if ( $row ) { // If we have a user with such session ID in the database

			$other_fields = @unserialize( $row->other_fields );

			if ( is_serialized( $row->location ) ) { // Since version 6.8
				   $location_data = unserialize( $row->location );
				   $country       = $location_data['country'];
				   $city          = $location_data['city'];
				   $postcode      = $location_data['postcode'];

			} else {
				$parts = explode( ',', $row->location ); // Splits the Location field into parts where there are commas
				if ( count( $parts ) > 1 ) {
					$country = $parts[0];
					$city    = trim( $parts[1] ); // Trim removes white space before and after the string
				} else {
					$country = $parts[0];
					$city    = '';
				}

				$postcode = '';
				if ( isset( $other_fields['ab_cart_billing_postcode'] ) ) {
					$postcode = $other_fields['ab_cart_billing_postcode'];
				}
			}

			( empty( $_POST['billing_first_name'] ) ) ? $_POST['billing_first_name'] = $row->name : '';
			( empty( $_POST['billing_last_name'] ) ) ? $_POST['billing_last_name']   = $row->surname : '';
			( empty( $_POST['billing_country'] ) ) ? $_POST['billing_country']       = $country : '';
			( empty( $_POST['billing_city'] ) ) ? $_POST['billing_city']             = $city : '';
			( empty( $_POST['billing_phone'] ) ) ? $_POST['billing_phone']           = $row->phone : '';
			( empty( $_POST['billing_email'] ) ) ? $_POST['billing_email']           = $row->email : '';
			( empty( $_POST['billing_postcode'] ) ) ? $_POST['billing_postcode']     = $postcode : '';

			if ( $other_fields ) {
				( empty( $_POST['billing_company'] ) ) ? $_POST['billing_company']         = $other_fields['ab_cart_billing_company'] : '';
				( empty( $_POST['billing_address_1'] ) ) ? $_POST['billing_address_1']     = $other_fields['ab_cart_billing_address_1'] : '';
				( empty( $_POST['billing_address_2'] ) ) ? $_POST['billing_address_2']     = $other_fields['ab_cart_billing_address_2'] : '';
				( empty( $_POST['billing_state'] ) ) ? $_POST['billing_state']             = $other_fields['ab_cart_billing_state'] : '';
				( empty( $_POST['shipping_first_name'] ) ) ? $_POST['shipping_first_name'] = $other_fields['ab_cart_shipping_first_name'] : '';
				( empty( $_POST['shipping_last_name'] ) ) ? $_POST['shipping_last_name']   = $other_fields['ab_cart_shipping_last_name'] : '';
				( empty( $_POST['shipping_company'] ) ) ? $_POST['shipping_company']       = $other_fields['ab_cart_shipping_company'] : '';
				( empty( $_POST['shipping_country'] ) ) ? $_POST['shipping_country']       = $other_fields['ab_cart_shipping_country'] : '';
				( empty( $_POST['shipping_address_1'] ) ) ? $_POST['shipping_address_1']   = $other_fields['ab_cart_shipping_address_1'] : '';
				( empty( $_POST['shipping_address_2'] ) ) ? $_POST['shipping_address_2']   = $other_fields['ab_cart_shipping_address_2'] : '';
				( empty( $_POST['shipping_city'] ) ) ? $_POST['shipping_city']             = $other_fields['ab_cart_shipping_city'] : '';
				( empty( $_POST['shipping_state'] ) ) ? $_POST['shipping_state']           = $other_fields['ab_cart_shipping_state'] : '';
				( empty( $_POST['shipping_postcode'] ) ) ? $_POST['shipping_postcode']     = $other_fields['ab_cart_shipping_postcode'] : '';
				( empty( $_POST['order_comments'] ) ) ? $_POST['order_comments']           = $other_fields['ab_cart_order_comments'] : '';
			}

			// Checking if Create account should be checked or not
			if ( isset( $other_fields['ab_cart_create_account'] ) ) {
				if ( $other_fields['ab_cart_create_account'] ) {
					add_filter( 'woocommerce_create_account_default_checked', '__return_true' );
				}
			}

			// Checking if Ship to a different location must be checked or not
			if ( isset( $other_fields['ab_cart_ship_elsewhere'] ) ) {
				if ( $other_fields['ab_cart_ship_elsewhere'] ) {
					add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
				}
			}
		}
		return $fields;
	}

	/**
	 * Increase captured abandoned cart count function.
	 *
	 * @return void
	 */
	function increase_captured_abandoned_cart_count() {
		$previously_captured_abandoned_cart_count = get_option( 'cart_captured_abandoned_cart_count' );
		update_option( 'cart_captured_abandoned_cart_count', $previously_captured_abandoned_cart_count + 1 ); // Increasing the count by one abandoned cart
	}

	/**
	 * Decrease captured abandoned cart count function.
	 *
	 * @param int $count count.
	 *
	 * @return void
	 */
	function decrease_captured_abandoned_cart_count( $count ) {
		if ( ! $count ) {
			$count = 1;
		}

		$previously_captured_abandoned_cart_count = get_option( 'cart_captured_abandoned_cart_count' );
		if ( $previously_captured_abandoned_cart_count > 0 ) {
			update_option( 'cart_captured_abandoned_cart_count', $previously_captured_abandoned_cart_count - $count ); // Decreasing the count by one abandoned cart
		}
	}

	/**
	 * Display exit intent form function.
	 *
	 * @return string
	 */
	function display_exit_intent_form() {
		$cart_insert = isset( $_POST['cart_insert'] ) ? sanitize_text_field( $_POST['cart_insert'] ) : false;
		if ( ! $this->exit_intent_enabled() || ! WC()->cart ) { // If Exit Intent disabled or WooCommerce cart does not exist
			return;
		}

		if ( WC()->cart->get_cart_contents_count() > 0 ) { // If the cart is not empty
			$current_user_is_admin = current_user_can( 'manage_options' );
			$output                = $this->build_exit_intent_output( $current_user_is_admin ); // Creating the Exit Intent output
			if ( $cart_insert ) { // In case function triggered using Ajax Add to Cart
				return wp_send_json_success( $output ); // Sending Output to Javascript function
			} else { // Outputing in case of page reload
				echo $output;
			}
		}
	}

	/**
	 * Remove exit intent form function.
	 *
	 * @return string
	 */
	function remove_exit_intent_form() {
		if ( ! WC()->cart ) {
			return;
		}
		if ( WC()->cart->get_cart_contents_count() === 0 ) { // If the cart is empty
			return wp_send_json_success( 'true' ); // Sending successful output to Javascript function
		} else {
			return wp_send_json_success( 'false' );
		}
	}

	/**
	 * Build exit intent output function.
	 *
	 * @param boolean $current_user_is_admin current_user_is_admin.
	 *
	 * @return string
	 */
	function build_exit_intent_output( $current_user_is_admin ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . SA_CART_TABLE_NAME;
		$cart_session_id = WC()->session->get( 'cart_session_id' ); // Retrieving current session ID from WooCommerce Session
		$main_color      = esc_attr( smsalert_get_option( 'cart_exit_intent_main_color', 'smsalert_abandoned_cart', '' ) );
		$inverse_color   = esc_attr( smsalert_get_option( 'cart_exit_intent_inverse_color', 'smsalert_abandoned_cart', '' ) );
		$image_id        = esc_attr( smsalert_get_option( 'cart_exit_intent_image', 'smsalert_abandoned_cart', '' ) );
		if ( ! $main_color ) {
			$main_color = '#e3e3e3';
		}
		if ( ! $inverse_color ) {
			$inverse_color = $this->invert_color( $main_color );
		}

		// Retrieve a single row with current customer ID
		$row = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT *
			FROM ' . $table_name . '
			WHERE session_id = %s',
				$cart_session_id
			)
		);

		if ( $row && ! $current_user_is_admin ) { // Exit if Abandoned Cart already saved and the current user is not admin
			return;
		}

		// In case the function is called via Ajax Add to Cart button
		// We must add wp_die() or otherwise the function does not return anything
		$enable_exit_intent = smsalert_get_option( 'enable_exit_intent_custom_page', 'smsalert_abandoned_cart', '0' );
		if ( $enable_exit_intent ) {
			$exit_intent_html = $this->get_template(
				'ab-cart-exit-intent.php',
				array(
					'main_color'         => '#ffffff',
					'inverse_color'      => '#000000',
					'image_id'           => '',
					'enable_custom_page' => 1,
				)
			);
		} else {
			$exit_intent_html = $this->get_template(
				'ab-cart-exit-intent.php',
				array(
					'main_color'         => $main_color,
					'inverse_color'      => $inverse_color,
					'image_id'           => $image_id,
					'enable_custom_page' => 0,
				)
			);
		}
		if ( isset( $_POST['cart_insert'] ) ) {
			$output = $exit_intent_html;
			die();
		} else {
			return $exit_intent_html;
		}
	}

	/**
	 * Exit intent enabled function.
	 *
	 * @return boolean
	 */
	function exit_intent_enabled() {
		$plugin_admin = new SA_Cart_Admin( SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION );

		$exit_intent_on        = smsalert_get_option( 'cart_exit_intent_status', 'smsalert_abandoned_cart', '0' );
		$test_mode_on          = smsalert_get_option( 'cart_exit_intent_test_mode', 'smsalert_abandoned_cart', '0' );
		$current_user_is_admin = current_user_can( 'manage_options' );

		if ( $test_mode_on && $current_user_is_admin ) {
			// Outputing Exit Intent for Testing purposes for Administrators
			return true;
		} elseif ( $exit_intent_on && ! is_user_logged_in() ) {
			// Outputing Exit Intent for all users who are not logged in
			return true;
		} else {
			// Do not Output Exit Intent
			return false;
		}
	}

	/**
	 * Invert color function.
	 *
	 * @param string $color color.
	 *
	 * @return string
	 */
	function invert_color( $color ) {
		$color = str_replace( '#', '', $color );
		if ( 6 !== strlen( $color ) ) {
			return '000000';
		}
		$rgb = '';
		for ( $x = 0; $x < 3; $x++ ) {
			$c    = 255 - hexdec( substr( $color, ( 2 * $x ), 2 ) );
			$c    = ( $c < 0 ) ? 0 : dechex( $c );
			$rgb .= ( strlen( $c ) < 2 ) ? '0' . $c : $c;
		}
		return '#' . $rgb;
	}

	/**
	 * Exit intent type function.
	 *
	 * @return string
	 */
	function exit_intent_type() {
		$exit_intent_type_value = 'cart-ei-center'; // Setting default class
		return $exit_intent_type_value;
	}

	/**
	 * Get exit intent template_path function.
	 *
	 * @param string $template_name template_name.
	 * @param string $template_path template_path.
	 * @param string $default_path  default_path.
	 *
	 * @return string
	 */
	function get_exit_intent_template_path( $template_name, $template_path = '', $default_path = '' ) {
		// Set variable to search in woocommerce-plugin-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'template/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = plugin_dir_path( __FILE__ ) . '../template/'; // Path to the template folder
		endif;

		// Search template file in theme folder.
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;
		return apply_filters( 'get_exit_intent_template_path', $template, $template_name, $template_path, $default_path );
	}

	/**
	 * Get template function.
	 *
	 * @param string $template_name template_name.
	 * @param array  $args          args.
	 * @param string $template_path template_path.
	 * @param string $default_path  default_path.
	 *
	 * @return bool
	 */
	function get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
		if ( is_array( $args ) && isset( $args ) ) {
			extract( $args );
		}
		$template_file = $this->get_exit_intent_template_path( $template_name, $tempate_path, $default_path );
		if ( ! file_exists( $template_file ) ) { // Handling error output in case template file does not exist
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '4.0' );
			return;
		}
		include $template_file;
	}
}
?>
