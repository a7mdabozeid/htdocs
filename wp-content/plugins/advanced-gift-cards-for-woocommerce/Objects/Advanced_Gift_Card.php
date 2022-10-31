<?php

namespace AGCFW\Objects;

/**
 * Model that houses the data model of an advanced gift card object.
 *
 * @since 1.0
 */
class Advanced_Gift_Card
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the advanced gift card data.
     *
     * @since 1.0
     * @access protected
     * @var array
     */
    protected $_data = array(
        'id'            => 0,
        'order_item_id' => 0,
        'date_created'  => '',
        'date_expire'   => '',
        'code'          => '',
        'value'         => 0.0,
        'status'        => '',
        'user_id'       => 0,
        'note'          => '',
    );

    /**
     * Property that holds the various objects utilized by the advanced gift card.
     *
     * @since 1.0
     * @access protected
     * @var array
     */
    protected $_objects = array(
        'order_item'   => null,
        'date_created' => null,
        'date_expire'  => null,
        'user'         => null,
    );

    /**
     * Stores boolean if the data has been read from the database or not.
     *
     * @since 1.0
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
     * @since 1.0
     * @access public
     *
     * @param mixed $arg Advanced gift card ID or raw data.
     */
    public function __construct($arg = 0)
    {
        // skip reading data if no valid argument.
        if (!$arg || empty($arg)) {
            return;
        }

        // if full data already provided in an array, then we just skip the other parts.
        if (is_array($arg)) {
            $this->set_id(absint($arg['id']));
            $this->_format_and_set_data($arg);
            return;
        }

        $this->set_id(absint($arg));
        $this->_read_data_from_db();
    }

    /**
     * Read data from database.
     *
     * @since 1.0
     * @since 1.1 Fetch data from object cache if present.
     * @access protected
     */
    protected function _read_data_from_db()
    {
        global $wpdb;

        // don't proceed if ID is not available.
        if (!$this->get_id()) {
            return;
        }

        $cache = wp_cache_get($this->get_cache_key(), 'agcfw');

        // load data from object cache.
        if ($cache) {
            $this->_data = $cache;
            $this->_read = true;
            return;
        }

        $gift_cards_db = $wpdb->prefix . \AGCFW()->Plugin_Constants->DB_TABLE_NAME;
        $result        = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$gift_cards_db} WHERE id = %d",
                $this->get_id()
            )
        );

        if (!$result) {
            $this->set_id(0);
            return;
        }

        $this->_format_and_set_data($result);
    }

    /**
     * Format raw data and set to property.
     *
     * @since 1.0
     * @since 1.1 Save date to object cache.
     * @access protected
     *
     * @param array $raw_data
     */
    protected function _format_and_set_data($raw_data)
    {
        $raw_data = wp_parse_args($raw_data, $this->_data);

        $this->_data = array(
            'id'            => absint($raw_data['id']),
            'order_item_id' => absint($raw_data['order_item_id']),
            'date_created'  => $raw_data['date_created'],
            'date_expire'   => $raw_data['date_expire'],
            'code'          => $raw_data['code'],
            'value'         => floatval($raw_data['value']),
            'status'        => $raw_data['status'],
            'user_id'       => absint($raw_data['user_id']),
            'note'          => $raw_data['note'],
        );

        /**
         * Save data to the object cache so WP won't need to refetch a gift card data multiple times in a single page load.
         * Cache expiry is set to 60 seconds, so fresh data is always fetched on every page load when a caching plugin is installed.
         */
        $check = wp_cache_set( $this->get_cache_key(), $this->_data, 'agcfw', 60);

        $this->_read = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Getter Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Get advanced gift card ID.
     *
     * @since 1.0
     * @access public
     *
     * @return int Advanced gift card ID.
     */
    public function get_id()
    {
        return $this->_data['id'];
    }

    /**
     * Get advanced gift card code.
     *
     * @since 1.0
     * @access public
     *
     * @return string Advanced gift card code.
     */
    public function get_code()
    {
        return $this->_data['code'];
    }

    public function get_cache_key()
    {
        return 'agcfw_object_' . $this->get_id();
    }

    /**
     * Get value for a given property and context.
     *
     * @since 1.0
     * @access protected
     *
     * @param string $prop    Property name.
     * @param string $context 'edit' or 'view' context.
     * @return mixed Property value.
     */
    public function get_prop($prop, $context = 'view')
    {
        if (!array_key_exists($prop, $this->_data)) {
            throw new \Exception(sprintf("%s property doesn't exist in Advanced_Gift_Card class", $prop), 400);
        }

        if ('edit' === $context) {
            return $this->_data[$prop];
        }

        return apply_filters('agcfw_advanced_gift_card_get_' . $prop, $this->_data[$prop], $prop);
    }

    /**
     * Get value.
     * 
     * @since 1.0.1
     * @access public
     * 
     * @return float Gift card value.
     */
    public function get_value($context = 'view')
    {
        return 'view' === $context ? apply_filters('acfw_filter_amount', $this->_data['value']) : $this->_data['value'];
    }

    /**
     * Get date object.
     *
     * @since 1.0
     * @access public
     *
     * @return null|WC_DateTime
     */
    public function get_date($prop)
    {
        if (!isset($this->_data[$prop]) || !$this->_data[$prop] || '0000-00-00 00:00:00' === $this->_data[$prop]) {
            return null;
        }

        if (is_null($this->_objects[$prop]) || $this->_objects[$prop]->format(\AGCFW()->Plugin_Constants->DB_DATE_FORMAT) !== $this->_data[$prop]) {
            $this->_set_wc_datetime_object($prop);
        }

        return $this->_objects[$prop];
    }

    /**
     * Get response data for API
     *
     * @since 1.0
     * @access public
     *
     * @param string $context     Data context.
     * @param string $date_format Date format.
     * @return array Virtual coupon response data.
     */
    public function get_response_for_api($context = 'view', $date_format = '')
    {
        $date_format  = $date_format ? $date_format : \AGCFW()->Plugin_Constants->DISPLAY_DATE_FORMAT;
        $date_created = $this->get_date('date_created');
        $date_expire  = $this->get_date('date_expire');

        return array(
            'key'           => (string) $this->get_id(),
            'id'            => $this->get_id(),
            'order_item_id' => $this->get_prop('order_item_id', $context),
            'date_created'  => is_object($date_created) ? $date_created->format($date_format) : '',
            'date_expire'   => is_object($date_expire) ? $date_expire->format($date_format) : '',
            'code'          => $this->get_prop('code', $context),
            'value'         => \ACFWF()->Helper_Functions->api_wc_price($this->get_prop('value', $context)),
            'value_raw'     => $this->get_prop('value', $context),
            'status'        => $this->get_prop('status', $context),
            'user_id'       => $this->get_prop('user_id', $context),
            'note'          => $this->get_prop('note', $context),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Setter Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Set store credit entry ID.
     *
     * @since 1.0
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
     * @since 1.0
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
     * @since 1.0
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

        $datetime = \DateTime::createFromFormat($format, $value, new \DateTimeZone(\AGCFW()->Helper_Functions->get_site_current_timezone()));
        $datetime->setTimezone(new \DateTimeZone('UTC'));

        $check = $this->set_prop($prop, $datetime->format('Y-m-d H:i:s'));
        if ($check) {
            $this->_set_wc_datetime_object($prop);
        }

        return $check;
    }

    /**
     * Set date expire based on a given interval string (5 years, 1 year, 100 days, etc.) based on the current datetime.
     * 
     * @since 1.1
     * @access public
     * 
     * @param string $interval_string The interval string to add. Default to 5 years.
     */
    public function set_date_expire_by_interval($interval_string = '5 years')
    {
        // skip if gift card should not expire.
        if ('noexpiry' === $interval_string) {
            return;
        }

        $expire_date = $this->_calculate_date_expiry($interval_string);
        $this->set_prop('date_expire', $expire_date);
    }

    /*
    |--------------------------------------------------------------------------
    | Save/Delete Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Validate data before saving.
     *
     * @since 1.0
     * @access protected
     *
     * @return bool True if valid, false otherwise.
     */
    protected function _validate_data_before_save()
    {
        return $this->_data['order_item_id'] && $this->_data['status'] && $this->_data['value'];
    }

    /**
     * Create gift card entry.
     *
     * @since 1.0
     * @since 1.1 Remove code that sets default expiry to 5 years to allow no expiry gift cards.
     * @access protected
     *
     * @return int|WP_Error Gift card ID on success, error object on failure.
     */
    protected function _create()
    {
        global $wpdb;

        // generate a coupon code if there is no code set.
        if (!$this->get_code()) {
            $this->set_prop('code', self::generate_code());
        }

        $check = $wpdb->insert(
            $wpdb->prefix . \AGCFW()->Plugin_Constants->DB_TABLE_NAME,
            array(
                'order_item_id' => $this->_data['order_item_id'],
                'date_created'  => $this->_data['date_created'] ? $this->_data['date_created'] : current_time('mysql', true),
                'date_expire'   => $this->_data['date_expire'],
                'code'          => $this->_data['code'],
                'value'         => $this->_data['value'],
                'status'        => $this->_data['status'],
                'user_id'       => $this->_data['user_id'],
                'note'          => $this->_data['note'],
            ),
            array(
                '%d',
                '%s',
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
                'agcfw_error_create_advanced_gift_card',
                __('There was an error trying to create the advanced gift card.', 'advanced-gift-cards-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $this->set_id($wpdb->insert_id);

        do_action('agcfw_advanced_gift_card_created', $this->_data, $this);

        return $this->get_id();
    }

    /**
     * Update advanced gift card data.
     *
     * @since 1.0
     * @access protected
     *
     * @return bool|WP_Error True if successfull, error object on failure.
     */
    protected function _update()
    {
        global $wpdb;

        $check = $wpdb->update(
            $wpdb->prefix . \AGCFW()->Plugin_Constants->DB_TABLE_NAME,
            array(
                'order_item_id' => $this->_data['order_item_id'],
                'date_created'  => $this->_data['date_created'],
                'date_expire'   => $this->_data['date_expire'],
                'code'          => $this->_data['code'],
                'value'         => $this->_data['value'],
                'status'        => $this->_data['status'],
                'user_id'       => $this->_data['user_id'],
                'note'          => $this->_data['note'],
            ),
            array(
                'id' => $this->get_id(),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            ),
            array(
                '%d',
            )
        );

        if (false === $check) {
            return new \WP_Error(
                'agcfw_error_update_advanced_gift_card',
                __('There was an error updating the advanced gift card.', 'advanced-gift-cards-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('agcfw_advanced_gift_card_updated', $this->_data, $this);

        return true;
    }

    /**
     * Save point entry.
     *
     * @since 1.0
     * @access protected
     *
     * @return int|WP_Error ID if successfull, error object on fail.
     */
    public function save()
    {
        if (!$this->_validate_data_before_save()) {
            return new \WP_Error(
                'agcfw_advanced_gift_card_missing_params',
                __('Unable to save the advanced gift card due to missing required parameters.', 'advanced-gift-cards-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $check = $this->get_id() ? $this->_update() : $this->_create();

        // update data fresh from db so we can apply changes that was added dynamically to the object.
        if ($check) {
            wp_cache_delete($this->get_cache_key(), 'agcfw');
            $this->_read_data_from_db();
        }

        do_action('agcfw_gift_cards_total_changed', $this);

        return $check;
    }

    /**
     * Delete advanced gift card.
     *
     * @since 1.0
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
                __('The store credit entry requires a valid ID to proceed.', 'advanced-gift-cards-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $check = $wpdb->delete(
            $wpdb->prefix . \AGCFW()->Plugin_Constants->DB_TABLE_NAME,
            array(
                'id' => $this->get_id(),
            ),
            array(
                '%d',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'agcfw_error_delete_point_entry',
                __('There was an error deleting the store credit entry.', 'advanced-gift-cards-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        wp_cache_delete($this->get_cache_key(), 'agcfw');

        do_action('agcfw_advanced_gift_card_deleted', $this->_data, $this);
        do_action('agcfw_gift_cards_total_changed', $this);

        $this->set_id(0);
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Generate advanced gift card code.
     *
     * @since 1.0
     * @access public
     *
     * @param int $length Number of characters to be generated.
     * @return string Advanced gift card generated code.
     */
    public static function generate_code($length = 10)
    {
        $settings = apply_filters(
            'agcfw_generate_code_settings',
            array(
                'characters' => 'ABCDEFGHJKMNPQRSTUVWXYZ23456789',
                'length'     => $length,
                'prefix'     => 'gc-',
                'suffix'     => '',
            )
        );

        // generate code based on provided length.
        $char_length = strlen($settings['characters']);
        $code        = '';
        for ($i = 0; $i < $settings['length']; $i++) {
            $index = (int) floor(rand() / getrandmax() * $char_length);
            $code .= $settings['characters'][$index];
        }

        // add prefix and suffix
        $code = sprintf('%s%s%s', $settings['prefix'], $code, $settings['suffix']);

        return $code;
    }

    /**
     * Calculate date expiry.
     *
     * @since 1.0
     * @since 1.1 Add $interval_string parameter.
     * @access protected
     *
     * @param string $interval_string The interval string to add. Default to 5 years.
     * @return string Date expiry.
     */
    protected function _calculate_date_expiry($interval_string = '5 years')
    {
        $datetime = new \DateTime();
        $datetime->add(\DateInterval::createFromDateString($interval_string));

        return $datetime->format('Y-m-d H:i:s');
    }

    /**
     * Create a WC_DateTime object for the given date.
     *
     * @since 1.0
     * @access protected
     */
    protected function _set_wc_datetime_object($prop)
    {
        $this->_objects[$prop] = new \WC_DateTime($this->_data[$prop], new \DateTimeZone('UTC'));
        $this->_objects[$prop]->setTimezone(new \DateTimeZone(\ACFWF()->Helper_Functions->get_site_current_timezone()));
    }
}
