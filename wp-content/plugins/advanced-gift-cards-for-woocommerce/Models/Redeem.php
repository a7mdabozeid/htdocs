<?php

namespace AGCFW\Models;

use ACFWF\Models\Objects\Store_Credit_Entry;
use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Initiable_Interface;
use AGCFW\Interfaces\Model_Interface;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Redeem module.
 *
 * @since 1.0
 */
class Redeem implements Model_Interface, Initiable_Interface
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
     * @var Redeem
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
     * @return Redeem
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /*
    |--------------------------------------------------------------------------
    | My Account UI
    |--------------------------------------------------------------------------
     */

    /**
     * Display my account gift card redeem form.
     *
     * @since 1.0
     * @access public
     */
    public function display_my_account_gift_card_redeem_form()
    {
        $args = $this->_get_default_redeem_form_template_args(array('agcfw-myaccount-redeem'));

        $this->_helper_functions->load_template(
            'agcfw-redeem-gift-card.php',
            $args
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout UI
    |--------------------------------------------------------------------------
     */

    /**
     * Display checkout gift card redeem form.
     *
     * @since 1.0
     * @access public
     */
    public function display_checkout_gift_card_redeem_form()
    {
        $args = $this->_get_default_redeem_form_template_args(array('agcfw-checkout-redeem'));

        $this->_helper_functions->load_template(
            'agcfw-redeem-gift-card.php',
            $args
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render gutenberg block / shortcode
    |--------------------------------------------------------------------------
     */

    /**
     * Render redeem form block.
     *
     * @since 1.0
     * @access private
     *
     * @param array $attributes Block attributes
     * @return string HTML markup of block.
     */
    public function render_redeem_form_block($attributes = array())
    {
        $classnames = array('agcfw-block-redeem');

        if (isset($attributes['className']) && $attributes['className']) {
            $classnames[] = $attributes['className'];
        }

        $args = wp_parse_args($attributes, $this->_get_default_redeem_form_template_args($classnames));

        ob_start();

        $this->_helper_functions->load_template(
            'agcfw-redeem-gift-card.php',
            $args
        );

        return ob_get_clean();
    }

    /**
     * Render redeem form via shortcode and enqueueing scripts and styles.
     *
     * @since 1.0
     * @access public
     *
     * @param array $attributes Shortcode attributes
     * @return string HTML markup of redeem form.
     */
    public function render_redeem_form_shortcode($attributes = array())
    {
        wp_enqueue_style('agcfw-redeem-gift-card');
        wp_enqueue_script('agcfw-redeem-gift-card');

        return $this->render_redeem_form_block($attributes);
    }

    /*
    |--------------------------------------------------------------------------
    | Redeem Implementation
    |--------------------------------------------------------------------------
     */

    /**
     * Validate gift card.
     *
     * @since 1.0
     * @access private
     *
     * @param Advanced_Gift_Card $gift_card Gift card object.
     * @param bool|WP_Error      true when valid, error object on failure.
     */
    private function _validate_gift_card($gift_card)
    {
        if (!$gift_card || !$gift_card->get_id() || 'pending' !== $gift_card->get_prop('status')) {
            return new \WP_Error(
                'agcfw_gift_card_invalid',
                __("The provided gift card doesn't exist or is invalid", 'advanced-gift-cards-for-woocommerce'),
                array('status' => 400, 'gift_card' => $gift_card)
            );
        }

        // validate gift card expiry
        if ($gift_card->get_date('date_expire')) {
            $now = new \WC_DateTime("now", new \DateTimeZone($this->_helper_functions->get_site_current_timezone()));

            if ($now > $gift_card->get_date('date_expire')) {
                return new \WP_Error(
                    'agcfw_gift_card_expired',
                    __("The gift card has already expired", 'advanced-gift-cards-for-woocommerce'),
                    array('status' => 400, 'gift_card' => $gift_card)
                );
            }
        }

        return true;
    }

    /**
     * Redeem gift card as store credits.
     *
     * @since 1.0
     * @access private
     *
     * @param Advanced_Gift_Card $gift_card Gift card object.
     * @param int                $user_id   User ID.
     */
    private function _redeem_gift_card($gift_card, $user_id = 0)
    {
        // validate gift card before proceeding.
        !$check = $this->_validate_gift_card($gift_card);
        if (is_wp_error($check)) {
            return $check;
        }

        $store_credit_entry = new Store_Credit_Entry();
        $user_id            = $user_id ? $user_id : get_current_user_id();

        // try catch is added here to prevent fatal errors when fetching invalid order item IDs.
        try {
            $order_item = new \WC_Order_Item_Product($gift_card->get_prop('order_item_id'));
            $order_id = $order_item->get_order_id();
        } catch(\Exception $e) {
            $order_id = 0;
        }

        $store_credit_entry->set_prop('amount', $gift_card->get_prop('value'));
        $store_credit_entry->set_prop('user_id', $user_id);
        $store_credit_entry->set_prop('type', 'increase');
        $store_credit_entry->set_prop('action', 'gift_card');
        $store_credit_entry->set_prop('object_id', $order_id);

        // save/create store credit entry.
        $entry_id = $store_credit_entry->save();

        if (is_wp_error($entry_id)) {
            return $entry_id;
        }

        $gift_card->set_prop('user_id', $user_id);
        $gift_card->set_prop('status', 'used');
        $gift_card->save();

        return $entry_id;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Functions
    |--------------------------------------------------------------------------
     */

    /**
     * AJAX redeem gift card to store credits.
     *
     * @since 1.0
     * @access public
     */
    public function ajax_redeem_gift_card()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $response = array('status' => 'fail', 'error_msg' => __('Invalid AJAX call', 'advanced-gift-cards-for-woocommerce'));
        } elseif (!is_user_logged_in() || !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'agcfw_redeem_gift_card')) {
            $response = array('status' => 'fail', 'error_msg' => __('You are not allowed to do this', 'advanced-gift-cards-for-woocommerce'));
        } elseif (!isset($_POST['gift_card_code'])) {
            $response = array('status' => 'fail', 'error_msg' => __('Missing required post data', 'advanced-gift-cards-for-woocommerce'));
        } else {
            $gift_card_code = sanitize_text_field($_POST['gift_card_code']);
            $gift_card      = $this->_helper_functions->get_gift_card_by_code($gift_card_code);

            $check = $this->_redeem_gift_card($gift_card);

            if (is_wp_error($check)) {
                $response = array(
                    'status'    => 'fail',
                    'error_msg' => $check->get_error_message(),
                );
            } else {
                $response = array(
                    'status'  => 'success',
                    'message' => __('Gift card was redeemed successfully!', 'advanced-gift-cards-for-woocommerce'),
                );
            }
        }

        if ('fail' === $response['status']) {
            wc_add_notice($response['error_msg'], 'error');
        } else {
            wc_add_notice($response['message'], 'success');
        }

        @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        echo wp_json_encode($response);
        wp_die();
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
     */

    /**
     * Get default redeem form template arguments.
     *
     * @since 1.0
     * @access private
     *
     * @return array
     */
    private function _get_default_redeem_form_template_args($classnames = array())
    {
        if (is_checkout()) {
            $classnames[] = 'agcfw-toggle-redeem-form';
        }

        return apply_filters('agcfw_default_redeem_form_template_args', array(
            'id'                => 'agcfw-redeem-gift-card',
            'classnames'        => $classnames,
            'title'             => __('Redeem a gift card?', 'advanced-gift-cards-for-woocommerce'),
            'description'       => __('Enter your gift card claim code.', 'advanced-gift-cards-for-woocommerce'),
            'caret_img_src'     => is_checkout() ? $this->_constants->IMAGES_ROOT_URL . 'caret.svg' : '',
            'tooltip_link_text' => __('How do I find the claim code?', 'advanced-gift-cards-for-woocommerce'),
            'tooltip_title'     => __('Gift Card Claim Code', 'advanced-gift-cards-for-woocommerce'),
            'tooltip_content'   => __('Your gift card claim code is found inside the email sent from the store when the gift card was purchased.', 'advanced-gift-cards-for-woocommerce'),
            'input_placeholder' => __('Enter code', 'advanced-gift-cards-for-woocommerce'),
            'button_text'       => __('Redeem', 'advanced-gift-cards-for-woocommerce'),
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin init.
     *
     * @since 1.0
     * @access public
     * @implements ACFWF\Interfaces\Initializable_Interface
     */
    public function initialize()
    {
        add_action('wp_ajax_agcfw_redeem_gift_card', array($this, 'ajax_redeem_gift_card'));
    }

    /**
     * Execute Redeem class.
     *
     * @since 1.0
     * @access public
     * @inherit AGCFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_action('acfw_store_credits_my_account_after', array($this, 'display_my_account_gift_card_redeem_form'), 90);
        add_action('woocommerce_checkout_order_review', array($this, 'display_checkout_gift_card_redeem_form'), 11);
        add_shortcode('agcfw_gift_card_redeem_form', array($this, 'render_redeem_form_shortcode'));
    }
}
