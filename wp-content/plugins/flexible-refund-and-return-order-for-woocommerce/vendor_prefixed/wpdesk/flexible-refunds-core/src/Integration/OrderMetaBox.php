<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use WC_Order;
use WP_Post;
class OrderMetaBox implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var PersistentContainer
     */
    private $settings;
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer, \FRFreeVendor\WPDesk\Persistence\PersistentContainer $settings)
    {
        $this->renderer = $renderer;
        $this->settings = $settings;
    }
    public function hooks()
    {
        \add_action('add_meta_boxes', [$this, 'add_meta_boxes'], 11, 2);
    }
    public function add_meta_boxes($post_type, $post)
    {
        if ($post->ID) {
            $order = \wc_get_order($post->ID);
            if ($order instanceof \WC_Order) {
                $meta = $order->get_meta('fr_refund_request_data');
                if (!empty($meta)) {
                    \add_meta_box($post_type . '_fr_meta_box', \__('Refund Request', 'flexible-refund-and-return-order-for-woocommerce'), [$this, 'fr_meta_box_content'], ['shop_order', 'shop_subscription'], 'normal', 'high', ['order' => $order]);
                }
            }
        }
    }
    /**
     * @param WP_Post $post
     * @param array   $data
     *
     * @return void
     */
    public function fr_meta_box_content(\WP_Post $post, array $data)
    {
        $order = $data['args']['order'];
        $this->renderer->output_render('order/refund-meta-box', ['order' => $order, 'post' => $post, 'settings' => $this->settings]);
    }
}
