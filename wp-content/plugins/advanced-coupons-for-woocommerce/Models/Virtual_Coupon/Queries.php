<?php
namespace ACFWP\Models\Virtual_Coupon;

use ACFWP\Abstracts\Abstract_Main_Plugin_Class;
use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;
use ACFWP\Interfaces\Activatable_Interface;
use ACFWP\Models\Objects\Advanced_Coupon;
use ACFWP\Models\Objects\Virtual_Coupon;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of extending the coupon system of woocommerce.
 * It houses the logic of handling coupon url.
 * Public Model.
 *
 * @since 3.0
 */
class Queries implements Activatable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 3.0
     * @access private
     * @var Queries
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 3.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 3.0
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
     * @since 3.0
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
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 3.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Queries
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Safe way of getting the instance for the singleton class.
     *
     * @since 3.0
     * @access public
     *
     * @return Queries|null
     */
    public static function safe_get_instance()
    {
        return self::$_instance instanceof self ? self::$_instance : null;
    }

    /*
    |--------------------------------------------------------------------------
    | DB Creation.
    |--------------------------------------------------------------------------
     */

    /**
     * Create database table for virtual codes.
     *
     * @since 3.0
     * @access private
     */
    private function _create_db_table()
    {
        global $wpdb;

        if (get_option($this->_constants->VIRTUAL_COUPONS_DB_CREATED) === 'yes') {
            return;
        }

        $virtual_coupons_db = $wpdb->prefix . $this->_constants->VIRTUAL_COUPONS_DB_NAME;
        $charset_collate    = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $virtual_coupons_db (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            virtual_coupon TEXT NOT NULL,
            coupon_id bigint(20) NOT NULL,
            coupon_status varchar(20) NOT NULL,
            user_id bigint(20) DEFAULT 0 NULL,
            date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            date_expire datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;\n";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        update_option($this->_constants->VIRTUAL_COUPONS_DB_CREATED, 'yes');
    }

    /*
    |--------------------------------------------------------------------------
    | Queries.
    |--------------------------------------------------------------------------
     */

    /**
     * Get the default query arguments.
     *
     * @since 3.0
     * @access public
     *
     * @return array Default query arguments.
     */
    public function get_default_query_args()
    {
        return array(
            'status'        => '',
            'page'          => 1,
            'per_page'      => 10,
            'search'        => '',
            'coupon_id'     => 0,
            'user_id'       => 0,
            'sort_by'       => 'date_created',
            'sort_order'    => 'desc',
            'date_created'  => '',
            'coupon_status' => '',
        );
    }

    /**
     * Query virtual codes.
     *
     * @since 3.0
     * @access public
     *
     * @param array $args       Query arguments.
     * @param bool  $total_only Flag to only return the total count.
     * @return int|array Query results or count.
     */
    public function query_virtual_coupons($args = array(), $total_only = false)
    {
        global $wpdb;

        $args = wp_parse_args($args, $this->get_default_query_args());
        extract($args);

        $virtual_coupons_db = $wpdb->prefix . $this->_constants->VIRTUAL_COUPONS_DB_NAME;
        $offset             = ($page - 1) * $per_page;
        $sort_columns       = array(
            'code'         => 'v.virtual_coupon',
            'date_created' => 'v.date_created',
            'id'           => 'v.id',
            'user_id'      => 'v.user_id',
            'status'       => 'v.coupon_status',
            'date_expire'  => 'v.date_expire',
        );

        $where = array();

        // limit query by search key.
        if ($search) {
            $search  = str_replace(array('-', ' '), '|', sanitize_text_field($search));
            $where[] = "AND (v.id REGEXP '{$search}' OR v.virtual_coupon REGEXP '{$search}' OR v.coupon_id REGEXP '{$search}' OR v.coupon_status REGEXP '{$search}' OR v.user_id  REGEXP '{$search}')";
        }

        // limit query to a specific status type.
        if ($status) {
            $where[] = $wpdb->prepare("AND v.coupon_status = %s", $status);
        }

        // restrict to set main coupon status(es).
        if (!empty($coupon_status)) {
            $coupon_status = is_array($coupon_status) ? implode("','", $coupon_status) : $coupon_status;
            $where[]       = $wpdb->prepare("AND c.post_status IN (%s)", $coupon_status);
        }

        // limit query to specific coupon(s).
        if (is_array($coupon_id) && !empty($coupon_id)) {
            $where[] = $wpdb->prepare("AND v.coupon_id IN (%s)", implode(",", $coupon_id));
        } elseif ($coupon_id > 0) {
            $where[] = $wpdb->prepare("AND v.coupon_id = %d", $coupon_id);
        }

        // limit query to a specific user(s).
        if (is_array($user_id) && !empty($user_id)) {
            $where[] = $wpdb->prepare("AND v.user_id IN (%s)", implode(",", $user_id));
        } elseif ($user_id > 0) {
            $where[] = $wpdb->prepare("AND v.user_id = %d", $user_id);
        }

        if ($date_created) {
            $where[] = $wpdb->prepare("AND v.date_created >= %s", $date_created);
        }

        // query parts.
        $where_query = implode(" ", $where);

        if ($total_only) {
            $total_count = $wpdb->get_var("SELECT count(v.id) FROM {$virtual_coupons_db} AS v
                INNER JOIN {$wpdb->posts} AS c ON (c.ID = v.coupon_id AND c.post_type = 'shop_coupon')
                WHERE 1 {$where_query}
            ");

            if (is_null($total_count)) {
                return new \WP_Error(
                    'virtual_coupons_query_fail',
                    __('There was an error with the virtual coupons query. Please try again.', 'advanced-coupons-for-woocommerce'),
                    array('status' => 400)
                );
            }

            return $total_count;
        }

        // sort query
        $sort_column = isset($sort_columns[$sort_by]) ? $sort_columns[$sort_by] : 'v.date_created';
        $sort_type   = 'desc' === $sort_order ? 'DESC' : 'ASC';
        $sort_query  = "ORDER BY {$sort_column} {$sort_type}";

        // limits
        $limit_query = 1 <= $page ? $wpdb->prepare("LIMIT %d OFFSET %d", $per_page, $offset) : '';

        // build query.
        $query = "SELECT v.* FROM {$virtual_coupons_db} AS v
            INNER JOIN {$wpdb->posts} AS c ON (c.ID = v.coupon_id AND c.post_type = 'shop_coupon')
            INNER JOIN {$wpdb->postmeta} AS cm1 ON (cm1.post_id = c.ID AND cm1.meta_key = '_acfw_enable_virtual_coupons')
            WHERE 1 {$where_query}
            AND cm1.meta_value = 1
            {$sort_query}
            {$limit_query}
        ";

        $results = $wpdb->get_results($query, ARRAY_A);

        if (is_null($results)) {
            return new \WP_Error(
                'virtual_coupons_query_fail',
                __('There was an error with the virtual coupons query. Please try again.', 'advanced-coupons-for-woocommerce'),
                array('status' => 400)
            );
        }

        $virtual_coupons = array_map(function ($args) {
            return new Virtual_Coupon($args);
        }, $results);

        return $virtual_coupons;
    }

    /**
     * Bulk create virtual coupon codes.
     *
     * @since 3.0
     * @access public
     *
     * @param int $count     Number of codes to create.
     * @param int $coupon_id Main coupon ID.
     * @return int|WP_Error Number of created codes on success, Error object on failure.
     */
    public function bulk_create_virtual_coupons($count, $coupon_id)
    {
        global $wpdb;

        if (!$count || !$coupon_id) {
            return new \WP_Error(
                'missing_params', __('Required parameters are missing.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'count'     => $count,
                        'coupon_id' => $coupon_id,
                    ),
                )
            );
        }

        $coupon = new Advanced_Coupon($coupon_id);

        if (!$coupon->get_id()) {
            return new \WP_Error(
                'invalid_coupon',
                __("The provided coupon doesn't exist.", 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'count'     => $count,
                        'coupon_id' => $coupon_id,
                    ),
                )
            );
        }

        $virtual_codes = array();
        for ($i = 1; $i <= $count; $i++) {
            $virtual_codes[] = Virtual_Coupon::generate_code();
        }

        $date_created = current_time('mysql', true); // current time in GMT/UTC
        $columns      = array(
            'virtual_coupon' => '',
            'coupon_id'      => $coupon->get_id(),
            'coupon_status'  => 'pending',
            'user_id'        => 0,
            'date_created'   => $date_created,
        );

        $insert_query = '';
        foreach ($virtual_codes as $virtual_code) {

            if (!empty($insert_query)) {
                $insert_query .= ', ';
            }

            $insert_query .= $wpdb->prepare(
                "('%s', %d, '%s', %d, '%s')",
                $virtual_code,
                $columns['coupon_id'],
                $columns['coupon_status'],
                $columns['user_id'],
                $columns['date_created']
            );
        }

        $virtual_coupons_db = $wpdb->prefix . $this->_constants->VIRTUAL_COUPONS_DB_NAME;
        $table_columns      = implode(",", array_keys($columns));

        $created_rows = $wpdb->query("INSERT INTO {$virtual_coupons_db} ({$table_columns}) VALUES {$insert_query}");

        if (false === $created_rows) {
            return new \WP_Error(
                'bulk_create_virtual_coupons_failure',
                __("Bulk creation of virtual coupon codes has failed. Please try again.", 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'count'     => $count,
                        'coupon_id' => $coupon_id,
                    ),
                )
            );
        }

        // update coupon meta latest date bulk creation of virtual coupons.
        // this is needed to properly fetch the newest generated virtual coupons.
        update_post_meta($coupon->get_id(), $this->_constants->VIRTUAL_COUPONS_BULK_CREATE_DATE, $date_created);

        do_action('acfwp_virtual_coupons_bulk_created', $virtual_codes, $coupon->get_id(), $coupon);

        return $created_rows;
    }

    /**
     * Bulk delete virtual coupon codes.
     *
     * @since 3.0
     * @access public
     *
     * @param array $ids       List of virtual coupon IDs.
     * @param int   $coupon_id Main coupon ID.
     * @return int|WP_Error Number of created codes on success, Error object on failure.
     */
    public function bulk_delete_virtual_coupons($ids, $coupon_id)
    {
        global $wpdb;

        if (!is_array($ids) || empty($ids)) {
            return new \WP_Error(
                'missing_params',
                __('Required parameters are missing.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $ids,
                )
            );
        }

        $coupon = new Advanced_Coupon($coupon_id);

        if (!$coupon->get_code()) {
            return new \WP_Error(
                'invalid_coupon',
                __("The provided coupon doesn't exist.", 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'ids'       => $ids,
                        'coupon_id' => $coupon_id,
                    ),
                )
            );
        }

        $virtual_coupons_db = $wpdb->prefix . $this->_constants->VIRTUAL_COUPONS_DB_NAME;
        $coupon_id          = $coupon->get_id();
        $imploded_ids       = esc_sql(implode(',', $ids));
        $deleted_rows       = $wpdb->query("DELETE FROM {$virtual_coupons_db} WHERE coupon_id = {$coupon_id} AND id IN ({$imploded_ids})");

        if (false === $deleted_rows) {
            return new \WP_Error(
                'bulk_delete_virtual_coupons_failure',
                __("Bulk deletion of virtual coupon codes has failed. Please try again.", 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array(
                        'ids'       => $ids,
                        'coupon_id' => $coupon_id,
                    ),
                )
            );
        }

        do_action('acfwp_virtual_coupons_bulk_deleted', $ids, $coupon->get_id(), $coupon);

        return $deleted_rows;
    }

    /**
     * Query search customers.
     *
     * @since 3.0
     * @access private
     *
     * @param string $search Search query.
     * @return array List of customers.
     */
    public function query_search_customers($search)
    {
        global $wpdb;

        if (!$search) {
            return new \WP_Error(
                'missing_params',
                __('Required parameters are missing.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => array('search' => $search),
                )
            );
        }

        $concat_search = 'billing_|nickname|first_name|last_name';
        $query         = "SELECT c.*,
            GROUP_CONCAT( IF(cm.meta_key REGEXP '{$concat_search}', cm.meta_key, null) ORDER BY cm.meta_key DESC SEPARATOR '||' ) AS meta_keys,
            GROUP_CONCAT( IF(cm.meta_key REGEXP '{$concat_search}', IFNULL(cm.meta_value, ''), null) ORDER BY cm.meta_key DESC SEPARATOR '||' ) AS meta_values
            FROM {$wpdb->users} AS c
            INNER JOIN {$wpdb->usermeta} AS cm ON (c.ID = cm.user_id)
            WHERE 1
            GROUP BY c.ID
            HAVING (c.ID LIKE '%{$search}%'
                OR c.user_login LIKE '%{$search}%'
                OR c.user_nicename LIKE '%{$search}%'
                OR c.display_name LIKE '%{$search}'
                OR c.user_email LIKE '%{$search}%'
                OR meta_values LIKE '%{$search}%')
            ORDER BY c.user_registered DESC
        ";

        return array_map(function ($cid) {
            $customer = new \WC_Customer($cid);
            return array(
                'user_id'       => absint($cid),
                'user_fullname' => $this->_helper_functions->get_customer_name($customer),
                'user_email'    => $this->_helper_functions->get_customer_email($customer),
            );
        }, $wpdb->get_col($query));
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 3.0
     * @access public
     * @implements ACFWP\Interfaces\Activatable_Interface
     */
    public function activate()
    {
        $this->_create_db_table();
    }
}
