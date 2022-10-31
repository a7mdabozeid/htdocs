<?php

namespace LPFW\Objects;

/**
 * Model that houses the data model of a point entry object.
 *
 * @since 1.2
 */
class Point_Entry
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the point entry data.
     * 
     * @since 1.2
     * @access protected
     * @var array
     */
    protected $_data = array(
        'id'        => 0,
        'points'    => 0,
        'user_id'   => 0,
        'object_id' => 0,
        'type'      => '',
        'action'    => '',
        'date'      => '',
        'activity'  => '',
    );

    /**
     * Property that holds the various objects utilized by the virtual coupon.
     *
     * @since 1.2
     * @access protected
     * @var array
     */
    protected $_objects = array(
        'user'     => null,
        'date'     => null,
        'registry' => null,
    );

    /**
     * Stores boolean if the data has been read from the database or not.
     *
     * @since 1.2
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
     * @since 1.2
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

        // if full data already provided in an array, then we just skip the other parts.
        if (is_array($arg)) {
            $arg = wp_parse_args($arg, $this->_data);
            $this->_format_and_set_data($arg);
            return;
        }

        $this->set_id(absint($arg));
        $this->_read_data_from_db();
    }

    /**
     * Read data from database.
     *
     * @since 1.2
     * @access protected
     */
    protected function _read_data_from_db()
    {
        global $wpdb;

        // don't proceed if ID is not available.
        if (!$this->get_id()) {
            return;
        }

        $lp_entries_db = $wpdb->prefix . \LPFW()->Plugin_Constants->DB_TABLE_NAME;
        $result        = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$lp_entries_db} WHERE entry_id = %d",
                $this->get_id()
            ),
            ARRAY_A
        );

        if (!$result) {
            return;
        }

        $this->_format_and_set_data($result);
    }

    /**
     * Format raw data and set to property.
     *
     * @since 1.2
     * @access protected
     *
     * @param array $raw_data
     */
    protected function _format_and_set_data($raw_data)
    {
        $id        = absint($raw_data['entry_id']);
        $action    = $raw_data['entry_type'] === 'redeem' && !$raw_data['entry_action'] ? 'coupon' : $raw_data['entry_action'];
        $object_id = !$raw_data['object_id'] && $action === 'user_register' ? absint($raw_data['user_id']) : absint($raw_data['object_id']);

        /**
         * @since 1.4 admin_adjust action key will not be used anymore, and is replaced by admin_decrease and
         *            admin_increase keys respectively.
         */
        if ('admin_adjust' === $action) {
            $action = $raw_data['entry_type'] === 'redeem' ? 'admin_decrease' : 'admin_increase';
        }

        $this->_data = array(
            'id'        => $id,
            'user_id'   => absint($raw_data['user_id']),
            'object_id' => $object_id,
            'type'      => sanitize_text_field($raw_data['entry_type']),
            'action'    => sanitize_text_field($action),
            'date'      => sanitize_text_field($raw_data['entry_date']),
            'points'    => intval($raw_data['entry_amount']),
            'user_id'   => absint($raw_data['user_id']),
        );

        $this->_read = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Getter Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get value for a given property and context.
     *
     * @since 1.2
     * @access protected
     *
     * @param string $prop    Property name.
     * @param string $context 'edit' or 'view' context.
     * @return mixed Property value.
     */
    public function get_prop($prop, $context = 'view')
    {
        if (!array_key_exists($prop, $this->_data)) {
            throw new \Exception(sprintf("%s property doesn't exist in Point_Entry class", $prop), 400);
        }

        if ('edit' === $context) {
            return $this->_data[$prop];
        }

        return apply_filters('lpfw_point_entry_get_' . $prop, $this->_data[$prop], $prop);
    }

    /**
     * Get point entry ID.
     *
     * @since 1.2
     * @access public
     *
     * @return int Point entry ID.
     */
    public function get_id()
    {
        return $this->_data['id'];
    }

    /**
     * Get point entry date.
     *
     * @since 1.2
     * @access public
     *
     * @return null|WC_DateTime
     */
    public function get_date()
    {
        if (!$this->_data['date'] || '0000-00-00 00:00:00' === $this->_data['date']) {
            return null;
        }

        if (is_null($this->_objects['date'])) {
            $this->_set_wc_datetime_object();
        }

        return $this->_objects['date'];
    }

    /**
     * Get point entry registry data.
     *
     * @since 1.4
     * @access public
     *
     * @return array|bool Source type data on success, false when not available.
     */
    public function get_registry()
    {
        if (\is_null($this->_objects['registry'])) {

            if ("redeem" === $this->_data['type']) {
                $this->_objects['registry'] = \LPFW()->Types->get_point_redeem_action_types($this->_data['action']);
            } else {
                $this->_objects['registry'] = \LPFW()->Types->get_point_earn_source_types($this->_data['action']);
            }
        }

        return $this->_objects['registry'];
    }

    /**
     * Get formatted data for API and other display UI.
     *
     * @since 1.2
     * @access public
     *
     * @return array Formatted data.
     */
    public function get_formatted_data($context = 'frontend', $date_format = 'F j, Y g:i a')
    {
        $date           = $this->get_date();
        $registry       = $this->get_registry();
        $activity_label = \LPFW()->Types->get_activity_label($registry, $this->_data['type']);

        return array(
            'id'        => $this->get_id(),
            'object_id' => $this->_data['object_id'],
            'action'    => $this->_data['action'],
            'date'      => $date ? $date->date_i18n($date_format) : '',
            'activity'  => $activity_label,
            'points'    => $this->_data['points'],
            'rel_link'  => $this->get_related_object_link($context),
            'rel_label' => $this->get_related_object_label($context),
        );
    }

    /**
     * Get related object link.
     *
     * @since 1.2
     * @access public
     *
     * @return string Object link.
     */
    public function get_related_object_link($context = 'frontend')
    {
        $constants = $this->get_registry();

        if ($constants && $constants->related) {

            $key      = 'admin' === $context ? 'admin_link_callback' : 'link_callback';
            $callback = isset($constants->related[$key]) ? $constants->related[$key] : false;

            if ($callback) {
                return \call_user_func($callback, $this->_data['object_id'], 'link');
            }
        }

        return '';
    }

    /**
     * Get related object label.
     *
     * @since 1.2
     * @access public
     *
     * @return string Label.
     */
    public function get_related_object_label($context = 'frontend')
    {
        $constants = $this->get_registry();

        if ($constants && $constants->related) {

            $key   = 'admin' === $context ? 'admin_label' : 'label';
            $label = isset($constants->related[$key]) ? $constants->related[$key] : '';

            // handle admin increase/decrease entries.
            if ('admin_decrease' === $this->_data['action'] || 'admin_increase' === $this->_data['action']) {
                $admin = \LPFW()->Helper_Functions->get_customer_name($this->_data['object_id']);
                return sprintf($label, $admin);
            }

            // handle coupon frontend label.
            if (!is_admin() && 'coupon' === $this->_data['action']) {
                $coupon = $this->_data['object_id'] ? new \WC_Coupon($this->_data['object_id']) : null;
                return $coupon ? sprintf($label, $coupon->get_code()) : '—';
            }

            return $label;
        }

        return '—';
    }

    /*
    |--------------------------------------------------------------------------
    | Setter Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Set point entry ID.
     *
     * @since 1.2
     * @access public
     *
     * @param int $id Virtual code ID.
     */
    public function set_id($id)
    {
        $this->_data['id'] = absint($id);
    }

    /**
     * Set data property value.
     *
     * @since 1.2
     * @access public
     *
     * @param string $prop  Property name.
     * @param mixed  $value Property value.
     * @return bool True if prop was set, false otherwise.
     */
    public function set_prop($prop, $value)
    {
        if (
            is_null($value) ||
            !array_key_exists($prop, $this->_data) ||
            gettype($value) !== gettype($this->_data[$prop])
        ) {
            return false;
        }

        $this->_data[$prop] = 'int' === gettype($this->_data[$prop]) ? absint($value) : $value;
        return true;
    }

    /**
     * Set datetime prop value that's using the site timezone, and save it as UTC equivalent.
     *
     * @since 1.2
     * @access public
     *
     * @param string $prop  Property name.
     * @param mixed  $value Property value.
     * @param string $format Date format.
     * @return bool True if prop was set, false otherwise.
     */
    public function set_date_prop($prop, $value, $format = 'Y-m-d H:i:s')
    {
        if (!$value) {
            return false;
        }

        $datetime = \DateTime::createFromFormat($format, $value, new \DateTimeZone(\LPFW()->Helper_Functions->get_site_current_timezone()));
        $datetime->setTimezone(new \DateTimeZone('UTC'));

        $check = $this->set_prop($prop, $datetime->format('Y-m-d H:i:s'));
        if ($check) {
            $this->_set_wc_datetime_object();
        }

        return $check;
    }

    /*
    |--------------------------------------------------------------------------
    | Save/Delete Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Create new point entry.
     *
     * @since 1.4
     * @access protected
     *
     * @return int|WP_Error Entry ID if successfull, error object on failure.
     */
    protected function create()
    {
        global $wpdb;

        $check = $wpdb->insert(
            $wpdb->prefix . \LPFW()->Plugin_Constants->DB_TABLE_NAME,
            array(
                'user_id'      => $this->_data['user_id'],
                'entry_date'   => $this->_data['date'] ? $this->_data['date'] : current_time('mysql', true),
                'entry_type'   => $this->_data['type'],
                'entry_action' => $this->_data['action'],
                'entry_amount' => $this->_data['points'],
                'object_id'    => $this->_data['object_id'],
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'lpfw_error_create_point_entry',
                __('There was an error trying to increase points for user.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('lpfw_point_entry_created', $this->_data, $this);

        $this->set_id($wpdb->insert_id);
        return $this->get_id();
    }

    /**
     * Update point entry data.
     *
     * @since 1.4
     * @access protected
     *
     * @return bool|WP_Error True if successfull, error object on failure.
     */
    protected function update()
    {
        global $wpdb;

        $check = $wpdb->update(
            $wpdb->prefix . \LPFW()->Plugin_Constants->DB_TABLE_NAME,
            array(
                'user_id'      => $this->_data['user_id'],
                'entry_date'   => $this->_data['date'] ? $this->_data['date'] : current_time('mysql', true),
                'entry_type'   => $this->_data['type'],
                'entry_action' => $this->_data['action'],
                'entry_amount' => $this->_data['points'],
                'object_id'    => $this->_data['object_id'],
            ),
            array(
                'entry_id' => $this->get_id(),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
            ),
            array(
                '%d',
            )
        );

        if (false === $check) {
            return new \WP_Error(
                'lpfw_error_update_point_entry',
                __('There was an error updating the point entry.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('lpfw_point_entry_updated', $this->_data, $this);

        return true;
    }

    /**
     * Save point entry.
     *
     * @since 1.4
     * @access protected
     *
     * @return int|WP_Error ID if successfull, error object on fail.
     */
    public function save()
    {
        if (!$this->get_registry()) {
            return new \WP_Error(
                'lpfw_invalid_action_point_entry',
                __('The earn/redeem action provided is invalid.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        if ($this->get_id()) {
            $check = $this->update();
        } else {
            $check = $this->create();
        } 

        do_action('lpfw_loyalty_points_total_changed', $this);

        return $check;
    }

    /**
     * Delete virtual code.
     *
     * @since 1.2
     * @access protected
     *
     * @return bool|WP_Error true if successfull, error object on fail.
     */
    public function delete()
    {
        global $wpdb;

        if (!$this->get_id()) {
            return new \WP_Error(
                'lpfw_missing_id_point_entry',
                __('The point entry requires a valid ID to proceed.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $check = $wpdb->delete(
            $wpdb->prefix . \LPFW()->Plugin_Constants->DB_TABLE_NAME,
            array(
                'entry_id' => $this->get_id(),
            ),
            array(
                '%d',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'lpfw_error_delete_point_entry',
                __('There was an error deleting the point entry.', 'loyalty-program-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('lpfw_point_entry_deleted', $this->_data, $this);
        do_action('lpfw_loyalty_points_total_changed', $this);

        $this->set_id(0);
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Create a WC_DateTime object for the point entry date.
     *
     * @since 1.2
     * @access protected
     */
    protected function _set_wc_datetime_object()
    {
        $this->_objects['date'] = new \WC_DateTime($this->_data['date'], new \DateTimeZone('UTC'));
        $this->_objects['date']->setTimezone(new \DateTimeZone(\LPFW()->Helper_Functions->get_site_current_timezone()));
    }
}
