<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;

use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Order;
use WP_Comment;
class OrderNote implements \FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    public function hooks()
    {
        \add_filter('woocommerce_order_note_class', [$this, 'order_note_css'], 10, 2);
        \add_filter('woocommerce_get_order_note', [$this, 'get_note_meta'], 10, 2);
    }
    /**
     * @param array      $comment_data
     * @param WP_Comment $data
     *
     * @return array
     */
    public function get_note_meta(array $comment_data, \WP_Comment $data) : array
    {
        $is_fr_note = (bool) \get_comment_meta($data->comment_ID, 'wpdesk_fr_note', \true);
        $comment_data['fr_note'] = $is_fr_note ? 'yes' : 'no';
        return $comment_data;
    }
    /**
     * @param array  $css_classes
     * @param object $note
     *
     * @return array
     */
    public function order_note_css(array $css_classes, $note) : array
    {
        if (isset($note->fr_note) && $note->fr_note === 'yes') {
            $css_classes[] = 'fr-note';
        }
        return $css_classes;
    }
    /**
     * @param string $note
     *
     * @return int
     */
    public function add_refund_note(\WC_Order $order, string $note) : int
    {
        if (!$order->get_id()) {
            return 0;
        }
        if (\is_user_logged_in() && \current_user_can('edit_shop_orders', $order->get_id())) {
            $user = \get_user_by('id', \get_current_user_id());
            $comment_author = $user->display_name;
            $comment_author_email = $user->user_email;
        } else {
            $comment_author = \esc_html__('WooCommerce', 'woocommerce');
            $comment_author_email = \strtolower(\esc_html__('WooCommerce', 'woocommerce')) . '@';
            $comment_author_email .= isset($_SERVER['HTTP_HOST']) ? \str_replace('www.', '', \sanitize_text_field(\wp_unslash($_SERVER['HTTP_HOST']))) : 'noreply.com';
            // WPCS: input var ok.
            $comment_author_email = \sanitize_email($comment_author_email);
        }
        /**
         * Filters comment data before insert.
         *
         * @since 1.0.0
         */
        $comment_data = \apply_filters('wpdesk/fr/action/refund/note/data', ['comment_post_ID' => $order->get_id(), 'comment_author' => $comment_author, 'comment_author_email' => $comment_author_email, 'comment_author_url' => '', 'comment_content' => $note, 'comment_agent' => 'WooCommerce', 'comment_type' => 'order_note', 'comment_parent' => 0, 'comment_approved' => 1], ['order_id' => $order->get_id(), 'is_fr_note' => 1]);
        $comment_id = \wp_insert_comment($comment_data);
        \add_comment_meta($comment_id, 'wpdesk_fr_note', 1);
        /**
         * Action hook fired after an order note is added.
         *
         * @param int      $order_note_id Order note ID.
         * @param WC_Order $order         Order data.
         *
         * @since 1.0.0
         */
        \do_action('wpdesk/fr/action/refund/note', $comment_id, $order);
        return $comment_id;
    }
}
