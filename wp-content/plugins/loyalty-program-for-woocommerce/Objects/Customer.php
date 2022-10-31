<?php

namespace LPFW\Objects;

/**
 * Model that houses the data model of a point entry object.
 *
 * @since 1.6
 */
class Customer
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the points data for customer.
     * 
     * @since 1.6
     * @access protected
     * @var array
     */
    protected $_points = 0;

    /**
     * Property that holds the various objects utilized by the class.
     *
     * @since 1.6
     * @access protected
     * @var array
     */
    protected $_objects = array(
        'user'        => null,
        'last_active' => null,
        'expiry'      => null,
    );

    /**
     * Stores boolean if the data has been read from the database or not.
     *
     * @since 1.6
     * @access private
     * @var object
     */
    protected $_read = false;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.6
     * @access public
     *
     * @param mixed $arg Point entry ID or raw data.
     */
    public function __construct($arg = 0)
    {
        // skip reading data if no valid argument
        if (!$arg || empty($arg)) {
            return;
        }

        // attach WP_User object to property.
        $this->_objects['user'] = $arg instanceof \WP_User ? $arg : new \WP_User($arg);

        // fetch points cache.
        $this->_points = intval(get_user_meta($this->get_id(), \LPFW()->Plugin_Constants->USER_TOTAL_POINTS, true));
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
     */

    /**
     * Get customer's ID.
     * 
     * @since 1.6
     * @access public
     * 
     * @return int User ID.
     */
    public function get_id()
    {
        return $this->_objects['user'] instanceof \WP_User ? $this->_objects['user']->ID : 0;
    }

    /**
     * Get customer total remaining loyalty points.
     * 
     * @since 1.6
     * @access public
     * 
     * @param bool $fresh Flag to check if fresh data should be calculated.
     * @return int Total points.
     */
    public function get_points($fresh = false)
    {
        if ($fresh) {
            $this->_calculate_points();
        }

        return $this->_points;
    }

    /**
     * Get user infor from WP_User object.
     * 
     * @since 1.6
     * @access public
     * 
     * @param string $prop Property name.
     * @return mixed User info.
     */
    public function get_user_info($prop)
    {
        if ($this->_objects['user'] instanceof \WP_User && $this->_objects['user']->$prop) {
            return $this->_objects['user']->$prop;
        }

        return '';
    }

    /**
     * Get last active datetime object.
     * 
     * @since 1.6
     * @access public
     * 
     * @return WC_DateTime Last active date object.
     */
    public function get_last_active()
    {
        if (!$this->_objects['last_active'] instanceof \WC_DateTime) {
            $this->_fetch_last_active();
        }

        return $this->_objects['last_active'];
    }

    /**
     * Get points expiry datetime object.
     * 
     * @since 1.6
     * @access public
     * 
     * @return WC_DateTime Expiry date object.
     */
    public function get_points_expiry()
    {
        if (!$this->_objects['expiry']) {
            $this->_calculate_points_expire_date();
        }
        
        return $this->_objects['expiry'];
    }

    /**
     * Get user data for API.
     * 
     * @since 1.6
     * @access public
     * 
     * @return array User data.
     */
    public function get_user_data_for_api()
    {
        $date_format = \LPFW()->Helper_Functions->get_default_datetime_format();
        return array(
            'key'        => (string) $this->get_id(),
            'id'         => $this->get_id(),
            'first_name' => $this->get_user_info('first_name'),
            'last_name'  => $this->get_user_info('last_name'),
            'name'       => $this->get_user_info('first_name') . ' ' . $this->get_user_info('last_name'),
            'email'      => $this->get_user_info('user_email'),
            'points'     => $this->get_points(),
            'expiry'     => $this->get_points() && is_object($this->get_points_expiry()) ? $this->get_points_expiry()->format($date_format) : '',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Utility methods
    |--------------------------------------------------------------------------
     */

    /**
     * Fetch last active datetime for customer fresh from the database.
     * 
     * @since 1.6
     * @access public
     * 
     * @return WC_DateTime Last active date object.
     */
    private function _fetch_last_active()
    {
        global $wpdb;

        $lp_entries_db    = $wpdb->prefix . \LPFW()->Plugin_Constants->DB_TABLE_NAME;
        $last_active_date = $wpdb->get_var($wpdb->prepare("SELECT entry_date FROM {$lp_entries_db} WHERE user_id = %d ORDER BY entry_date DESC", $this->get_id()));

        if ($last_active_date) {
            $datetime = new \WC_DateTime($last_active_date, new \DateTimeZone('UTC'));
            $datetime->setTimezone(new \DateTimeZone(\LPFW()->Helper_Functions->get_site_current_timezone()));
            $this->_objects['last_active'] = $datetime;
        }
    }

    /**
     * Calculate customer's points expiry date.
     * 
     * @since 1.6
     * @access private
     */
    private function _calculate_points_expire_date()
    {
        if (!$this->get_last_active()) {
            return;
        }

        $valid_days = (int) get_option(\LPFW()->Plugin_Constants->INACTIVE_DAYS_POINTS_EXPIRE, 365);
        $timestamp  = $this->get_last_active()->getTimestamp() + ($valid_days * DAY_IN_SECONDS);

        $this->_objects['expiry'] = new \WC_DateTime("now", new \DateTimeZone(\LPFW()->Helper_Functions->get_site_current_timezone()));
        $this->_objects['expiry']->setTimestamp($timestamp);
    }

    /**
     * Calculate customer's loyalty points fresh from the database.
     * 
     * @since 1.6
     * @access private
     */
    private function _calculate_points()
    {
        global $wpdb;

        $lp_entries_db = $wpdb->prefix . \LPFW()->Plugin_Constants->DB_TABLE_NAME;
        $user_id       = $this->get_id();

        $data = $wpdb->get_results(
                $wpdb->prepare(
                "(SELECT SUM(entry_amount) FROM `$lp_entries_db` WHERE user_id = %d AND entry_type = 'earn') 
                UNION (SELECT SUM(entry_amount) FROM `$lp_entries_db` WHERE user_id = %d AND entry_type = 'redeem'
                )", 
                $user_id, 
                $user_id
            ),
            ARRAY_N
        );

        // subtract reedeemed from earned. if value is negative, then return zero.
        $earned = isset($data[0]) ? intval($data[0][0]) : 0;

        // if second result is not set, it means that the total redeemed is equal to the total earned.
        $redeem = isset($data[1]) ? intval($data[1][0]) : $earned; 

        $this->_points = max(0, $earned - $redeem);
    }
}