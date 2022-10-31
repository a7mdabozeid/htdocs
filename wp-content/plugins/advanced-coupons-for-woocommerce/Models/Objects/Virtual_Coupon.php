<?php

namespace ACFWP\Models\Objects;

use ACFWP\Models\Objects\Advanced_Coupon;

/**
 * Model that houses the data model of a virtual code object.
 *
 * @since 1.0
 */
class Virtual_Coupon
{
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Stores virtual code data.
     *
     * @since 1.0
     * @access private
     * @var array
     */
    protected $_data = array(
        'id'           => 0,
        'coupon_id'    => 0,
        'code'         => '',
        'main_code'    => '',
        'status'       => '',
        'user_id'      => 0,
        'date_created' => '',
        'date_expire'  => '',
    );

    /**
     * Property that holds the various objects utilized by the virtual coupon.
     *
     * @since 3.0
     * @access protected
     * @var array
     */
    protected $_objects = array(
        'coupon'       => null,
        'user'         => null,
        'date_created' => null,
        'date_expire'  => null,
    );

    /**
     * Property that holds the coupon code to be used on frontend.
     *
     * @since 3.0
     * @access protected
     * @var string
     */
    protected $_coupon_code = '';

    /**
     * Stores boolean if the data has been read from the database or not.
     *
     * @since 1.0
     * @access private
     * @var object
     */
    protected $_read = false;

    /**
     * Store's the error code if the virtual coupon is not valid.
     *
     * @since 3.0
     * @access protected
     * @var int
     */
    protected $_error_code = 0;

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
     * @param mixed $code Virtual_Coupon ID, code or object.
     */
    public function __construct($arg = 0)
    {
        // skip reading data if no valid argument provided.
        if (!$arg) {
            return;
        }

        // if full data already provided in an array, then we just skip the other parts.
        if (is_array($arg)) {
            $this->_format_and_set_data($arg);
            return;
        }

        if (is_int($arg)) {
            $this->set_id(absint($arg));
        } elseif (is_string($arg)) {
            $this->set_prop('code', $arg);
        } elseif ($arg instanceof Virtual_Coupon) {
            $this->set_id($arg->get_id());
        }

        $this->read_data_from_db();
    }

    /**
     * Create fresh virtual coupon object by a given coupon code (applied from cart).
     *
     * @since 3.0
     * @access private
     *
     * @param string $coupon_code Coupon code.
     * @return Virtual_Coupon
     */
    public static function create_from_coupon_code($coupon_code)
    {
        $data = explode('-', $coupon_code);
        return new self(end($data));
    }

    /**
     * Read data from database.
     *
     * @since 1.0
     * @access public
     */
    public function read_data_from_db()
    {
        global $wpdb;

        // don't proceed if neither ID nor code is available.
        if (!$this->get_id() && !$this->get_code()) {
            return;
        }

        $virtual_coupons_db = $wpdb->prefix . \ACFWP()->Plugin_Constants->VIRTUAL_COUPONS_DB_NAME;

        $where_query = $this->get_code() ? $wpdb->prepare("virtual_coupon = %s", $this->get_code()) : $wpdb->prepare("id = %d", $this->get_id());
        $result      = $wpdb->get_row("SELECT * FROM {$virtual_coupons_db} WHERE {$where_query}", ARRAY_A);

        if (!$result) {
            return;
        }

        $this->_format_and_set_data($result);
    }

