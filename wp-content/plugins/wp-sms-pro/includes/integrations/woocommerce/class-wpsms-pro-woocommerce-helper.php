<?php

namespace WP_SMS\Pro\WooCommerce;

use WP_SMS\Option;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Helper
{
    protected static $wc_mobile_field;

    public function __construct()
    {
        // Set mobile field type
        if (!self::$wc_mobile_field) {
            self::$wc_mobile_field = Option::getOption('wc_mobile_field', true);

            switch (self::$wc_mobile_field) {
                case  'add_new_field':
                    self::$wc_mobile_field = \WP_SMS\Helper::getUserMobileFieldName();
                    break;
                case  'used_current_field':
                    self::$wc_mobile_field = 'billing_phone';
                    break;
                default:
                    self::$wc_mobile_field = '';
            }
        }
    }

    /**
     * Get WooCommerce customers
     *
     * @param string $role
     * @param bool $count
     *
     * @return array|int
     */
    public static function getCustomersNumbers($roles = [])
    {
        // Check the WC mobile enabled or not
        if (!self::$wc_mobile_field) {
            return array();
        }

        $args = array(
            'meta_query' => array(
                array(
                    'key'     => self::$wc_mobile_field,
                    'compare' => '>',
                ),
                array(
                    'key'     => 'billing_phone',
                    'compare' => '>',
                ),
            ),
            'fields'     => 'all_with_meta'
        );

        if ($roles) {
            $args['role__in'] = $roles;
        }

        $customers = get_users($args);
        $numbers   = array();

        foreach ($customers as $customer) {
            $numbers[] = $customer->{self::$wc_mobile_field};
        }

        return $numbers;
    }

    /**
     * Get mobile field type
     *
     * @return string
     */
    public static function getMobileField()
    {
        return self::$wc_mobile_field;
    }
}

new Helper();