<?php
namespace ACFWF\Models\Store_Credits;

use ACFWF\Abstracts\Abstract_Main_Plugin_Class;
use ACFWF\Helpers\Helper_Functions;
use ACFWF\Helpers\Plugin_Constants;
use ACFWF\Interfaces\Model_Interface;
use ACFWF\Interfaces\Deactivatable_Interface;
use ACFWF\Models\Objects\Store_Credit_Entry;
use ACFWF\Models\Objects\Date_Period_Range;
use Automattic\WooCommerce\Utilities\NumberUtil;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Registry module.
 *
 * @since 4.0
 */
class Calculate implements Model_Interface, Deactivatable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that houses the model name to be used when calling publicly.
     *
     * @since 4.0
     * @access private
     * @var string
     */
    private $_model_name = 'Store_Credits_Calculate';

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 4.0
     * @access private
     * @var Registry
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 4.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 4.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 4.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this, $this->_model_name);
        $main_plugin->add_to_public_models($this, $this->_model_name);

    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 4.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Registry
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate methods
    |--------------------------------------------------------------------------
     */

    /**
     * Calculate store credit status and data based on the provided entries.
     *
     * @since 4.0
     * @access public
     *
     * @param array $entries List of entries.
     *
     */
    public function calculate_credits_status_and_sources($entries)
    {
        $status = array(
            'total'     => 0,
            'unclaimed' => 0,
            'claimed'   => 0,
            'expired'   => 0,
            'deducted'  => 0,
        );

        $sources = \ACFWF()->Store_Credits_Registry->get_initial_counters('increase');

        foreach ($entries as $entry) {
            $action = $entry['action'];

            if ('increase' === $entry['type']) {

                // increment all "increase" credits to the total counter.
                $status['total'] += $entry['amount'];

                // increment source counter.
                if (isset($sources[$action])) {
                    $sources[$action] += $entry['amount'];
                }

            } elseif ('decrease' === $entry['type']) {

                switch ($action) {
                    case 'discount':
                        $status['claimed'] += $entry['amount'];
                        break;
                    case 'expire':
                        $status['expired'] += $entry['amount'];
                        break;
                    case 'admin_decrease':
                        $status['deducted'] += $entry['amount'];
                        break;
                }
            }
        }

        // calculate unclaimed credits by deducting the claimed, expired and deducted credits from the total.
        $status['unclaimed'] = $status['total'] - $status['claimed'] - $status['expired'] - $status['deducted'];

        return array(
            'status'  => \ACFWF()->Store_Credits_Registry->format_store_credits_status_data($status),
            'sources' => \ACFWF()->Store_Credits_Registry->format_store_credits_sources_data($sources),
        );
    }

    /**
     * Calculate unclaimed store credit entries.
     *
     * @since 4.0
     * @access public
     *
     * @return float Total unclaimed store credits.
     */
    public function calculate_unclaimed_store_credits()
    {
        $increase = $this->_get_entries_sum(array('type' => 'increase'));
        $decrease = $this->_get_entries_sum(array('type' => 'decrease'));

        return max(0, $increase - $decrease);
    }

    /**
     * Calculate store credits period statistics (added and used).
     *
     * @deprecated 4.3
     * 
     * @since 4.0
     * @access public
     *
     * @param string $before_date Range start date (mysql date format).
     * @param string $after_date  Range end date (mysql date format).
     * @return array Calculated period statistics.
     */
    public function calculate_store_credits_period_statistics($before_date = '', $after_date = '')
    {
        \wc_deprecated_function('ACFWF\Models\Store_Credits\Calculate::' . __FUNCTION__, '4.3', 'ACFWF\Models\Store_Credits\Calculate::calculate_store_credits_report_period_statistics');
        $report_period = new Date_Period_Range($before_date, $after_date);
        return $this->calculate_store_credits_report_period_statistics($report_period);
    }

    /**
     * Calculate statistics for gift cards sold and claimed within a given date period range data.
     * 
     * @since 4.3
     * @access public
     * 
     * @param Date_Period_Range $report_period
     * @return array Calculated period statistics.
     */
    public function calculate_store_credits_report_period_statistics(Date_Period_Range $report_period)
    {
        $report_period->use_utc_timezone();

        $cache_key   = $report_period->generate_period_cache_key('acfwf_store_credits_period_stats::%s::%s');
        $cached_data = get_transient( $cache_key);

        // return cached data if already present in object cache.
        if (is_array($cached_data) && isset($cached_data['added_in_period']) && isset($cached_data['used_in_period'])) {
            $report_period->use_site_timezone(); // reset timezone back to site timezone.
            return $cached_data;
        }

        $period_params = array(
            'start_period' => $report_period->start_period->format('Y-m-d H:i:s'),
            'end_period'   => $report_period->end_period->format('Y-m-d H:i:s'),
        );

        $data = array(
            'added_in_period' => $this->_get_entries_sum(array_merge(array('type' => 'increase'), $period_params)),
            'used_in_period'  => $this->_get_entries_sum(array_merge(array('type' => 'decrease'), $period_params)),
        );

        // save data to the cache for a maximum of one day.
        set_transient($cache_key, $data, DAY_IN_SECONDS);

        // reset timezone back to site timezone.
        $report_period->use_site_timezone();

        return $data;
    }

    /**
     * Get customer store credit balance.
     *
     * @since 4.0
     * @since 4.2 Add hook to trigger actions based on customer's current store credits balance.
     * @since 4.2.1 Wrap returned value with NumberUtil::round round function so it returns precise value.
     * @access private
     *
     * @param int $user_id User ID
     * @param bool $is_fresh Flag if need to calculate fresh again.
     * @param float Customer balance.
     */
    public function get_customer_balance($user_id, $is_fresh = false)
    {
        /**
         * expire user's credits when user last active is not valid anymore.
         * We also return 0.00 as user's balance here as user's full balance will be expired in this scenario.
         */
        if (!$this->validate_user_last_active($user_id)) {
            $this->expire_user_credit_balance($user_id);
            return 0.0;
        }

        $cached_balance = get_user_meta($user_id, Plugin_Constants::STORE_CREDIT_USER_BALANCE, true);

        if ($is_fresh || false !== $cached_balance) {
            $balance = $this->_calculate_customer_balance($user_id);
            update_user_meta($user_id, Plugin_Constants::STORE_CREDIT_USER_BALANCE, $balance);

            /**
             * Filter for doing additional actions based on customer's new balance.
             * We are only running this filter when the user's balance is calculated and is not fetched from cache.
             */
            do_action('acfw_get_customer_store_credit_balance', $balance, $user_id);

        } else {
            $balance = $cached_balance;
        }

        return NumberUtil::round($balance, wc_get_price_decimals());
    }

    /**
     * Freshly calculate customer store credit balance.
     *
     * @since 4.0
     * @since 4.2 Add action hooks to trigger actions based on user's total earned/used store credits.
     * @access private
     *
     * @param int  $user_id    User ID
     * @param bool $skip_hooks Flag to skip triggering the hooks.
     * @return float Customer balance.
     */
    private function _calculate_customer_balance($user_id, $skip_hooks = false)
    {
        $total_earned = $this->_get_entries_sum(array('user_id' => $user_id, 'type' => 'increase'));
        $total_used   = $this->_get_entries_sum(array('user_id' => $user_id, 'type' => 'decrease'));

        /**
         * Allow third party plugins to run actions based on the total value of store credits earned and/or used by the customer.
         * the hook should only run when doing normal calculations of customer's balance.
         */
        if (!$skip_hooks) {
            do_action('acfw_user_total_store_credits_earned', $total_earned, $user_id);
            do_action('acfw_user_total_store_credits_used', $total_used, $user_id);
        }
        
        return max(0, $total_earned - $total_used);
    }

    /*
    |--------------------------------------------------------------------------
    | Credit balance expiry
    |--------------------------------------------------------------------------
     */

    /**
     * Get user last activate datetime.
     *
     * @since 4.0
     * @access public
     *
     * @param int $user_id User ID.
     * @return WC_DateTime Datetime object.
     */
    public function get_last_active($user_id)
    {
        global $wpdb;

        if (!isset($this->_last_active[$user_id])) {
            $db_table  = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;
            $last_date = $wpdb->get_var($wpdb->prepare("SELECT entry_date FROM {$db_table} WHERE user_id = %d ORDER BY entry_date DESC", $user_id));

            $this->_last_active[$user_id] = new \WC_DateTime($last_date, new \DateTimeZone('UTC'));
            $this->_last_active[$user_id]->setTimezone(new \DateTimeZone($this->_helper_functions->get_site_current_timezone()));
        }

        return $this->_last_active[$user_id];
    }

    /**
     * Vaidate user last activate date.
     *
     * @since 4.0
     * @access public
     *
     * @param int $user_id User ID.
     * @return bool True if still active, false otherwise.
     */
    public function validate_user_last_active($user_id)
    {
        $last_active      = $this->get_last_active($user_id);
        $expire_period    = 365; // TODO: change to setting
        $datetime         = new \DateTime("now", new \DateTimeZone($this->_helper_functions->get_site_current_timezone()));
        $expire_timestamp = $datetime->getTimestamp() - ($expire_period * DAY_IN_SECONDS);

        return $last_active->getTimestamp() > $expire_timestamp;
    }

    /**
     * Expire user's store credit balance.
     *
     * @since 4.0
     * @access public
     *
     * @param int $user_id
     */
    public function expire_user_credit_balance($user_id)
    {
        $previous_balance = $this->_calculate_customer_balance($user_id, true);
        $expire_entry     = new Store_Credit_Entry();

        $expire_entry->set_prop('amount', $previous_balance);
        $expire_entry->set_prop('user_id', $user_id);
        $expire_entry->set_prop('type', 'decrease');
        $expire_entry->set_prop('action', 'expire');
        $check = $expire_entry->save(true);

        if (!is_wp_error($check)) {
            do_action('acfw_expire_user_store_credits', $previous_balance, $user_id, $expire_entry);
            update_user_meta($user_id, Plugin_Constants::STORE_CREDIT_USER_BALANCE, 0);
        }
    }

    /**
     * Maybe run check/validate for all user's store credit balance needs to be expired or not.
     * This function should only run once per day.
     *
     * @since 4.0
     * @access public
     *
     * @return bool True if it should run, false otherwise.
     */
    public function maybe_check_on_all_users_balance_expiry()
    {
        global $wpdb;

        $last_run = get_option(Plugin_Constants::STORE_CREDITS_EXPIRY_CHECK_DATE, null);

        // only run function once a day.
        if ($last_run && $last_run > current_time('timestamp', true) - DAY_IN_SECONDS) {
            return;
        }

        $expire_period    = 365; // TODO: change to setting
        $expire_timestamp = current_time('timestamp', true) - ($expire_period * DAY_IN_SECONDS);

        $db_table = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;
        $raw_data = $wpdb->get_results(
            "SELECT u.ID, s.entry_date FROM wp_users AS u
            INNER JOIN {$db_table} AS s ON (u.ID = s.user_id)
            WHERE 1
            GROUP BY u.ID
            ORDER BY s.entry_date DESC"
        );

        // filter users to only list IDs of most recent entry date is earlier than the expiry date value.
        $data = array_filter($raw_data, function ($r) use ($expire_timestamp) {
            $datetime = new \DateTime($r->entry_date);
            return $datetime->getTimestamp() <= $expire_timestamp;
        });

        // validate expiration for each user.
        if (!empty($data)) {
            foreach ($data as $row) {
                $user_id = absint($row->ID);
                if (!$this->validate_user_last_active($user_id)) {
                    $this->expire_user_credit_balance($user_id);
                }
            }
        }

        update_option(Plugin_Constants::STORE_CREDITS_EXPIRY_CHECK_DATE, current_time('timestamp', true));
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
     */

    /**
     * Get all store credit entries.
     *
     * @since 4.0
     * @access private
     *
     * @return array|WP_Error Store credit entries on success, error ojbect on failure.
     */
    private function _get_all_entries($start_period = '', $end_period = '')
    {
        global $wpdb;

        $db_table = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;
        $query    = "SELECT entry_type,entry_action,entry_amount FROM {$db_table} WHERE 1";

        if ($start_period && $end_period) {
            $query .= $wpdb->prepare(" AND entry_date BETWEEN %s AND %s", $start_period, $end_period);
        }

        $raw_data = $wpdb->get_results($query, ARRAY_A);

        if (!is_array($raw_data)) {
            return new \WP_Error(
                'acfw_query_all_store_credit_entries_fail',
                __('There was an error loading store credits data.', 'advanced-coupons-for-woocommerce-free'),
                array('status' => 400)
            );
        }

        return array_map(function ($r) {
            return array(
                'type'   => $r['entry_type'],
                'action' => $r['entry_action'],
                'amount' => floatval($r['entry_amount']),
            );
        }, $raw_data);
    }

    /**
     * Get sum of all entries based on the provided parameters.
     *
     * @since 4.0
     * @access private
     *
     * @param array $params Query parameters.
     * @return float Sum of queried entries.
     */
    private function _get_entries_sum($params = array())
    {
        global $wpdb;

        $params = wp_parse_args($params, array(
            'user_id'      => 0,
            'type'         => '',
            'start_period' => '',
            'end_period'   => '',
            'precision'    => $this->get_decimal_precision(),
            'decimals'     => wc_get_price_decimals(),
        ));
        extract($params);

        $user_query       = $user_id ? $wpdb->prepare("AND user_id = %d", $user_id) : '';
        $type_query       = $type ? $wpdb->prepare("AND entry_type = %s", $type) : '';
        $period_query     = $start_period && $end_period ? $wpdb->prepare("AND entry_date BETWEEN %s AND %s", $start_period, $end_period) : '';
        $store_credits_db = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;

        // build the querys
        $query = $wpdb->prepare("SELECT SUM(CONVERT(entry_amount, DECIMAL(%d,%d)))
            FROM {$store_credits_db}
            WHERE 1
            {$user_query} {$type_query} {$period_query}
        ", $precision, $decimals);

        return (float) $wpdb->get_var($query);
    }

    /**
     * Delete store credits cached data.
     * 
     * @since 4.3
     * @access public
     */
    public function delete_store_credits_cached_data()
    {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%acfwf_store_credits_period_stats%'");
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate query constants
    |--------------------------------------------------------------------------
     */

    /**
     * Get the decimal precision value used in converting "string" to "decimal" type in MySQL.
     * Note: the entry_amount value is saved as string in the db. We need to convert the values into a decimal type in queries which requires a decimal value.
     *       for reference: https://www.mysqltutorial.org/mysql-decimal/
     *
     * @since 4.0
     * @access public
     *
     * @return int Decimal precision
     */
    public function get_decimal_precision()
    {
        return apply_filters('acfw_store_credits_decimal_precision', 19);
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Contract for deactivate.
     *
     * @since 1.0.1
     * @access public
     * @implements ACFWF\Interfaces\Deactivatable_Interface
     */
    public function deactivate() {
        $this->delete_store_credits_cached_data();
    }

    /**
     * Execute Store_Credits class.
     *
     * @since 4.0
     * @access public
     * @inherit ACFWF\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('acfw_store_credits_total_changed', array($this, 'delete_store_credits_cached_data'));
    }

}
