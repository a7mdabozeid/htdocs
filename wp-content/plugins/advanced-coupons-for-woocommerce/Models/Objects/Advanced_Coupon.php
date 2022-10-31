<?php

namespace ACFWP\Models\Objects;

use ACFWP\Helpers\Plugin_Constants;

/**
 * Model that houses the data model of an advanced coupon object.
 *
 * @since 2.0
 */
class Advanced_Coupon extends \ACFWF\Models\Objects\Advanced_Coupon
{

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 2.0
     * @access public
     *
     * @param mixed $code WC_Coupon ID, code or object.
     */
    public function __construct($code)
    {
        // construct parent object and set the code.
        parent::__construct($code);

        // disable URL coupon when virtual coupon is enabled.
        if (!is_admin() && $this->get_advanced_prop('enable_virtual_coupons')) {
            $this->advanced_data['disable_url_coupon'] = 'yes';
        }
    }

    /**
     * Return extra default advanced data.
     *
     * @since 2.0
     * @access protected
     *
     * @return array Extra default advanced data.
     */
    protected function extra_default_advanced_data()
    {
        return array(
            'bogo_auto_add_products'                   => true,
            'add_before_conditions'                    => false,
            'enable_day_time_schedules'                => '',
            'day_time_schedule_error_msg'              => '',
            'day_time_schedules'                       => array(),
            'add_products_data'                        => array(),
            'excluded_coupons'                         => array(),
            'shipping_overrides'                       => array(),
            'auto_apply_coupon'                        => false,
            'enable_apply_notification'                => false,
            'apply_notification_message'               => '',
            'apply_notification_btn_text'              => '',
            'apply_notification_type'                  => 'info',
            'reset_usage_limit_period'                 => 'none',
            'coupon_sort_priority'                     => 30,
            'loyalty_program_user'                     => 0,
            'loyalty_program_points'                   => 0,
            'cart_condition_display_notice_auto_apply' => '',
            'enable_payment_methods_restrict'          => '',
            'payment_methods_restrict_type'            => 'allowed',
            'payment_methods_restrict_selection'       => array(),
            'enable_virtual_coupons'                   => false,
            'virtual_coupon_for_display'               => false,
            'percentage_discount_cap'                  => 0.0,
            'defer_apply_url_coupon'                   => false,
        );
    }

    /**
     * Advanced read property.
     *
     * @since 2.0
     * @access protected
     *
     * @param mixed  $raw_data     Property raw data value.
     * @param string $prop         Property name.
     * @param string $default_data Default data value.
     * @param array  $meta_data    Coupon metadata list.
     * @return mixed Data value.
     */
    protected function advanced_read_property($raw_data, $prop, $default_data, $meta_data)
    {
        $data = null;

        switch ($prop) {

            case 'bogo_auto_add_products':
            case 'enable_virtual_coupons':
            case 'add_before_conditions':
            case 'defer_apply_url_coupon':
                $data = (bool) $raw_data;
                break;

            case 'apply_notification_message':
            case 'apply_notification_btn_text':
            case 'apply_notification_type':
            case 'reset_usage_limit_period':
            case 'cart_condition_display_notice_auto_apply':
            case 'enable_payment_methods_restrict':
            case 'payment_methods_restrict_type':
            case 'enable_day_time_schedules':
            case 'day_time_schedule_error_msg':
                $data = 'string' === gettype($raw_data) ? $raw_data : $default_data;
                break;

            case 'loyalty_program_user':
            case 'loyalty_program_points':
            case 'coupon_sort_priority':
                $data = in_array(gettype($raw_data), array('string', 'integer')) ? intval($raw_data) : $default_data;
                break;

            case 'day_time_schedules':
            case 'add_products_data':
            case 'excluded_coupons':
            case 'payment_methods_restrict_selection':
            case 'shipping_overrides':
                $data = is_array($raw_data) ? $raw_data : $default_data;
                break;

            case 'auto_apply_coupon':
                $data = in_array($this->id, $this->_helper_functions->get_option(ACFWP()->Plugin_Constants->AUTO_APPLY_COUPONS, array()));
                break;

            case 'enable_apply_notification':
                $data = in_array($this->id, $this->_helper_functions->get_option(ACFWP()->Plugin_Constants->APPLY_NOTIFICATION_CACHE, array()));
                break;

            case 'percentage_discount_cap':
                $data = floatval($raw_data);
        }

        return $data;
    }

    /**
     * Handle when propert meta value is empty.
     *
     * @since 2.6
     * @access protected
     *
     * @param string $prop Property name.
     * @param array  $meta_data Coupon meta data.
     * @return mixed Property value.
     */
    protected function handle_read_empty_value($prop, $meta_data)
    {
        return null;
    }

