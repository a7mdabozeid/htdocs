<?php
namespace AGCFW\Models;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Model_Interface;
use AGCFW\Objects\Advanced_Gift_Card;
use AGCFW\Objects\Product;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Purchasing module.
 *
 * @since 1.0
 */
class Purchasing implements Model_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.0
     * @access private
     * @var Purchasing
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

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
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Purchasing
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Process advanced gift card add to cart action.
     *
     * @since 1.0
     * @access public
     */
    public function process_gift_card_add_to_cart()
    {
        $product_id        = absint(wp_unslash($_REQUEST['add-to-cart']));
        $quantity          = empty($_REQUEST['quantity']) ? 1 : wc_stock_amount(wp_unslash($_REQUEST['quantity']));
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity); // we are intentionally using the WC hook here.
        $send_to           = isset($_REQUEST['send_to']) ? sanitize_text_field($_REQUEST['send_to']) : 'me';

        if ('friend' === $send_to) {
            $data = array(
                'agcfw_data' => array(
                    'send_to'         => 'friend',
                    'recipient_name'  => isset($_REQUEST['recipient_name']) ? sanitize_text_field($_REQUEST['recipient_name']) : '',
                    'recipient_email' => isset($_REQUEST['recipient_email']) ? sanitize_text_field($_REQUEST['recipient_email']) : '',
                    'short_message'   => isset($_REQUEST['short_message']) ? sanitize_text_field($_REQUEST['short_message']) : '',
                ),
            );
        } else {
            $data = array(
                'agcfw_data' => array(
                    'send_to' => 'me',
                ),
            );
        }

        if ($passed_validation && false !== $this->_add_to_cart($product_id, $quantity, $data)) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
            return true;
        }

        return false;
    }

    /**
     * Appending missing cart item data for gift cards.
     * This is for when products are added to cart directly via AJAX.
     * 
     * @since 1.0
     * @access public
     * 
     * @param array $cart_item_data Cart item data.
     * @param int   $product_id     Product ID.
     * @return array Filtered cart item data.
     */
    public function append_missing_gift_card_data($cart_item_data, $product_id) 
    {
        if (!isset($cart_item_data['agcfw_data'])) {
            $product = wc_get_product($product_id);

            if ($product instanceof Product) {
                $cart_item_data = array(
                    'agcfw_data' => array(
                        'send_to' => 'me',
                    ),
                );
            }
        }

        return $cart_item_data;
    }

    /**
     * Append gift card recipient data when "send to" is set to "friend" to the displayed item data list in the cart item row.
     *
     * @since 1.0
     * @access public
     *
     * @param array $item_data Displayed cart item data.
     * @param array $cart_item Cart item data.
     * @return array Filtered Displayed cart item data.
     */
    public function append_gift_card_cart_item_recipient_data($item_data, $cart_item)
    {
        if (isset($cart_item['agcfw_data']) && isset($cart_item['agcfw_data']['send_to']) && 'friend' === $cart_item['agcfw_data']['send_to']) {

            $item_data[] = array(
                'key'   => __('Recipient name', 'advanced-gift-cards-for-woocommerce'),
                'value' => wp_unslash($cart_item['agcfw_data']['recipient_name']),
            );

            $item_data[] = array(
                'key'   => __('Recipient email', 'advanced-gift-cards-for-woocommerce'),
                'value' => wp_unslash($cart_item['agcfw_data']['recipient_email']),
            );

            if (isset($cart_item['agcfw_data']['short_message']) && $cart_item['agcfw_data']['short_message']) {
                $item_data[] = array(
                    'key'   => __('Short message', 'advanced-gift-cards-for-woocommerce'),
                    'value' => wp_unslash($cart_item['agcfw_data']['short_message']),
                );
            }
        }

        return $item_data;
    }

    /**
     * Save gift card data as order line item meta data during checkout process.
     *
     * @since 1.0
     * @access public
     *
     * @param WC_Order_Item_Product $item          Order item object.
     * @param string                $cart_item_key Cart item key.
     * @param array                 $cart_item     Cart item data.
     */
    public function save_gift_card_data_as_order_line_item_meta($item, $cart_item_key, $cart_item)
    {
        // skip if item is not a gift card.
        if (!isset($cart_item['agcfw_data']) || !$cart_item['data'] instanceof Product) {
            return;
        }

        $send_to = sanitize_text_field($cart_item['agcfw_data']['send_to']);

        $item->add_meta_data($this->_constants->GIFT_CARD_SEND_TO_META, $send_to);
        $item->add_meta_data($this->_constants->GIFT_CARD_DATA, $cart_item['data']->get_gift_card_data());

        if ('friend' === $send_to) {
            $item->add_meta_data($this->_constants->GIFT_CARD_RECIPIENT_NAME_META, sanitize_text_field($cart_item['agcfw_data']['recipient_name']));
            $item->add_meta_data($this->_constants->GIFT_CARD_RECIPIENT_EMAIL_META, sanitize_text_field($cart_item['agcfw_data']['recipient_email']));

            if (isset($cart_item['agcfw_data']['short_message'])) {
                $item->add_meta_data($this->_constants->GIFT_CARD_SHORT_MESSAGE_META, sanitize_text_field($cart_item['agcfw_data']['short_message']));
            }
        }
    }

    /**
     * Create gift card for order when order status is changed to either "processing" or "completed".
     *
     * @since 1.0
     * @access public
     *
     * @param int      $order_id    Order ID.
     * @param string   $prev_status Previous status.
     * @param string   $new_status  New status.
     * @param WC_Order $order       Order object.
     */
    public function create_gift_card_for_order($order_id, $prev_status, $new_status, $order)
    {
        // skip if status is not processing or completed.
        if (!in_array($new_status, wc_get_is_paid_statuses())) {
            return;
        }

        foreach ($order->get_items('line_item') as $item) {

            // skip if item is not a gift card, or when gift card was already created for the item.
            if (!$item->get_meta($this->_constants->GIFT_CARD_SEND_TO_META)) {
                continue;
            }

            if ($gift_card_id = $item->get_meta($this->_constants->GIFT_CARD_ENTRY_ID_META)) {

                $gift_card = new Advanced_Gift_Card($gift_card_id);

                // Only update gift cards that have been invalidated.
                if ('invalid' === $gift_card->get_prop('status')) {
                    $gift_card->set_prop('status', 'pending');
                    $gift_card->save();
                }

            } else {
                $gift_card = new Advanced_Gift_Card();
                $data      = $item->get_meta($this->_constants->GIFT_CARD_DATA);

                $gift_card->set_prop('order_item_id', $item->get_id());
                $gift_card->set_prop('value', $data['value']);
                $gift_card->set_prop('status', 'pending');

                if (isset($data['expiry'])) {
                    $gift_card->set_date_expire_by_interval($data['expiry']);
                }

                $gift_card->save();

                $item->add_meta_data($this->_constants->GIFT_CARD_ENTRY_ID_META, $gift_card->get_id());
                $item->save_meta_data();
            }

            do_action('agcfw_after_create_gift_card_for_order', $gift_card, $item, $order);
        }
    }

    /**
     * Invalidate gift card when order status is changed from either "processing" or "completed" to either "cancelled", "refunded", or "failed.
     *
     * @since 1.0
     * @access public
     *
     * @param int      $order_id    Order ID.
     * @param string   $prev_status Previous status.
     * @param string   $new_status  New status.
     * @param WC_Order $order       Order object.
     */
    public function invalidate_gift_card_on_order_status_change($order_id, $prev_status, $new_status, $order)
    {
        if (!in_array($prev_status, wc_get_is_paid_statuses()) || !in_array($new_status, array('cancelled', 'refunded', 'failed'))) {
            return;
        }

        foreach ($order->get_items('line_item') as $item) {

            if ($gift_card_id = $item->get_meta($this->_constants->GIFT_CARD_ENTRY_ID_META)) {

                $gift_card = new Advanced_Gift_Card($gift_card_id);

                // Only invalidate gift cards that have not been yet used.
                if ('pending' === $gift_card->get_prop('status')) {
                    $gift_card->set_prop('status', 'invalid');
                    $gift_card->save();
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Utility functions.
    |--------------------------------------------------------------------------
     */

    /**
     * Add advanced gift card to cart.
     *
     * @since 1.0
     * @access private
     *
     * @param int   $product_id Product ID.
     * @param int   $quantity   Item quantity.
     * @param array $data       Gift card data.
     * @return bool True if successfully added to cart, false otherwise.
     */
    private function _add_to_cart($product_id, $quantity, $data = array())
    {
        return \WC()->cart->add_to_cart(
            $product_id,
            $quantity,
            0,
            array(),
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Purchasing class.
     *
     * @since 1.0
     * @access public
     * @inherit AGCFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('woocommerce_add_to_cart_handler_advanced_gift_card', array($this, 'process_gift_card_add_to_cart'));
        add_filter('woocommerce_add_cart_item_data', array($this, 'append_missing_gift_card_data'), 10, 2);
        add_filter('woocommerce_get_item_data', array($this, 'append_gift_card_cart_item_recipient_data'), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'save_gift_card_data_as_order_line_item_meta'), 10, 3);
        add_action('woocommerce_order_status_changed', array($this, 'create_gift_card_for_order'), 10, 4);
        add_action('woocommerce_order_status_changed', array($this, 'invalidate_gift_card_on_order_status_change'), 10, 4);
    }

}