    /**
     * Format raw data and set to property.
     *
     * @since 3.0
     * @access protected
     *
     * @param array $raw_data
     */
    protected function _format_and_set_data($raw_data)
    {
        $coupon_id   = absint($raw_data['coupon_id']);
        $post_object = get_post($coupon_id);
        $this->_data = array(
            'id'           => absint($raw_data['id']),
            'coupon_id'    => $coupon_id,
            'code'         => $raw_data['virtual_coupon'],
            'main_code'    => isset($raw_data['main_code']) ? $raw_data['main_code'] : $post_object->post_title,
            'status'       => $raw_data['coupon_status'],
            'user_id'      => !is_null($raw_data['user_id']) ? absint($raw_data['user_id']) : 0,
            'date_created' => $raw_data['date_created'],
            'date_expire'  => !is_null($raw_data['user_id']) ? $raw_data['date_expire'] : '',
        );

        if (isset($raw_data['coupon_code'])) {
            $this->_coupon_code = $raw_data['coupon_code'];
        }

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
     * @since 3.0
     * @access protected
     *
     * @param string $prop    Property name.
     * @param string $context 'edit' or 'view' context.
     * @return mixed Property value.
     */
    public function get_prop($prop, $context = 'view')
    {
        if (!array_key_exists($prop, $this->_data)) {
            throw new \Exception(sprintf("%s property doesn't exist in Virtual_Coupons class", $prop), 400);
        }

        if ('edit' === $context) {
            return $this->_data[$prop];
        }

        return apply_filters('acfw_virtual_coupon_get_' . $prop, $this->_data[$prop], $prop);
    }

    /**
     * Get virtual code ID.
     *
     * @since 3.0
     * @access public
     *
     * @return int Virtual code ID.
     */
    public function get_id()
    {
        return $this->_data['id'];
    }

    /**
     * Get virtual code string.
     *
     * @since 3.0
     * @access public
     *
     * @return string Virtual code string.
     */
    public function get_code()
    {
        return $this->_data['code'];
    }

    /**
     * Get the main coupon code.
     *
     * @since 3.0
     * @since 3.5.1 Only get the main coupon code when a valid coupon ID is provided.
     * @access public
     *
     * @return string Main coupon code.
     */
    public function get_main_code()
    {
        if (!$this->_data['main_code'] && $this->_data['coupon_id']) {
            $post_object              = get_post($this->_data['coupon_id']);
            $this->_data['main_code'] = $post_object->post_title; // fetch via main coupon code via WP way to prevent infinite loop.
        }

        return $this->_data['main_code'];
    }

    /**
     * Get coupon code to be used on frontend.
     *
     * @since 3.0
     * @since 3.5.1 Only get the virtual coupon code when a valid virtual coupon ID is present.
     * @access public
     */
    public function get_coupon_code()
    {
        if (!$this->_coupon_code && $this->get_id()) {
            $this->_coupon_code = strtolower(
                sprintf(
                    '%s%s%s',
                    $this->get_main_code(),
                    apply_filters('acfw_virtual_coupon_code_separator', '-'),
                    $this->get_code()
                )
            );
        }

        return $this->_coupon_code;
    }

    /**
     * Get virtual code status.
     *
     * @since 3.0
     * @access public
     *
     * @return string Virtual code status.
     */
    public function get_status($context = 'view')
    {
        return $this->get_prop('status', $context);
    }

    /**
     * Get date object based on type.
     *
     * @since 3.0
     * @access protected
     *
     * @param string $date  Date type.
     * @param bool   $fresh Check if we need to create a fresh object or not.
     * @return null|WC_DateTime
     */
    protected function _get_date($date, $fresh = false)
    {
        if (!in_array($date, array('date_expire', 'date_created')) || !$this->_data[$date] || '0000-00-00 00:00:00' === $this->_data[$date]) {
            return null;
        }

        if ($fresh || is_null($this->_objects[$date])) {
            $this->_objects[$date] = new \WC_DateTime($this->_data[$date], new \DateTimeZone('UTC'));
            $this->_objects[$date]->setTimezone(new \DateTimeZone(\ACFWP()->Helper_Functions->get_site_current_timezone()));
        }

        return $this->_objects[$date];
    }

    /**
     * Get date created.
     *
     * @since 3.0
     * @access public
     *
     * @param bool $fresh Check if we need to create a fresh object or not.
     * @return null|WC_DateTime
     */
    public function get_date_created($fresh = false)
    {
        return $this->_get_date('date_created', $fresh);
    }

    /**
     * Get date created.
     *
     * @since 3.0
     * @access public
     *
     * @param bool $fresh Check if we need to create a fresh object or not.
     * @return null|WC_DateTime
     */
    public function get_date_expire($fresh = false)
    {
        return $this->_get_date('date_expire', $fresh);
    }

    /**
     * Get advanced coupon object.
     *
     * @since 3.0
     * @access public
     *
     * @return Advanced_Coupon
     */
    public function get_coupon()
    {
        if (is_null($this->_objects['coupon']) || $this->_objects['coupon']->get_id() !== $this->_data['coupon_id']) {
            $this->_objects['coupon'] = new Advanced_Coupon($this->_data['coupon_id']);
        }

        return $this->_objects['coupon'];
    }

    /**
     * Get customer object.
     *
     * @since 3.0
     * @access public
     *
     * @return WC_Customer
     */
    public function get_customer()
    {
        if (is_null($this->_objects['user']) || $this->_objects['user']->get_id() !== $this->_data['user_id']) {
            $this->_objects['user'] = new \WC_Customer($this->_data['user_id']);
        }

        return $this->_objects['user'];
    }

    /**
     * Get data to be saved for session.
     *
     * @since 3.0
     * @access public
     *
     * @return array Data for session.
     */
    public function get_data_for_session()
    {
        return array(
            'id'             => $this->_data['id'],
            'coupon_id'      => $this->_data['coupon_id'],
            'virtual_coupon' => $this->_data['code'],
            'coupon_status'  => $this->_data['status'],
            'user_id'        => $this->_data['user_id'],
            'date_created'   => $this->_data['date_created'],
            'date_expire'    => $this->_data['date_expire'],
            'coupon_code'    => $this->get_coupon_code(),
        );
    }

    /**
     * Get response data for API
     *
     * @since 3.0
     * @access public
     *
     * @param string $context     Data context.
     * @param string $date_format Date format.
     * @return array Virtual coupon response data.
     */
    public function get_response_for_api($context = 'view', $date_format = '')
    {
        $date_format  = $date_format ? $date_format : \ACFWP()->Plugin_Constants->DISPLAY_DATE_FORMAT;
        $date_created = $this->get_date_created();
        $date_expire  = $this->get_date_expire();
        $user_id      = $this->get_prop('user_id', $context);

        return array(
            'key'           => (string) $this->get_id(),
            'id'            => $this->get_id(),
            'code'          => $this->get_code(),
            'main_code'     => $this->get_main_code(),
            'coupon_code'   => $this->get_coupon_code(),
            'status'        => $this->get_prop('status', $context),
            'coupon_id'     => $this->get_prop('coupon_id', $context),
            'user_id'       => $user_id,
            'user_fullname' => $user_id ? \ACFWP()->Helper_Functions->get_customer_name($this->get_customer()) : '',
            'user_email'    => $user_id ? \ACFWP()->Helper_Functions->get_customer_email($this->get_customer()) : '',
            'date_created'  => is_object($date_created) ? $date_created->format($date_format) : '',
            'date_expire'   => is_object($date_expire) ? $date_expire->format($date_format) : $this->get_main_coupon_expire($date_format),
        );
    }

    /**
     * Get main coupon expire.
     *
     * @since 1.4
     * @access public
     *
     * @param string $date_format Date format.
     * @return string Main coupon exiry date.
     */
    public function get_main_coupon_expire($date_format)
    {
        $datetime = null;
        $utc_zone = new \DateTimeZone('UTC');

        if ($schedule_end = get_post_meta($this->get_prop('coupon_id', 'edit'), \ACFWP()->Plugin_Constants->META_PREFIX . 'scheduled_end', true)) {
            $datetime = new \WC_DateTime($schedule_end, $utc_zone);
        } elseif ($date_expires = get_post_meta($this->get_prop('coupon_id', 'edit'), 'date_expires', true)) {
            $datetime = new \WC_DateTime('today', $utc_zone);
            $datetime->setTimestamp($date_expires);
        }

        if ($datetime) {
            $datetime->setTimezone(new \DateTimeZone(\ACFWP()->Helper_Functions->get_site_current_timezone()));
            return $datetime->format($date_format);
        }

        return '';
    }

    /**
     * Get the error code.
     *
     * @since 3.0
     * @access public
     *
     * @return int Error code.
     */
    public function get_error_code()
    {
        return $this->_error_code;
    }

    /*
    |--------------------------------------------------------------------------
    | Setter Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Set virtual code ID.
     *
     * @since 3.0
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
     * @since 3.0
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
     * Set datetime prop.
     *
     * @since 3.0
     * @access public
     *
     * @param string $prop  Property name.
     * @param mixed  $value Property value.
     * @param string $format Date format.
     * @return bool True if prop was set, false otherwise.
     */
    public function set_datetime_prop($prop, $value, $format = 'Y-m-d H:i:s')
    {
        if (!in_array($prop, array('date_created', 'date_expire')) || !$value) {
            return false;
        }

        $datetime = \WC_DateTime::createFromFormat($format, $value, new \DateTimeZone(\ACFWP()->Helper_Functions->get_site_current_timezone()));

        if (!is_object($datetime)) {
            return false;
        }

        $datetime->setTimezone(new \DateTimeZone('UTC'));
        return $this->set_prop($prop, $datetime->format('Y-m-d H:i:s'));
    }

    /**
     * Set coupon object.
     *
     * @since 3.0
     * @access public
     *
     * @param WC_Coupon $coupon
     * @return bool True if set, false on fail.
     */
    public function set_coupon($coupon)
    {
        if (!$coupon instanceof \WC_Coupon || $this->get_prop('coupon_id') !== $coupon->get_id()) {
            return false;
        }

        $this->_objects['coupon'] = $coupon;
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Save/Delete Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Create new virtual code.
     *
     * @since 3.0
     * @access protected
     *
     * @return int|WP_Erorr ID if successfull, error object on fail.
     */
    protected function create()
    {
        global $wpdb;

        // generate a coupon code if there is no code set.
        if (!$this->get_code()) {
            $this->set_prop('code', self::generate_code());
        }

        $check = $wpdb->insert(
            $wpdb->prefix . \ACFWP()->Plugin_Constants->VIRTUAL_COUPONS_DB_NAME,
            array(
                'coupon_id'      => $this->_data['coupon_id'],
                'virtual_coupon' => $this->_data['code'],
                'coupon_status'  => $this->_data['status'],
                'user_id'        => $this->_data['user_id'],
                'date_created'   => $this->_data['date_created'],
                'date_expire'    => $this->_data['date_expire'],
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'acfwp_error_creating_virtual_coupon',
                __('Creating the new virtual coupon was unsuccessful.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $this->set_id($wpdb->insert_id);

        do_action('acfwp_virtual_coupon_created', $this->_data, $this);
        do_action('acfwp_virtual_coupons_count_changed', $this);

        return $this->get_id();
    }

    /**
     * Update virtual code data.
     *
     * @since 3.0
     * @access protected
     *
     * @return int|WP_Error ID if successfull, error object on fail.
     */
    protected function update()
    {
        global $wpdb;

        $check = $wpdb->update(
            $wpdb->prefix . \ACFWP()->Plugin_Constants->VIRTUAL_COUPONS_DB_NAME,
            array(
                'coupon_id'      => $this->_data['coupon_id'],
                'virtual_coupon' => $this->_data['code'],
                'coupon_status'  => $this->_data['status'],
                'user_id'        => $this->_data['user_id'],
                'date_created'   => $this->_data['date_created'],
                'date_expire'    => $this->_data['date_expire'],
            ),
            array(
                'id' => $this->get_id(),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            ),
            array(
                '%d',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'acfwp_error_update_virtual_coupon',
                __('There was an error updating the virtual coupon.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('acfwp_virtual_coupon_updated', $this->_data, $this);
        do_action('acfwp_virtual_coupons_count_changed', $this);

        return true;
    }

    /**
     * Save virtual code.
     *
     * @since 3.0
     * @access protected
     *
     * @return int|WP_Error ID if successfull, error object on fail.
     */
    public function save()
    {
        if ($this->get_id()) {
            return $this->update();
        }

        return $this->create();
    }

    /**
     * Delete virtual code.
     *
     * @since 3.0
     * @access protected
     *
     * @return bool|WP_Error true if successfull, error object on fail.
     */
    public function delete()
    {
        global $wpdb;

        if (!$this->get_id()) {
            return new \WP_Error(
                'acfwp_missing_id_virtual_coupon',
                __('Virtual coupon requires a valid ID to proceed.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        $check = $wpdb->delete(
            $wpdb->prefix . \ACFWP()->Plugin_Constants->VIRTUAL_COUPONS_DB_NAME,
            array(
                'id' => $this->get_id(),
            ),
            array(
                '%d',
            )
        );

        if (!$check) {
            return new \WP_Error(
                'acfwp_error_delete_virtual_coupon',
                __('There was an error deleting the virtual code.', 'advanced-coupons-for-woocommerce'),
                array(
                    'status' => 400,
                    'data'   => $this->_data,
                )
            );
        }

        do_action('acfwp_virtual_coupon_deleted', $this->_data, $this);
        do_action('acfwp_virtual_coupons_count_changed', $this);

        $this->set_id(0);
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Generate unique code string.
     *
     * @since 3.0
     * @access public
     *
     * @param int $length Number of characters of code to generate.
     * @return string Unique code string.
     */
    public static function generate_code($length = 10)
    {
        $settings = apply_filters(
            'acfw_virtual_coupon_generate_code_settings',
            array(
                'characters' => 'ABCDEFGHJKMNPQRSTUVWXYZ23456789',
                'length'     => $length,
                'prefix'     => '',
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

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Validate virtual coupon.
     *
     * @since 3.0
     * @access private
     *
     * @param Virtual_Coupon $virtual_coupon
     * @return bool True if valid, false otherwise.
     */
    public function is_valid()
    {
        // if it's already invalid, then skip.
        if (0 !== $this->_error_code) {
            return false;
        }

        if (!$this->get_coupon()->get_advanced_prop('enable_virtual_coupons')) {
            $this->_error_code = \WC_Coupon::E_WC_COUPON_INVALID_FILTERED;
            return false;
        }

        // Validate virtual coupon usage status.
        if ($this->get_prop('status') === 'used') {
            $this->_error_code = \WC_Coupon::E_WC_COUPON_INVALID_FILTERED;
            return false;
        }

        // Validate virtual coupon user when value is not 0.
        if ($this->get_prop('user_id') !== 0 && get_current_user_id() !== $this->get_prop('user_id')) {
            $this->_error_code = \WC_Coupon::E_WC_COUPON_INVALID_FILTERED;
            return false;
        }

        return true;
    }
}
