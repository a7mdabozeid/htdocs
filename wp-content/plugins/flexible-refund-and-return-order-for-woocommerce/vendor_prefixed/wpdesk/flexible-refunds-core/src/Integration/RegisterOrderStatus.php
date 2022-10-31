<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class RegisterOrderStatus implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const REQUEST_REFUND_STATUS = 'wc-refund-request';
    /**
     * @return void
     */
    public function hooks() : void
    {
        \add_action('init', [$this, 'register_status'], 200);
        \add_filter('wc_order_statuses', [$this, 'add_wc_refund_status']);
    }
    /**
     * @return void
     */
    public function register_status() : void
    {
        \register_post_status(self::REQUEST_REFUND_STATUS, ['label' => \esc_html__('Refund Request', 'flexible-refund-and-return-order-for-woocommerce'), 'public' => \true, 'exclude_from_search' => \false, 'show_in_admin_all_list' => \true, 'show_in_admin_status_list' => \true, 'label_count' => \_n_noop('Refund Request <span class="count">(%s)</span>', 'Refund Request <span class="count">(%s)</span>')]);
    }
    /**
     * @param array $statuses
     *
     * @return array
     */
    public function add_wc_refund_status(array $statuses) : array
    {
        $statuses[self::REQUEST_REFUND_STATUS] = \esc_html__('Refund Request', 'flexible-refund-and-return-order-for-woocommerce');
        return $statuses;
    }
}
