<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class AdminMenu implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    public function hooks()
    {
        \add_action('admin_menu', [$this, 'admin_menu'], 10);
        \add_action('admin_menu', [$this, 'menu_order_count'], 20);
    }
    public function admin_menu()
    {
        \add_submenu_page('woocommerce', \_x('Refund Request', 'Admin menu name', 'flexible-refund-and-return-order-for-woocommerce'), \_x('Refund Request', 'Admin menu name', 'flexible-refund-and-return-order-for-woocommerce'), 'manage_woocommerce', \admin_url('edit.php?post_status=wc-refund-request&post_type=shop_order'), '', 2);
    }
    /**
     * Adds the order processing count to the menu.
     */
    public function menu_order_count()
    {
        global $submenu;
        if (isset($submenu['woocommerce'])) {
            unset($submenu['woocommerce'][0]);
            if (\apply_filters('woocommerce_include_processing_order_count_in_menu', \true) && \current_user_can('edit_others_shop_orders')) {
                $order_count = \wc_orders_count('refund-request');
                if ($order_count) {
                    foreach ($submenu['woocommerce'] as $key => $menu_item) {
                        if (0 === \strpos($menu_item[0], \_x('Refund Request', 'Admin menu name', 'flexible-refund-and-return-order-for-woocommerce'))) {
                            $submenu['woocommerce'][$key][0] .= ' <span class="awaiting-mod update-plugins count-' . \esc_attr($order_count) . '"><span class="processing-count">' . \number_format_i18n($order_count) . '</span></span>';
                            // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                            break;
                        }
                    }
                }
            }
        }
    }
}