    /**
     * Override the get_code method to return the original code when displaying a parent virtual coupon
     * in the coupons by category gutenberg block.
     *
     * The original coupon code should only be returned on non cart/checkout contexts and when the virtual coupon
     * is not intended to be displayed to the customer's view.
     *
     * @since 3.1.2
     * @access public
     *
     * @param string $context Context type.
     * @return string Coupon code.
     */
    public function get_code($context = 'view')
    {
        if (
            $this->get_advanced_prop('enable_virtual_coupons') &&
            !$this->get_advanced_prop('virtual_coupon_for_display') &&
            !(
                \ACFWF()->Helper_Functions->is_checkout_fragments() ||
                \ACFWP()->Helper_Functions->is_apply_coupon() ||
                \ACFWF()->Helper_Functions->is_cart() ||
                is_checkout()
            )
        ) {
            return strtolower(get_the_title($this->get_id()));
        }

        return parent::get_code($context);
    }

    /**
     * Get extra get advanced prop global value.
     *
     * @since 2.0
     * @access protected
     *
     * @param string $prop Property name.
     * @return string Property global option name.
     */
    protected function get_extra_advanced_prop_global_value($prop)
    {
        $option = '';

        if ('day_time_schedule_error_msg' === $prop) {
            $option = \ACFWP()->Plugin_Constants->DAYTIME_SCHEDULES_ERROR_MESSAGE;
        }

        return $option;
    }

    /**
     * Check if to skip saving the advanced prop value as post meta.
     *
     * @since 2.0
     * @since 2.1 Prevent saving _acfw_schedule_expire meta.
     * @since 3.5 Move 'schedule_expire' skip code to ACFWF.
     * @access protected
     *
     * @param mixed  $value Property value.
     * @param string $prop  Property name.
     * @param bool True if skip, false otherwise.
     */
    protected function is_skip_save_advanced_prop($value, $prop)
    {
        if ('auto_apply_coupon' === $prop && $this->_helper_functions->is_module(Plugin_Constants::AUTO_APPLY_MODULE)) {
            $this->save_prop_to_global_option_cache(ACFWP()->Plugin_Constants->AUTO_APPLY_COUPONS, $value);
            return true;
        }

        if ('enable_apply_notification' == $prop && $this->_helper_functions->is_module(Plugin_Constants::APPLY_NOTIFICATION_MODULE)) {
            $this->save_prop_to_global_option_cache(ACFWP()->Plugin_Constants->APPLY_NOTIFICATION_CACHE, $value);
            return true;
        }

        return false;
    }

    /**
     * Get the "Add Products" data with backwards compatiblity for the "Add Free Products" data.
     *
     * @since 2.0
     * @access public
     */
    public function get_add_products_data($context = 'view')
    {
        $add_products  = $this->get_advanced_prop('add_products_data', array());
        $free_products = $this->get_advanced_prop('add_free_products', array());

        if ((!is_array($add_products) || empty($add_products)) && !empty($free_products)) {

            foreach ($free_products as $product_id => $quantity) {

                $product        = wc_get_product($product_id);
                $add_products[] = array(
                    'product_id'     => $product_id,
                    'product_label'  => $product->get_formatted_name(),
                    'quantity'       => $quantity,
                    'discount_type'  => 'override',
                    'discount_value' => 0,
                );
            }
        }

        // format discount value to localized version for editing context.
        if ('edit' === $context) {
            $add_products = array_map(function ($p) {
                $p['discount_value'] = wc_format_localized_price($p['discount_value']);
                return $p;
            }, $add_products);
        }

        return apply_filters('acfwp_coupon_get_add_products_data', $add_products, $this);
    }

    /**
     * Get shipping overrides data for editing context.
     *
     * @since
     */
    public function get_shipping_overrides_data_edit()
    {
        $overrides = array_map(function ($o) {
            $o['discount_value'] = wc_format_localized_price($o['discount_value']);
            return $o;
        }, $this->get_advanced_prop('shipping_overrides', array()));

        return $overrides;
    }

    /**
     * Get advanced sort value.
     * 
     * @since 2.5
     * @access public
     * 
     * @return int Advanced sort value.
     */
    public function get_advanced_sort_value() {
        $advanced_sort = $this->get_advanced_prop('coupon_sort_priority', 30);

        switch ($this->get_discount_type()) {
            case 'fixed_product':
                $advanced_sort += 1;
                break;
            case 'percent':
                $advanced_sort += 2;
                break;
            case 'fixed_cart':
                $advanced_sort += 3;
                break;
        }

        return $advanced_sort;
    }

    /**
     * Apply changes made to the objects data prop.
     *
     * @since 3.1
     * @access public
     */
    public function apply_advanced_changes()
    {
        foreach ($this->advanced_changes as $prop => $value) {
            $this->advanced_data[$prop] = $value;
        }
    }

    /**
     * Check if coupon being applied is the main coupon code and not a virtual one.
     *
     * @since 3.1.2
     * @access public
     *
     * @return bool True if coupon code is main code, false otherwise.
     */
    public function is_virtual_coupon_main_code()
    {
        if (!$this->get_advanced_prop('enable_virtual_coupons')) {
            return false;
        }

        return $this->get_code() === strtolower(get_the_title($this->get_id()));
    }
}
