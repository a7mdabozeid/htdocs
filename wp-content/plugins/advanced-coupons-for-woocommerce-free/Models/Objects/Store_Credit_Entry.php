<?php

namespace ACFWF\Models\Objects;

use ACFWF\Helpers\Plugin_Constants;

/**
 * Model that houses the data model of a store credit entry object.
 *
 * @since 4.0
 */
class Store_Credit_Entry
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the point entry data.
     *
     * @since 4.0
     * @access protected
     * @var array
     */
    protected $_data = array(
        'id'        => 0,
        'amount'    => 0.0,
        'user_id'   => 0,
        'object_id' => 0,
        'type'      => '',
        'action'    => '',
        'date'      => '',
        'note'      => '',
    );

    /**
     * Property that holds the various objects utilized by the virtual coupon.
     *
     * @since 4.0
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
     * @since 4.0
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
     * @since 4.0
     * @access public
     *
     * @param mixed $arg Store credit entry ID or raw data.
     */
    public function __construct($arg = 0)
    {
        // skip reading data if no valid argument
        if (!$arg || empty($arg)) {
            return;
        }

        // if full data already provided in an array, then we just skip the other parts.
        if (is_array($arg)) {
            $this->_format_and_set_data($arg);
            return;
        }

        $this->set_id(absint($arg));
        $this->_read_data_from_db();
    }

    /**
     * Read data from database.
     *
     * @since 4.0
     * @access protected
     */
    protected function _read_data_from_db()
    {
        global $wpdb;

        // don't proceed if ID is not available.
        if (!$this->get_id()) {
            return;
        }

        $store_credits_db = $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME;
        $result           = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$store_credits_db} WHERE entry_id = %d",
                $this->get_id()
            )
        );

        if (!$result) {
            return;
        }

        $this->_format_and_set_data($result);
    }

    /**
     * Format raw data and set to property.
     *
     * @since 4.0
     * @access protected
     *
     * @param array $raw_data
     */
    protected function _format_and_set_data($raw_data)
    {
        $raw_data = wp_parse_args($raw_data, $this->_data);

        $this->_data = array(
            'id'        => absint($raw_data['entry_id']),
            'user_id'   => absint($raw_data['user_id']),
            'object_id' => absint($raw_data['object_id']),
            'type'      => $raw_data['entry_type'],
            'action'    => $raw_data['entry_action'],
            'date'      => $raw_data['entry_date'],
            'amount'    => floatval($raw_data['entry_amount']),
            'note'      => $raw_data['entry_note'],
        );

        $this->_read = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Getter Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get store credit entry ID.
     *
     * @since 4.0
     * @access public
     *
     * @return int Point entry ID.
     */
    public function get_id()
    {
        return $this->_data['id'];
    }

    /**
     * Get value for a given property and context.
     *
     * @since 4.0
     * @access protected
     *
     * @param string $prop    Property name.
     * @param string $context 'edit' or 'view' context.
     * @return mixed Property value.
     */
    public function get_prop($prop, $context = 'view')
    {
        if (!array_key_exists($prop, $this->_data)) {
            throw new \Exception(sprintf("%s property doesn't exist in Store_Credit_Entry class", $prop), 400);
        }

        if ('edit' === $context) {
            return $this->_data[$prop];
        }

        return apply_filters('acfw_store_credit_entry_get_' . $prop, $this->_data[$prop], $prop);
    }

    /**
     * Get point entry date.
     *
     * @since 4.0
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
            $this->_set_wc_datetime_object('date');
        }

        return $this->_objects['date'];
    }

    /**
     * Get customer object.
     *
     * @since 4.0
     * @access public
     *
     * @return WC_Customer|null Customer object on success, null on failure.
     */
    public function get_customer()
    {
        if (!$this->_data['user_id']) {
            return null;
        }

        if (is_null($this->_objects['user'])) {
            $this->_objects['user'] = new \WC_Customer($this->_data['user_id']);
        }

        // return null when the given ID is not a valid user.
        if (!$this->_objects['user']->get_id()) {
            return null;
        }

        return $this->_objects['user'];
    }

    /**
     * Get store credit entry registry data.
     *
     * @since 4.0
     * @access public
     *
     * @return array|bool Source type data on success, false when not available.
     */
    public function get_registry()
    {
        if (\is_null($this->_objects['registry'])) {

            if ('increase' === $this->_data['type']) {
                $this->_objects['registry'] = \ACFWF()->Store_Credits_Registry->get_store_credits_increase_source_types($this->_data['action']);
            } else {
                $this->_objects['registry'] = \ACFWF()->Store_Credits_Registry->get_store_credit_decrease_action_types($this->_data['action']);
            }
        }

        return $this->_objects['registry'];
    }

    /**
     * Get response data for API
     *
     * @since 4.0
     * @access public
     *
     * @param string $context     Data context.
     * @param string $date_format Date format.
     * @return array Virtual coupon response data.
     */
    public function get_response_for_api($context = 'view', $date_format = '', $is_admin = false)
    {
        $registry    = $this->get_registry();
        $date_format = $date_format ? $date_format : Plugin_Constants::DISPLAY_DATE_FORMAT;
        $date        = $this->get_date();

        return array(
            'key'        => (string) $this->get_id(),
            'id'         => $this->get_id(),
            'amount_raw' => $this->get_prop('amount', $context),
            'amount'     => \ACFWF()->Helper_Functions->api_wc_price($this->get_prop('amount', $context)),
            'type'       => $this->get_prop('type', $context),
            'activity'   => is_object($registry) ? $registry->name : '',
            'user_id'    => $this->get_prop('user_id', $context),
            'date'       => is_object($date) ? $date->format($date_format) : '',
            'rel_link'   => $this->get_related_object_link($is_admin),
            'rel_label'  => $this->get_related_object_label($is_admin),
            'note'       => $this->get_prop('note', $context),
        );
    }

    /**
     * Get related object link.
     *
     * @since 4.0
     * @access public
     *
     * @return string Object link.
     */
    public function get_related_object_link($is_admin = false)
    {
        $registry = $this->get_registry();

        if (is_object($registry) && isset($registry->related)) {

            $key      = $is_admin ? 'admin_link_callback' : 'link_callback';
            $callback = isset($registry->related[$key]) ? $registry->related[$key] : false;

            try {
                if ($callback) {
                    return \call_user_func($callback, $this->_data['object_id'], 'link');
                }
            } catch (\Error $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * Get related object label.
     *
     * @since 4.0
     * @access public
     *
     * @return string Label.
     */
    public function get_related_object_label($is_admin = false)
    {
        $registry = $this->get_registry();

        if (is_object($registry) && isset($registry->related)) {

            $key   = $is_admin ? 'admin_label' : 'label';
            $label = isset($registry->related[$key]) ? $registry->related[$key] : '';

            // handle admin increase/decrease entries.
            if ('admin_decrease' === $this->_data['action'] || 'admin_increase' === $this->_data['action']) {
                $admin = \ACFWF()->Helper_Functions->get_customer_name($this->_data['object_id']);
                return sprintf($label, $admin);
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
     * Set store credit entry ID.
     *
     * @since 4.0
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
     * @since 4.0
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
     * @since 4.0
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

        $datetime = \DateTime::createFromFormat($format, $value, new \DateTimeZone(\ACFWF()->Helper_Functions->get_site_current_timezone()));
        $datetime->setTimezone(new \DateTimeZone('UTC'));

        $check = $this->set_prop($prop, $datetime->format('Y-m-d H:i:s'));
        if ($check) {
            $this->_set_wc_datetime_object($prop);
        }

        return $check;
    }

    /*
    |--------------------------------------------------------------------------
    | Save/Delete Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Validate data before saving.
     *
     * @since 4.0
     * @access protected
     *
     * @return bool True if valid, false otherwise.
     */
    protected function _validate_data_before_save()
    {
        return is_object($this->get_customer()) &&
        $this->_data['amount'] &&
        $this->_data['type'] &&
        $this->_data['action'] &&
        is_object($this->get_registry());
    }

    /**
     * Validate customer balance.
     * 
     * @since 4.0
     * @access protected
     */
    protected function _validate_customer_balance()
    {
        $balance = \ACFWF()->Store_Credits_Calculate->get_customer_balance($this->_data['user_id'], true);

        if (!$this->_data['id'] && 'decrease' === $this->_data['type'] && $balance < $this->_data['amount']) {
            return new \WP_Error(
                'insufficient_user_store_credit_balance',
                __("The user's store credit balance is insufficient.", 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        return true;
    }

    /**
     * Create store credit entry data.
     *
     * @since 4.0
     * @access protected
     *
     * @return bool|WP_Error True if successfull, error object on failure.
     */
    protected function _create()
    {
        global $wpdb;

        $check = $wpdb->insert(
            $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME,
            array(
                'user_id'      => $this->_data['user_id'],
                'entry_date'   => $this->_data['date'] ? $this->_data['date'] : current_time('mysql', true),
                'entry_type'   => $this->_data['type'],
                'entry_action' => $this->_data['action'],
                'entry_amount' => $this->_data['amount'],
                'object_id'    => $this->_data['object_id'],
                'entry_note'   => $this->_data['note'],
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'acfw_error_create_store_credit_entry',
                __('There was an error trying to add store credits for user.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('acfw_create_store_credit_entry', $this->_data, $this);

        $this->set_id($wpdb->insert_id);
        return $this->get_id();
    }

    /**
     * Update store credit entry data.
     *
     * @since 4.0
     * @access protected
     *
     * @return bool|WP_Error True if successfull, error object on failure.
     */
    protected function _update()
    {
        global $wpdb;

        $check = $wpdb->update(
            $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME,
            array(
                'user_id'      => $this->_data['user_id'],
                'entry_date'   => $this->_data['date'] ? $this->_data['date'] : current_time('mysql', true),
                'entry_type'   => $this->_data['type'],
                'entry_action' => $this->_data['action'],
                'entry_amount' => $this->_data['amount'],
                'object_id'    => $this->_data['object_id'],
                'entry_note'   => $this->_data['note'],
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
                '%s',
            ),
            array(
                '%d',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'acfw_error_create_store_credit_entry',
                __('There was an error updating the store credit entry.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('acfw_update_store_credit_entry', $this->_data, $this);

        return true;
    }

    /**
     * Save point entry.
     *
     * @since 4.0
     * @access protected
     *
     * @return int|WP_Error ID if successfull, error object on fail.
     */
    public function save($skip_validate_balance = false)
    {
        if (!$this->_validate_data_before_save()) {
            return new \WP_Error(
                'store_credit_entry_missing_params',
                __('Unable to save store credit entry due to missing or invalid required parameters.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        if (!$skip_validate_balance) {
            $check = $this->_validate_customer_balance();
            if (is_wp_error($check)) {
                return $check;
            }
        }

        if ($this->get_id()) {
            return $this->_update();
        }

        do_action('acfw_store_credits_total_changed', $this);

        return $this->_create();
    }

    /**
     * Delete virtual code.
     *
     * @since 4.0
     * @access protected
     *
     * @return bool|WP_Error true if successfull, error object on fail.
     */
    public function delete()
    {
        global $wpdb;

        if (!$this->get_id()) {
            return new \WP_Error(
                'acfwp_missing_id_store_credit_entry',
                __('The store credit entry requires a valid ID to proceed.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $check = $wpdb->delete(
            $wpdb->prefix . Plugin_Constants::STORE_CREDITS_DB_NAME,
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
                __('There was an error deleting the store credit entry.', 'advanced-coupons-for-woocommerce-free'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('acfw_delete_store_credit_entry', $this->_data, $this);
        do_action('acfw_store_credits_total_changed', $this);

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
     * @since 4.0
     * @access protected
     */
    protected function _set_wc_datetime_object($prop)
    {
        $this->_objects[$prop] = new \WC_DateTime($this->_data[$prop], new \DateTimeZone('UTC'));
        $this->_objects[$prop]->setTimezone(new \DateTimeZone(\ACFWF()->Helper_Functions->get_site_current_timezone()));
    }
}
