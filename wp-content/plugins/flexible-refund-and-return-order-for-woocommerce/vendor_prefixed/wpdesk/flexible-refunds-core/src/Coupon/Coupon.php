<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Coupon;

use WC_Coupon;
use Exception;
use WC_Order;
/**
 * Create refund coupon.
 *
 * @package WPDesk\Library\FlexibleRefundsCore\Coupon
 */
class Coupon
{
    /**
     * @var WC_Order
     */
    private $order;
    public function __construct(\WC_Order $order)
    {
        $this->order = $order;
    }
    /**
     * @param float $amount
     *
     * @return int
     * @throws \Exception
     */
    public function create_coupon(float $amount) : int
    {
        $coupon = new \WC_Coupon();
        $coupon_code = $this->get_coupon_code();
        $coupon->set_code($this->get_coupon_code());
        $coupon->set_date_created(\current_time('mysql'));
        $coupon->set_usage_limit(1);
        $coupon->set_amount($amount);
        if ($this->order->get_customer_id()) {
            $coupon->set_used_by([$this->order->get_customer_id()]);
        }
        /**
         * Set coupon data before save.
         *
         * @param WC_Coupon $coupon
         *
         * @since 1.0.0
         */
        $coupon = \apply_filters('wpdesk/fr/coupon/before/create', $coupon, $this->order);
        if ($coupon instanceof \WC_Coupon) {
            $coupon = $coupon->save();
            $this->order->add_meta_data('fr_coupon_ids', $coupon);
            $this->order->add_meta_data('fr_coupon_codes', $coupon_code);
            $this->order->save();
        } else {
            throw new \Exception('Failed to save coupon. Check before filter! ');
        }
        return $coupon;
    }
    /**
     * @return string
     */
    private function generate_random_code() : string
    {
        /**
         * Coupon code length.
         *
         * @param int $length
         *
         * @since 1.0.0
         */
        $length = (int) \apply_filters('wpdesk/fr/coupon/code/length', 6);
        return \substr(\str_shuffle(\str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', $length)), 0, $length);
    }
    /**
     * @return string
     */
    private function get_prefix() : string
    {
        /**
         * Defines coupon code prefix.
         *
         * @param string $prefix
         *
         * @since 1.0.0
         */
        return \apply_filters('wpdesk/fr/coupon/code/prefix', 'refund-' . $this->order->get_id() . '-');
    }
    /**
     * @return string
     */
    private function get_suffix() : string
    {
        /**
         * Define coupon code suffix.
         *
         * @since 1.0.0
         */
        return \apply_filters('wpdesk/fr/coupon/code/suffix', '');
    }
    /**
     * Generate coupon code.
     *
     * @return string
     */
    private function get_coupon_code() : string
    {
        $coupon_code = $this->get_prefix() . $this->generate_random_code() . $this->get_suffix();
        /**
         * Filters coupon code.
         *
         * @param string   $coupon_code Coupon code.
         * @param WC_Order $order       Order.
         *
         * @since 1.4.0
         */
        return \apply_filters('wpdesk/fr/coupon/code', $coupon_code, $this->order);
    }
}
